import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';

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
  late final WebViewController _controller;

  @override
  void initState() {
    super.initState();
    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setBackgroundColor(Colors.white)
      ..setNavigationDelegate(
        NavigationDelegate(
          onPageFinished: (_) => widget.onLoaded?.call(),
          onWebResourceError: (error) {
            widget.onError?.call(error.description);
          },
        ),
      )
      ..loadHtmlString(widget.html, baseUrl: widget.baseUrl);
  }

  @override
  Widget build(BuildContext context) {
    return WebViewWidget(controller: _controller);
  }
}
