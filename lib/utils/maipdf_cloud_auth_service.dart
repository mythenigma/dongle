import 'dart:convert';

import 'package:firebase_auth/firebase_auth.dart';
import 'package:http/http.dart' as http;

class MaiPdfCloudAuthService {
  MaiPdfCloudAuthService._();

  static final MaiPdfCloudAuthService instance = MaiPdfCloudAuthService._();

  static const String apiBaseUrl = 'https://maipdf.com/app-api';

  String? _syncedUid;

  bool get hasCloudAccount => _syncedUid != null;

  // Kept for old Cloud requests. The new App API does not need PHP cookies.
  Map<String, String> get cookieHeaders => const {};

  Future<void> ensureSession() async {
    final user = FirebaseAuth.instance.currentUser;
    if (user == null) return;
    if (_syncedUid == user.uid) return;
    await syncFromFirebaseUser(user);
  }

  Future<void> syncFromFirebaseUser([User? user]) async {
    final currentUser = user ?? FirebaseAuth.instance.currentUser;
    if (currentUser == null) return;

    final token = await currentUser.getIdToken();
    if (token == null || token.isEmpty) return;

    await _postAppJson(
      '$apiBaseUrl/cloud-login.php',
      token: token,
      body: const {},
    );
    _syncedUid = currentUser.uid;
  }

  Future<String?> recordCloudLink({
    required String filePath,
    required String identifier,
  }) async {
    final user = FirebaseAuth.instance.currentUser;
    if (user == null) return null;

    await ensureSession();
    final token = await user.getIdToken();
    if (token == null || token.isEmpty) return null;

    final data = await _postAppJson(
      '$apiBaseUrl/cloud-record.php',
      token: token,
      body: {'file_path': filePath, 'identifier': identifier},
    );
    final mode = data['mode']?.toString();
    if (mode == 'updated') return 'Cloud record updated for ${user.email}';
    return 'Cloud record saved for ${user.email}';
  }

  void clear() {
    _syncedUid = null;
  }

  Future<Map<String, dynamic>> _postAppJson(
    String url, {
    required String token,
    required Map<String, String> body,
  }) async {
    final response = await http
        .post(
          Uri.parse(url),
          headers: {
            'Authorization': 'Bearer $token',
            'Content-Type': 'application/json; charset=utf-8',
            'X-Requested-With': 'FlutterApp',
          },
          body: jsonEncode(body),
        )
        .timeout(const Duration(seconds: 30));

    Map<String, dynamic> decoded = {};
    try {
      final value = jsonDecode(response.body);
      if (value is Map<String, dynamic>) decoded = value;
    } catch (_) {
      // Keep the HTTP status readable when the response is not JSON.
    }

    if (response.statusCode < 200 || response.statusCode >= 300) {
      final message =
          decoded['message']?.toString() ??
          decoded['error']?.toString() ??
          response.reasonPhrase ??
          'HTTP ${response.statusCode}';
      throw StateError(message);
    }
    return decoded;
  }
}
