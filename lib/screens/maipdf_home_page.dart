import 'package:flutter/material.dart';
import 'maipdf2026/maipdf2026_screen.dart';

/// 首页：只展示「Online MaiPDF Cloud Sharing」入口，点击进入原生 2026 页面。
class MaipdfHomePage extends StatelessWidget {
  const MaipdfHomePage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('MaiPDF'),
        backgroundColor: Theme.of(context).colorScheme.inversePrimary,
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              const SizedBox(height: 16),
              Text(
                'Smart Sharing, Secure Transfer',
                style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                      fontWeight: FontWeight.bold,
                    ),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 32),
              _CloudShareCard(
                onStart: () => _openCloudShare(context),
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _openCloudShare(BuildContext context) {
    Navigator.of(context).push(
      MaterialPageRoute<void>(
        builder: (context) => const Maipdf2026Screen(),
      ),
    );
  }
}

class _CloudShareCard extends StatelessWidget {
  const _CloudShareCard({required this.onStart});

  final VoidCallback onStart;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Card(
      elevation: 4,
      shadowColor: Colors.black26,
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(
              Icons.cloud_upload_outlined,
              size: 48,
              color: theme.colorScheme.primary,
            ),
            const SizedBox(height: 12),
            Text(
              'Online MaiPDF Cloud Sharing',
              style: theme.textTheme.titleLarge?.copyWith(
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'Convenient online secure PDF document sharing experience '
              'with multiple security options to protect your documents...',
              style: theme.textTheme.bodyMedium?.copyWith(
                color: theme.colorScheme.onSurfaceVariant,
              ),
            ),
            const SizedBox(height: 16),
            FilledButton.icon(
              onPressed: onStart,
              icon: const Icon(Icons.arrow_forward, size: 18),
              label: const Text('START'),
            ),
          ],
        ),
      ),
    );
  }
}
