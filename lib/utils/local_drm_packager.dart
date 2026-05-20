import 'dart:convert';
import 'dart:math';
import 'dart:typed_data';

import 'package:cryptography/cryptography.dart';
import 'package:http/http.dart' as http;

class LocalDrmPackageResult {
  const LocalDrmPackageResult({
    required this.packageBytes,
    required this.packageFilename,
    required this.licenseId,
    required this.modCode,
  });

  final Uint8List packageBytes;
  final String packageFilename;
  final String licenseId;
  final String modCode;
}

class LocalDrmPackager {
  LocalDrmPackager({required this.apiBase});

  final String apiBase;

  static const String drmVersion = '2.0.0-app-local';

  Future<LocalDrmPackageResult> packPdf({
    required Uint8List pdfBytes,
    required String filename,
    required int maxOpens,
    required int expiresInSeconds,
  }) async {
    if (!_looksLikePdf(pdfBytes)) {
      throw const FormatException('Selected file is not a PDF.');
    }

    final fullKey = _randomBytes(32);
    final htmlHalf = _randomBytes(32);
    final serverHalf = Uint8List(32);
    for (var i = 0; i < 32; i += 1) {
      serverHalf[i] = fullKey[i] ^ htmlHalf[i];
    }

    final contentHash = await Sha256().hash(pdfBytes);
    final license = await _createLicense(
      filename: filename,
      fileSizeBytes: pdfBytes.length,
      contentSha256: _base64UrlNoPad(contentHash.bytes),
      keyshareServer: _base64UrlNoPad(serverHalf),
      maxOpens: maxOpens,
      expiresInSeconds: expiresInSeconds,
    );

    final licenseId = license.licenseId;
    final iv = _randomBytes(12);
    final cipher = AesGcm.with256bits();
    final secretBox = await cipher.encrypt(
      pdfBytes,
      secretKey: SecretKey(fullKey),
      nonce: iv,
      aad: utf8.encode(licenseId),
    );
    final ciphertextWithTag = Uint8List(
      secretBox.cipherText.length + secretBox.mac.bytes.length,
    )
      ..setAll(0, secretBox.cipherText)
      ..setAll(secretBox.cipherText.length, secretBox.mac.bytes);

    final mask = await Sha256().hash(utf8.encode(licenseId));
    final maskedHtmlHalf = Uint8List(32);
    for (var i = 0; i < 32; i += 1) {
      maskedHtmlHalf[i] = htmlHalf[i] ^ mask.bytes[i];
    }

    final html = _renderLockedHtml(
      licenseId: licenseId,
      filename: filename,
      apiBase: license.apiBase,
      encryptedB64: _base64UrlNoPad(ciphertextWithTag),
      ivB64: _base64UrlNoPad(iv),
      keyHtmlHalfB64: _base64UrlNoPad(maskedHtmlHalf),
      createdAt: license.createdAt,
      drmVersion: license.drmVersion,
    );

    final stem = _safeStem(filename);
    final htmlFilename = 'MaiPDF-AppSecureShare-$stem-locked.html';
    final zipBytes = buildSingleFileZip(htmlFilename, utf8.encode(html));

    return LocalDrmPackageResult(
      packageBytes: zipBytes,
      packageFilename: 'MaiPDF-AppSecureShare-$stem-locked.maipdf',
      licenseId: licenseId,
      modCode: license.modCode,
    );
  }

  Future<_CreatedLicense> _createLicense({
    required String filename,
    required int fileSizeBytes,
    required String contentSha256,
    required String keyshareServer,
    required int maxOpens,
    required int expiresInSeconds,
  }) async {
    final uri = Uri.parse('$apiBase/api/app/licenses/create');
    final response = await http
        .post(
          uri,
          headers: const {'Content-Type': 'application/json'},
          body: jsonEncode({
            'filename': filename,
            'file_size_bytes': fileSizeBytes,
            'content_sha256': contentSha256,
            'keyshare_server': keyshareServer,
            'max_opens': maxOpens,
            'expires_in_seconds': expiresInSeconds,
            'chunk_count': 1,
          }),
        )
        .timeout(const Duration(seconds: 60));

    final body = jsonDecode(response.body) as Map<String, dynamic>;
    if (response.statusCode != 200 || body['ok'] != true) {
      throw StateError(body['message']?.toString() ??
          'License create failed: HTTP ${response.statusCode}');
    }

    return _CreatedLicense(
      licenseId: body['license_id'].toString(),
      modCode: body['mod_code'].toString(),
      apiBase: body['api_base']?.toString() ?? apiBase,
      createdAt: (body['created_at'] as num?)?.round() ??
          DateTime.now().millisecondsSinceEpoch ~/ 1000,
      drmVersion: body['drm_version']?.toString() ?? drmVersion,
    );
  }
}

