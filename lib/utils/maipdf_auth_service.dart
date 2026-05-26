import 'dart:async';
import 'dart:convert';

import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;

class MaiPdfAuthService extends ChangeNotifier {
  MaiPdfAuthService._();

  static final MaiPdfAuthService instance = MaiPdfAuthService._();

  static const String apiBase = 'https://drm.maipdf.com';

  final FirebaseAuth _auth = FirebaseAuth.instance;
  StreamSubscription<User?>? _authSub;
  bool _initialized = false;
  bool _busy = false;
  String? _lastError;

  bool get busy => _busy;

  String? get lastError => _lastError;

  User? get user => _auth.currentUser;

  bool get isSignedIn => user != null;

  String? get email => user?.email;

  String? get displayName => user?.displayName;

  String? get photoUrl => user?.photoURL;

  Future<void> initialize() async {
    if (_initialized) return;
    _initialized = true;
    _authSub = _auth.authStateChanges().listen((user) async {
      _lastError = null;
      notifyListeners();
      if (user != null) {
        try {
          await syncSession();
        } catch (e) {
          _lastError = e.toString();
          notifyListeners();
        }
      }
    });
  }

  Future<void> signInWithGoogle() async {
    _setBusy(true);
    try {
      final provider = GoogleAuthProvider()
        ..setCustomParameters({'prompt': 'select_account'});
      if (kIsWeb) {
        await _auth.signInWithPopup(provider);
      } else {
        await _auth.signInWithProvider(provider);
      }
      await syncSession();
      _lastError = null;
    } catch (e) {
      _lastError = e.toString();
      rethrow;
    } finally {
      _setBusy(false);
    }
  }

  Future<void> signOut() async {
    _setBusy(true);
    try {
      await _auth.signOut();
      _lastError = null;
    } finally {
      _setBusy(false);
    }
  }

  Future<String?> idToken({bool forceRefresh = false}) async {
    return user?.getIdToken(forceRefresh);
  }

  Future<Map<String, String>> authHeaders() async {
    final token = await idToken();
    if (token == null || token.isEmpty) return {};
    return {'Authorization': 'Bearer $token'};
  }

  Future<void> syncSession() async {
    final token = await idToken();
    if (token == null || token.isEmpty) return;
    final response = await http
        .post(
          Uri.parse('$apiBase/api/auth/session'),
          headers: {'Authorization': 'Bearer $token'},
        )
        .timeout(const Duration(seconds: 30));
    if (response.statusCode < 200 || response.statusCode >= 300) {
      var message = response.reasonPhrase ?? 'HTTP ${response.statusCode}';
      try {
        final decoded = jsonDecode(response.body);
        if (decoded is Map<String, dynamic>) {
          message =
              decoded['message']?.toString() ??
              decoded['error']?.toString() ??
              message;
        }
      } catch (_) {
        // Keep the HTTP status readable when the response is not JSON.
      }
      throw StateError(message);
    }
  }

  void _setBusy(bool value) {
    if (_busy == value) return;
    _busy = value;
    notifyListeners();
  }

  @override
  void dispose() {
    _authSub?.cancel();
    super.dispose();
  }
}
