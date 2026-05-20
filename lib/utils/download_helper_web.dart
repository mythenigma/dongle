import 'dart:html' as html;
import 'dart:typed_data';

Future<String> saveBytesAsFile({
  required String filename,
  required Uint8List bytes,
}) async {
  final blob = html.Blob([bytes], 'application/vnd.maipdf');
  final url = html.Url.createObjectUrlFromBlob(blob);
  final anchor = html.AnchorElement(href: url)
    ..download = filename
    ..style.display = 'none';

  html.document.body?.append(anchor);
  anchor.click();
  anchor.remove();
  html.Url.revokeObjectUrl(url);

  return 'download_started';
}