class _CreatedLicense {
  const _CreatedLicense({
    required this.licenseId,
    required this.modCode,
    required this.apiBase,
    required this.createdAt,
    required this.drmVersion,
  });

  final String licenseId;
  final String modCode;
  final String apiBase;
  final int createdAt;
  final String drmVersion;
}

bool _looksLikePdf(Uint8List bytes) {
  return bytes.length >= 5 &&
      bytes[0] == 0x25 &&
      bytes[1] == 0x50 &&
      bytes[2] == 0x44 &&
      bytes[3] == 0x46 &&
      bytes[4] == 0x2D;
}

Uint8List _randomBytes(int length) {
  final random = Random.secure();
  return Uint8List.fromList(
    List<int>.generate(length, (_) => random.nextInt(256)),
  );
}

String _base64UrlNoPad(List<int> bytes) {
  return base64UrlEncode(bytes).replaceAll('=', '');
}

String _safeStem(String filename) {
  final withoutPdf = filename.replaceFirst(
    RegExp(r'\.pdf$', caseSensitive: false),
    '',
  );
  final cleaned = withoutPdf.replaceAll(RegExp(r'[^A-Za-z0-9._-]'), '_');
  return cleaned.isEmpty ? 'document' : cleaned;
}

String _escapeHtml(String value) {
  return value
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#39;');
}

