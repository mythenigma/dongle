import 'dart:html' as html_browser;
import 'dart:ui_web' as ui_web;

import 'package:flutter/material.dart';

class HtmlViewer extends StatefulWidget {
  const HtmlViewer({
    super.key,
    required this.html,
    required this.baseUrl,
    this.onLoaded,
    this.onError,
  });

  final String html;
  final String baseUrl;
  final VoidCallback? onLoaded;
  final ValueChanged<String>? onError;

  @override
  State<HtmlViewer> createState() => _HtmlViewerState();
}

class _HtmlViewerState extends State<HtmlViewer> {
  late final String _viewType;

  @override
  void initState() {
    super.initState();
    _viewType = 'maipdf-html-viewer-${DateTime.now().microsecondsSinceEpoch}';

    ui_web.platformViewRegistry.registerViewFactory(_viewType, (int viewId) {
      final iframe = html_browser.IFrameElement()
        ..style.border = '0'
        ..style.width = '100%'
        ..style.height = '100%'
        ..style.backgroundColor = 'white'
        ..allow = 'fullscreen'
        ..srcdoc = _withBaseTag(widget.html, widget.baseUrl);

      iframe.onLoad.listen((_) => widget.onLoaded?.call());
      iframe.onError.listen((_) {
        widget.onError?.call('Browser iframe failed to load.');
      });

      return iframe;
    });
  }

  String _withBaseTag(String source, String baseUrl) {
    final base = '<base href="$baseUrl">';
    if (source.contains('<base ')) return source;
    if (source.contains('<head>')) {
      return source.replaceFirst('<head>', '<head>$base');
    }
    return '$base$source';
  }

  @override
  Widget build(BuildContext context) {
    return HtmlElementView(viewType: _viewType);
  }
}
