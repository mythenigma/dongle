import 'package:flutter/services.dart';

class SecureScreen {
  static const MethodChannel _channel = MethodChannel('maipdf/secure_screen');

  static Future<bool> setEnabled(bool enabled) async {
    try {
      final result = await _channel.invokeMethod<bool>(
        'setSecure',
        {'enabled': enabled},
      );
      return result ?? false;
    } on MissingPluginException {
      return false;
    }
  }
}