String _renderLockedHtml({
  required String licenseId,
  required String filename,
  required String apiBase,
  required String encryptedB64,
  required String ivB64,
  required String keyHtmlHalfB64,
  required int createdAt,
  required String drmVersion,
}) {
  final embedJson = jsonEncode({
    'v': drmVersion,
    'lid': licenseId,
    'fn': filename,
    'api': apiBase,
    'ct': encryptedB64,
    'iv': ivB64,
    'kh': keyHtmlHalfB64,
    'cn': 1,
    'ts': createdAt,
  }).replaceAll('<', r'\u003c');
  final safeTitle = _escapeHtml(filename);
  final safeLid = _escapeHtml(licenseId);

  return '''<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex,nofollow,noarchive">
<title>MaiPDF Protected - $safeTitle</title>
<style>
  @media print { html, body { display:none !important; } }
  * { box-sizing: border-box; -webkit-user-select: none; user-select: none; }
  html, body { margin:0; min-height:100%; background:#111827; color:#e5e7eb; font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif; }
  body { overflow:hidden; }
  #gate { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:22px; }
  .card { width:min(430px,100%); background:#1f2937; border:1px solid #374151; border-radius:10px; padding:24px; text-align:center; box-shadow:0 18px 45px rgba(0,0,0,.38); }
  h1 { margin:0 0 12px; font-size:24px; }
  .sub { color:#cbd5e1; line-height:1.55; font-size:14px; word-break:break-word; }
  .lid { margin:16px 0; padding:10px; background:#0f172a; border-radius:7px; font:12px ui-monospace,Consolas,monospace; color:#dbeafe; word-break:break-all; }
  button { border:0; border-radius:8px; background:#2563eb; color:white; padding:13px 26px; font-weight:700; font-size:15px; }
  #status { margin-top:16px; color:#93c5fd; font-size:13px; line-height:1.45; min-height:20px; }
  #app { display:none; height:100vh; flex-direction:column; }
  #toolbar { display:flex; align-items:center; gap:8px; padding:8px 10px; background:#1f2937; border-bottom:1px solid #374151; }
  #title { flex:1; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:#cbd5e1; font-size:13px; }
  #toolbar button { width:36px; height:34px; padding:0; border:1px solid #4b5563; background:#111827; }
  #viewerWrap { flex:1; overflow:auto; background:#374151; padding:14px 0 40px; }
  #viewer { display:flex; flex-direction:column; align-items:center; gap:14px; }
  .page { position:relative; background:white; box-shadow:0 8px 24px rgba(0,0,0,.35); max-width:100%; }
  canvas { display:block; max-width:100%; height:auto; pointer-events:none; }
  .wm { position:absolute; inset:0; pointer-events:none; opacity:.16; background-repeat:repeat; background-size:260px 160px; mix-blend-mode:multiply; }
  #shield { display:none; position:fixed; inset:0; z-index:30; align-items:center; justify-content:center; background:rgba(15,23,42,.88); backdrop-filter:blur(18px); color:#cbd5e1; }
  #shield.on { display:flex; }
</style>
</head>
<body>
<div id="gate">
  <div class="card">
    <h1>Protected Viewer</h1>
    <div class="sub">File: <strong>$safeTitle</strong><br>Opening this file will consume one authorization and requires an online check.</div>
    <div class="lid">License: $safeLid</div>
    <button id="openBtn">Open / Unlock</button>
    <div id="status"></div>
  </div>
</div>
<div id="app">
  <div id="toolbar">
    <button id="zoomOut">-</button>
    <button id="zoomIn">+</button>
    <button id="fitWidth">Fit</button>
    <div id="title">$safeTitle</div>
  </div>
  <div id="viewerWrap"><div id="viewer"></div></div>
</div>
<div id="shield">Protected content paused</div>
<script id="drm-embed" type="application/json">$embedJson</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
(function(){
  "use strict";
  var EMBED = JSON.parse(document.getElementById("drm-embed").textContent);
  var statusEl = document.getElementById("status");
  var viewer = document.getElementById("viewer");
  var viewerWrap = document.getElementById("viewerWrap");
  var scale = 1.1;
  var pdfDoc = null;

  function setStatus(s, isErr){ statusEl.textContent = s; statusEl.style.color = isErr ? "#fca5a5" : "#93c5fd"; }
  function b64uToBytes(s){ var pad=s.length%4===0?"":"=".repeat(4-s.length%4); var bin=atob(s.replace(/-/g,"+").replace(/_/g,"/")+pad); var out=new Uint8Array(bin.length); for(var i=0;i<bin.length;i++) out[i]=bin.charCodeAt(i); return out; }
  async function sha256(bytes){ return new Uint8Array(await crypto.subtle.digest("SHA-256", bytes)); }
  async function htmlHalf(){ var masked=b64uToBytes(EMBED.kh); var mask=await sha256(new TextEncoder().encode(EMBED.lid)); var out=new Uint8Array(32); for(var i=0;i<32;i++) out[i]=masked[i]^mask[i]; return out; }
  async function unlock(){ var res=await fetch(EMBED.api + "/api/unlock", { method:"POST", headers:{"Content-Type":"application/json"}, body:JSON.stringify({license_id:EMBED.lid, client_ts:Math.floor(Date.now()/1000)}) }); var data=await res.json().catch(function(){return null}); if(!res.ok || !data || !data.ok) throw new Error((data&&data.message)||("unlock failed "+res.status)); return data.parts || []; }
  async function decryptWith(fullKey){ var key=await crypto.subtle.importKey("raw", fullKey, {name:"AES-GCM"}, false, ["decrypt"]); var pt=await crypto.subtle.decrypt({name:"AES-GCM", iv:b64uToBytes(EMBED.iv), additionalData:new TextEncoder().encode(EMBED.lid)}, key, b64uToBytes(EMBED.ct)); return new Uint8Array(pt); }
  async function decryptPdf(parts, half){ for(var n=0;n<parts.length;n++){ var share; try{ share=b64uToBytes(parts[n].k); }catch(e){ continue; } if(share.length!==32) continue; var k=new Uint8Array(32); for(var i=0;i<32;i++) k[i]=half[i]^share[i]; try{ return await decryptWith(k); }catch(e){} } throw new Error("No valid server key part."); }
  function wmDataUrl(){ var svg='<svg xmlns="http://www.w3.org/2000/svg" width="260" height="160"><text x="18" y="80" transform="rotate(-25 18 80)" font-size="16" fill="black">MaiPDF Protected '+EMBED.lid+'</text></svg>'; return "url(data:image/svg+xml;base64,"+btoa(unescape(encodeURIComponent(svg)))+")"; }
  async function render(){ viewer.innerHTML=""; for(var p=1;p<=pdfDoc.numPages;p++){ var page=await pdfDoc.getPage(p); var viewport=page.getViewport({scale:scale}); var shell=document.createElement("div"); shell.className="page"; var canvas=document.createElement("canvas"); var dpr=window.devicePixelRatio||1; canvas.width=Math.floor(viewport.width*dpr); canvas.height=Math.floor(viewport.height*dpr); canvas.style.width=viewport.width+"px"; canvas.style.height=viewport.height+"px"; shell.appendChild(canvas); var wm=document.createElement("div"); wm.className="wm"; wm.style.backgroundImage=wmDataUrl(); shell.appendChild(wm); viewer.appendChild(shell); var ctx=canvas.getContext("2d"); ctx.setTransform(dpr,0,0,dpr,0,0); await page.render({canvasContext:ctx, viewport:viewport}).promise; } }
  async function openFile(){ try{ setStatus("Verifying authorization..."); var parts=await unlock(); setStatus("Decrypting locally..."); var half=await htmlHalf(); var pdfBytes=await decryptPdf(parts, half); setStatus("Rendering PDF..."); if(!window.pdfjsLib) throw new Error("PDF.js failed to load"); pdfjsLib.GlobalWorkerOptions.workerSrc="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js"; pdfDoc=await pdfjsLib.getDocument({data:pdfBytes}).promise; document.getElementById("gate").style.display="none"; document.getElementById("app").style.display="flex"; await render(); }catch(e){ setStatus(String(e.message||e), true); } }
  document.getElementById("openBtn").onclick=openFile;
  document.getElementById("zoomIn").onclick=function(){ scale*=1.2; render(); };
  document.getElementById("zoomOut").onclick=function(){ scale/=1.2; render(); };
  document.getElementById("fitWidth").onclick=function(){ var first=viewer.querySelector("canvas"); if(first){ scale*=Math.max(.2,(viewerWrap.clientWidth-24)/first.clientWidth); render(); } };
  document.addEventListener("contextmenu", function(e){ e.preventDefault(); }, true);
  document.addEventListener("copy", function(e){ e.preventDefault(); }, true);
  document.addEventListener("keydown", function(e){ var k=e.key; if(k==="PrintScreen" || ((e.ctrlKey||e.metaKey) && (k==="s"||k==="p"||k==="u"))){ e.preventDefault(); } }, true);
  document.addEventListener("visibilitychange", function(){ document.getElementById("shield").classList.toggle("on", document.hidden); });
})();
</script>
</body>
</html>''';
}

