import 'dart:async';
import 'dart:convert';
import 'dart:typed_data';

import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';

import '../utils/secure_screen.dart';
import '../widgets/html_viewer.dart';

class DrmProtectedViewerPage extends StatefulWidget {
  const DrmProtectedViewerPage({super.key});

  @override
  State<DrmProtectedViewerPage> createState() => _DrmProtectedViewerPageState();
}

class _DrmProtectedViewerPageState extends State<DrmProtectedViewerPage> {
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

  String? _html;
  bool _loading = false;
  String _statusText = 'Choose a .maipdf or locked HTML file to read.';
  String? _fileName;

  @override
  void initState() {
    super.initState();
    SecureScreen.setEnabled(true);
  }

  Future<void> _pickAndOpen() async {
    final result = await FilePicker.platform.pickFiles(
      type: FileType.custom,
      allowedExtensions: ['maipdf', 'zip', 'html', 'htm'],
      allowMultiple: false,
      withData: true,
    );
    if (result == null || result.files.isEmpty) return;

    final file = result.files.single;
    setState(() {
      _loading = true;
      _fileName = file.name;
      _statusText = 'Loading protected file...';
    });

    try {
      final bytes = file.bytes;
      if (bytes == null || bytes.isEmpty) {
        throw const FormatException('Cannot read selected file bytes.');
      }

      final lowerName = file.name.toLowerCase();
      final isPackage =
          lowerName.endsWith('.maipdf') || lowerName.endsWith('.zip');
      final html = isPackage
          ? _extractLockedHtmlFromPackage(bytes)
          : utf8.decode(bytes, allowMalformed: true);

      if (!mounted) return;
      setState(() {
        _html = html;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _loading = false;
        _statusText = 'Open failed: $e';
      });
    }
  }

  String _extractLockedHtmlFromPackage(Uint8List packageBytes) {
    final zipBytes = _unwrapMaipdfPackage(packageBytes);
    final data = ByteData.sublistView(zipBytes);
    var offset = 0;

    while (offset + 30 <= zipBytes.length) {
      final signature = data.getUint32(offset, Endian.little);
      if (signature != 0x04034b50) {
        offset += 1;
        continue;
      }

      final method = data.getUint16(offset + 8, Endian.little);
      final compressedSize = data.getUint32(offset + 18, Endian.little);
      final uncompressedSize = data.getUint32(offset + 22, Endian.little);
      final fileNameLength = data.getUint16(offset + 26, Endian.little);
      final extraLength = data.getUint16(offset + 28, Endian.little);
      final nameStart = offset + 30;
      final nameEnd = nameStart + fileNameLength;
      final contentStart = nameEnd + extraLength;
      final contentEnd = contentStart + compressedSize;

      if (nameEnd > zipBytes.length || contentEnd > zipBytes.length) {
        throw const FormatException('Invalid MaiPDF package.');
      }

      final name = utf8.decode(
        zipBytes.sublist(nameStart, nameEnd),
        allowMalformed: true,
      );
      final lowerName = name.toLowerCase();

      if (lowerName.endsWith('.html') || lowerName.endsWith('.htm')) {
        if (method != 0) {
          throw const FormatException(
            'This package uses compression. Please select the locked HTML file after extracting it.',
          );
        }
        if (compressedSize != uncompressedSize) {
          throw const FormatException('Unexpected package entry size.');
        }
        return utf8.decode(
          zipBytes.sublist(contentStart, contentEnd),
          allowMalformed: true,
        );
      }

      offset = contentEnd;
    }

    throw const FormatException('No locked HTML file found in this package.');
  }

  Uint8List _unwrapMaipdfPackage(Uint8List packageBytes) {
    if (packageBytes.length < _maipdfHeader.length) {
      return packageBytes;
    }

    for (var i = 0; i < _maipdfHeader.length; i += 1) {
      if (packageBytes[i] != _maipdfHeader[i]) {
        return packageBytes;
      }
    }

    return Uint8List.sublistView(packageBytes, _maipdfHeader.length);
  }

  @override
  Widget build(BuildContext context) {
    final html = _html;

    return Scaffold(
      backgroundColor: const Color(0xFFFCFAFF),
      body: Center(
        child: ConstrainedBox(
          constraints: const BoxConstraints(maxWidth: 430),
          child: SafeArea(
            child: Column(
              children: [
                _buildHeader(context),
                Padding(
                  padding: const EdgeInsets.fromLTRB(16, 10, 16, 12),
                  child: _buildPickerCard(),
                ),
                Expanded(
                  child: Container(
                    margin: const EdgeInsets.fromLTRB(16, 0, 16, 16),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(color: const Color(0xFFDCE5F3)),
                    ),
                    clipBehavior: Clip.antiAlias,
                    child: html == null
                        ? _buildEmptyViewer()
                        : Stack(
                            children: [
                              HtmlViewer(
                                html: html,
                                baseUrl: 'https://drm.maipdf.com/',
                                onLoaded: () {
                                  if (!mounted) return;
                                  setState(() {
                                    _loading = false;
                                    _statusText = 'Protected viewer is ready.';
                                  });
                                },
                                onError: (message) {
                                  if (!mounted) return;
                                  setState(() {
                                    _loading = false;
                                    _statusText = 'Viewer error: $message';
                                  });
                                },
                              ),
                              if (_loading) const LinearProgressIndicator(),
                            ],
                          ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildHeader(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(8, 8, 16, 4),
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
              children: [
                Text(
                  'Protected Viewer',
                  style: TextStyle(
                    fontSize: 21,
                    fontWeight: FontWeight.w800,
                    color: Color(0xFF23172F),
                  ),
                ),
                SizedBox(height: 2),
                Text(
                  'Open .maipdf or locked HTML in the app',
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
      ),
    );
  }

  Widget _buildPickerCard() {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: const Color(0xFFDCE5F3)),
      ),
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
          const SizedBox(height: 12),
          FilledButton.icon(
            onPressed: _loading ? null : _pickAndOpen,
            icon: _loading
                ? const SizedBox(
                    width: 18,
                    height: 18,
                    child: CircularProgressIndicator(
                      strokeWidth: 2,
                      color: Colors.white,
                    ),
                  )
                : const Icon(Icons.folder_open_outlined, size: 18),
            label: Text(_loading ? 'Opening...' : 'Choose .maipdf / HTML'),
            style: FilledButton.styleFrom(
              minimumSize: const Size.fromHeight(46),
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

  Widget _buildEmptyViewer() {
    return const Center(
      child: Padding(
        padding: EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              Icons.picture_as_pdf_outlined,
              size: 44,
              color: Color(0xFF7A6D8D),
            ),
            SizedBox(height: 12),
            Text(
              'Viewer frame',
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w800,
                color: Color(0xFF23172F),
              ),
            ),
            SizedBox(height: 6),
            Text(
              'Select a .maipdf file to load the protected reader here.',
              textAlign: TextAlign.center,
              style: TextStyle(fontSize: 13, color: Color(0xFF7A6D8D)),
            ),
          ],
        ),
      ),
    );
  }

  @override
  void dispose() {
    SecureScreen.setEnabled(false);
    super.dispose();
  }
}
