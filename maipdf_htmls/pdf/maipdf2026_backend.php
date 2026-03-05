<?php 
// Preserve all PHP functionality at the top
ini_set("display_errors", true);
ini_set("html_errors", false); 

// Set PHP charset and encoding
ini_set('default_charset', 'utf-8');
mb_internal_encoding('UTF-8');

// CORS：预检 OPTIONS 必须带头；来自 App 的请求加 Allow-Origin 以便 localhost/Web 可调
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, X-Requested-With, Cache-Control");
    http_response_code(200);
    exit;
}
$isAppRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (stripos((string)$_SERVER['HTTP_X_REQUESTED_WITH'], 'flutter') !== false);

// Set HTTP response header encoding
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
if ($isAppRequest) {
    header("Access-Control-Allow-Origin: *");
}

// Initialize variables to prevent undefined variable warnings
$identifier = '';
$messagebox = "Don't forget <span style='color:green';> Create</span> in Step2";
$pdflinkshort = "";
$pdflinkfull = "";
$formSubmitted = false; // Flag to track if form has been submitted
$r2UploadStatus = 'idle';
$r2UploadMessage = 'R2 upload status: waiting for Generate Link.';
$r2HttpStatus = '';
$r2ResponseSnippet = '';

$year = date("Y");
$month = date("m");
$week = date("d");

// Daily storage paths
$fileplaceSHOW = "/".$year."/".$month."/".$week."/";
$fileplace = "yes/".$year."/".$month."/".$week."/";
$picplace = "yes/".$year."/".$month."/".$week."/preview/";
$encryfile = '';

// Basic blocklist check before upload
if (isset($_COOKIE["shenfen"])){
   if($_COOKIE["shenfen"] == 'badd'){exit('Not available to you=== joe@pdfhost.online');}
}

$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
if(strlen($ip) < 1) {
   $ip = $_SERVER['REMOTE_ADDR'];
}

if (isset($_COOKIE["dc"])) {
    session_start();
} else {
    //echo 'mai';
}

