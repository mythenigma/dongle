import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:webview_flutter/webview_flutter.dart';

/// 内嵌打开 pdf/maipdf2026.html（Online MaiPDF Cloud Sharing）。
/// 手机端：优先从 assets 加载本地 HTML（你提供的源码），后端请求仍走线上；
/// 失败时回退为直接打开线上 URL。
class CloudSharePage extends StatefulWidget {
  const CloudSharePage({
    super.key,
    required this.url,
    this.assetHtmlPath,
  });

  /// 线上地址，用于回退或 Web 打开
  final String url;

  /// 本地 HTML 在 assets 中的路径，例如 'assets/html/maipdf2026.html'
  final String? assetHtmlPath;

  @override
  State<CloudSharePage> createState() => _CloudSharePageState();
}

class _CloudSharePageState extends State<CloudSharePage> {
  late final WebViewController _controller;
  static const String _baseUrl = 'https://maipdf.com/pdf/';

  @override
  void initState() {
    super.initState();
    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setNavigationDelegate(
        NavigationDelegate(
          onPageStarted: (url) {},
          onPageFinished: (url) {},
          onWebResourceError: (error) {},
        ),
      );

    if (widget.assetHtmlPath != null && widget.assetHtmlPath!.isNotEmpty) {
      _loadFromAsset();
    } else {
      _loadFromUrl();
    }
  }

  Future<void> _loadFromAsset() async {
    try {
      final String html = await rootBundle.loadString(widget.assetHtmlPath!);
      await _controller.loadHtmlString(html, baseUrl: _baseUrl);
    } catch (e) {
      await _loadFromUrl();
    }
  }

  Future<void> _loadFromUrl() async {
    await _controller.loadRequest(Uri.parse(widget.url));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('MaiPDF Cloud Sharing'),
        backgroundColor: Theme.of(context).colorScheme.inversePrimary,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => Navigator.of(context).pop(),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => _loadFromAsset(),
            tooltip: '刷新',
          ),
        ],
      ),
      body: WebViewWidget(controller: _controller),
    );
  }
}
