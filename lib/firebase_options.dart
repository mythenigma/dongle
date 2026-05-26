import 'package:firebase_core/firebase_core.dart';
import 'package:flutter/foundation.dart'
    show TargetPlatform, defaultTargetPlatform, kIsWeb;

class DefaultFirebaseOptions {
  static FirebaseOptions get currentPlatform {
    if (kIsWeb) return web;
    switch (defaultTargetPlatform) {
      case TargetPlatform.android:
        return android;
      case TargetPlatform.iOS:
        return ios;
      case TargetPlatform.macOS:
        return macos;
      case TargetPlatform.windows:
        return windows;
      case TargetPlatform.linux:
        return linux;
      default:
        return web;
    }
  }

  static const FirebaseOptions web = FirebaseOptions(
    apiKey: 'AIzaSyA-Y2zgMaXR08CYjS3HrucYi9xlcMr2_wQ',
    authDomain: 'maipdf-login.firebaseapp.com',
    projectId: 'maipdf-login',
    storageBucket: 'maipdf-login.firebasestorage.app',
    messagingSenderId: '150464233488',
    appId: '1:150464233488:web:b365ab4e4a52eca157ca95',
  );

  // The DRM backend only verifies Firebase ID tokens for project
  // "maipdf-login". Native Firebase apps should later be registered in the
  // same project for release builds; these options keep the prototype wired to
  // the same backend project.
  static const FirebaseOptions android = web;
  static const FirebaseOptions ios = web;
  static const FirebaseOptions macos = web;
  static const FirebaseOptions windows = web;
  static const FirebaseOptions linux = web;
}