if (isset($_SESSION["user"])) {
    $dengru = $_SESSION["user"];
} else {
    $dengru = 'wofocibeifox';
    //session_destroy();
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

// Bootstrap API for maipdf2026.html initialization
if (isset($_GET['action']) && $_GET['action'] === 'bootstrap') {
    header('Content-Type: application/json; charset=utf-8');
    if ($isAppRequest) {
        header("Access-Control-Allow-Origin: *");
    }
    echo json_encode([
        'status' => 'ok',
        'ip' => $ip,
        'dengru' => $dengru,
        'picplace' => $picplace,
        'fileplaceSHOW' => $fileplaceSHOW
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

echo "<script>var dizhi = '$ip';var dengru = '$dengru'; var picplace  ='$picplace'; var fileplaceSHOW ='$fileplaceSHOW';</script>";

if(!isset($_SERVER['HTTPS'])) {
   $url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
   header("Location: $url");
   exit();
}
$br = $_SERVER['HTTP_USER_AGENT'];

// Process form submission
if(isset($_POST['limit'])) {
    $formSubmitted = true;
    $limit = htmlspecialchars($_POST['limit']);
    if(isset($_POST['password'])) {
        $password = htmlspecialchars($_POST['password']);
        
        if($password < 30 || $limit < 1) {
            exit("<script>
                document.getElementById(\"1step\").className=\"text-danger\";
                document.getElementById(\"1step\").innerHTML = 
                \"Reading Session too Short<br>or open limit not set<br>Please Retry.....\";
                </script>");
        }

        if ($limit > 10000 && isset($_POST['darkmode'])) {
            exit("<script>
                document.getElementById(\"2step\").className=\"text-danger\";
                document.getElementById(\"2step\").innerHTML =
                \"Access-Limit over 10k cannot use Dynamowatermark<br>Please choose normal mode\";
                </script>");
        }
        
        $url = $_POST['sender'];
        $zhangai = $_POST['zhangai'];
        $identifierProvided = false;
        // NOTE: Disabled custom identifier input because there is no form field/JS
        // posting "identifier" in this page; keeping for reference in case of
        // future external API usage.
        /*
        if (isset($_POST['identifier'])) {
            $candidate = strtolower(trim($_POST['identifier']));
            if (preg_match('/^[akd][a-f0-9]{8,32}$/', $candidate)) {
                $identifier = $candidate;
                $identifierProvided = true;
            }
        }
        */
        if (!$identifierProvided) {
            // Keep length stable (13 hex) but make the first 7 hex high-entropy
            try {
                $randomHex = substr(bin2hex(random_bytes(4)), 0, 7);
            } catch (Exception $e) {
                $randomHex = substr(uniqid(), 0, 7);
            }
            $uniqTail = substr(uniqid(), -6);
            $identifier = $randomHex . $uniqTail;
        }
        
        // 来自 Flutter App 的请求不带网页 Cookie，通过头标识放行
        $isAppRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && (strpos(strtolower((string)$_SERVER['HTTP_X_REQUESTED_WITH']), 'flutter') !== false);
        if (!$isAppRequest && isset($_COOKIE["uploadedfile"]) && $_COOKIE["uploadedfile"] == "notyet") {
            exit("<script>
                document.getElementById(\"2step\").className=\"text-danger\";
                document.getElementById(\"2step\").innerHTML = 
                \"Please do not refresh the page<br>Reopen it instead\";
                
                document.getElementById(\"2step3\").innerHTML = 
                \"Please do not refresh the page<br>Reopen it instead\";
                </script>");
        }
        
        if(substr($url, -3) != 'pdf') {
            exit();
        }
        
        if($password > 99999999) {
            $password = 99999999;
        }
        if($limit > 99999999) {
            $limit = 99999999;
        }
        
        $chat_id = '0';
        if (isset($_POST['enableTelegramAlert']) && $_POST['enableTelegramAlert'] === 'yes') {
            $raw = $_POST['mailalert'] ?? '';
            $raw = trim($raw);
            $raw = preg_replace('/\D+/', '', $raw);
            if ($raw !== '') {
                $chat_id = $raw;
            }
        }

        // Expiration handling (timestamp based)
        $expiration_ts = 0; // 0 = unlimited
        if (isset($_POST['expiration_ts']) && is_numeric($_POST['expiration_ts'])) {
            $expiration_ts = (int)$_POST['expiration_ts'];
            if ($expiration_ts < 1) {
                $expiration_ts = 0;
            }
        } elseif (isset($_POST['expiration_day']) && is_numeric($_POST['expiration_day'])) {
            $expiration_days = floatval($_POST['expiration_day']);
            if ($expiration_days > 0) {
                $expiration_ts = time() + (int)round($expiration_days * 86400);
            }
        } else {
            // default to 2099-01-01 00:00:00 UTC if nothing submitted
            $expiration_ts = 4070908800;
        }

        // Merge chat_id and expiration timestamp into mailalert (no DB schema changes)
        $mailalert = $chat_id . '|' . $expiration_ts;

        // No pending fallback: keep mailalert as provided (or 1998).
        
        // Email addresses handling
        $emailAddresses = $_POST['emailAddresses'] ?? '';
        $emailAddresses = trim($emailAddresses);
        // Normalize common separators to comma.
        $emailAddresses = str_replace(['，', ';', '；'], ',', $emailAddresses);
        
        $byteLength = strlen($emailAddresses);
        if($byteLength > 3500) {
            echo "Email list is too long. Maximum 3500 bytes.";
            $emailAddresses = null;
        } else {
            $emailArray = explode(',', $emailAddresses);
            
            $valid = true;
            if(count($emailArray) > 50) {
                $valid = false;
            } else {
                foreach($emailArray as $email) {
                    $email = trim($email);
                    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $valid = false;
                        break;
                    }
                }
            }
            
            if(!$valid) {
                $emailAddresses = null;
            }
        }
        
        if (!$identifierProvided) {
            if($zhangai == 'obstacle') {
                $identifier = 'k'.$identifier;
            } elseif($zhangai == 'topen') {
                $identifier = 'd'.$identifier;
            } else {
                $identifier = 'a'.$identifier;
            }
        }
        
        $pdflinkshort = "maipdf.com/file/".$identifier."@pdf";
        $pdflinkfull = "https://maipdf.com/file/".$identifier."@pdf";

        // ====== Telegram message fields (prepare only; send via Cloudflare Worker later) ======
        $loginStatus = ($dengru === 'wofocibeifox') ? 'Guest' : ("Logged in as: " . $dengru);
        $readLink = $pdflinkfull;
        $passwordSend = crypt('joe' . $identifier, 'su');
        $accessLimit = $limit;
        $sessionSeconds = $password;
        $protectionType = $zhangai;
        $emailVerificationEnabled = (isset($_POST['enableEmailValidation']) && $_POST['enableEmailValidation'] === 'yes');
        $emailList = $emailAddresses ?: '';
        $expirationUtc = $expiration_ts; // Unix timestamp (UTC)
        
        // Pass Telegram notification data to JavaScript (for client-side sending)
        // JavaScript will decide whether to send based on chat_id
        if ($chat_id !== '0' && $chat_id !== '') {
            $telegramNotificationData = [
                'chat_id' => $chat_id,
                'login' => $loginStatus,
                'read_link' => $readLink,
                'identifier' => $identifier,
                'password' => $passwordSend,
                'access_limit' => $accessLimit,
                'session_seconds' => $sessionSeconds,
                'protection_type' => $protectionType,
                'email_verification' => $emailVerificationEnabled ? $emailList : '',
                'expiration_utc_ts' => $expirationUtc
            ];
            
            // Output data as JavaScript variable for client-side processing
            echo "<script>";
            echo "var telegramNotificationData = " . json_encode($telegramNotificationData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ";";
            echo "</script>";
        }
        
        $previewFile = $picplace . basename($url); // Processed file in preview directory
        $originalFile = 'yes'.$url; // Original uploaded file
        
        if (file_exists($previewFile) && filesize($previewFile) > 3072) {
            $selectedFile = $previewFile;
            echo "<script>console.log('Using processed file from preview directory: " . addslashes($selectedFile) . " (" . number_format(filesize($previewFile)) . " bytes)');</script>";
        } else {
            $selectedFile = $originalFile;
            if (file_exists($previewFile) && filesize($previewFile) <= 3072) {
                echo "<script>console.log('Preview file too small (" . filesize($previewFile) . " bytes), using original file instead');</script>";
            }
            echo "<script>console.log('Using original file: " . addslashes($selectedFile) . "');</script>";
        }
        
        if(!file_exists($selectedFile)) {
            echo "<script>console.error('Source file not found: " . addslashes($selectedFile) . "');</script>";
        } else {
            echo "<script>console.log('Found source file: " . addslashes($selectedFile) . " (" . number_format(filesize($selectedFile)) . " bytes)');</script>";
        }
        
        if($zhangai != 'topen') {
            $fileToEncrypt = $selectedFile;  // Keep source path for encryption fallback
            
            if(filesize($fileToEncrypt) < 3797152) {
                try {
                    $encryptedTempFile = $fileToEncrypt . '.encrypted';
                    
                    $qpdfEncryptCommand = "qpdf --encrypt " . 
                                         escapeshellarg('guaguashimaimai') . " " . 
                                         escapeshellarg('qweewqer') . " " . 
                                         "256 --print=none --modify=none --extract=n -- " . 
                                         escapeshellarg($fileToEncrypt) . " " . 
                                         escapeshellarg($encryptedTempFile) . " 2>&1";
                    
                    exec($qpdfEncryptCommand, $qpdfOutput, $qpdfReturnCode);
                    
                    if ($qpdfReturnCode === 0 && file_exists($encryptedTempFile)) {
                        if (rename($encryptedTempFile, $fileToEncrypt)) {
                            echo "<script>console.log('PDF encryption successful using qpdf');</script>";
                            $selectedFile = $fileToEncrypt;
                        } else {
                            throw new Exception("Failed to replace file with encrypted version");
                        }
                    } else {
                        throw new Exception("qpdf encryption failed: " . implode(' ', $qpdfOutput));
                    }
                    
                } catch(Exception $e) {
                    echo "<script>console.error('PDF encryption failed: " . addslashes($e->getMessage()) . "');</script>";
                    echo "<script>console.warn('Continuing with unencrypted PDF');</script>";
                }
            } else {
                echo "<script>console.warn('File too large for encryption, keep unencrypted file');</script>";
            }
        } else {
            echo "<script>console.log('File set to unrestricted mode, using original file');</script>";
        }

        // ========== Unified R2 upload logic (aligned with maipdf.php) ==========
        try {
            echo "<script>console.log('Starting R2 upload process...');</script>";
            $r2UploadStatus = 'started';
            $r2UploadMessage = 'R2 upload started.';
            
            $fileContentForR2 = file_get_contents($selectedFile);
            if ($fileContentForR2 !== false) {
                
                if (strpos($selectedFile, 'preview/') !== false) {
                    $uploadFileName = basename($selectedFile);
                    $serverPathForR2 = $fileplace; // Path without preview folder
                    echo "<script>console.log('Using preview file, server path: " . addslashes($serverPathForR2) . "');</script>";
                } else {
                    $uploadFileName = basename($selectedFile);
                    $serverPathForR2 = $fileplace;
                    echo "<script>console.log('Using original file, server path: " . addslashes($serverPathForR2) . "');</script>";
                }
                
                $workerUrl = 'https://fetch.maipdf.com/upload';
                
                $currentDomain = $_SERVER['HTTP_HOST'] ?? 'maipdf.com';
                $originHeader = "https://" . $currentDomain;
                
                $allowedDomains = ['maipdf.com', 'www.maipdf.com', 'privnote.maipdf.com'];
                if (!in_array($currentDomain, $allowedDomains)) {
                    $originHeader = "https://maipdf.com"; // Use main domain as default
                }
                
                $boundary = uniqid();
                $postData = '';
                
                $postData .= "--$boundary\r\n";
                $postData .= "Content-Disposition: form-data; name=\"file\"; filename=\"$uploadFileName\"\r\n";
                $postData .= "Content-Type: application/pdf\r\n\r\n";
                $postData .= $fileContentForR2 . "\r\n";
                
                $postData .= "--$boundary\r\n";
                $postData .= "Content-Disposition: form-data; name=\"server_path\"\r\n\r\n";
                $postData .= $serverPathForR2 . "\r\n";
                
                $postData .= "--$boundary\r\n";
                $postData .= "Content-Disposition: form-data; name=\"server_timestamp\"\r\n\r\n";
                $postData .= time() . "\r\n";
                
                $postData .= "--$boundary\r\n";
                $postData .= "Content-Disposition: form-data; name=\"file_type\"\r\n\r\n";
                $fileType = ($zhangai != 'topen') ? 'encrypted_pdf' : 'standard_pdf';
                $postData .= $fileType . "\r\n";
                
                $postData .= "--$boundary\r\n";
                $postData .= "Content-Disposition: form-data; name=\"processed_by\"\r\n\r\n";
                $postData .= "maipdf_unified\r\n";
                
                $postData .= "--$boundary--\r\n";
                
                $context = stream_context_create([
                    'http' => [
                        'method' => 'POST',
                        'header' => "Content-Type: multipart/form-data; boundary=$boundary\r\n" .
                                  "Origin: $originHeader\r\n" .
                                  "User-Agent: MaiPDF-Unified-Server/1.0\r\n" .
                                  "X-Forwarded-For: " . ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1') . "\r\n",
                        'content' => $postData,
                        'timeout' => 120,
                        'ignore_errors' => true
                    ]
                ]);
                
                echo "<script>console.log('Sending file to R2: " . addslashes($uploadFileName) . " (" . strlen($fileContentForR2) . " bytes)');</script>";
                
                $response = file_get_contents($workerUrl, false, $context);
                
                if ($response !== false) {
                    $workerResult = json_decode($response, true);
                    if ($workerResult && $workerResult['status'] === 'upload_success') {
                        $r2UploadStatus = 'upload_success';
                        $r2UploadMessage = 'R2 upload succeeded.';
                        echo "<script>console.log('File successfully uploaded to R2: " . addslashes($uploadFileName) . "');</script>";
                        echo "<script>console.log('File now available on both local server and Cloudflare R2');</script>";
                    } else {
                        $errorMsg = $workerResult['message'] ?? 'Unknown error';
                        $statusLine = isset($http_response_header[0]) ? $http_response_header[0] : 'no_status_line';
                        $responseSnippet = substr(preg_replace('/\s+/', ' ', (string)$response), 0, 200);
                        $r2UploadStatus = 'upload_failed';
                        $r2UploadMessage = 'R2 upload failed: ' . $errorMsg;
                        $r2HttpStatus = $statusLine;
                        $r2ResponseSnippet = $responseSnippet;
                        echo "<script>console.warn('Failed to upload file to R2: " . addslashes($errorMsg) . "');</script>";
                        echo "<script>console.warn('R2 HTTP status: " . addslashes($statusLine) . "');</script>";
                        echo "<script>console.warn('R2 raw response (first 200 chars): " . addslashes($responseSnippet) . "');</script>";
                        echo "<script>console.log('File remains available on local server');</script>";
                    }
                } else {
                    $lastErr = error_get_last();
                    $lastErrMsg = isset($lastErr['message']) ? $lastErr['message'] : 'unknown_error';
                    $r2UploadStatus = 'request_error';
                    $r2UploadMessage = 'R2 request failed: ' . $lastErrMsg;
                    echo "<script>console.error('Failed to connect to R2 Worker');</script>";
                    echo "<script>console.error('R2 request error: " . addslashes($lastErrMsg) . "');</script>";
                    echo "<script>console.log('File remains available on local server');</script>";
                }
            } else {
                $r2UploadStatus = 'read_error';
                $r2UploadMessage = 'R2 upload skipped: unable to read source file.';
                echo "<script>console.error('Failed to read file content for R2 upload');</script>";
            }
        } catch (Exception $r2Exception) {
            $r2UploadStatus = 'exception';
            $r2UploadMessage = 'R2 upload exception: ' . $r2Exception->getMessage();
            echo "<script>console.error('R2 upload exception: " . addslashes($r2Exception->getMessage()) . "');</script>";
            echo "<script>console.log('File processing completed, available on local server');</script>";
        }
        
        $messagebox = 'Your Reading link is Created';
        if(substr($url, -3) == 'pdf') {
            include_once('../password.php');
            $conn = new mysqli($servernameMai, $usernameMai, $passwordMai, $dbnameMai);
            if($conn->connect_error) {
                die("CANNOT INSERT");
            }
            
            // Set database connection charset to UTF-8
            if (!$conn->set_charset("utf8")) {
                // If utf8 doesn't work, try utf8mb4
                $conn->set_charset("utf8mb4");
            }
            
            $day = date('Y-m-d');
            $url = str_replace("'", "\'", $url);
            
            if(isset($_POST['darkmode'])) {
                $allurl = 'water'.$url;  
            } else {
                $allurl = $url;
            }
            
            $sql = "INSERT INTO `pdf` VALUES('$identifier','$allurl',$password,$limit,'$day','$mailalert','$emailAddresses')";
            
            // Add debug information to console
            echo "<script>console.log('Database insert - identifier: ".$identifier."', 'URL: ".$url."', 'Link Full: ".$pdflinkfull."');</script>";
            
            if($dengru == 'wofocibeifox') {
                $sqlres = "INSERT INTO `block`(`ip`,`md5`,`attr`) VALUES('$ip','$url','pdf') ON DUPLICATE KEY UPDATE `number` = 1";
            } else {   
                $sqlres = "INSERT INTO `block`(`ip`,`md5`,`attr`) VALUES('$url','m#$identifier','$dengru') ON DUPLICATE KEY UPDATE `number` = 1";
            }
            
            // Execute the database queries
            $result = mysqli_query($conn, $sql);
            $resultres = mysqli_query($conn, $sqlres);
            $conn->close();
            
            // JavaScript to handle the UI updates after successful form submission
            echo "<script>
                // Hide the upload and configuration sections
                document.getElementById('section2').style.display='none'; 
                document.getElementById('section1').style.display='none';
                
                // Set variables for use in other scripts
                var at='".$identifier."';
                var bt='".$identifier."';
                var place='".($url ?? '')."';
                
                // Update step indicators and scroll to results
                document.addEventListener('DOMContentLoaded', function() {
                    // Update step progress indicators
                    document.getElementById('step-indicator-1').classList.add('completed');
                    document.getElementById('step-indicator-1').classList.remove('active');
                    document.getElementById('step-indicator-2').classList.add('completed');
                    document.getElementById('step-indicator-2').classList.remove('active');
                    document.getElementById('step-indicator-3').classList.add('active');
                    
                    // Auto-scroll to Step 3 (results section)
                    document.getElementById('contact').scrollIntoView({ behavior: 'smooth' });
                    
                    // Add highlight effect to guide user attention
                    setTimeout(function() {
                        document.getElementById('contact').classList.add('highlight-section');
                        setTimeout(function() {
                            document.getElementById('contact').classList.remove('highlight-section');
                            
                            // Focus attention on the access records button
                            setTimeout(function() {
                                const accessButton = document.querySelector('.btn-access-records');
                                if (accessButton) {
                                    accessButton.classList.add('emphasize-button');
                                    setTimeout(function() {
                                        accessButton.classList.remove('emphasize-button');
                                    }, 2000);
                                }
                            }, 500);
                        }, 1500);
                    }, 800);
                });
            </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta -->
    <title>MaiPDF - Online PDF Sharing & QR Code Generator</title>
    <meta name="keywords" content="online pdf share, pdf qr code, free pdf online reader, pdf view only, pdf online link, maipdf">
    <meta name="description" content="Share PDFs online instantly. Generate links and QR codes for easy viewing, with options to allow or block download and printing. 100% free and no account required.">
    <meta name="author" content="MaiPDF">

    <!-- Social Media (OG & Twitter) -->
    <meta property="og:title" content="MaiPDF - Secure & Free Online PDF Sharing">
    <meta property="og:description" content="Generate links & QR codes for PDFs. View online, no download needed. Choose to allow or block download & print. Free and simple to use.">
    <meta property="og:image" content="https://article.maipdf.com/maipdf-images/share_pdf_wordwide.png">
    <meta property="og:url" content="https://maipdf.com/pdf/maipdf.php">
    <meta name="twitter:card" content="summary_large_image">

    <!-- External Stylesheets -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    
    <!-- Bootstrap & jQuery -->
 
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.7/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.7/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Dropzone -->
    <script src="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/dropzone.js"></script>
    <script>
      if (typeof Dropzone !== "undefined") {
        Dropzone.autoDiscover = false;
      }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/dropzone.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/basic.css">
    
    <!-- QR Code -->
    <script type="text/javascript" src="qrcode.min.js"></script>
    
    <!-- Google AdSense -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9224406325142860" crossorigin="anonymous"></script>
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="maipdf2026012.css?v=<?php echo time(); ?>">
</head>


    <script>
       var d = new Date();
       document.cookie ="usertime="+d.getHours();
       document.cookie ="usertime2="+encodeURI(d);
       d=d+'maipdf';

    </script>
    
    <!-- Script to handle form submission page reload -->
    <script>
        // Run after page is fully loaded
        window.addEventListener('DOMContentLoaded', function() {            // Check if the results section is visible (form was submitted)
            if (document.getElementById('contact') && 
                document.getElementById('contact').style.display !== 'none' && 
                document.getElementById('myInput') && 
                document.getElementById('myInput').value) {
                
                console.log("Form submitted, scrolling to results section");
                
                // Update step indicators
                document.getElementById('step-indicator-1').classList.add('completed');
                document.getElementById('step-indicator-1').classList.remove('active');
                document.getElementById('step-indicator-2').classList.add('completed');
                document.getElementById('step-indicator-2').classList.remove('active');
                document.getElementById('step-indicator-3').classList.add('active');
                
                // Scroll to Step 3 section - with a slight delay to ensure everything has loaded
                setTimeout(function() {
                    // Try both IDs to ensure we find the right element
                    if (document.getElementById('3step')) {
                        document.getElementById('3step').scrollIntoView({ behavior: 'smooth' });
                    } else if (document.getElementById('contact')) {
                        document.getElementById('contact').scrollIntoView({ behavior: 'smooth' });
                    }
                    
                    // Add highlight effect
                    document.getElementById('contact').classList.add('highlight-section');
                    setTimeout(function() {
                        document.getElementById('contact').classList.remove('highlight-section');
                        
                        // Focus attention on the action buttons
                        const actionButtonsContainer = document.querySelector('.action-buttons-container');
                        if (actionButtonsContainer) {
                            actionButtonsContainer.classList.add('emphasize-button');
                            setTimeout(() => {
                                actionButtonsContainer.classList.remove('emphasize-button');
                            }, 2000);
                        }
                    }, 1500);
                }, 500);
            }
            
            // Add scroll event to handle sticky header effects
            const progressContainer = document.querySelector('.step-progress-container');
            let isSticky = false;
            
            window.addEventListener('scroll', function() {
                const scrollPosition = window.scrollY;
                if (scrollPosition > 100 && !isSticky) {
                    progressContainer.classList.add('sticky-active');
                    isSticky = true;
                } else if (scrollPosition <= 100 && isSticky) {
                    progressContainer.classList.remove('sticky-active');
                    isSticky = false;
                }
            });
        });
    </script>




<body>

<!-- Hero Section - Styled like newhome.html -->
<header class="hero-section">
    <div class="hero-background">
        <div class="hero-pattern"></div>
        <div class="animated-bg"></div>
    </div>
    <div class="container">
        <div class="logo logo-main animate__animated animate__fadeInDown">MaiPDF</div>
        <h1 class="animate__animated animate__fadeInUp">Share PDF with Expiration Time and Restrictions</h1>
        <p class="animate__animated animate__fadeInUp animate__delay-1s">Professional and reliable secure PDF sharing solution.</p>
        
        <!-- User authentication area -->
        <div class="user-auth-area animate__animated animate__fadeInUp animate__delay-1s">
            <div id="userInfo"></div>
            <div class="auth-buttons">
                <button type="button" class="btn btn-light" id="loginBtn">Login</button>
                <button class="btn btn-outline-light" id="logoutBtn" style="display:none;">Log Out</button>
                <a href='../6/list.php' class='btn btn-outline-light' id="controlpanel" style="display:none;">Control Panel</a>
            </div>
        </div>
          <!-- Hero action buttons -->
        <div class="hero-action-buttons animate__animated animate__fadeInUp animate__delay-2s">
            <a href="../getresult.html" class="btn btn-hero-primary" target="_blank">
                <i class="fas fa-chart-line me-1"></i> Access Records
            </a>
            <a href="https://maipdf.com/pdf/hahachange.php" class="btn btn-hero-secondary" target="_blank">
                <i class="fas fa-cogs me-1"></i> Edit Link
            </a>
            <a href="../watermark.html" class="btn btn-hero-tertiary" target="_blank">
                <i class="fas fa-search me-1"></i> Find by Watermark ID
            </a>
        </div>
        
        <div class="scroll-indicator animate__animated animate__fadeIn animate__delay-2s">
            <span class="arrow-down"></span>
            <span class="arrow-down"></span>
        </div>
    </div>
    <div class="floating-elements">
        <div class="float-element" style="--i:1"></div>
        <div class="float-element" style="--i:2"></div>
        <div class="float-element" style="--i:3"></div>
        <div class="float-element" style="--i:4"></div>
        <div class="float-element" style="--i:5"></div>
    </div>
</header>

<main>    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-container">
        <div class="container">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a class="link-secondary" href="https://maipdf.com/est/k6776416f71665@pdf">FenceView</a></li>
                <li class="breadcrumb-item"><a class="link-secondary" href="https://maipdf.com/est/a677641030889c@pdf">SafeLink</a></li>
                <li class="breadcrumb-item"><a class="link-secondary" href="https://maipdf.com/est/d67764148dd446@pdf">OpenLink</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Step Progress Indicator -->
    <div class="step-progress-container">
        <div class="container">
            <div class="step-progress">
                <div class="step-item active" id="step-indicator-1">
                    <div class="step-number">1</div>
                    <div class="step-text">Upload File</div>
                </div>
                <div class="step-item" id="step-indicator-2">
                    <div class="step-number">2</div>
                    <div class="step-text">Configure</div>
                </div>
                <div class="step-item" id="step-indicator-3">
                    <div class="step-number">3</div>
                    <div class="step-text">Share</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Section -->
    <section class="features-section" id="section1">
        <div class="container">
            <h2 id="1step">1: Upload File</h2>
            <div class="upload-container">
                <div class="form-group">
                    <label class="title" id="pleaseupload"></label>
                    <div id="dropz" class="dropzone"></div>
                </div>
                <input type="hidden" name="file_id" ng-model="file_id" id="file_id"/>
            </div>
        </div>
    </section>
    
    <!-- Settings Section -->    <section class="why-choose-us-section" id="section2">
        <div class="container">
            <form role="form" action="maipdf2026_backend.php" method="post">
                <h2 id="2step">2: Set Up Reading Times and Each Period of Length</h2>
                
                <input type="text" class="form-control text-center" id="name" name="sender" value="File" readonly="readonly">
                
                <div class="settings-grid" id="2step3">
                    <div class="setting-box">
                        <div class="setting-icon">
                            <i class="fas fa-folder-open fa-icon"></i>
                        </div>
                        <div class="inline-option-row access-limit-row">
                            <h3>Access Limit</h3>
                            <button type="button" class="help-toggle" data-bs-toggle="collapse" data-bs-target="#accessLimitHelp" aria-expanded="false" aria-controls="accessLimitHelp">?</button>
                        </div>
                        <div id="accessLimitHelp" class="collapse help-collapse">
                            Unlimited open will be applied if Access-Limit is over 10k, and no access record will be logged.
                        </div>
                        <input class="form-control" type="number" id="limit" name="limit" placeholder="Number of Opens">
                        
                        <div class="mt-4">
                            <i class="fas fa-user-clock fa-icon"></i>
                            <h3>Each Session</h3>
                            <input class="form-control" type="number" name="password" placeholder="in (seconds)">
                        </div>
                    </div>
                    
                    <div class="setting-box protection-box">
                        <div class="setting-icon">
                            <i class="fas fa-lock fa-icon"></i>
                        </div>
                        <h3>Protection Type</h3>
                        <div class="protection-options">
                            <div class="inline-option-row">
                                <label class="toggle-switch" for="darkmode">
                                    <input type="checkbox" name="darkmode" id="darkmode" value="yes">
                                    <span class="toggle-track"></span>
                                    <span class="toggle-text">Dynamic Watermark</span>
                                </label>
                                <button type="button" class="help-toggle" data-bs-toggle="collapse" data-bs-target="#dynamoWatermarkHelp" aria-expanded="false" aria-controls="dynamoWatermarkHelp">?</button>
                            </div>
                            <div id="darkmodeLimitNote" class="form-text" style="display:none;">Access-Limit over 10k cannot use Dynamowatermark.</div>
                            <div id="dynamoWatermarkHelp" class="collapse help-collapse">
                                Dynamic watermark shows viewer info and time on the document to discourage screenshots and sharing.
                            </div>
                            
                            <div class="view-type-row">
                                <div class="view-type-label no-text">
                                    <span class="view-type-icons" aria-hidden="true">
                                        <i class="fas fa-desktop"></i>
                                    </span>
                                </div>
                                <select class="form-select view-type-select" name="zhangai" aria-label="View Type">
                                    <option value="straight" selected>SecureView</option>
                                    <option value="obstacle">FenceView</option>
                                    <option value="topen">Unrestricted</option>
                                </select>
                                <button type="button" class="help-toggle view-type-help-toggle" data-bs-toggle="collapse" data-bs-target="#viewTypeHelp" aria-expanded="false" aria-controls="viewTypeHelp">?</button>
                            </div>
                            <div id="viewTypeHelp" class="collapse help-collapse view-type-help">
                                SecureView: default view-only mode. FenceView: extra protection against screenshots. Unrestricted: allows normal viewing without restrictions.
                            </div>
                        </div>

                        <div class="mt-3 expiration-row">
                            <h3>
                                <span class="expiration-icon" aria-hidden="true">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                                Expiration
                            </h3>
                            <div class="expiration-controls">
                                <select class="form-select" id="expiration_preset" name="expiration_preset" aria-label="Expiration preset">
                                    <option value="">Select duration</option>
                                    <option value="1h">1 hour</option>
                                    <option value="3h">3 hours</option>
                                    <option value="24h">24 hours</option>
                                    <option value="5d">5 days</option>
                                    <option value="custom">Custom days</option>
                                    <option value="unlimited">Unlimited</option>
                                </select>
                                <input class="form-control" type="number" id="expiration_custom_days" min="1" step="1" placeholder="Custom days" style="display:none;">
                                <input type="hidden" name="expiration_day" id="expiration_day" value="">
                                <input type="hidden" name="expiration_ts" id="expiration_ts" value="">
                            </div>
                            <div class="expiration-summary-row">
                                <div class="expiration-result" id="expiration_result" style="display:none;">UTC expiry time: -</div>
                            </div>
                            <div class="expiration-timezone">
                                <div class="timezone-row" id="timezone_row" style="display:none;">
                                    <select class="form-select" id="timezone_tz" aria-label="Time zone selection">
                                        <option value="">Time zone</option>
                                    </select>
                                </div>
                                <div class="expiration-local" id="expiration_local" style="display:none;">Local time: -</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notification Section -->
                    <div class="setting-box">
                        <div class="setting-icon">
                            <i class="fas fa-bell fa-icon"></i>
                        </div>
                        <h3>Read Alerts (Telegram)</h3>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="enableTelegramAlert" name="enableTelegramAlert" value="yes">
                            <label class="form-check-label">Enable read alert (optional)</label>
                        </div>

                        <div id="telegramBindPanel" style="display: none;">
                            <div class="mb-2">
                                <a class="btn btn-feature-start" href="https://t.me/maipdfbot" id="telegram-bind-link" target="_blank" rel="noopener">Add bot</a>
                                <button type="button" class="btn btn-feature-start telegram-refresh">Get chat_id</button>
                            </div>
                            <input class="form-control mb-2" type="text" name="mailalert" id="telegram-chat-id" placeholder="chat_id (auto)" readonly>
                            <input type="hidden" id="telegram-token" name="telegram_token" value="">
                            <p class="mt-2 telegram-status">Telegram: Not linked</p>
                            <div class="form-text mb-3">Add bot -> send /start -> Get chat_id (auto).</div>
                        </div>
                    </div>

                    <!-- Email Verification Section -->
                    <div class="setting-box">
                        <div class="setting-icon">
                            <i class="fas fa-shield-alt fa-icon"></i>
                        </div>
                        <h3>Email Verification</h3>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="enableEmailValidation" name="enableEmailValidation" value="yes">
                            <label class="form-check-label">Require email verification</label>
                        </div>
                        
                        <div id="emailAddressesInput" style="display: none;">
                            <textarea class="form-control" name="emailAddresses" placeholder="Enter up to 50 email addresses, separated by commas" rows="4"></textarea>
                        </div>

                        <div class="setting-cta">
                            <button type="submit" class="btn btn-feature-start cta-glow">Create Secure Link</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    
    <div class="section-divider">
        <p id="cishu">Unlimited open will be applied if 'Access-Limit' is over 10k, and no access record will be logged./p>
    </div>
    
    <!-- Results Section - Only shown after form submission -->
    <section class="cta-section" id="contact" style="<?php echo true ? '' : 'display:none;'; ?>">
        <div class="container">
            <div class="cta-box">
                <h2 id="3step">3: URL and QR Created</h2>
                  <div class="results-grid">
                    <div class="result-box">
                        <h3 id="pdf-link-display"><?php echo isset($pdflinkshort) ? $pdflinkshort : ''; ?></h3>
                        <div class="link-copy-area">
                            <input type="text" class="form-control" value="<?php echo isset($pdflinkfull) ? $pdflinkfull : ''; ?>" id="myInput">
                            <button class="btn btn-copy mt-3" onclick="myFunction()">
                                <i class="fas fa-copy me-2"></i>Copy This Link
                            </button>
                        </div>
                        <h5 id='Copied'></h5>
                        <h6 class="mt-3" id="result-message"><?php echo isset($messagebox) ? $messagebox : ''; ?></h6>
                        <h5 class="mb-3" id="result-password">Password: "<?php $identifier2 = isset($identifier) ? 'joe'.$identifier : 'joe'; echo (strlen(isset($identifier) ? $identifier : '') < 2) ? 'To Del.MOD Link' : crypt($identifier2,'su'); ?>"</h5>
                        <p class="mt-2" id="result-r2-status"><?php echo htmlspecialchars($r2UploadMessage, ENT_QUOTES, 'UTF-8'); ?></p>
                        <div id="r2-upload-meta"
                             data-status="<?php echo htmlspecialchars($r2UploadStatus, ENT_QUOTES, 'UTF-8'); ?>"
                             data-message="<?php echo htmlspecialchars($r2UploadMessage, ENT_QUOTES, 'UTF-8'); ?>"
                             data-http-status="<?php echo htmlspecialchars($r2HttpStatus, ENT_QUOTES, 'UTF-8'); ?>"
                             data-response-snippet="<?php echo htmlspecialchars($r2ResponseSnippet, ENT_QUOTES, 'UTF-8'); ?>"
                             style="display:none;"></div>
                        <div class="action-buttons-container">
                            <h5 class="action-title">Available Actions:</h5>
                            <div class="action-buttons">
                                <a class="btn btn-feature-start" href="https://www.maipdf.com/pdf/hahachange.php" target="_blank">
                                    <i class="fas fa-edit me-1"></i> Change File
                                </a>
                                <a href="../getresult.html" class="btn btn-feature-start" target="_blank">  
                                    <i class="fas fa-chart-line me-1"></i> Access Records
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="result-box text-center">
                        <div id="qrcode" class="qr-container"></div>
                        <p><small>Scan QR Code To Read</small></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Chinese Redirect Modal -->
    <div class="modal fade" id="chinaRedirectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Access Optimization</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>检测到您可能位于中国大陆，推荐访问中文站点获得更快的访问速度。</p>
                    <p>当前站点：国际站（全球加速）</p>
                    <p>推荐站点：中文站（中国大陆优化）</p>
                </div>
                <div class="modal-footer">
                    <a href="https://maipdf.cn/maifile.php" class="btn btn-danger" target="_blank" rel="noopener">
                        Visit China Site
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Stay on Global Site
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Reminder Modal -->
    <div class="modal fade" id="loginReminderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quick Tip</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    We recommend logging in before you upload. You can continue without login - your link will still work.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Stay on Global Site
                    </button>
                    <button type="button" class="btn btn-primary" id="loginRecommendBtn">Login</button>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="footer-section">
    <div class="container footer-content">
        <div class="footer-left">
            <div class="logo">MaiPDF</div>
            <p>Advanced document sharing solutions. <br> Secure, reliable, and easy to use. <br> &copy; 2026 MaiPDF. All rights reserved.</p>
            <div class="footer-social">
             
                <a href="https://www.facebook.com/MaiPDFer" class="social-icon"><i class="fab fa-facebook-f"></i></a>
         
                <a href="https://github.com/mythenigma" class="social-icon"><i class="fab fa-github"></i></a>
            </div>
        </div>
        <div class="footer-right">                
            <div class="contact-info">
                <h4>Contact Us</h4>
                <p><i class="fas fa-envelope"></i> joe@pdfhost.online</p>
                <p class="contact-note">Feel free to reach out with any questions or feedback.</p>
            </div>
        </div>
    </div>
</footer>

<div class="back-to-top" id="backToTop">
    <i class="fas fa-arrow-up"></i>
</div>

<script>
document.cookie = "uploadedfile=notyet"; 

// Toggle email input visibility
document.getElementById("enableEmailValidation").addEventListener("change", function() {
    var emailInput = document.getElementById("emailAddressesInput");
    if (this.checked) {
        emailInput.style.display = "block"; // Show email input
    } else {
        emailInput.style.display = "none"; // Hide email input
    }
});

// Toggle Telegram alert panel visibility
document.getElementById("enableTelegramAlert").addEventListener("change", function() {
    var telegramPanel = document.getElementById("telegramBindPanel");
    if (this.checked) {
        telegramPanel.style.display = "block";
    } else {
        telegramPanel.style.display = "none";
    }
});

// Initialize Chinese redirect modal
document.addEventListener('DOMContentLoaded', function() {
    // Check if user might be in China
    const isChinese = () => {
        const lang = navigator.language.toLowerCase();
        const langs = navigator.languages?.map(l => l.toLowerCase()) || [lang];
        return langs.some(l => l === 'zh-cn' || l.startsWith('zh-hans'));
    };

    const isChinaTimeZone = () => {
        try {
            const tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
            if (['Asia/Shanghai', 'Asia/Chongqing', 'Asia/Urumqi'].includes(tz)) return true;
        } catch(e) { /* Fallback */ }
        return new Date().getTimezoneOffset() === -480;
    };

    // Show modal if conditions met
    if (isChinese() && isChinaTimeZone()) {
        const modal = new bootstrap.Modal('#chinaRedirectModal', {
            backdrop: 'static',
            keyboard: false
        });
        modal.show();
    }

    const loginRecommendBtn = document.getElementById('loginRecommendBtn');
    if (loginRecommendBtn) {
        loginRecommendBtn.addEventListener('click', function() {
            const loginBtn = document.getElementById('loginBtn');
            const modalEl = document.getElementById('loginReminderModal');
            const modalInstance = modalEl ? bootstrap.Modal.getInstance(modalEl) : null;
            if (modalInstance) {
                modalInstance.hide();
            }
            if (loginBtn) {
                loginBtn.click();
            }
        });
    }

    // Show login reminder on first dropzone click (once per session, only if not logged in)
    const dropzoneEl = document.getElementById('dropz');
    if (dropzoneEl) {
        const showLoginReminder = () => {
            const lastShown = parseInt(localStorage.getItem('login_reminder_last_ts') || '0', 10);
            if (Date.now() - lastShown < 3600000) {
                return;
            }
            const loginBtn = document.getElementById('loginBtn');
            if (!loginBtn || loginBtn.style.display === 'none') {
                return;
            }
            const loginReminderModal = new bootstrap.Modal('#loginReminderModal');
            loginReminderModal.show();
            localStorage.setItem('login_reminder_last_ts', String(Date.now()));
        };

        dropzoneEl.addEventListener('click', showLoginReminder, { once: true, capture: true });
        dropzoneEl.addEventListener('pointerdown', showLoginReminder, { once: true, capture: true });

        const dropzoneInput = dropzoneEl.querySelector('input[type=\"file\"]');
        if (dropzoneInput) {
            dropzoneInput.addEventListener('click', showLoginReminder, { once: true, capture: true });
        }
    }
    
    // Back to top button functionality
    const backToTopButton = document.getElementById('backToTop');
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopButton.classList.add('show');
        } else {
            backToTopButton.classList.remove('show');
        }
    });
    
    backToTopButton.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});

// Dropzone configuration
if (typeof Dropzone !== "undefined") {
    Dropzone.autoDiscover = false;
}
const CHUNK_THRESHOLD_BYTES = 5 * 1024 * 1024; // >5MB use chunk upload
const CHUNK_SIZE_BYTES = 2 * 1024 * 1024;      // 2MB per chunk
var myDropzone = new Dropzone("#dropz", {
    url: "r2upload.php",
    method: "post",
    paramName: "file",
    maxFiles: 1,
    maxFilesize: 82,
    chunking: false,               // enabled dynamically per file size
    forceChunking: false,
    chunkSize: CHUNK_SIZE_BYTES,
    retryChunks: true,
    retryChunksLimit: 3,
    parallelChunkUploads: false,
    acceptedFiles: ".png,.jpg,.jpeg,.gif,image/*,.pdf",
    addRemoveLinks: true,
    parallelUploads: 1,
    dictDefaultMessage: 'Choose File<br><small style="font-style: italic;">Or Drop File Here</small>',
    dictMaxFilesExceeded: "One file only",
    dictResponseError: 'Failed!',
    dictInvalidFileType: "Only *.pdf, *.png, *.jpeg are allowed",
    dictFallbackMessage: "You have an Antique Browser",
    dictFileTooBig: "Reach Size Limit.",
    dictRemoveLinks: "Delete",
    dictCancelUpload: "Cancel",
    timeout: 190000,
    
    init: function() {
        var renderUploadStatus = function(text) {
            var el = document.getElementById("pleaseupload");
            if (el) {
                el.innerHTML = text;
            }
        };

        this.on("sending", function(file) {
            if (this.options.chunking) {
                var totalChunks = Math.ceil(file.size / CHUNK_SIZE_BYTES);
                renderUploadStatus("Uploading (Chunk Mode): 0% - 0/" + totalChunks + " chunks");
            } else {
                renderUploadStatus("Uploading (Standard Mode): 0%");
            }
        });

        this.on("uploadprogress", function(file, progress) {
            var safeProgress = Math.max(0, Math.min(100, Math.round(progress || 0)));
            if (this.options.chunking) {
                var totalChunks = Math.ceil(file.size / CHUNK_SIZE_BYTES);
                var doneChunks = Math.min(totalChunks, Math.floor((safeProgress / 100) * totalChunks));
                renderUploadStatus("Uploading (Chunk Mode): " + safeProgress + "% - " + doneChunks + "/" + totalChunks + " chunks");
            } else {
                renderUploadStatus("Uploading (Standard Mode): " + safeProgress + "%");
            }
        });

        this.on("addedfile", function(file) {
            // Decide upload mode per file: <=5MB full upload, >5MB chunk upload
            this.options.chunking = file.size > CHUNK_THRESHOLD_BYTES;

            var dzDefault = document.querySelector('div .dz-default');
            if (dzDefault) {
                dzDefault.style.display = 'none';
            }
            
            if(file.name.endsWith('.PDF')){
                alert('.PDF extention cannot be in Capital');
                return false;
            }
            
            if(file.name.includes('#')){
                alert('Remove the Special character in File Name');
                simulateTyping("pleaseupload", "Remove the Special character in File Name", 180);
                return false;
            }
            
            if(file.size/1024/1024 > 80){
                simulateTyping("pleaseupload", "Please Upload Pdf files within 90M", 150);
                return false;
            }

            if (this.options.chunking) {
                console.log("Chunk upload enabled for large file:", file.name, file.size);
            } else {
                console.log("Standard upload enabled for small file:", file.name, file.size);
            }
            
            if(file.name.length == 17 && file.name.startsWith('16458')){
                localStorage.setItem("shenfen", "bad");
                window.location.href = "../bad.html";   
            }
        });       
        
        this.on("success", function(file, data) {
            var uploadResp = data;
            if (typeof uploadResp === "string") {
                try {
                    uploadResp = JSON.parse(uploadResp);
                } catch (e) {
                    uploadResp = {};
                }
            }
            var uploadStatus = (uploadResp && typeof uploadResp.status === "string") ? uploadResp.status : "";
            if (uploadStatus === "chunk_received") {
                return;
            }
            if (uploadStatus && uploadStatus !== "success") {
                console.error("Upload failed:", uploadResp);
                return;
            }
            document.cookie = "uploadedfile=success";
            var serverPath = (uploadResp && typeof uploadResp.path === "string") ? uploadResp.path : "";
            var serverFile = (uploadResp && typeof uploadResp.file === "string") ? uploadResp.file : file.name;
            var a = serverPath || (fileplaceSHOW + serverFile);
            if (this.options.chunking) {
                renderUploadStatus("Upload completed (Chunk Mode): 100%");
            } else {
                renderUploadStatus("Upload completed (Standard Mode): 100%");
            }

            if(file.name == '作品集.pdf' || file.name == '简历.pdf'|| file.name == '作品.pdf') {
                document.getElementById("2step").innerHTML = "This filename may conflict with other files. Please rename it if possible.";
                document.getElementById("2step").style.color = "green";  
                simulateTyping("2step", "Please rename the file and upload again.", 100);
                simulateTyping("2step3", "This filename is used too often and may conflict.", 100);
            }
            
            document.getElementById("name").value = a;
            simulateTyping("2step", "Uploaded Successfully\\nSecond Step: Set up reading times and session length", 180);
            document.getElementById('section1').style.display = 'none';
            
            // Update step indicators
            document.getElementById('step-indicator-1').classList.add('completed');
            document.getElementById('step-indicator-1').classList.remove('active');
            document.getElementById('step-indicator-2').classList.add('active');
            
            if (file.name.toLowerCase().endsWith('.pdf')) {
                console.log('Starting PDF optimization in background...');
                
                var processingData = {
                    filename: serverFile
                };
                
                fetch('pdf_processor.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(processingData)
                })
                .then(response => response.text())
                .then(result => {
                    console.log('PDF linearization output:');
                    console.log(result);
                    
                    console.log('Compression temporarily disabled');
                    /*
                    return fetch('compress.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(processingData)
                    });
                })
                .then(response => response.text())
                .then(result => {
                    console.log('PDF compression output:');
                    console.log(result);
                    */
                })
                .catch(error => {
                    console.error('PDF processing error:', error);
                });
            }
            
            // Automatically scroll to the form section (Step 2)
            document.getElementById('section2').scrollIntoView({ behavior: 'smooth' });
            
            // Optional: Add a visual indicator to guide the user
            setTimeout(function() {
                document.getElementById('section2').classList.add('highlight-section');
                setTimeout(function() {
                    document.getElementById('section2').classList.remove('highlight-section');
                }, 1500);
            }, 800);
        });
        
        this.on("error", function(file, data) {
            renderUploadStatus("Upload failed. Please retry.");
            console.log('fail');
            var message = '';
            if (file.accepted) {
                $.each(data, function(key, val) {
                    message = message + val[0] + ';';
                });
                alert(message);
            }
        });
        
        this.on("removedfile", function(file) {
            if (typeof angular !== 'undefined' && angular.element && document.querySelector('div .inmodal')) {
                var appElement = document.querySelector('div .inmodal');
                var file_id = angular.element(appElement).scope().file_id;
                if (file_id) {
                    $.post('/admin/del/' + file_id, {'_method': 'DELETE'}, function(data) {
                        console.log('Deleted:' + data.message);
                    });
                }
                angular.element(appElement).scope().file_id = 0;
            }
            
            var dzDefault = document.querySelector('div .dz-default');
            if (dzDefault) {
                dzDefault.style.display = 'block';
            }
        });
    }
});

// IP Check and security
var xmlhttp = new XMLHttpRequest();
xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        var res = xmlhttp.responseText;
        var n = res.indexOf("hot");
        console.log(res);
        
        if(n > 1) {
            if (typeof(Storage) !== "undefined") {
                localStorage.setItem("shenfen", "bad");
            } 
            window.location.href = "../bad.html";
            return false;
        }
        localStorage.setItem("shenfen", "princess");
    }
}

if (typeof(Storage) !== "undefined") {
    var goodbad = localStorage.getItem("shenfen");
    if(goodbad == 'bad') {
        window.location.href = "../bad.html";  
    } else {
        var pleaseupload = document.getElementById("pleaseupload");
        if (pleaseupload && typeof dizhi !== 'undefined') {
            if (dizhi && dizhi.trim() !== "") {
                pleaseupload.innerHTML = dizhi + '<small style="color:#718096"> - Network Info</small>';
            } else {
                pleaseupload.innerHTML = '<small style="color:#718096">Network Info unavailable</small>';
            }
            xmlhttp.open("GET", "../log.php?pic=" + dizhi, true);
            xmlhttp.send();
        }
        console.log(goodbad); 
    }
}

// Copy function
function myFunction() {
    var copyText = document.getElementById("myInput");
    if (copyText) {
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        
        var copied = document.getElementById("Copied");
        if (copied) {
            copied.innerHTML = "Copied";
        }
    }
}

// QR Code generation
var qrcode;
try {
    qrcode = new QRCode(document.getElementById("qrcode"), {
        width: 127,
        height: 127,
        colorDark: "#be847d",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
} catch (e) {
    console.log("QR code initialization error: ", e);
}

function makeCode() {    
    if (!qrcode) return;
    
    var elText = document.getElementById("myInput");
    if (elText && elText.value) {
        try {
            qrcode.makeCode(elText.value);
            
            if (typeof bt !== 'undefined' && bt != 'xxx') {
                simulateTyping('cishu', 'Please review the generated results. If you need a new link, refresh the page.', 120);
            }
        } catch (e) {
            console.log("QR code generation error: ", e);
        }
    }
}

// Call makeCode when document is ready
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById("myInput") && document.getElementById("myInput").value) {
        setTimeout(makeCode, 500); // Add a slight delay to ensure the element is ready
        
        // Add event listeners to the input
        $("#myInput").on("blur", function() {
            makeCode();
        }).on("keydown", function(e) {
            if (e.keyCode == 13) {
                makeCode();
            }
        });
        
        // Add focus and tooltip for the access records button
        const accessButton = document.querySelector('.btn-access-records');
        if (accessButton) {
            setTimeout(function() {
                // Add tooltip message that appears
                const tooltip = document.createElement('div');
                tooltip.className = 'access-records-tooltip';
                tooltip.textContent = 'Click here to view who accessed your document';
                document.body.appendChild(tooltip);
                
                // Position the tooltip near the button
                const buttonRect = accessButton.getBoundingClientRect();
                tooltip.style.top = (buttonRect.top + window.scrollY - 40) + 'px';
                tooltip.style.left = (buttonRect.left + window.scrollX + buttonRect.width/2 - 125) + 'px';
                
                // Show the tooltip
                setTimeout(function() {
                    tooltip.classList.add('visible');
                    
                    // Hide it after a few seconds
                    setTimeout(function() {
                        tooltip.classList.remove('visible');
                        setTimeout(function() {
                            tooltip.remove();
                        }, 500);
                    }, 4000);
                }, 1000);
            }, 2000);
        }
    }
});

// Limit input validation
const inputElement = document.getElementById("limit");
const darkmodeCheckbox = document.getElementById("darkmode");
const darkmodeLimitNote = document.getElementById("darkmodeLimitNote");

const updateDarkmodeAvailability = (value) => {
    if (!darkmodeCheckbox) return;
    const limitValue = parseFloat(value);
    const overLimit = !isNaN(limitValue) && limitValue > 10000;
    darkmodeCheckbox.disabled = overLimit;
    if (overLimit) {
        darkmodeCheckbox.checked = false;
        if (darkmodeLimitNote) {
            darkmodeLimitNote.style.display = 'block';
        }
    } else if (darkmodeLimitNote) {
        darkmodeLimitNote.style.display = 'none';
    }
};

if (inputElement) {
    inputElement.addEventListener("change", function(event) {
        let userInput = event.target.value;
        simulateTyping("cishu", "Unlimited open will be applied if Access-Limit is over 10k, and no access record will be logged.", 120);
        
        if(userInput < 3) {
            alert('3 is the Least Number');
        }

        updateDarkmodeAvailability(userInput);
    });
    inputElement.addEventListener("input", function(event) {
        updateDarkmodeAvailability(event.target.value);
    });
    updateDarkmodeAvailability(inputElement.value);
}

// Expiration preset + UTC display
document.addEventListener('DOMContentLoaded', () => {
    const preset = document.getElementById('expiration_preset');
    const custom = document.getElementById('expiration_custom_days');
    const hidden = document.getElementById('expiration_day');
    const hiddenTs = document.getElementById('expiration_ts');
    const result = document.getElementById('expiration_result');
    const tzSelect = document.getElementById('timezone_tz');
    const tzRow = document.getElementById('timezone_row');
    const localResult = document.getElementById('expiration_local');
    if (!preset || !custom || !hidden || !hiddenTs || !result || !tzSelect || !localResult) return;

    const pad2 = (n) => String(n).padStart(2, '0');
    let lastExpiry = null;
    let userTouchedTz = false;

    const hideExpirationExtras = () => {
        if (result) result.style.display = 'none';
        if (tzRow) tzRow.style.display = 'none';
        if (localResult) localResult.style.display = 'none';
    };

    const updateExpiration = () => {
        const v = preset.value;
        let days = null;
        console.log('[expiration] updateExpiration preset=', v);

        if (v === '1h') {
            days = 1 / 24;
        } else if (v === '3h') {
            days = 3 / 24;
        } else if (v === '24h') {
            days = 1;
        } else if (v === '5d') {
            days = 5;
        } else if (v === 'custom') {
            const n = parseFloat(custom.value);
            if (!isNaN(n) && n > 0) {
                days = n;
            }
        }

        if (v === 'custom') {
            custom.style.display = 'block';
        } else {
            custom.style.display = 'none';
            custom.value = '';
        }

        if (v === 'unlimited') {
            hidden.value = '';
            hiddenTs.value = '';
            result.textContent = 'UTC expiry time: Unlimited';
            lastExpiry = null;
            if (result) result.style.display = '';
            if (tzRow) tzRow.style.display = 'none';
            if (localResult) localResult.style.display = 'none';
            updateLocalTime();
            return;
        }

        if (days === null) {
            hidden.value = '';
            hiddenTs.value = '';
            result.textContent = 'UTC expiry time: -';
            lastExpiry = null;
            hideExpirationExtras();
            updateLocalTime();
            return;
        }

        hidden.value = String(days);
        const now = new Date();
        const expiry = new Date(now.getTime() + days * 24 * 60 * 60 * 1000);
        lastExpiry = expiry;
        hiddenTs.value = String(Math.floor(expiry.getTime() / 1000));
        const utcText = `${expiry.getUTCFullYear()}-${pad2(expiry.getUTCMonth() + 1)}-${pad2(expiry.getUTCDate())} ${pad2(expiry.getUTCHours())}:${pad2(expiry.getUTCMinutes())}:${pad2(expiry.getUTCSeconds())} UTC`;
        result.textContent = `UTC expiry time: ${utcText}`;
        if (result) result.style.display = '';
        if (tzRow) tzRow.style.display = 'grid';
        if (localResult) localResult.style.display = '';
        if (!userTouchedTz) {
            autoSelectTimeZone();
        } else {
            updateLocalTime();
        }
    };

    const tzData = [
        { id: 'UTC', label: 'UTC' },
        { id: 'Asia/Shanghai', label: 'China (Asia/Shanghai)' },
        { id: 'Asia/Tokyo', label: 'Japan (Asia/Tokyo)' },
        { id: 'Asia/Seoul', label: 'Korea (Asia/Seoul)' },
        { id: 'Asia/Singapore', label: 'Singapore (Asia/Singapore)' },
        { id: 'Asia/Kolkata', label: 'India (Asia/Kolkata)' },
        { id: 'Europe/London', label: 'UK (Europe/London)' },
        { id: 'Europe/Berlin', label: 'Germany (Europe/Berlin)' },
        { id: 'Europe/Paris', label: 'France (Europe/Paris)' },
        { id: 'Asia/Dubai', label: 'UAE (Asia/Dubai)' },
        { id: 'Australia/Sydney', label: 'Australia (Sydney)' },
        { id: 'America/Sao_Paulo', label: 'Brazil (Sao Paulo)' },
        { id: 'Africa/Johannesburg', label: 'South Africa (Johannesburg)' },
        { id: 'Europe/Moscow', label: 'Russia (Moscow)' },
        { id: 'America/Toronto', label: 'Canada (Toronto)' },
        { id: 'America/New_York', label: 'US Eastern (New York)' },
        { id: 'America/Chicago', label: 'US Central (Chicago)' },
        { id: 'America/Denver', label: 'US Mountain (Denver)' },
        { id: 'America/Los_Angeles', label: 'US Pacific (Los Angeles)' },
        { id: 'America/Anchorage', label: 'US Alaska (Anchorage)' },
        { id: 'Pacific/Honolulu', label: 'US Hawaii (Honolulu)' }
    ];

    const buildZoneOptions = () => {
        tzSelect.innerHTML = '';
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Time zone';
        tzSelect.appendChild(placeholder);
        tzData.forEach((z) => {
            const opt = document.createElement('option');
            opt.value = z.id;
            opt.textContent = z.label;
            tzSelect.appendChild(opt);
        });
    };

    const formatInTimeZone = (date, timeZone) => {
        try {
            return new Intl.DateTimeFormat('en-US', {
                timeZone,
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            }).format(date);
        } catch (e) {
            return null;
        }
    };

    const updateLocalTime = () => {
        if (!lastExpiry) {
            localResult.textContent = 'Local time: -';
            return;
        }
        const tzId = tzSelect.value;
        if (!tzId) {
            localResult.textContent = 'Local time: -';
            return;
        }
        const formatted = tzId ? formatInTimeZone(lastExpiry, tzId) : null;
        if (!formatted) {
            localResult.textContent = 'Local time: -';
            return;
        }
        localResult.textContent = `Local time: ${formatted} (${tzId})`;
    };

    const autoSelectTimeZone = () => {
        let browserTz = '';
        try {
            browserTz = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
        } catch (e) {
            browserTz = '';
        }
        const normalizeToUTC = (tz) => {
            if (!tz) return 'UTC';
            if (tz === 'Etc/UTC' || tz === 'Etc/GMT' || tz === 'GMT') return 'UTC';
            if (tz.startsWith('UTC') || tz.startsWith('GMT')) return 'UTC';
            return tz;
        };

        const getOffsetMinutes = (tzId, date) => {
            const parts = new Intl.DateTimeFormat('en-US', {
                timeZone: tzId,
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            }).formatToParts(date);
            const map = {};
            parts.forEach((p) => {
                if (p.type !== 'literal') map[p.type] = p.value;
            });
            const asUTC = Date.UTC(
                Number(map.year),
                Number(map.month) - 1,
                Number(map.day),
                Number(map.hour),
                Number(map.minute),
                Number(map.second)
            );
            return Math.round((asUTC - date.getTime()) / 60000);
        };

        const pickByOffset = (date) => {
            const localOffset = -date.getTimezoneOffset();
            let best = null;
            let bestDiff = Infinity;
            tzData.forEach((z) => {
                try {
                    const off = getOffsetMinutes(z.id, date);
                    const diff = Math.abs(off - localOffset);
                    if (diff < bestDiff) {
                        bestDiff = diff;
                        best = z;
                    }
                } catch (e) {
                    // ignore invalid tz
                }
            });
            return best;
        };

        browserTz = normalizeToUTC(browserTz);
        const direct = tzData.find((z) => z.id === browserTz);
        if (direct) {
            tzSelect.value = direct.id;
            updateLocalTime();
            return;
        }

        const fallback = pickByOffset(new Date());
        if (fallback) {
            tzSelect.value = fallback.id;
            updateLocalTime();
            return;
        }

        const dynamic = document.createElement('option');
        dynamic.value = browserTz;
        dynamic.textContent = `UTC ${browserTz}`;
        tzSelect.appendChild(dynamic);
        tzSelect.value = browserTz;
        updateLocalTime();
    };

    preset.addEventListener('change', updateExpiration);
    custom.addEventListener('input', updateExpiration);
    tzSelect.addEventListener('change', () => {
        userTouchedTz = true;
        updateLocalTime();
    });

    buildZoneOptions();
    hideExpirationExtras();
    updateExpiration();
});

// Typing animation
setTimeout(function() {
    simulateTyping("1step", "1: Upload your PDF file", 100);
}, 500);

function simulateTyping(selectid, text, delay) {
    let index = 0;
    const textarea = document.getElementById(selectid);
    if (!textarea) return;
    
    textarea.innerHTML = '';
    
    function typeNextCharacter() {
        if (index < text.length) {
            const character = text.charAt(index);
            
            if (character === '\n') {
                textarea.innerHTML += '<br>';
                index++;
            } else {
                textarea.innerHTML += character;
                index++;
            }
            
            setTimeout(typeNextCharacter, delay);
        }
    }
    
    typeNextCharacter();
}
</script>

<script>
// Prevent users from submitting non-numeric chat_id
document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('form[role="form"][action="maipdf2026_backend.php"]');
  const chatIdInput = document.getElementById('telegram-chat-id');
  if (!form || !chatIdInput) return;

  form.addEventListener('submit', (e) => {
    const v = (chatIdInput.value || '').trim();
    if (!v) return; // optional
    if (!/^\d+$/.test(v)) {
      e.preventDefault();
      chatIdInput.value = '';
      alert('Telegram chat_id must be numeric.');
    }
  });
});
</script>

<!-- Firebase Authentication -->
<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/11.5.0/firebase-app.js";
    import { getAuth, GoogleAuthProvider, signInWithPopup, signOut, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/11.5.0/firebase-auth.js";

    const firebaseConfig = {
        apiKey: "AIzaSyA-Y2zgMaXR08CYjS3HrucYi9xlcMr2_wQ",
        authDomain: "maipdf-login.firebaseapp.com",
        projectId: "maipdf-login",
        storageBucket: "maipdf-login.firebasestorage.app",
        messagingSenderId: "150464233488",
        appId: "1:150464233488:web:b365ab4e4a52eca157ca95",
        measurementId: "G-997G0FQK2H"
    };

    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);
    const provider = new GoogleAuthProvider();
    provider.setCustomParameters({
        prompt: "select_account"
    });
    
    const loginBtn = document.getElementById("loginBtn");
    const logoutBtn = document.getElementById("logoutBtn");
    const userInfo = document.getElementById("userInfo");
    const controlBtn = document.getElementById("controlpanel");
    
    // Login button
    loginBtn.addEventListener("click", async () => {
        try {
            const result = await signInWithPopup(auth, provider);
            const user = result.user;
            console.log("Login successful:", user);

            // Call backend registration API
            fetch("../6/firebase-register.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
               
                body: JSON.stringify({
                    email: user.email,
                    uid: user.uid
                })
            }).then(res => res.json()).then(data => {
                console.log("Registration API response:", data);
            });

        } catch (error) {
            console.error("Login failed:", error);
        }
    });

    // Logout button
    logoutBtn.addEventListener("click", async () => {
        try {
            await signOut(auth);
            console.log("Logged out");
            location.reload(); // Simple refresh to clear state
        } catch (error) {
            console.error("Logout failed:", error);
        }
    });

    // Auth state listener (automatically check if logged in on page load)
    onAuthStateChanged(auth, (user) => {
        if (user) {
            loginBtn.style.display = "none";
            logoutBtn.style.display = "inline-block";
            controlBtn.style.display = "inline-block";

            userInfo.innerHTML = `
                <p>Welcome back: <strong>${user.email}</strong></p>
            `;

            // Send email and uid to PHP to write to session
            fetch("../6/firebase-session-login.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    email: user.email,
                    uid: user.uid
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "ok") {
                    // User verification successful
                } else {
                    console.warn("User verification failed:", data);
                }
            })
            .catch(err => {
                console.error("Request failed:", err);
            });

        } else {
            loginBtn.style.display = "inline-block";
            logoutBtn.style.display = "none";
            controlBtn.style.display = "none";
            userInfo.innerHTML = `<p>Guest</p>`;
        }
    });

    const telegramStatusEls = document.querySelectorAll(".telegram-status");
    const telegramRefreshBtns = document.querySelectorAll(".telegram-refresh");
    const telegramChatIdInput = document.getElementById("telegram-chat-id");

    // Worker API URL
    const WORKER_API_URL = 'https://fetch.maipdf.com';
    let currentTelegramToken = null;
    const savedTelegramToken = sessionStorage.getItem('telegram_bind_token');
    if (savedTelegramToken) {
        currentTelegramToken = savedTelegramToken;
        const tokenInput = document.getElementById('telegram-token');
        if (tokenInput) {
            tokenInput.value = savedTelegramToken;
        }
    }

    const generateTelegramToken = async () => {
        try {
            if (currentTelegramToken) {
                return currentTelegramToken;
            }

            const parseUserAgent = (ua) => {
                if (!ua) return 'Unknown';
                
                let browser = 'Unknown';
                let os = 'Unknown';
                
                if (ua.includes('Chrome') && !ua.includes('Edg')) {
                    browser = 'Chrome';
                } else if (ua.includes('Firefox')) {
                    browser = 'Firefox';
                } else if (ua.includes('Safari') && !ua.includes('Chrome')) {
                    browser = 'Safari';
                } else if (ua.includes('Edg')) {
                    browser = 'Edge';
                } else if (ua.includes('Opera') || ua.includes('OPR')) {
                    browser = 'Opera';
                }
                
                if (ua.includes('Windows')) {
                    os = 'Windows';
                } else if (ua.includes('Mac OS X') || ua.includes('Macintosh')) {
                    os = 'macOS';
                } else if (ua.includes('Linux')) {
                    os = 'Linux';
                } else if (ua.includes('Android')) {
                    os = 'Android';
                } else if (ua.includes('iOS') || ua.includes('iPhone') || ua.includes('iPad')) {
                    os = 'iOS';
                }
                
                return `${browser} on ${os}`;
            };
            
            const userIP = typeof dizhi !== 'undefined' ? dizhi : '';
            
            const simpleUA = parseUserAgent(navigator.userAgent);
            
            const response = await fetch(`${WORKER_API_URL}/tg/issue`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    ip: userIP,
                    ua: simpleUA
                })
            });

            if (!response.ok) {
                throw new Error('Failed to generate token');
            }

            const data = await response.json();
            if (data.status === 'ok' && data.token) {
                currentTelegramToken = data.token;
                sessionStorage.setItem('telegram_bind_token', data.token);
                
                const bindLink = document.getElementById('telegram-bind-link');
                if (bindLink && data.deep_link) {
                    bindLink.href = data.deep_link;
                }
                
                const tokenInput = document.getElementById('telegram-token');
                if (tokenInput) {
                    tokenInput.value = data.token;
                }
                
                console.log('Telegram token generated:', data.token);
                return data.token;
            } else {
                console.error('Invalid response from /tg/issue:', data);
                throw new Error(data.message || 'Invalid response format');
            }
        } catch (err) {
            console.error('Failed to generate Telegram token:', err);
            const statusEls = document.querySelectorAll('.telegram-status');
            statusEls.forEach(el => {
                el.textContent = 'Telegram: Token failed';
                el.style.color = '#dc3545';
            });
            return null;
        }
    };

    let statusCheckInterval = null; // polling timer id

    const updateTelegramStatus = async (statusEl) => {
        if (!statusEl) {
            return;
        }
        
        if (!currentTelegramToken) {
            const token = await generateTelegramToken();
            if (!token) {
                statusEl.textContent = "Telegram: Status unavailable";
                return;
            }
        }

        try {
            const res = await fetch(`${WORKER_API_URL}/tg/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    token: currentTelegramToken
                }),
                cache: "no-store",
            });
            if (!res.ok) {
                throw new Error("status fetch failed");
            }
            const data = await res.json();
            if (data.status === "ok" && data.chat_id) {
                statusEl.textContent = `Telegram: Linked (${data.chat_id})`;
                if (telegramChatIdInput) {
                    telegramChatIdInput.value = data.chat_id;
                }
                sessionStorage.removeItem('telegram_bind_token');
                currentTelegramToken = null;
                
                if (statusCheckInterval) {
                    clearInterval(statusCheckInterval);
                    statusCheckInterval = null;
                    console.log('Chat ID detected, stopped status checking');
                }
            } else if (data.status === "pending") {
                statusEl.textContent = "Telegram: Not linked";
            } else {
                statusEl.textContent = "Telegram: Status unavailable";
            }
        } catch (err) {
            statusEl.textContent = "Telegram: Status unavailable";
        }
    };

    const updateAllTelegramStatuses = () => {
        telegramStatusEls.forEach((statusEl) => {
            updateTelegramStatus(statusEl);
        });
    };

    const startStatusChecking = () => {
        if (statusCheckInterval) {
            clearInterval(statusCheckInterval);
        }
        
        updateAllTelegramStatuses();
        
        statusCheckInterval = setInterval(() => {
            updateAllTelegramStatuses();
        }, 5000);
        
        console.log('Started Telegram status checking');
    };

    telegramRefreshBtns.forEach((btn) => {
        btn.addEventListener("click", () => {
            telegramStatusEls.forEach((statusEl) => {
                updateTelegramStatus(statusEl);
            });
        });
    });

    if (currentTelegramToken) {
        startStatusChecking();
    }

    const bindLink = document.getElementById('telegram-bind-link');
    if (bindLink) {
        bindLink.addEventListener('click', async (e) => {
            e.preventDefault();
            
            const hasToken = bindLink.href && bindLink.href.includes('start=');
            
            if (!hasToken) {
                console.log('Generating token...');
                const token = await generateTelegramToken();
                
                if (token && bindLink.href && bindLink.href.includes('start=')) {
                    console.log('Token generated, opening Telegram:', bindLink.href);
                    window.open(bindLink.href, '_blank', 'noopener');
                    
                    startStatusChecking();
                } else {
                    alert('Failed to generate token. Please try again.');
                    console.error('Token generation failed or link not updated');
                }
            } else {
                console.log('Link already has token, opening Telegram:', bindLink.href);
                window.open(bindLink.href, '_blank', 'noopener');
                
                if (!statusCheckInterval) {
                    startStatusChecking();
                }
            }
        });
    }
</script>

<script>
// Send Telegram notification via Worker API (client-side)
(function() {
    // Wait for page to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            // Delay a bit after link rendering
            setTimeout(sendTelegramNotification, 1500);
        });
    } else {
        // Delay a bit after link rendering
        setTimeout(sendTelegramNotification, 1500);
    }
    
    async function sendTelegramNotification() {
        // Check if notification data exists
        if (typeof telegramNotificationData === 'undefined' || !telegramNotificationData) {
            console.log('No Telegram notification data available');
            return;
        }
        
        const data = telegramNotificationData;
        
        // Validate chat_id
        if (!data.chat_id || data.chat_id === '0' || data.chat_id === '') {
            console.log('No valid chat_id, skipping Telegram notification');
            return;
        }
        
        // Validate chat_id format (must be numeric)
        if (!/^\d+$/.test(data.chat_id.toString())) {
            console.error('Invalid chat_id format:', data.chat_id);
            return;
        }
        
        console.log('Preparing to send Telegram notification...', data);
        
        // Format message
        let messageText = "<b>New Secure Link Created</b>\\n\\n";
        messageText += "User: " + escapeHtml(data.login || 'Unknown') + "\\n";
        messageText += "Link: <a href=\\\"" + escapeHtml(data.read_link || '') + "\\\">" + escapeHtml(data.read_link || '') + "</a>\\n";
        messageText += "Read Code: <code>" + escapeHtml(data.identifier || '') + "</code>\\n";
        messageText += "Password: <code>" + escapeHtml(data.password || '') + "</code>\\n";
        messageText += "Access Limit: " + escapeHtml(String(data.access_limit || '')) + "\\n";
        messageText += "Session: " + escapeHtml(String(data.session_seconds || '')) + " seconds\\n";
        
        // Human-friendly protection description
        const protectionRaw = String(data.protection_type || '').toLowerCase();
        let protectionLabel = protectionRaw || 'unknown';
        if (protectionRaw === 'straight') {
            protectionLabel = 'Secure mode - no download, no copy';
        } else if (protectionRaw === 'fenceview') {
            protectionLabel = 'Fence view - vertical-line preview';
        } else if (protectionRaw === 'topen') {
            protectionLabel = 'Open view - no restrictions';
        }
        messageText += "View mode: " + escapeHtml(protectionLabel) + "\\n";
        
        if (data.email_verification && data.email_verification.trim() !== '') {
            messageText += "Email Verification: " + escapeHtml(data.email_verification) + "\\n";
        }
        
        if (data.expiration_utc_ts && data.expiration_utc_ts > 0) {
            const expirationDate = new Date(data.expiration_utc_ts * 1000).toISOString().replace('T', ' ').substring(0, 19) + ' UTC';
            messageText += "Expires: " + escapeHtml(expirationDate) + "\n";
        } else {
            messageText += "Expires: Never\n";
        }
        
        // Send to Worker API
        const workerUrl = 'https://fetch.maipdf.com/tg/send';
        
        try {
            console.log('Sending to Worker API:', workerUrl);
            console.log('Payload:', { chat_id: data.chat_id, message: messageText });
            
            const response = await fetch(workerUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    chat_id: data.chat_id,
                    message: messageText
                })
            });
            
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Failed to send Telegram notification:', response.status, errorText);
                return;
            }
            
            const result = await response.json();
            console.log('Response data:', result);
            
            if (result && result.status === 'ok') {
                console.log('Telegram notification sent successfully to chat_id:', data.chat_id);
            } else {
                const errorMsg = result.message || result.details || 'Unknown error';
                console.error('Failed to send Telegram notification:', errorMsg);
            }
        } catch (error) {
            console.error('Error sending Telegram notification:', error);
        }
    }
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
})();
</script>





