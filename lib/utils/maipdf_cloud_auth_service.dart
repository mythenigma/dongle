import 'dart:convert';

import 'package:firebase_auth/firebase_auth.dart';
import 'package:http/http.dart' as http;

class MaiPdfCloudAuthService {
  MaiPdfCloudAuthService._();

  static final MaiPdfCloudAuthService instance = MaiPdfCloudAuthService._();

  static const String baseUrl = 'https://maipdf.com';

  final Map<String, String> _cookies = {};
  String? _syncedUid;

  bool get hasCloudSession => _cookies.isNotEmpty;

  Map<String, String> get cookieHeaders {
    if (_cookies.isEmpty) return {};
    final cookie = _cookies.entries
        .map((entry) => '${entry.key}=${entry.value}')
        .join('; ');
    return {'Cookie': cookie};
  }

  Future<void> ensureSession() async {
    final user = FirebaseAuth.instance.currentUser;
    if (user == null) return;
    if (_syncedUid == user.uid && _cookies.isNotEmpty) return;
    await syncFromFirebaseUser(user);
  }

  Future<void> syncFromFirebaseUser([User? user]) async {
    final currentUser = user ?? FirebaseAuth.instance.currentUser;
    if (currentUser == null) return;

    final email = currentUser.email;
    final uid = currentUser.uid;
    if (email == null || email.isEmpty || uid.isEmpty) return;

    await _postJson('$baseUrl/6/firebase-register.php', {
      'email': email,
      'uid': uid,
    });
    await _postJson('$baseUrl/6/firebase-session-login.php', {
      'email': email,
      'uid': uid,
    });

    _syncedUid = uid;
  }

  void clear() {
    _cookies.clear();
    _syncedUid = null;
  }

  Future<void> _postJson(String url, Map<String, String> body) async {
    final response = await http
        .post(
          Uri.parse(url),
          headers: {
            'Content-Type': 'application/json; charset=utf-8',
            'X-Requested-With': 'FlutterApp',
            ...cookieHeaders,
          },
          body: jsonEncode(body),
        )
        .timeout(const Duration(seconds: 30));

    _storeCookies(response);

    if (response.statusCode < 200 || response.statusCode >= 300) {
      var message = response.reasonPhrase ?? 'HTTP ${response.statusCode}';
      try {
        final decoded = jsonDecode(response.body);
        if (decoded is Map<String, dynamic>) {
          message =
              decoded['error']?.toString() ??
              decoded['msg']?.toString() ??
              message;
        }
      } catch (_) {
        // Keep the HTTP status readable when the response is not JSON.
      }
      throw StateError(message);
    }
  }

  void _storeCookies(http.Response response) {
    final raw = response.headers['set-cookie'];
    if (raw == null || raw.isEmpty) return;
    for (final name in const ['PHPSESSID', 'dc']) {
      final match = RegExp('$name=([^;,\\s]+)').firstMatch(raw);
      final value = match?.group(1);
      if (value != null && value.isNotEmpty) {
        _cookies[name] = value;
      }
    }
  }
}
