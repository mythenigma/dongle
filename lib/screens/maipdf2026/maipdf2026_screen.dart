import 'dart:async';
import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:http/http.dart' as http;
import 'package:file_picker/file_picker.dart';
import 'package:qr_flutter/qr_flutter.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../utils/maipdf_cloud_auth_service.dart';

/// 原生实现的 MaiPDF Cloud Sharing 页面（对应 maipdf2026.html）
class Maipdf2026Screen extends StatefulWidget {
  const Maipdf2026Screen({super.key});

  @override
  State<Maipdf2026Screen> createState() => _Maipdf2026ScreenState();
}

class _Maipdf2026ScreenState extends State<Maipdf2026Screen> {
  static const String _baseUrl = 'https://maipdf.com/pdf';
  static const String _workerApiUrl = 'https://fetch.maipdf.com';

  int _currentStep = 1;
  String _uploadStatus = 'Network Info';
  String?
  _filePath; // sender value after upload (display via _fileNameDisplayController)
  String? _fileId;
  bool _uploading = false;
  bool _submitting = false;
  String? _submitError;

  // Step 2 form
  final _limitController = TextEditingController(text: '');
  final _sessionSecondsController = TextEditingController(text: '');
  bool _dynamicWatermark = false;
  String _viewType = 'straight'; // straight, obstacle, topen
  String _expirationPreset = '';
  final _expirationCustomDaysController = TextEditingController(text: '');
  bool _enableTelegram = false;
  final _telegramChatIdController = TextEditingController(text: '');
  String? _telegramBindToken; // 从 /tg/issue 拿到，用于 /tg/status 取 chat_id
  String _telegramStatusText = 'Telegram: Not linked';
  bool _telegramStatusLoading = false;
  bool _enableEmailValidation = false;
  final _emailAddressesController = TextEditingController(text: '');
  final _fileNameDisplayController = TextEditingController(text: 'File');
  final _resultLinkController = TextEditingController();

  // Step 3 result
  String? _resultLink;
  String? _readCode;
  String? _modifyCode;
  String? _cloudRecordStatus;

  @override
  void initState() {
    super.initState();
    _loadBootstrap();
  }

  Future<void> _loadBootstrap() async {
    try {
      await _syncCloudAccountBestEffort();
      final r = await http.get(
        Uri.parse('$_baseUrl/maipdf2026_backend.php?action=bootstrap'),
        headers: {
          'Cache-Control': 'no-store',
          'X-Requested-With': 'FlutterApp',
          ...MaiPdfCloudAuthService.instance.cookieHeaders,
        },
      );
      if (r.statusCode == 200) {
        final data = jsonDecode(r.body) as Map<String, dynamic>?;
        if (data != null && data['status'] == 'ok') {
          setState(() {
            _uploadStatus = (data['ip'] as String?) ?? 'Ready';
          });
        }
      }
    } catch (_) {}
  }

  Future<void> _syncCloudAccountBestEffort() async {
    try {
      await MaiPdfCloudAuthService.instance.ensureSession();
    } catch (_) {
      // Cloud account recording must never block upload or link generation.
    }
  }

