<?php
ini_set("display_errors", true);
ini_set("html_errors", false);

header("Content-Type: application/json; charset=utf-8");

// CORS：预检 OPTIONS 必须带这些头，浏览器才会放行随后的 POST；POST 时再根据是否 App 决定
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, X-Requested-With, Cache-Control");
    http_response_code(200);
    exit;
}
$isApp = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (stripos((string)$_SERVER['HTTP_X_REQUESTED_WITH'], 'flutter') !== false);
if ($isApp) {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, X-Requested-With, Cache-Control");
}

// Use same timezone as config (bootstrap) so date-based path matches where file is saved
date_default_timezone_set('UTC');

const MAX_FILE_BYTES = 118097152; // 112MB

$year = date("Y");
$month = date("m");
$day = date("d");

$fileplaceShow = "/" . $year . "/" . $month . "/" . $day . "/";
$fileplaceRel = "yes/" . $year . "/" . $month . "/" . $day . "/";
$picplaceRel = "yes/" . $year . "/" . $month . "/" . $day . "/preview/";

$baseDir = __DIR__ . DIRECTORY_SEPARATOR;
$fileplace = $baseDir . str_replace("/", DIRECTORY_SEPARATOR, $fileplaceRel);
$picplace = $baseDir . str_replace("/", DIRECTORY_SEPARATOR, $picplaceRel);
$tmpBase = $baseDir . "yes" . DIRECTORY_SEPARATOR . "upload_tmp" . DIRECTORY_SEPARATOR;

function jsonResponse(array $data, int $status = 200): void {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function ensureDir(string $dir): void {
    if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
        jsonResponse(["status" => "error", "message" => "Failed to create folders"], 500);
    }
}

function rrmdir(string $dir): void {
    if (!is_dir($dir)) {
        return;
    }
    $items = scandir($dir);
    if (!is_array($items)) {
        return;
    }
    foreach ($items as $item) {
        if ($item === "." || $item === "..") {
            continue;
        }
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            rrmdir($path);
        } else {
            @unlink($path);
        }
    }
    @rmdir($dir);
}

function cleanOldUploadTmp(string $tmpBasePath): void {
    if (!is_dir($tmpBasePath)) {
        return;
    }
    $todayStart = strtotime(date("Y-m-d") . " 00:00:00");
    $list = @scandir($tmpBasePath);
    if (!is_array($list)) {
        return;
    }
    foreach ($list as $name) {
        if ($name === "." || $name === "..") {
            continue;
        }
        $path = $tmpBasePath . $name;
        if (!is_dir($path)) {
            continue;
        }
        $created = null;
        $metaFile = $path . DIRECTORY_SEPARATOR . "meta.json";
        if (is_file($metaFile)) {
            $raw = @file_get_contents($metaFile);
            if ($raw !== false) {
                $meta = json_decode($raw, true);
                if (is_array($meta) && isset($meta["createdAt"])) {
                    $created = (int)$meta["createdAt"];
                }
            }
        }
        if ($created === null) {
            $created = @filemtime($path);
        }
        if ($created !== false && $created < $todayStart) {
            rrmdir($path);
        }
    }
}

function maybeCleanupOldTmp(string $tmpBasePath): void {
    ensureDir($tmpBasePath);
    $markerFile = $tmpBasePath . ".last_clean";
    $now = time();
    $last = is_file($markerFile) ? (int)@file_get_contents($markerFile) : 0;
    if (($now - $last) < 3600) {
        return;
    }
    cleanOldUploadTmp($tmpBasePath);
    @file_put_contents($markerFile, (string)$now);
}

function generatePreview(string $pdfPath, string $previewPath): void {
    if (!class_exists("Imagick")) {
        return;
    }
    try {
        $im = new Imagick();
        $im->setResolution(75, 75);
        $im->readimage($pdfPath . "[0]");
        $im->setImageFormat("jpeg");
        $im->setImageCompression(Imagick::COMPRESSION_JPEG);
        $im->setImageCompressionQuality(50);
        $im->writeImage($previewPath);
        $im->clear();
        $im->destroy();
    } catch (Throwable $e) {
        // Preview generation should not block upload flow.
    }
}

function sanitizeFileName(string $name): string {
    $name = basename($name);
    $name = preg_replace('/[\\\\\\/:"*?<>|]+/', "_", $name);
    return $name ?: ("upload_" . time() . ".pdf");
}

maybeCleanupOldTmp($tmpBase);

$allowedExts = ["pdf", "htm"];
$action = isset($_REQUEST["action"]) ? (string)$_REQUEST["action"] : "";
if ($action === "check_name") {
    $checkNameRaw = isset($_REQUEST["filename"]) ? (string)$_REQUEST["filename"] : "";
    $checkName = sanitizeFileName($checkNameRaw);
    $checkExt = strtolower(pathinfo($checkName, PATHINFO_EXTENSION));
    if ($checkName === "" || !in_array($checkExt, $allowedExts, true)) {
        jsonResponse(["status" => "error", "message" => "Invalid filename"], 400);
    }
    ensureDir($fileplace);
    $checkPath = $fileplace . $checkName;
    jsonResponse([
        "status" => "ok",
        "exists" => file_exists($checkPath),
        "file" => $checkName,
        "path" => $fileplaceShow . $checkName
    ]);
}

if (empty($_FILES) || !isset($_FILES["file"])) {
    jsonResponse(["status" => "error", "message" => "nofile"], 400);
}

$filename = sanitizeFileName((string)$_FILES["file"]["name"]);
$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

