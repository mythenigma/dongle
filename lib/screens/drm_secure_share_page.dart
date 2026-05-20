import 'dart:async';
import 'dart:typed_data';

import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:url_launcher/url_launcher.dart';

import '../utils/download_helper.dart';
import '../utils/local_drm_packager.dart';

class DrmSecureSharePage extends StatefulWidget {
  const DrmSecureSharePage({super.key});

  @override
  State<DrmSecureSharePage> createState() => _DrmSecureSharePageState();
}

class _DrmSecureSharePageState extends State<DrmSecureSharePage> {
  static const String _apiBase = 'https://drm.maipdf.com';
  static const List<int> _maipdfHeader = [
    0x4D,
    0x41,
    0x49,
    0x50,
    0x44,
    0x46,
    0x31,
    0x0A,
  ];

  final _maxOpensController = TextEditingController(text: '5');
  final _customDaysController = TextEditingController(text: '');

  String _expiresPreset = '604800';
  bool _packing = false;
  String _statusText = 'Select a PDF to create a protected .maipdf file.';
  String? _fileName;
  String? _licenseId;
  String? _modCode;
  String? _packageFilename;

  Future<void> _openWebVersion() async {
    await launchUrl(
      Uri.parse('https://drm.maipdf.com/'),
      mode: LaunchMode.externalApplication,
    );
  }

  Future<void> _openManage() async {
    await launchUrl(
      Uri.parse('https://drm.maipdf.com/manage'),
      mode: LaunchMode.externalApplication,
    );
  }

  int _expiresInSeconds() {
    if (_expiresPreset == 'custom') {
      final days = double.tryParse(_customDaysController.text.trim()) ?? 7;
      return (days.clamp(1, 3650) * 86400).round();
    }
    return int.tryParse(_expiresPreset) ?? 604800;
  }

  Future<void> _pickAndPack() async {
    final result = await FilePicker.platform.pickFiles(
      type: FileType.custom,
      allowedExtensions: ['pdf'],
      allowMultiple: false,
      withData: true,
    );
    if (result == null || result.files.isEmpty) return;

    final file = result.files.single;
    final maxOpens = int.tryParse(_maxOpensController.text.trim()) ?? 5;
    if (maxOpens < -1 || maxOpens == 0) {
      _showMessage('Max opens must be -1 or at least 1.');
      return;
    }

    setState(() {
      _packing = true;
      _fileName = file.name;
      _licenseId = null;
      _modCode = null;
      _packageFilename = null;
      _statusText = 'Encrypting and packing locally...';
    });

    try {
      final bytes = file.bytes;
      if (bytes == null || bytes.isEmpty) {
        _showMessage('Cannot read this file.');
        setState(() => _packing = false);
        return;
      }

      final package = await LocalDrmPackager(apiBase: _apiBase)
          .packPdf(
            pdfBytes: bytes,
            filename: file.name,
            maxOpens: maxOpens,
            expiresInSeconds: _expiresInSeconds(),
          )
          .timeout(
            const Duration(seconds: 180),
            onTimeout: () => throw TimeoutException('Local pack timed out'),
          );

      String saveStatus = 'Protected .maipdf file created.';
      try {
        final packageBytes = _wrapMaipdfPackage(package.packageBytes);
        final saveResult = await saveBytesAsFile(
          filename: package.packageFilename,
          bytes: packageBytes,
        );
        if (saveResult == 'download_started') {
          saveStatus = 'Protected .maipdf file created. Download started.';
        } else if (saveResult == 'cancelled') {
          saveStatus = 'Protected .maipdf file created. Save was cancelled.';
        } else {
          saveStatus = 'Protected .maipdf file created and saved.';
        }
      } catch (_) {
        saveStatus =
            'Protected .maipdf file created. Save is not available on this platform.';
      }

      if (!mounted) return;
      setState(() {
        _packing = false;
        _licenseId = package.licenseId;
        _modCode = package.modCode;
        _packageFilename = package.packageFilename;
        _statusText = saveStatus;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _packing = false;
        _statusText = 'Pack failed: $e';
      });
      _showMessage(_statusText);
    }
  }

  Uint8List _wrapMaipdfPackage(Uint8List zipBytes) {
    final packageBytes = Uint8List(_maipdfHeader.length + zipBytes.length);
    packageBytes.setAll(0, _maipdfHeader);
    packageBytes.setAll(_maipdfHeader.length, zipBytes);
    return packageBytes;
  }

