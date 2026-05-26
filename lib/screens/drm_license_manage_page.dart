import 'dart:async';
import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:http/http.dart' as http;

import '../utils/maipdf_auth_service.dart';

class DrmLicenseManagePage extends StatefulWidget {
  const DrmLicenseManagePage({
    super.key,
    required this.apiBase,
    this.initialLicenseId,
    this.initialModCode,
  });

  final String apiBase;
  final String? initialLicenseId;
  final String? initialModCode;

  @override
  State<DrmLicenseManagePage> createState() => _DrmLicenseManagePageState();
}

class _DrmLicenseManagePageState extends State<DrmLicenseManagePage> {
  late final TextEditingController _licenseController;
  late final TextEditingController _modCodeController;
  final _addOpensController = TextEditingController(text: '5');
  final _extendDaysController = TextEditingController(text: '7');

  bool _loading = false;
  bool _loadingLicenses = false;
  String _statusText = 'Enter license details to manage access.';
  _LicenseStatus? _license;
  List<_LicenseStatus> _myLicenses = const [];
  int _myLicenseTotal = 0;

  @override
  void initState() {
    super.initState();
    _licenseController = TextEditingController(
      text: widget.initialLicenseId ?? '',
    );
    _modCodeController = TextEditingController(
      text: widget.initialModCode ?? '',
    );

    if (_licenseController.text.isNotEmpty &&
        (_modCodeController.text.isNotEmpty ||
            MaiPdfAuthService.instance.isSignedIn)) {
      WidgetsBinding.instance.addPostFrameCallback((_) => _checkStatus());
    }
    if (MaiPdfAuthService.instance.isSignedIn) {
      WidgetsBinding.instance.addPostFrameCallback((_) => _loadMyLicenses());
    }
  }

  Future<void> _checkStatus() async {
    await _runAction(
      busyText: 'Checking license...',
      successText: 'License status loaded.',
      request: () => _post('check', _requestBody()),
      updateFromResponse: true,
    );
  }

  Future<void> _addOpens() async {
    final count = int.tryParse(_addOpensController.text.trim()) ?? 0;
    if (count == 0) {
      _showMessage('Enter a non-zero number of opens.');
      return;
    }
    await _runAction(
      busyText: 'Adding opens...',
      successText: 'Open limit updated.',
      request: () => _post(
        'extend',
        _requestBody({'add_opens': count, 'note': 'Updated from MaiPDF app'}),
      ),
      updateFromResponse: true,
    );
  }

  Future<void> _extendDays() async {
    final days = double.tryParse(_extendDaysController.text.trim()) ?? 0;
    if (days <= 0) {
      _showMessage('Enter days greater than 0.');
      return;
    }
    await _runAction(
      busyText: 'Extending expiration...',
      successText: 'Expiration updated.',
      request: () => _post(
        'extend',
        _requestBody({
          'extend_seconds': (days * 86400).round(),
          'note': 'Updated from MaiPDF app',
        }),
      ),
      updateFromResponse: true,
    );
  }

  Future<void> _reactivate() async {
    await _runAction(
      busyText: 'Reactivating license...',
      successText: 'License reactivated.',
      request: () => _post(
        'extend',
        _requestBody({
          'set_status_active': true,
          'note': 'Reactivated from MaiPDF app',
        }),
      ),
      updateFromResponse: true,
    );
  }

  Future<void> _revoke() async {
    final ok = await _confirm(
      title: 'Revoke access?',
      message:
          'Readers will no longer be able to open this protected file unless you reactivate it later.',
    );
    if (!ok) return;

    await _runAction(
      busyText: 'Revoking license...',
      successText: 'License revoked.',
      request: () =>
          _post('revoke', _requestBody({'reason': 'Revoked from MaiPDF app'})),
      statusOverride: 'revoked',
      revokeReasonOverride: 'Revoked from MaiPDF app',
    );
  }

  Future<void> _deleteLicense() async {
    final ok = await _confirm(
      title: 'Delete license?',
      message:
          'This is a soft delete on the server. Readers will be blocked from opening the file.',
      destructive: true,
    );
    if (!ok) return;

    await _runAction(
      busyText: 'Deleting license...',
      successText: 'License deleted.',
      request: () => _post('delete', _requestBody()),
      statusOverride: 'deleted',
    );
  }

