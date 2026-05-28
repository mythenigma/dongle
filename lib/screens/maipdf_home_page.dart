import 'package:flutter/material.dart';
import 'drm_license_manage_page.dart';
import 'drm_protected_viewer_page.dart';
import 'drm_secure_share_page.dart';
import 'maipdf2026/maipdf2026_screen.dart';
import '../utils/maipdf_cloud_auth_service.dart';
import '../utils/maipdf_auth_service.dart';

/// 首页：只展示「Online MaiPDF Cloud Sharing」入口，点击进入原生 2026 页面。
class MaipdfHomePage extends StatelessWidget {
  const MaipdfHomePage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF6F2FF),
      body: SafeArea(
        child: Center(
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 430),
            child: DefaultTabController(
              length: 2,
              child: Column(
                children: [
                  Padding(
                    padding: const EdgeInsets.fromLTRB(16, 14, 16, 8),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        const Text(
                          'MaiPDF',
                          style: TextStyle(
                            fontSize: 28,
                            fontWeight: FontWeight.w800,
                            color: Color(0xFF23172F),
                          ),
                        ),
                        const SizedBox(height: 4),
                        const Text(
                          'Smart Sharing, Secure Transfer',
                          style: TextStyle(
                            fontSize: 14,
                            color: Color(0xFF7A6D8D),
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                        const SizedBox(height: 12),
                        _AccountStrip(
                          onSignIn: () => _signIn(context),
                          onSignOut: () => _signOut(context),
                        ),
                        const SizedBox(height: 18),
                        Container(
                          padding: const EdgeInsets.all(4),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(color: const Color(0xFFE5DFF0)),
                          ),
                          child: TabBar(
                            indicator: BoxDecoration(
                              color: const Color(0xFF8B5CF6),
                              borderRadius: BorderRadius.circular(6),
                            ),
                            indicatorSize: TabBarIndicatorSize.tab,
                            dividerColor: Colors.transparent,
                            labelColor: Colors.white,
                            unselectedLabelColor: const Color(0xFF7A6D8D),
                            labelStyle: const TextStyle(
                              fontSize: 13,
                              fontWeight: FontWeight.w800,
                            ),
                            tabs: const [
                              Tab(text: 'Cloud'),
                              Tab(text: 'DRM'),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                  Expanded(
                    child: TabBarView(
                      children: [
                        SingleChildScrollView(
                          padding: const EdgeInsets.fromLTRB(16, 10, 16, 24),
                          child: _CloudShareCard(
                            onStart: () => _openCloudShare(context),
                          ),
                        ),
                        SingleChildScrollView(
                          padding: const EdgeInsets.fromLTRB(16, 10, 16, 24),
                          child: Column(
                            children: [
                              _DrmShareCard(
                                onStart: () => _openDrmShare(context),
                              ),
                              const SizedBox(height: 14),
                              _DrmViewerCard(
                                onStart: () => _openDrmViewer(context),
                              ),
                              const SizedBox(height: 14),
                              _DrmManageCard(
                                onStart: () => _openDrmManage(context),
                              ),
                            ],
                          ),
                        ),
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

  void _openCloudShare(BuildContext context) {
    Navigator.of(context).push(
      MaterialPageRoute<void>(builder: (context) => const Maipdf2026Screen()),
    );
  }

  void _openDrmShare(BuildContext context) {
    Navigator.of(context).push(
      MaterialPageRoute<void>(builder: (context) => const DrmSecureSharePage()),
    );
  }

  void _openDrmViewer(BuildContext context) {
    Navigator.of(context).push(
      MaterialPageRoute<void>(
        builder: (context) => const DrmProtectedViewerPage(),
      ),
    );
  }

  void _openDrmManage(BuildContext context) {
    Navigator.of(context).push(
      MaterialPageRoute<void>(
        builder: (context) =>
            const DrmLicenseManagePage(apiBase: 'https://drm.maipdf.com'),
      ),
    );
  }

  Future<void> _signIn(BuildContext context) async {
    try {
      await MaiPdfAuthService.instance.signInWithGoogle();
      try {
        await MaiPdfCloudAuthService.instance.ensureSession();
      } catch (_) {
        // Cloud account sync is optional; Google sign-in itself succeeded.
      }
      if (!context.mounted) return;
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(const SnackBar(content: Text('Signed in with Google')));
    } catch (e) {
      if (!context.mounted) return;
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(SnackBar(content: Text('Sign in failed: $e')));
    }
  }

  Future<void> _signOut(BuildContext context) async {
    await MaiPdfAuthService.instance.signOut();
    MaiPdfCloudAuthService.instance.clear();
    if (!context.mounted) return;
    ScaffoldMessenger.of(
      context,
    ).showSnackBar(const SnackBar(content: Text('Signed out')));
  }
}

class _AccountStrip extends StatelessWidget {
  const _AccountStrip({required this.onSignIn, required this.onSignOut});

  final VoidCallback onSignIn;
  final VoidCallback onSignOut;

  @override
  Widget build(BuildContext context) {
    return AnimatedBuilder(
      animation: MaiPdfAuthService.instance,
      builder: (context, _) {
        final auth = MaiPdfAuthService.instance;
        final signedIn = auth.isSignedIn;
        return Container(
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(8),
            border: Border.all(color: const Color(0xFFE5DFF0)),
          ),
          child: Row(
            children: [
              Icon(
                signedIn ? Icons.account_circle : Icons.login,
                size: 22,
                color: const Color(0xFF6F5596),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  signedIn
                      ? (auth.email ?? auth.displayName ?? 'Google account')
                      : 'Sign in to save licenses',
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w700,
                    color: Color(0xFF4D405F),
                  ),
                ),
              ),
              TextButton(
                onPressed: auth.busy ? null : (signedIn ? onSignOut : onSignIn),
                child: Text(signedIn ? 'SIGN OUT' : 'SIGN IN'),
              ),
            ],
          ),
        );
      },
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
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(8),
        side: const BorderSide(color: Color(0xFFE5DFF0)),
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(
              Icons.cloud_upload_outlined,
              size: 40,
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
              style: FilledButton.styleFrom(
                minimumSize: const Size.fromHeight(46),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _DrmViewerCard extends StatelessWidget {
  const _DrmViewerCard({required this.onStart});

  final VoidCallback onStart;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Card(
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(8),
        side: const BorderSide(color: Color(0xFFE5DFF0)),
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(
              Icons.visibility_outlined,
              size: 40,
              color: theme.colorScheme.primary,
            ),
            const SizedBox(height: 12),
            Text(
              'Protected Viewer',
              style: theme.textTheme.titleLarge?.copyWith(
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'Open a .maipdf protected file or locked HTML inside the app. '
              'The reading screen enables screenshot protection where supported.',
              style: theme.textTheme.bodyMedium?.copyWith(
                color: theme.colorScheme.onSurfaceVariant,
              ),
            ),
            const SizedBox(height: 16),
            FilledButton.icon(
              onPressed: onStart,
              icon: const Icon(Icons.folder_open_outlined, size: 18),
              label: const Text('OPEN FILE'),
              style: FilledButton.styleFrom(
                minimumSize: const Size.fromHeight(46),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _DrmShareCard extends StatelessWidget {
  const _DrmShareCard({required this.onStart});

  final VoidCallback onStart;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Card(
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(8),
        side: const BorderSide(color: Color(0xFFE5DFF0)),
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(
              Icons.lock_outline,
              size: 40,
              color: theme.colorScheme.primary,
            ),
            const SizedBox(height: 12),
            Text(
              'DRM Secure Share',
              style: theme.textTheme.titleLarge?.copyWith(
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'Create a .maipdf protected file with open limits, expiration, '
              'and a modification code for later management.',
              style: theme.textTheme.bodyMedium?.copyWith(
                color: theme.colorScheme.onSurfaceVariant,
              ),
            ),
            const SizedBox(height: 16),
            FilledButton.icon(
              onPressed: onStart,
              icon: const Icon(Icons.arrow_forward, size: 18),
              label: const Text('START'),
              style: FilledButton.styleFrom(
                minimumSize: const Size.fromHeight(46),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _DrmManageCard extends StatelessWidget {
  const _DrmManageCard({required this.onStart});

  final VoidCallback onStart;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Card(
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(8),
        side: const BorderSide(color: Color(0xFFE5DFF0)),
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(
              Icons.settings_outlined,
              size: 40,
              color: theme.colorScheme.primary,
            ),
            const SizedBox(height: 12),
            Text(
              'License Manager',
              style: theme.textTheme.titleLarge?.copyWith(
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'Manage an existing .maipdf license with the License ID and '
              'Modification Code. Check status, add opens, extend, or revoke.',
              style: theme.textTheme.bodyMedium?.copyWith(
                color: theme.colorScheme.onSurfaceVariant,
              ),
            ),
            const SizedBox(height: 16),
            FilledButton.icon(
              onPressed: onStart,
              icon: const Icon(Icons.tune, size: 18),
              label: const Text('MANAGE'),
              style: FilledButton.styleFrom(
                minimumSize: const Size.fromHeight(46),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