  void _showMessage(String message) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message)),
    );
  }

  Future<void> _copy(String label, String? value) async {
    if (value == null || value.isEmpty) return;
    await Clipboard.setData(ClipboardData(text: value));
    _showMessage('$label copied');
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
                _buildWebCard(),
                const SizedBox(height: 14),
                _buildPackCard(),
                if (_licenseId != null || _modCode != null) ...[
                  const SizedBox(height: 14),
                  _buildResultCard(),
                ],
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
                'DRM Secure Share',
                style: TextStyle(
                  fontSize: 21,
                  fontWeight: FontWeight.w800,
                  color: Color(0xFF23172F),
                ),
              ),
              SizedBox(height: 2),
              Text(
                'Protected .maipdf package for PDFs',
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

  Widget _buildWebCard() {
    return _DrmCard(
      title: 'Web version',
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          const Text(
            'Open the same DRM tool in the browser. This is closest to the existing desktop web workflow.',
            style: TextStyle(fontSize: 13, color: Color(0xFF6B6278)),
          ),
          const SizedBox(height: 12),
          OutlinedButton.icon(
            onPressed: _openWebVersion,
            icon: const Icon(Icons.open_in_new, size: 18),
            label: const Text('Open drm.maipdf.com'),
          ),
        ],
      ),
    );
  }

  Widget _buildPackCard() {
    return _DrmCard(
      title: 'App pack',
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Text(
            _statusText,
            style: const TextStyle(fontSize: 13, color: Color(0xFF6B6278)),
          ),
          if (_fileName != null) ...[
            const SizedBox(height: 8),
            Text(
              _fileName!,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w700,
                color: Color(0xFF23172F),
              ),
            ),
          ],
          const SizedBox(height: 14),
          TextField(
            controller: _maxOpensController,
            keyboardType: TextInputType.number,
            decoration: const InputDecoration(
              labelText: 'Max opens',
              hintText: '5',
              border: OutlineInputBorder(),
              isDense: true,
              contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 12),
            ),
          ),
          const SizedBox(height: 12),
          DropdownButtonFormField<String>(
            initialValue: _expiresPreset,
            decoration: const InputDecoration(
              labelText: 'Expiration',
              border: OutlineInputBorder(),
              isDense: true,
              contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 12),
            ),
            items: const [
              DropdownMenuItem(value: '86400', child: Text('1 day')),
              DropdownMenuItem(value: '604800', child: Text('7 days')),
              DropdownMenuItem(value: '2592000', child: Text('30 days')),
              DropdownMenuItem(value: 'custom', child: Text('Custom days')),
              DropdownMenuItem(value: '-1', child: Text('Never expires')),
            ],
            onChanged: (value) {
              setState(() => _expiresPreset = value ?? '604800');
            },
          ),
          if (_expiresPreset == 'custom') ...[
            const SizedBox(height: 12),
            TextField(
              controller: _customDaysController,
              keyboardType: TextInputType.number,
              decoration: const InputDecoration(
                labelText: 'Custom days',
                border: OutlineInputBorder(),
                isDense: true,
                contentPadding:
                    EdgeInsets.symmetric(horizontal: 12, vertical: 12),
              ),
            ),
          ],
          const SizedBox(height: 16),
          FilledButton.icon(
            onPressed: _packing ? null : _pickAndPack,
            icon: _packing
                ? const SizedBox(
                    width: 18,
                    height: 18,
                    child: CircularProgressIndicator(
                      strokeWidth: 2,
                      color: Colors.white,
                    ),
                  )
                : const Icon(Icons.lock_outline, size: 18),
            label: Text(_packing ? 'Packing...' : 'Choose PDF and Pack'),
            style: FilledButton.styleFrom(
              minimumSize: const Size.fromHeight(48),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(8),
              ),
              backgroundColor: const Color(0xFF316CFF),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildResultCard() {
    return _DrmCard(
      title: 'Result',
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          if (_packageFilename != null)
            Text(
              _packageFilename!,
              style: const TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w700,
                color: Color(0xFF23172F),
              ),
            ),
          const SizedBox(height: 12),
          _CodeRow(
            label: 'License ID',
            value: _licenseId ?? '-',
            onCopy: () => _copy('License ID', _licenseId),
          ),
          const SizedBox(height: 10),
          _CodeRow(
            label: 'Modification Code',
            value: _modCode ?? '-',
            onCopy: () => _copy('Modification code', _modCode),
          ),
          const SizedBox(height: 12),
          const Text(
            'Send this .maipdf file to readers. Keep the modification code to manage or revoke access later.',
            style: TextStyle(fontSize: 12, color: Color(0xFF7A6D8D)),
          ),
          const SizedBox(height: 12),
          OutlinedButton.icon(
            onPressed: _openManage,
            icon: const Icon(Icons.settings_outlined, size: 18),
            label: const Text('Manage license'),
          ),
        ],
      ),
    );
  }

  @override
  void dispose() {
    _maxOpensController.dispose();
    _customDaysController.dispose();
    super.dispose();
  }
}

class _DrmCard extends StatelessWidget {
  const _DrmCard({required this.title, required this.child});

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

class _CodeRow extends StatelessWidget {
  const _CodeRow({
    required this.label,
    required this.value,
    required this.onCopy,
  });

  final String label;
  final String value;
  final VoidCallback onCopy;

  @override
  Widget build(BuildContext context) {
    return Container(
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
                  label,
                  style: const TextStyle(
                    fontSize: 11,
                    color: Color(0xFF61748E),
                    fontWeight: FontWeight.w700,
                  ),
                ),
                const SizedBox(height: 4),
                SelectableText(
                  value,
                  style: const TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.w700,
                    color: Color(0xFF0F2741),
                  ),
                ),
              ],
            ),
          ),
          IconButton(
            onPressed: onCopy,
            icon: const Icon(Icons.copy, size: 18),
          ),
        ],
      ),
    );
  }
}