  Future<void> _loadMyLicenses() async {
    final auth = MaiPdfAuthService.instance;
    if (!auth.isSignedIn) return;
    setState(() => _loadingLicenses = true);
    try {
      final headers = await auth.authHeaders();
      final uri = Uri.parse(
        '${widget.apiBase}/api/me/licenses?limit=50&offset=0&order=created_desc',
      );
      final response = await http
          .get(uri, headers: headers)
          .timeout(const Duration(seconds: 30));
      final data = jsonDecode(response.body) as Map<String, dynamic>;
      if (response.statusCode != 200 || data['ok'] != true) {
        throw StateError(
          data['message']?.toString() ??
              data['error']?.toString() ??
              'HTTP ${response.statusCode}',
        );
      }
      final rows = data['rows'];
      final licenses = rows is List
          ? rows
                .map(_LicenseStatus.fromJson)
                .whereType<_LicenseStatus>()
                .toList()
          : <_LicenseStatus>[];
      if (!mounted) return;
      setState(() {
        _loadingLicenses = false;
        _myLicenses = licenses;
        _myLicenseTotal = (data['total'] as num?)?.round() ?? licenses.length;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() => _loadingLicenses = false);
      _showMessage('Load licenses failed: $e');
    }
  }

  Future<void> _bindCurrentLicense() async {
    if (!MaiPdfAuthService.instance.isSignedIn) {
      _showMessage('Sign in first.');
      return;
    }
    if (_licenseId().isEmpty || _modCode().isEmpty) {
      _showMessage('License ID and modification code are required.');
      return;
    }
    setState(() {
      _loading = true;
      _statusText = 'Binding license to your account...';
    });
    try {
      final headers = {
        'Content-Type': 'application/json; charset=utf-8',
        ...await MaiPdfAuthService.instance.authHeaders(),
      };
      final response = await http
          .post(
            Uri.parse('${widget.apiBase}/api/me/bind'),
            headers: headers,
            body: jsonEncode({
              'license_id': _licenseId(),
              'mod_code': _modCode(),
            }),
          )
          .timeout(const Duration(seconds: 30));
      final data = jsonDecode(response.body) as Map<String, dynamic>;
      if (response.statusCode != 200 || data['ok'] != true) {
        throw StateError(
          data['message']?.toString() ??
              data['error']?.toString() ??
              'HTTP ${response.statusCode}',
        );
      }
      if (!mounted) return;
      setState(() {
        _loading = false;
        _statusText = 'License bound to your account.';
      });
      _showMessage('License bound to your account.');
      await _loadMyLicenses();
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _loading = false;
        _statusText = 'Bind failed: $e';
      });
      _showMessage(_statusText);
    }
  }

  Future<void> _runAction({
    required String busyText,
    required String successText,
    required Future<Map<String, dynamic>> Function() request,
    bool updateFromResponse = false,
    bool refreshAfter = false,
    String? statusOverride,
    String? revokeReasonOverride,
  }) async {
    if (!_validateInputs()) return;
    setState(() {
      _loading = true;
      _statusText = busyText;
    });

    try {
      final data = await request().timeout(const Duration(seconds: 30));
      _LicenseStatus? nextLicense;
      if (updateFromResponse) {
        nextLicense = _LicenseStatus.fromJson(data['license']);
      }

      if (refreshAfter) {
        final refreshed = await _post('check', _requestBody());
        nextLicense = _LicenseStatus.fromJson(refreshed['license']);
      }
      if (statusOverride != null && (nextLicense ?? _license) != null) {
        nextLicense = (nextLicense ?? _license)!.copyWith(
          status: statusOverride,
          revokeReason: revokeReasonOverride,
        );
      }

      if (!mounted) return;
      setState(() {
        _loading = false;
        _license = nextLicense ?? _license;
        _statusText = successText;
      });
      _showMessage(successText);
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _loading = false;
        _statusText = 'Action failed: $e';
      });
      _showMessage(_statusText);
    }
  }

  Future<Map<String, dynamic>> _post(
    String action,
    Map<String, dynamic> body,
  ) async {
    final lid = _licenseId();
    final url = Uri.parse(
      '${widget.apiBase}/api/licenses/${Uri.encodeComponent(lid)}/$action',
    );
    final headers = {
      'Content-Type': 'application/json; charset=utf-8',
      ...await MaiPdfAuthService.instance.authHeaders(),
    };
    final response = await http.post(
      url,
      headers: headers,
      body: jsonEncode(body),
    );

    Map<String, dynamic> data = {};
    try {
      final decoded = jsonDecode(response.body);
      if (decoded is Map<String, dynamic>) data = decoded;
    } catch (_) {
      // Keep the HTTP status message below readable when the body is not JSON.
    }

    if (response.statusCode < 200 || response.statusCode >= 300) {
      final message = data['message'] ?? data['error'] ?? response.reasonPhrase;
      throw Exception(message ?? 'HTTP ${response.statusCode}');
    }
    if (data['ok'] != true) {
      throw Exception(data['message'] ?? data['error'] ?? 'Request failed');
    }
    return data;
  }

  bool _validateInputs() {
    if (_licenseId().isEmpty) {
      _showMessage('License ID is required.');
      return false;
    }
    if (_modCode().isEmpty && !MaiPdfAuthService.instance.isSignedIn) {
      _showMessage('Sign in or enter the modification code.');
      return false;
    }
    return true;
  }

  Map<String, dynamic> _requestBody([Map<String, dynamic>? extra]) {
    final body = <String, dynamic>{...?extra};
    final modCode = _modCode();
    if (modCode.isNotEmpty) body['mod_code'] = modCode;
    return body;
  }

  void _useLicense(_LicenseStatus license) {
    _licenseController.text = license.licenseId;
    if (license.modCode != null && license.modCode!.isNotEmpty) {
      _modCodeController.text = license.modCode!;
    }
    _checkStatus();
  }

  String _licenseId() => _licenseController.text.trim();

  String _modCode() => _modCodeController.text.trim();

  Future<void> _copy(String label, String value) async {
    if (value.isEmpty) return;
    await Clipboard.setData(ClipboardData(text: value));
    _showMessage('$label copied');
  }

  Future<bool> _confirm({
    required String title,
    required String message,
    bool destructive = false,
  }) async {
    final result = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: Text(title),
        content: Text(message),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(false),
            child: const Text('Cancel'),
          ),
          FilledButton(
            onPressed: () => Navigator.of(context).pop(true),
            style: destructive
                ? FilledButton.styleFrom(
                    backgroundColor: const Color(0xFFD83A3A),
                  )
                : null,
            child: const Text('Confirm'),
          ),
        ],
      ),
    );
    return result ?? false;
  }

  Future<void> _signIn() async {
    try {
      await MaiPdfAuthService.instance.signInWithGoogle();
      _showMessage('Signed in with Google');
      await _loadMyLicenses();
    } catch (e) {
      _showMessage('Sign in failed: $e');
    }
  }

  Future<void> _signOut() async {
    await MaiPdfAuthService.instance.signOut();
    if (!mounted) return;
    setState(() {
      _myLicenses = const [];
      _myLicenseTotal = 0;
    });
    _showMessage('Signed out');
  }

  void _showMessage(String message) {
    if (!mounted) return;
    ScaffoldMessenger.of(
      context,
    ).showSnackBar(SnackBar(content: Text(message)));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFCFAFF),
      body: Center(
        child: ConstrainedBox(
          constraints: const BoxConstraints(maxWidth: 430),
          child: SafeArea(
            child: ListView(
              padding: const EdgeInsets.fromLTRB(16, 14, 16, 24),
              children: [
                _buildHeader(context),
                const SizedBox(height: 14),
                _buildAccountCard(),
                const SizedBox(height: 14),
                _buildLookupCard(),
                const SizedBox(height: 14),
                if (_license != null) _buildStatusCard(),
                if (_license != null) const SizedBox(height: 14),
                _buildActionsCard(),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildHeader(BuildContext context) {
    return Row(
      children: [
        IconButton(
          icon: const Icon(Icons.arrow_back_ios_new, size: 20),
          onPressed: () => Navigator.of(context).pop(),
        ),
        const SizedBox(width: 2),
        const Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Manage license',
                style: TextStyle(
                  fontSize: 21,
                  fontWeight: FontWeight.w800,
                  color: Color(0xFF23172F),
                ),
              ),
              SizedBox(height: 2),
              Text(
                'Control opens, expiry, and revoke access',
                style: TextStyle(
                  fontSize: 12,
                  color: Color(0xFF7A6D8D),
                  fontWeight: FontWeight.w500,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildLookupCard() {
    return _ManageCard(
      title: 'License',
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Text(
            _statusText,
            style: const TextStyle(fontSize: 13, color: Color(0xFF6B6278)),
          ),
          const SizedBox(height: 14),
          TextField(
            controller: _licenseController,
            textCapitalization: TextCapitalization.characters,
            decoration: const InputDecoration(
              labelText: 'License ID',
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
            controller: _modCodeController,
            textCapitalization: TextCapitalization.characters,
            decoration: const InputDecoration(
              labelText: 'Modification Code',
              border: OutlineInputBorder(),
              isDense: true,
              contentPadding: EdgeInsets.symmetric(
                horizontal: 12,
                vertical: 12,
              ),
            ),
          ),
          const SizedBox(height: 14),
          FilledButton.icon(
            onPressed: _loading ? null : _checkStatus,
            icon: _loading
                ? const SizedBox(
                    width: 18,
                    height: 18,
                    child: CircularProgressIndicator(
                      strokeWidth: 2,
                      color: Colors.white,
                    ),
                  )
                : const Icon(Icons.manage_search, size: 18),
            label: Text(_loading ? 'Working...' : 'Check status'),
            style: FilledButton.styleFrom(
              minimumSize: const Size.fromHeight(48),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(8),
              ),
              backgroundColor: const Color(0xFF316CFF),
            ),
          ),
          AnimatedBuilder(
            animation: MaiPdfAuthService.instance,
            builder: (context, _) {
              if (!MaiPdfAuthService.instance.isSignedIn) {
                return const SizedBox.shrink();
              }
              return Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  const SizedBox(height: 10),
                  OutlinedButton.icon(
                    onPressed: _loading ? null : _bindCurrentLicense,
                    icon: const Icon(Icons.bookmark_add_outlined, size: 18),
                    label: const Text('Save this license to my account'),
                  ),
                ],
              );
            },
          ),
        ],
      ),
    );
  }

  Widget _buildAccountCard() {
    return AnimatedBuilder(
      animation: MaiPdfAuthService.instance,
      builder: (context, _) {
        final auth = MaiPdfAuthService.instance;
        if (!auth.isSignedIn) {
          return _ManageCard(
            title: 'Google account',
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                const Text(
                  'Sign in to save new .maipdf licenses to your account and manage them without pasting modification codes.',
                  style: TextStyle(fontSize: 13, color: Color(0xFF6B6278)),
                ),
                const SizedBox(height: 12),
                FilledButton.icon(
                  onPressed: auth.busy ? null : _signIn,
                  icon: auth.busy
                      ? const SizedBox(
                          width: 18,
                          height: 18,
                          child: CircularProgressIndicator(
                            strokeWidth: 2,
                            color: Colors.white,
                          ),
                        )
                      : const Icon(Icons.login, size: 18),
                  label: Text(
                    auth.busy ? 'Signing in...' : 'Sign in with Google',
                  ),
                ),
              ],
            ),
          );
        }

        return _ManageCard(
          title: 'Google account',
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Row(
                children: [
                  const Icon(
                    Icons.account_circle,
                    size: 34,
                    color: Color(0xFF6F5596),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'Signed in',
                          style: TextStyle(
                            fontSize: 11,
                            color: Color(0xFF61748E),
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        Text(
                          auth.email ?? auth.displayName ?? 'Google account',
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(
                            fontSize: 13,
                            color: Color(0xFF0F2741),
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                      ],
                    ),
                  ),
                  TextButton(
                    onPressed: auth.busy ? null : _signOut,
                    child: const Text('SIGN OUT'),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              Row(
                children: [
                  Expanded(
                    child: Text(
                      _loadingLicenses
                          ? 'Loading your licenses...'
                          : 'My licenses: $_myLicenseTotal',
                      style: const TextStyle(
                        fontSize: 12,
                        color: Color(0xFF7A6D8D),
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ),
                  IconButton(
                    tooltip: 'Refresh',
                    onPressed: _loadingLicenses ? null : _loadMyLicenses,
                    icon: const Icon(Icons.refresh, size: 20),
                  ),
                ],
              ),
              if (_myLicenses.isEmpty && !_loadingLicenses)
                const Text(
                  'Licenses created while signed in will appear here.',
                  style: TextStyle(fontSize: 12, color: Color(0xFF7A6D8D)),
                )
              else
                ..._myLicenses.take(8).map(_buildMyLicenseTile),
            ],
          ),
        );
      },
    );
  }

  Widget _buildMyLicenseTile(_LicenseStatus license) {
    return Container(
      margin: const EdgeInsets.only(top: 8),
      padding: const EdgeInsets.all(10),
      decoration: BoxDecoration(
        color: const Color(0xFFF7FAFE),
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: const Color(0xFFDCE5F3)),
      ),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  license.filename ?? license.licenseId,
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    fontSize: 13,
                    color: Color(0xFF0F2741),
                    fontWeight: FontWeight.w800,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  '${license.status} · ${license.remainingText} left · ${license.expiresText}',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    fontSize: 11,
                    color: Color(0xFF61748E),
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ],
            ),
          ),
          TextButton(
            onPressed: () => _useLicense(license),
            child: const Text('MANAGE'),
          ),
        ],
      ),
    );
  }

  Widget _buildStatusCard() {
    final license = _license!;
    return _ManageCard(
      title: 'Current status',
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                child: Text(
                  license.filename ?? 'Unknown file',
                  style: const TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w800,
                    color: Color(0xFF23172F),
                  ),
                ),
              ),
              _StatusPill(status: license.status),
            ],
          ),
          const SizedBox(height: 12),
          _InfoGrid(
            items: [
              _InfoItem('Max opens', license.maxOpensText),
              _InfoItem('Used', license.opensUsedText),
              _InfoItem('Remaining', license.remainingText),
              _InfoItem('Expires', license.expiresText),
              _InfoItem('Created', license.createdText),
              _InfoItem('Last read', license.lastUnlockedText),
            ],
          ),
          if (license.revokeReason != null &&
              license.revokeReason!.isNotEmpty) ...[
            const SizedBox(height: 12),
            Text(
              'Reason: ${license.revokeReason}',
              style: const TextStyle(fontSize: 12, color: Color(0xFF7A6D8D)),
            ),
          ],
          const SizedBox(height: 12),
          OutlinedButton.icon(
            onPressed: () => _copy('License ID', license.licenseId),
            icon: const Icon(Icons.copy, size: 18),
            label: const Text('Copy license ID'),
          ),
        ],
      ),
    );
  }

  Widget _buildActionsCard() {
    return _ManageCard(
      title: 'Actions',
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Row(
            children: [
              Expanded(
                child: TextField(
                  controller: _addOpensController,
                  keyboardType: TextInputType.number,
                  decoration: const InputDecoration(
                    labelText: 'Add opens',
                    border: OutlineInputBorder(),
                    isDense: true,
                    contentPadding: EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 12,
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 10),
              FilledButton(
                onPressed: _loading ? null : _addOpens,
                child: const Text('Apply'),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: TextField(
                  controller: _extendDaysController,
                  keyboardType: TextInputType.number,
                  decoration: const InputDecoration(
                    labelText: 'Extend days',
                    border: OutlineInputBorder(),
                    isDense: true,
                    contentPadding: EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 12,
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 10),
              FilledButton(
                onPressed: _loading ? null : _extendDays,
                child: const Text('Apply'),
              ),
            ],
          ),
          const SizedBox(height: 14),
          OutlinedButton.icon(
            onPressed: _loading ? null : _reactivate,
            icon: const Icon(Icons.play_circle_outline, size: 18),
            label: const Text('Reactivate'),
          ),
          const SizedBox(height: 10),
          OutlinedButton.icon(
            onPressed: _loading ? null : _revoke,
            icon: const Icon(Icons.block, size: 18),
            label: const Text('Revoke access'),
          ),
          const SizedBox(height: 10),
          OutlinedButton.icon(
            onPressed: _loading ? null : _deleteLicense,
            icon: const Icon(Icons.delete_outline, size: 18),
            label: const Text('Delete license'),
            style: OutlinedButton.styleFrom(
              foregroundColor: const Color(0xFFD83A3A),
            ),
          ),
        ],
      ),
    );
  }

  @override
  void dispose() {
    _licenseController.dispose();
    _modCodeController.dispose();
    _addOpensController.dispose();
    _extendDaysController.dispose();
    super.dispose();
  }
}

class _LicenseStatus {
  const _LicenseStatus({
    required this.licenseId,
    required this.status,
    this.filename,
    this.maxOpens,
    this.opensUsed,
    this.remainingOpens,
    this.expiresAt,
    this.createdAt,
    this.lastUnlockedAt,
    this.revokeReason,
    this.modCode,
  });

  final String licenseId;
  final String status;
  final String? filename;
  final int? maxOpens;
  final int? opensUsed;
  final int? remainingOpens;
  final int? expiresAt;
  final int? createdAt;
  final int? lastUnlockedAt;
  final String? revokeReason;
  final String? modCode;

  _LicenseStatus copyWith({String? status, String? revokeReason}) {
    return _LicenseStatus(
      licenseId: licenseId,
      status: status ?? this.status,
      filename: filename,
      maxOpens: maxOpens,
      opensUsed: opensUsed,
      remainingOpens: remainingOpens,
      expiresAt: expiresAt,
      createdAt: createdAt,
      lastUnlockedAt: lastUnlockedAt,
      revokeReason: revokeReason ?? this.revokeReason,
      modCode: modCode,
    );
  }

  static _LicenseStatus? fromJson(Object? json) {
    if (json is! Map<String, dynamic>) return null;
    return _LicenseStatus(
      licenseId: _asString(json['license_id']) ?? '',
      status: _asString(json['status']) ?? 'unknown',
      filename: _asString(json['filename']),
      maxOpens: _asInt(json['max_opens']),
      opensUsed: _asInt(json['opens_used']),
      remainingOpens: _asInt(json['remaining_opens']),
      expiresAt: _asInt(json['expires_at']),
      createdAt: _asInt(json['created_at']),
      lastUnlockedAt: _asInt(json['last_unlocked_at']),
      revokeReason: _asString(json['revoke_reason']),
      modCode: _asString(json['mod_code']),
    );
  }

  String get maxOpensText => maxOpens == -1 ? 'Unlimited' : _num(maxOpens);

  String get opensUsedText => _num(opensUsed);

  String get remainingText =>
      remainingOpens == -1 ? 'Unlimited' : _num(remainingOpens);

  String get expiresText => _date(expiresAt, nullText: 'Never');

  String get createdText => _date(createdAt);

  String get lastUnlockedText => _date(lastUnlockedAt, nullText: 'Not yet');

  static String? _asString(Object? value) {
    if (value == null) return null;
    final text = value.toString().trim();
    return text.isEmpty ? null : text;
  }

  static int? _asInt(Object? value) {
    if (value is int) return value;
    if (value is num) return value.round();
    if (value is String) return int.tryParse(value);
    return null;
  }

  static String _num(int? value) => value == null ? '-' : value.toString();

  static String _date(int? seconds, {String nullText = '-'}) {
    if (seconds == null || seconds <= 0) return nullText;
    final dt = DateTime.fromMillisecondsSinceEpoch(seconds * 1000).toLocal();
    String two(int value) => value.toString().padLeft(2, '0');
    return '${dt.year}-${two(dt.month)}-${two(dt.day)} ${two(dt.hour)}:${two(dt.minute)}';
  }
}

class _InfoItem {
  const _InfoItem(this.label, this.value);

  final String label;
  final String value;
}

class _InfoGrid extends StatelessWidget {
  const _InfoGrid({required this.items});

  final List<_InfoItem> items;

  @override
  Widget build(BuildContext context) {
    return Wrap(
      spacing: 8,
      runSpacing: 8,
      children: items
          .map(
            (item) => SizedBox(
              width: 124,
              child: Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: const Color(0xFFF7FAFE),
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(color: const Color(0xFFDCE5F3)),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      item.label,
                      style: const TextStyle(
                        fontSize: 11,
                        color: Color(0xFF61748E),
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      item.value,
                      style: const TextStyle(
                        fontSize: 13,
                        color: Color(0xFF0F2741),
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          )
          .toList(),
    );
  }
}

class _StatusPill extends StatelessWidget {
  const _StatusPill({required this.status});

  final String status;

  @override
  Widget build(BuildContext context) {
    final active = status == 'active';
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
        color: active ? const Color(0xFFE9F8EF) : const Color(0xFFFFF0F0),
        borderRadius: BorderRadius.circular(999),
        border: Border.all(
          color: active ? const Color(0xFF82C99B) : const Color(0xFFE7A2A2),
        ),
      ),
      child: Text(
        status,
        style: TextStyle(
          fontSize: 11,
          fontWeight: FontWeight.w800,
          color: active ? const Color(0xFF1A7F3C) : const Color(0xFFA82323),
        ),
      ),
    );
  }
}

class _ManageCard extends StatelessWidget {
  const _ManageCard({required this.title, required this.child});

  final String title;
  final Widget child;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: const Color(0xFFDCE5F3)),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF0F2741).withValues(alpha: 0.06),
            blurRadius: 14,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: const TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.w800,
              color: Color(0xFF0F2741),
            ),
          ),
          const SizedBox(height: 12),
          child,
        ],
      ),
    );
  }
}