Uint8List buildSingleFileZip(String filename, List<int> fileData) {
  final nameBytes = utf8.encode(filename);
  final data = Uint8List.fromList(fileData);
  final crc = _crc32(data);
  final now = DateTime.now();
  final dosTime =
      ((now.hour << 11) | (now.minute << 5) | (now.second >> 1)) & 0xffff;
  final dosDate =
      (((now.year - 1980) << 9) | (now.month << 5) | now.day) & 0xffff;
  final localHeaderLen = 30 + nameBytes.length;
  final centralDirLen = 46 + nameBytes.length;
  final totalLen = localHeaderLen + data.length + centralDirLen + 22;
  final out = Uint8List(totalLen);
  final view = ByteData.sublistView(out);
  var p = 0;

  void u16(int v) {
    view.setUint16(p, v, Endian.little);
    p += 2;
  }

  void u32(int v) {
    view.setUint32(p, v, Endian.little);
    p += 4;
  }

  u32(0x04034b50);
  u16(20);
  u16(0);
  u16(0);
  u16(dosTime);
  u16(dosDate);
  u32(crc);
  u32(data.length);
  u32(data.length);
  u16(nameBytes.length);
  u16(0);
  out.setAll(p, nameBytes);
  p += nameBytes.length;
  out.setAll(p, data);
  p += data.length;

  final centralDirStart = p;
  u32(0x02014b50);
  u16(20);
  u16(20);
  u16(0);
  u16(0);
  u16(dosTime);
  u16(dosDate);
  u32(crc);
  u32(data.length);
  u32(data.length);
  u16(nameBytes.length);
  u16(0);
  u16(0);
  u16(0);
  u16(0);
  u32(0);
  u32(0);
  out.setAll(p, nameBytes);
  p += nameBytes.length;

  u32(0x06054b50);
  u16(0);
  u16(0);
  u16(1);
  u16(1);
  u32(centralDirLen);
  u32(centralDirStart);
  u16(0);

  return out;
}

int _crc32(Uint8List data) {
  var crc = 0xffffffff;
  for (final byte in data) {
    crc = (crc >>> 8) ^ _crc32Table[(crc ^ byte) & 0xff];
  }
  return (crc ^ 0xffffffff) >>> 0;
}

final List<int> _crc32Table = List<int>.generate(256, (n) {
  var c = n;
  for (var k = 0; k < 8; k += 1) {
    c = (c & 1) != 0 ? 0xedb88320 ^ (c >>> 1) : c >>> 1;
  }
  return c >>> 0;
});