if (!in_array($extension, $allowedExts, true)) {
    jsonResponse(["status" => "error", "message" => "Invalid file type"], 400);
}

if (isset($_POST["dzuuid"], $_POST["dzchunkindex"], $_POST["dztotalchunkcount"])) {
    $totalSize = isset($_POST["dztotalfilesize"]) ? (int)$_POST["dztotalfilesize"] : 0;
    if ($totalSize > MAX_FILE_BYTES) {
        jsonResponse(["status" => "error", "message" => "File too large"], 400);
    }

    if ((int)$_FILES["file"]["error"] > 0) {
        jsonResponse(["status" => "error", "message" => "Chunk upload error"], 400);
    }

    $uploadId = preg_replace('/[^a-zA-Z0-9_-]/', "", (string)$_POST["dzuuid"]);
    $chunkIndex = (int)$_POST["dzchunkindex"];
    $totalChunks = (int)$_POST["dztotalchunkcount"];

    if ($uploadId === "" || $chunkIndex < 0 || $totalChunks < 1) {
        jsonResponse(["status" => "error", "message" => "Invalid chunk metadata"], 400);
    }

    ensureDir($fileplace);
    ensureDir($picplace);
    $targetPdf = $fileplace . $filename;
    if (file_exists($targetPdf)) {
        jsonResponse([
            "status" => "success",
            "mode" => "chunk",
            "file" => $filename,
            "path" => $fileplaceShow . $filename,
            "reused_existing" => true,
            "message" => "Same filename already exists. Existing file reused."
        ]);
    }

    $sessionDir = $tmpBase . $uploadId . DIRECTORY_SEPARATOR;
    ensureDir($sessionDir);

    $metaFile = $sessionDir . "meta.json";
    if (!is_file($metaFile)) {
        @file_put_contents($metaFile, json_encode([
            "filename" => $filename,
            "totalChunks" => $totalChunks,
            "totalSize" => $totalSize,
            "createdAt" => time()
        ], JSON_UNESCAPED_UNICODE));
    }

    $chunkPath = $sessionDir . sprintf("chunk_%06d.part", $chunkIndex);
    if (!move_uploaded_file($_FILES["file"]["tmp_name"], $chunkPath)) {
        jsonResponse(["status" => "error", "message" => "Failed to save chunk"], 500);
    }

    $chunks = glob($sessionDir . "chunk_*.part");
    $received = is_array($chunks) ? count($chunks) : 0;

    if ($received < $totalChunks) {
        jsonResponse([
            "status" => "chunk_received",
            "chunkIndex" => $chunkIndex,
            "received" => $received,
            "totalChunks" => $totalChunks
        ]);
    }

    $lockFile = $sessionDir . "merge.lock";
    $lock = fopen($lockFile, "c");
    if ($lock === false || !flock($lock, LOCK_EX)) {
        jsonResponse(["status" => "error", "message" => "Failed to lock merge"], 500);
    }

    if (!file_exists($targetPdf)) {
        $out = fopen($targetPdf, "wb");
        if ($out === false) {
            flock($lock, LOCK_UN);
            fclose($lock);
            jsonResponse(["status" => "error", "message" => "Failed to create target file"], 500);
        }

        for ($i = 0; $i < $totalChunks; $i++) {
            $part = $sessionDir . sprintf("chunk_%06d.part", $i);
            if (!is_file($part)) {
                fclose($out);
                @unlink($targetPdf);
                flock($lock, LOCK_UN);
                fclose($lock);
                jsonResponse(["status" => "error", "message" => "Missing chunk: " . $i], 409);
            }
            $in = fopen($part, "rb");
            if ($in === false) {
                fclose($out);
                @unlink($targetPdf);
                flock($lock, LOCK_UN);
                fclose($lock);
                jsonResponse(["status" => "error", "message" => "Failed to read chunk: " . $i], 500);
            }
            stream_copy_to_stream($in, $out);
            fclose($in);
        }
        fclose($out);
    }

    flock($lock, LOCK_UN);
    fclose($lock);

    generatePreview($targetPdf, $picplace . $filename . ".jpg");
    rrmdir($sessionDir);

    jsonResponse([
        "status" => "success",
        "mode" => "chunk",
        "file" => $filename,
        "path" => $fileplaceShow . $filename,
        "reused_existing" => false
    ]);
}

if ((int)$_FILES["file"]["size"] >= MAX_FILE_BYTES) {
    jsonResponse(["status" => "error", "message" => "File too large"], 400);
}

if ((int)$_FILES["file"]["error"] > 0) {
    jsonResponse(["status" => "error", "message" => "Upload error"], 400);
}

ensureDir($fileplace);
ensureDir($picplace);

$targetPdf = $fileplace . $filename;
if (file_exists($targetPdf)) {
    jsonResponse([
        "status" => "success",
        "mode" => "standard",
        "file" => $filename,
        "path" => $fileplaceShow . $filename,
        "reused_existing" => true,
        "message" => "Same filename already exists. Existing file reused."
    ]);
}

if (!move_uploaded_file($_FILES["file"]["tmp_name"], $targetPdf)) {
    jsonResponse(["status" => "error", "message" => "Failed to move uploaded file"], 500);
}
generatePreview($targetPdf, $picplace . $filename . ".jpg");

jsonResponse([
    "status" => "success",
    "mode" => "standard",
    "file" => $filename,
    "path" => $fileplaceShow . $filename,
    "reused_existing" => false
]);
