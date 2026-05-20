import 'dart:io';
import 'dart:typed_data';

import 'package:file_picker/file_picker.dart';

Future<String> saveBytesAsFile({
  required String filename,
  required Uint8List bytes,
}) async {
  if (Platform.isWindows || Platform.isMacOS || Platform.isLinux) {
    final outputPath = await FilePicker.platform.saveFile(
      dialogTitle: 'Save protected MaiPDF package',
      fileName: filename,
    );
    if (outputPath == null) return 'cancelled';

    await File(outputPath).writeAsBytes(bytes, flush: true);
    return 'saved';
  }

  final savedPath = await FilePicker.platform.saveFile(
    dialogTitle: 'Save protected MaiPDF package',
    fileName: filename,
    bytes: bytes,
  );
  return savedPath == null ? 'cancelled' : 'saved';
}