  void _showUploadError(String message) {
    if (!mounted) return;
    setState(() {
      _uploadStatus = message;
      _uploading = false;
    });
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: Colors.red.shade700,
        duration: const Duration(seconds: 5),
      ),
    );
  }

  /// 与网页一致：先请求 /tg/issue 拿到 token 和 deep_link，再打开 t.me/maipdfbot?start=TOKEN
  Future<void> _openTelegramBind() async {
    setState(() {
      _telegramStatusText = 'Telegram: Requesting…';
      _telegramStatusLoading = true;
    });
    try {
      final r = await http.post(
        Uri.parse('$_workerApiUrl/tg/issue'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({'ip': '', 'ua': 'Flutter'}),
      );
      if (!mounted) return;
      if (r.statusCode != 200) {
        setState(() {
          _telegramStatusText = 'Telegram: Token failed';
          _telegramStatusLoading = false;
        });
        return;
      }
      final data = jsonDecode(r.body) as Map<String, dynamic>?;
      final token = data?['token'] as String?;
      final deepLink = data?['deep_link'] as String?;
      final link = (deepLink != null && deepLink.isNotEmpty)
          ? deepLink
          : (token != null && token.isNotEmpty
                ? 'https://t.me/maipdfbot?start=$token'
                : 'https://t.me/maipdfbot');
      if (token != null && token.isNotEmpty) {
        setState(() {
          _telegramBindToken = token;
          _telegramStatusText =
              'Telegram: Open bot → send /start → tap Get chat_id';
          _telegramStatusLoading = false;
        });
        await launchUrl(Uri.parse(link));
      } else {
        setState(() {
          _telegramStatusText = 'Telegram: Not linked';
          _telegramStatusLoading = false;
        });
        await launchUrl(Uri.parse(link));
      }
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _telegramStatusText = 'Telegram: Token failed';
        _telegramStatusLoading = false;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Telegram: $e'),
          backgroundColor: Colors.red.shade700,
        ),
      );
    }
  }

  /// 调用 /tg/status 用当前 token 取 chat_id，填到输入框
  Future<void> _fetchTelegramChatId() async {
    String? token = _telegramBindToken;
    if (token == null || token.isEmpty) {
      await _openTelegramBind();
      return;
    }
    setState(() {
      _telegramStatusText = 'Telegram: Checking…';
      _telegramStatusLoading = true;
    });
    try {
      final r = await http.post(
        Uri.parse('$_workerApiUrl/tg/status'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({'token': token}),
      );
      if (!mounted) return;
      final data = jsonDecode(r.body) as Map<String, dynamic>?;
      final status = data?['status'] as String?;
      final chatId = data?['chat_id'];
      if (status == 'ok' &&
          chatId != null &&
          chatId.toString().trim().isNotEmpty) {
        final id = chatId.toString().trim().replaceAll(RegExp(r'\D'), '');
        if (id.isNotEmpty) {
          _telegramChatIdController.text = id;
          setState(() {
            _telegramBindToken = null;
            _telegramStatusText = 'Telegram: Linked ($id)';
            _telegramStatusLoading = false;
          });
          if (mounted) {
            ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(
                content: Text('chat_id 已填入'),
                backgroundColor: Colors.green,
              ),
            );
          }
          return;
        }
      }
      setState(() {
        _telegramStatusText = status == 'pending'
            ? 'Telegram: Not linked (send /start in bot)'
            : 'Telegram: Not linked';
        _telegramStatusLoading = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _telegramStatusText = 'Telegram: Status failed';
        _telegramStatusLoading = false;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Get chat_id: $e'),
          backgroundColor: Colors.red.shade700,
        ),
      );
    }
  }

  Future<void> _pickAndUpload() async {
    final result = await FilePicker.platform.pickFiles(
      type: FileType.custom,
      allowedExtensions: ['pdf'],
      allowMultiple: false,
    );
    if (result == null || result.files.isEmpty) {
      if (mounted) {
        ScaffoldMessenger.of(
          context,
        ).showSnackBar(const SnackBar(content: Text('未选择文件')));
      }
      return;
    }
    final file = result.files.single;
    // Web 上访问 file.path 会抛错，必须先取 bytes；移动端用 path 更省内存
    String? path;
    try {
      path = file.path;
    } catch (_) {
      path = null;
    }
    final bytes = file.bytes;

    if ((path == null || path.isEmpty) && (bytes == null || bytes.isEmpty)) {
      _showUploadError('无法读取文件');
      return;
    }

    if (!mounted) return;
    setState(() {
      _uploading = true;
      _uploadStatus = '正在上传…';
    });

    try {
      await _syncCloudAccountBestEffort();
      final uri = Uri.parse('$_baseUrl/r2upload.php');
      final request = http.MultipartRequest('POST', uri);
      request.headers['X-Requested-With'] = 'FlutterApp';
      request.headers.addAll(MaiPdfCloudAuthService.instance.cookieHeaders);

      if (path != null && path.isNotEmpty) {
        request.files.add(
          await http.MultipartFile.fromPath('file', path, filename: file.name),
        );
      } else if (bytes != null && bytes.isNotEmpty) {
        request.files.add(
          http.MultipartFile.fromBytes('file', bytes, filename: file.name),
        );
      } else {
        _showUploadError('无法读取文件');
        return;
      }

      final streamed = await request.send().timeout(
        const Duration(seconds: 90),
        onTimeout: () => throw TimeoutException('上传超时'),
      );
      final response = await http.Response.fromStream(streamed);
      final body = response.body;

      if (!mounted) return;
      if (response.statusCode != 200) {
        _showUploadError('上传失败: HTTP ${response.statusCode}');
        return;
      }

      String? pathValue;
      try {
        final j = jsonDecode(body) as Map<String, dynamic>?;
        if (j != null) {
          if (j['status'] == 'error') {
            final msg = j['message'] as String? ?? 'Unknown error';
            _showUploadError('服务器返回: $msg');
            return;
          }
          pathValue = j['path'] as String? ?? j['filepath'] as String?;
          _fileId = j['file_id'] as String?;
        }
      } catch (_) {}
      if (pathValue == null && body.contains('"path"')) {
        final match = RegExp(r'"path"\s*:\s*"([^"]+)"').firstMatch(body);
        if (match != null) pathValue = match.group(1);
      }
      if (pathValue == null || pathValue.isEmpty) {
        _showUploadError('服务器未返回文件路径');
        return;
      }

      _fileNameDisplayController.text = pathValue;
      if (!mounted) return;
      setState(() {
        _uploading = false;
        _uploadStatus = '上传完成';
        _filePath = pathValue;
        _currentStep = 2;
      });
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('上传成功'), backgroundColor: Colors.green),
        );
      }
    } on TimeoutException catch (_) {
      _showUploadError('上传超时，请检查网络后重试');
    } catch (e) {
      _showUploadError('上传失败: $e');
    }
  }

  /// 与网页 JS 一致：根据预设算出过期时间戳（秒）。0 或空表示永久。
  int _computeExpirationTs() {
    final preset = _expirationPreset;
    if (preset.isEmpty || preset == 'unlimited') return 0;
    double days = 0;
    if (preset == '1h') {
      days = 1 / 24;
    } else if (preset == '3h') {
      days = 3 / 24;
    } else if (preset == '24h') {
      days = 1;
    } else if (preset == '5d') {
      days = 5;
    } else if (preset == 'custom') {
      final n = double.tryParse(_expirationCustomDaysController.text.trim());
      if (n != null && n > 0) days = n;
    }
    if (days <= 0) return 0;
    final now = DateTime.now().toUtc();
    final expiry = now.add(
      Duration(microseconds: (days * 24 * 60 * 60 * 1000000).round()),
    );
    return expiry.millisecondsSinceEpoch ~/ 1000;
  }

  Future<void> _submitForm() async {
    if (_filePath == null || _filePath!.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please upload a file first.')),
      );
      return;
    }
    final limitStr = _limitController.text.trim();
    final passwordStr = _sessionSecondsController.text.trim();
    final limit = int.tryParse(limitStr) ?? 1;
    final password = int.tryParse(passwordStr) ?? 30;
    if (limit < 1 || password < 30) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Access limit must be ≥1, session seconds ≥30.'),
        ),
      );
      return;
    }
    setState(() {
      _submitting = true;
      _submitError = null;
    });
    try {
      await _syncCloudAccountBestEffort();
      final uri = Uri.parse('$_baseUrl/maipdf2026_backend.php');
      final request = http.MultipartRequest('POST', uri);
      request.headers['X-Requested-With'] = 'FlutterApp';
      request.headers.addAll(MaiPdfCloudAuthService.instance.cookieHeaders);
      request.fields['sender'] = _filePath!;
      if (_fileId != null) request.fields['file_id'] = _fileId!;
      request.fields['limit'] = limit.toString();
      request.fields['password'] = password.toString();
      request.fields['darkmode'] = _dynamicWatermark ? 'yes' : '';
      request.fields['zhangai'] = _viewType;
      final expTs = _computeExpirationTs();
      request.fields['expiration_ts'] = expTs > 0 ? expTs.toString() : '';
      if (_expirationPreset == 'custom') {
        request.fields['expiration_day'] = _expirationCustomDaysController.text
            .trim();
      } else {
        request.fields['expiration_day'] = '';
      }
      request.fields['enableTelegramAlert'] = _enableTelegram ? 'yes' : '';
      if (_enableTelegram) {
        final chatId = _telegramChatIdController.text.trim().replaceAll(
          RegExp(r'\D'),
          '',
        );
        request.fields['mailalert'] = chatId;
      }
      request.fields['enableEmailValidation'] = _enableEmailValidation
          ? 'yes'
          : '';
      if (_enableEmailValidation) {
        request.fields['emailAddresses'] = _emailAddressesController.text
            .trim();
      }
      final streamed = await request.send();
      final response = await http.Response.fromStream(streamed);
      final html = response.body;
      if (response.statusCode != 200) {
        setState(() {
          _submitting = false;
          _submitError = 'Server error: ${response.statusCode}';
        });
        return;
      }
      _parseResultFromHtml(html);
      await _recordCloudLinkForAccount();
      setState(() {
        _submitting = false;
        _currentStep = 3;
      });
    } catch (e) {
      setState(() {
        _submitting = false;
        _submitError = 'Request failed: $e';
      });
    }
  }

  void _parseResultFromHtml(String html) {
    String? linkFull;
    // 后端 PHP 输出顺序是 value 在前、id="myInput" 在后，先按此匹配
    final inputMatchValueFirst = RegExp(
      r'value="([^"]*)"[^>]*id="myInput"',
    ).firstMatch(html);
    if (inputMatchValueFirst != null) {
      linkFull = inputMatchValueFirst.group(1)?.trim();
    }
    if (linkFull == null) {
      final inputMatch = RegExp(
        r'id="myInput"[^>]*value="([^"]*)"',
        dotAll: true,
      ).firstMatch(html);
      if (inputMatch != null) linkFull = inputMatch.group(1)?.trim();
    }
    if (linkFull == null) {
      final inputMatch2 = RegExp(
        r'id="myInput"[^>]*>[\s\S]*?value="([^"]*)"',
      ).firstMatch(html);
      linkFull = inputMatch2?.group(1)?.trim();
    }
    if (linkFull == null && html.contains('myInput')) {
      final idIdx = html.indexOf('id="myInput"');
      if (idIdx != -1) {
        final tagStart = html.lastIndexOf('<input', idIdx);
        if (tagStart != -1) {
          final valueIdx = html.indexOf('value="', tagStart);
          if (valueIdx != -1 && valueIdx < idIdx) {
            final start = valueIdx + 7;
            final end = html.indexOf('"', start);
            if (end > start) linkFull = html.substring(start, end).trim();
          }
        }
      }
    }
    final link = (linkFull != null && linkFull.isNotEmpty)
        ? linkFull
        : 'https://maipdf.com';

    final readCode =
        _extractIdentifierFromResultLink(link) ??
        _cleanResultCode(_extractTextById(html, 'result-message'));
    final modifyCode = _cleanResultCode(
      _extractResultChip(html, 'result-password') ??
          _extractTextById(html, 'result-password'),
    );

    _resultLinkController.text = link;
    setState(() {
      _resultLink = link;
      _readCode = readCode ?? '—';
      _modifyCode = modifyCode ?? '—';
      _cloudRecordStatus = null;
    });
  }

  String? _extractResultChip(String html, String id) {
    final match = RegExp(
      '$id[\\s\\S]*?result-chip[^>]*>([^<]+)<',
      caseSensitive: false,
    ).firstMatch(html);
    return match?.group(1)?.trim();
  }

  String? _extractTextById(String html, String id) {
    final idIdx = html.indexOf('id="$id"');
    if (idIdx < 0) return null;
    final tagEnd = html.indexOf('>', idIdx);
    if (tagEnd < 0) return null;
    final closingStart = html.indexOf('</', tagEnd + 1);
    if (closingStart < 0) return null;
    final raw = html.substring(tagEnd + 1, closingStart);
    return _stripHtml(raw).trim();
  }

  String _stripHtml(String value) {
    return value
        .replaceAll(RegExp(r'<[^>]+>'), '')
        .replaceAll('&quot;', '"')
        .replaceAll('&#34;', '"')
        .replaceAll('&#39;', "'")
        .replaceAll('&amp;', '&')
        .replaceAll('&lt;', '<')
        .replaceAll('&gt;', '>');
  }

  String? _cleanResultCode(String? value) {
    if (value == null) return null;
    var code = value
        .replaceFirst(RegExp(r'^Password:\s*', caseSensitive: false), '')
        .replaceFirst(RegExp(r'^Modify Code:\s*', caseSensitive: false), '')
        .replaceFirst(RegExp(r'^Read Code:\s*', caseSensitive: false), '')
        .trim();
    code = code.replaceAll(RegExp(r'^"+|"+$'), '').trim();
    if (code.isEmpty ||
        RegExp(r'To Del\.MOD Link', caseSensitive: false).hasMatch(code)) {
      return null;
    }
    return code;
  }

  Future<void> _recordCloudLinkForAccount() async {
    final filePath = _filePath;
    final link = _resultLink;
    if (filePath == null || filePath.isEmpty || link == null || link.isEmpty) {
      return;
    }
    final identifier = _extractIdentifierFromResultLink(link);
    if (identifier == null || identifier.isEmpty) return;

    try {
      final status = await MaiPdfCloudAuthService.instance.recordCloudLink(
        filePath: filePath,
        identifier: identifier,
      );
      if (!mounted || status == null) return;
      setState(() {
        _cloudRecordStatus = status;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _cloudRecordStatus = 'Cloud record failed: $e';
      });
    }
  }

  String? _extractIdentifierFromResultLink(String link) {
    final direct = RegExp(r'/file/([^@/?#]+)@pdf').firstMatch(link);
    if (direct != null) return direct.group(1);

    final uri = Uri.tryParse(link);
    if (uri == null) return null;
    final segments = uri.pathSegments;
    final fileIndex = segments.indexOf('file');
    if (fileIndex < 0 || fileIndex + 1 >= segments.length) return null;
    final value = segments[fileIndex + 1];
    final atIndex = value.indexOf('@pdf');
    if (atIndex <= 0) return null;
    return value.substring(0, atIndex);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF6F2FF),
      body: BackgroundDecoration(
        child: Center(
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 430),
            child: SafeArea(
              bottom: false,
              child: Column(
                children: [
                  _buildMobileHeader(),
                  Expanded(
                    child: ListView(
                      padding: const EdgeInsets.fromLTRB(16, 12, 16, 24),
                      children: [
                        _buildStepProgress(),
                        const SizedBox(height: 14),
                        if (_currentStep >= 1) _buildSection1(),
                        if (_currentStep >= 2) _buildSection2(),
                        if (_currentStep >= 3) _buildSection3(),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildMobileHeader() {
    return Container(
      padding: const EdgeInsets.fromLTRB(8, 8, 16, 10),
      decoration: BoxDecoration(
        color: Colors.white.withValues(alpha: 0.96),
        border: const Border(bottom: BorderSide(color: Color(0xFFE7DEF8))),
      ),
      child: Row(
        children: [
          IconButton(
            icon: const Icon(Icons.arrow_back_ios_new, size: 20),
            onPressed: () => Navigator.of(context).pop(),
          ),
          const SizedBox(width: 2),
          const Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  'MaiPDF',
                  style: TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.w800,
                    color: Color(0xFF23172F),
                  ),
                ),
                SizedBox(height: 2),
                Text(
                  'Cloud Sharing',
                  style: TextStyle(
                    fontSize: 12,
                    color: Color(0xFF7A6D8D),
                    fontWeight: FontWeight.w500,
                  ),
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
            decoration: BoxDecoration(
              color: const Color(0xFFF1EAFF),
              borderRadius: BorderRadius.circular(8),
            ),
            child: const Text(
              'PDF',
              style: TextStyle(
                fontSize: 12,
                color: Color(0xFF6D3FE8),
                fontWeight: FontWeight.w700,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStepProgress() {
    return Container(
      padding: const EdgeInsets.all(8),
      decoration: BoxDecoration(
        color: Colors.white.withValues(alpha: 0.98),
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: const Color(0xFFE5DFF0)),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF4C1D95).withValues(alpha: 0.06),
            blurRadius: 10,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Row(
        children: [
          Expanded(
            child: _stepChip(1, 'Upload', _currentStep >= 1, _currentStep > 1),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: _stepChip(2, 'Set', _currentStep >= 2, _currentStep > 2),
          ),
          const SizedBox(width: 8),
          Expanded(child: _stepChip(3, 'Share', _currentStep >= 3, false)),
        ],
      ),
    );
  }

  Widget _stepChip(int step, String label, bool active, bool completed) {
    final isActive = active && !completed;
    return Container(
      height: 46,
      padding: const EdgeInsets.symmetric(horizontal: 10),
      decoration: BoxDecoration(
        gradient: isActive
            ? const LinearGradient(
                colors: [Color(0xFF5B21B6), Color(0xFF7C3AED)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              )
            : null,
        color: completed
            ? const Color(0xFF10B981)
            : (isActive ? null : const Color(0xFFF7F3FF)),
        borderRadius: BorderRadius.circular(8),
        border: Border.all(
          color: completed
              ? const Color(0xFF10B981)
              : (isActive ? const Color(0xFF7C3AED) : const Color(0xFFE0D7F4)),
        ),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            width: 24,
            height: 24,
            decoration: BoxDecoration(
              color: completed
                  ? Colors.white.withValues(alpha: 0.22)
                  : (isActive
                        ? Colors.white.withValues(alpha: 0.25)
                        : const Color(0xFFE7DEF8)),
              shape: BoxShape.circle,
            ),
            alignment: Alignment.center,
            child: Text(
              '$step',
              style: TextStyle(
                fontWeight: FontWeight.w800,
                color: completed
                    ? Colors.white
                    : (isActive ? Colors.white : const Color(0xFF718096)),
                fontSize: 13,
              ),
            ),
          ),
          const SizedBox(width: 6),
          Flexible(
            child: Text(
              label,
              overflow: TextOverflow.ellipsis,
              style: TextStyle(
                fontWeight: isActive || completed
                    ? FontWeight.w700
                    : FontWeight.w600,
                color: completed || isActive
                    ? Colors.white
                    : const Color(0xFF7A6D8D),
                fontSize: 13,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSection1() {
    final isError =
        _uploadStatus.contains('失败') ||
        _uploadStatus.contains('超时') ||
        _uploadStatus.contains('无法') ||
        _uploadStatus.contains('unavailable');
    return _SectionCard(
      title: '1: Upload File',
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Text(
            _uploadStatus,
            style: TextStyle(
              color: isError ? Colors.red.shade700 : const Color(0xFF718096),
              fontSize: 13,
              fontWeight: isError ? FontWeight.w600 : FontWeight.normal,
            ),
          ),
          if (_uploading) ...[
            const SizedBox(height: 10),
            const LinearProgressIndicator(backgroundColor: Color(0xFFE5DFF0)),
          ],
          const SizedBox(height: 12),
          GestureDetector(
            onTap: _uploading ? null : _pickAndUpload,
            child: Container(
              padding: const EdgeInsets.symmetric(vertical: 24, horizontal: 18),
              decoration: BoxDecoration(
                color: const Color(0xFFFAF7FF),
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: const Color(0xFF8B5CF6), width: 1.4),
              ),
              child: _uploading
                  ? const Column(
                      children: [
                        SizedBox(
                          width: 36,
                          height: 36,
                          child: CircularProgressIndicator(strokeWidth: 2),
                        ),
                        SizedBox(height: 12),
                        Text(
                          '正在上传…',
                          style: TextStyle(fontWeight: FontWeight.w500),
                        ),
                      ],
                    )
                  : Column(
                      children: [
                        Icon(
                          Icons.cloud_upload_outlined,
                          size: 40,
                          color: Theme.of(context).colorScheme.primary,
                        ),
                        const SizedBox(height: 10),
                        const Text(
                          'Choose File',
                          style: TextStyle(fontWeight: FontWeight.w600),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          'Tap to pick a PDF',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey[600],
                          ),
                        ),
                      ],
                    ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSection2() {
    return _SectionCard(
      title: '2: Set Up Reading Times and Session',
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          TextField(
            controller: _fileNameDisplayController,
            readOnly: true,
            style: const TextStyle(fontSize: 13),
            decoration: const InputDecoration(
              labelText: 'File',
              border: OutlineInputBorder(),
              filled: true,
              isDense: true,
              contentPadding: EdgeInsets.symmetric(
                horizontal: 12,
                vertical: 12,
              ),
            ),
          ),
          const SizedBox(height: 12),
          TextField(
            controller: _limitController,
            keyboardType: TextInputType.number,
            decoration: const InputDecoration(
              labelText: 'Access Limit (Number of Opens)',
              hintText: 'Number of Opens',
              border: OutlineInputBorder(),
              isDense: true,
              contentPadding: EdgeInsets.symmetric(
                horizontal: 12,
                vertical: 12,
              ),
            ),
          ),
          const SizedBox(height: 12),
          TextField(
            controller: _sessionSecondsController,
            keyboardType: TextInputType.number,
            decoration: const InputDecoration(
              labelText: 'Each Session (seconds)',
              hintText: 'in (seconds)',
              border: OutlineInputBorder(),
              isDense: true,
              contentPadding: EdgeInsets.symmetric(
                horizontal: 12,
                vertical: 12,
              ),
            ),
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Icon(Icons.lock_outline, size: 20, color: Colors.grey[700]),
              const SizedBox(width: 8),
              const Text(
                'Dynamic Watermark',
                style: TextStyle(fontWeight: FontWeight.w500),
              ),
              const Spacer(),
              Switch(
                value: _dynamicWatermark,
                onChanged: (v) => setState(() => _dynamicWatermark = v),
              ),
            ],
          ),
          const SizedBox(height: 12),
          DropdownButtonFormField<String>(
            initialValue: _viewType,
            decoration: const InputDecoration(
              labelText: 'View Type',
              border: OutlineInputBorder(),
              isDense: true,
              contentPadding: EdgeInsets.symmetric(
                horizontal: 12,
                vertical: 12,
              ),
            ),
            items: const [
              DropdownMenuItem(value: 'straight', child: Text('SecureView')),
              DropdownMenuItem(value: 'obstacle', child: Text('FenceView')),
              DropdownMenuItem(value: 'topen', child: Text('Unrestricted')),
            ],
            onChanged: (v) => setState(() => _viewType = v ?? 'straight'),
          ),
          const SizedBox(height: 16),
          DropdownButtonFormField<String>(
            initialValue: _expirationPreset,
            decoration: const InputDecoration(
              labelText: 'Expiration',
              border: OutlineInputBorder(),
              isDense: true,
              contentPadding: EdgeInsets.symmetric(
                horizontal: 12,
                vertical: 12,
              ),
            ),
            items: const [
              DropdownMenuItem(value: '', child: Text('Select duration')),
              DropdownMenuItem(value: '1h', child: Text('1 hour')),
              DropdownMenuItem(value: '3h', child: Text('3 hours')),
              DropdownMenuItem(value: '24h', child: Text('24 hours')),
              DropdownMenuItem(value: '5d', child: Text('5 days')),
              DropdownMenuItem(value: 'custom', child: Text('Custom days')),
              DropdownMenuItem(value: 'unlimited', child: Text('Unlimited')),
            ],
            onChanged: (v) => setState(() => _expirationPreset = v ?? ''),
          ),
          if (_expirationPreset == 'custom') ...[
            const SizedBox(height: 8),
            TextField(
              controller: _expirationCustomDaysController,
              keyboardType: TextInputType.number,
              decoration: const InputDecoration(
                hintText: 'Custom days',
                border: OutlineInputBorder(),
                isDense: true,
                contentPadding: EdgeInsets.symmetric(
                  horizontal: 12,
                  vertical: 12,
                ),
              ),
            ),
          ],
          const SizedBox(height: 16),
          Row(
            children: [
              Icon(
                Icons.notifications_outlined,
                size: 20,
                color: Colors.grey[700],
              ),
              const SizedBox(width: 8),
              const Text(
                'Read Alerts (Telegram)',
                style: TextStyle(fontWeight: FontWeight.w500),
              ),
              const Spacer(),
              Switch(
                value: _enableTelegram,
                onChanged: (v) => setState(() => _enableTelegram = v),
              ),
            ],
          ),
          if (_enableTelegram) ...[
            const SizedBox(height: 8),
            Text(
              _telegramStatusText,
              style: TextStyle(
                fontSize: 13,
                color: _telegramStatusLoading
                    ? Colors.grey
                    : (_telegramStatusText.contains('Linked')
                          ? Colors.green.shade700
                          : Colors.grey[700]),
              ),
            ),
            const SizedBox(height: 8),
            TextField(
              controller: _telegramChatIdController,
              keyboardType: TextInputType.number,
              decoration: const InputDecoration(
                hintText: 'chat_id (auto after Add bot + /start + Get chat_id)',
                labelText: 'Chat ID',
                border: OutlineInputBorder(),
                prefixIcon: Icon(Icons.chat_bubble_outline),
                isDense: true,
                contentPadding: EdgeInsets.symmetric(
                  horizontal: 12,
                  vertical: 12,
                ),
              ),
            ),
            const SizedBox(height: 8),
            Row(
              children: [
                FilledButton.tonalIcon(
                  onPressed: _telegramStatusLoading ? null : _openTelegramBind,
                  icon: const Icon(Icons.open_in_new, size: 18),
                  label: const Text('Add bot'),
                ),
                const SizedBox(width: 8),
                OutlinedButton.icon(
                  onPressed: _telegramStatusLoading
                      ? null
                      : _fetchTelegramChatId,
                  icon: _telegramStatusLoading
                      ? const SizedBox(
                          width: 18,
                          height: 18,
                          child: CircularProgressIndicator(strokeWidth: 2),
                        )
                      : const Icon(Icons.refresh, size: 18),
                  label: const Text('Get chat_id'),
                ),
              ],
            ),
            const SizedBox(height: 4),
            Text(
              'Add bot → 在 Telegram 里发 /start → 点「Get chat_id」',
              style: TextStyle(fontSize: 12, color: Colors.grey[600]),
            ),
            const SizedBox(height: 12),
          ],
          Row(
            children: [
              Icon(Icons.email_outlined, size: 20, color: Colors.grey[700]),
              const SizedBox(width: 8),
              const Text(
                'Email Verification',
                style: TextStyle(fontWeight: FontWeight.w500),
              ),
              const Spacer(),
              Switch(
                value: _enableEmailValidation,
                onChanged: (v) => setState(() => _enableEmailValidation = v),
              ),
            ],
          ),
          if (_enableEmailValidation) ...[
            const SizedBox(height: 8),
            TextField(
              controller: _emailAddressesController,
              maxLines: 3,
              decoration: const InputDecoration(
                hintText: 'Enter up to 50 email addresses, separated by commas',
                border: OutlineInputBorder(),
                isDense: true,
                contentPadding: EdgeInsets.symmetric(
                  horizontal: 12,
                  vertical: 12,
                ),
              ),
            ),
          ],
          const SizedBox(height: 18),
          if (_submitError != null)
            Padding(
              padding: const EdgeInsets.only(bottom: 12),
              child: Text(
                _submitError!,
                style: const TextStyle(color: Colors.red),
              ),
            ),
          FilledButton.icon(
            onPressed: _submitting ? null : _submitForm,
            icon: _submitting
                ? const SizedBox(
                    width: 20,
                    height: 20,
                    child: CircularProgressIndicator(
                      strokeWidth: 2,
                      color: Colors.white,
                    ),
                  )
                : const Icon(Icons.link),
            label: Text(_submitting ? 'Generating...' : 'Create Secure Link'),
            style: FilledButton.styleFrom(
              minimumSize: const Size.fromHeight(48),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(8),
              ),
              backgroundColor: const Color(0xFF8B5CF6),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSection3() {
    final link = _resultLink ?? 'https://maipdf.com';
    return _SectionCard(
      title: '3: URL and QR Created',
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          SelectableText(
            link,
            style: const TextStyle(fontWeight: FontWeight.w600),
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: TextField(
                  controller: _resultLinkController,
                  readOnly: true,
                  decoration: const InputDecoration(
                    border: OutlineInputBorder(),
                    isDense: true,
                    contentPadding: EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 12,
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 8),
              IconButton.filled(
                onPressed: () {
                  Clipboard.setData(ClipboardData(text: link));
                  ScaffoldMessenger.of(
                    context,
                  ).showSnackBar(const SnackBar(content: Text('Link copied')));
                },
                icon: const Icon(Icons.copy),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Text('Read Code: $_readCode', style: const TextStyle(fontSize: 13)),
          const SizedBox(height: 4),
          Text(
            'Modify Code: $_modifyCode',
            style: const TextStyle(fontSize: 13),
          ),
          if (_cloudRecordStatus != null) ...[
            const SizedBox(height: 8),
            Text(
              _cloudRecordStatus!,
              style: TextStyle(
                fontSize: 12,
                color: _cloudRecordStatus!.contains('failed')
                    ? Colors.red.shade700
                    : Colors.green.shade700,
              ),
            ),
          ],
          const SizedBox(height: 20),
          Center(
            child: QrImageView(
              data: link,
              version: QrVersions.auto,
              size: 140,
              backgroundColor: Colors.white,
            ),
          ),
          const SizedBox(height: 8),
          const Center(
            child: Text(
              'Scan QR Code To Read',
              style: TextStyle(fontSize: 12, color: Colors.grey),
            ),
          ),
          const SizedBox(height: 16),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              TextButton.icon(
                onPressed: () => launchUrl(
                  Uri.parse('https://maipdf.com/pdf/hahachange.php'),
                ),
                icon: const Icon(Icons.edit, size: 18),
                label: const Text('Change File'),
              ),
              const SizedBox(width: 12),
              TextButton.icon(
                onPressed: () =>
                    launchUrl(Uri.parse('https://maipdf.com/getresult.html')),
                icon: const Icon(Icons.list_alt, size: 18),
                label: const Text('Access Records'),
              ),
            ],
          ),
        ],
      ),
    );
  }

  @override
  void dispose() {
    _limitController.dispose();
    _sessionSecondsController.dispose();
    _expirationCustomDaysController.dispose();
    _telegramChatIdController.dispose();
    _emailAddressesController.dispose();
    _fileNameDisplayController.dispose();
    _resultLinkController.dispose();
    super.dispose();
  }
}

class BackgroundDecoration extends StatelessWidget {
  const BackgroundDecoration({super.key, required this.child});

  final Widget child;

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: [Color(0xFFFCFAFF), Color(0xFFF1ECFF)],
        ),
      ),
      child: child,
    );
  }
}

class _SectionCard extends StatelessWidget {
  const _SectionCard({required this.title, required this.child});

  final String title;
  final Widget child;

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(8),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF4C1D95).withValues(alpha: 0.07),
            blurRadius: 14,
            offset: const Offset(0, 5),
          ),
        ],
        border: Border.all(color: const Color(0xFFE5DFF0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: const TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.w700,
              color: Color(0xFF5B21B6),
            ),
          ),
          const SizedBox(height: 14),
          child,
        ],
      ),
    );
  }
}
