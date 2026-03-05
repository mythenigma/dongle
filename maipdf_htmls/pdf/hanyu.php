<?php 
//ini_set("display_errors", true);
//ini_set("html_errors", false); 

// Database connection details
$db_host = 'localhost';
$db_user = 'your_db_user'; // Update with your actual database user
$db_pass = 'your_db_password'; // Update with your actual database password
$db_name = 'maipdf_db'; // Update with your actual database name

// Variables for form result display
$pdflinkfull = '';
$identifier = '';

// Handle form submissions (similar to maipdf.php functionality)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_FILES['file'])) {
    // Start session if not already started
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    
    try {
        // Check if file was uploaded previously
        if (!isset($_SESSION['uploaded_file']) || empty($_SESSION['uploaded_file']['path'])) {
            throw new Exception('No file uploaded or session expired. Please upload a file first.');
        }
        
        // Get file information from session
        $filePath = $_SESSION['uploaded_file']['path'];
        $originalFileName = $_SESSION['uploaded_file']['name'];
        
        // Validate file existence
        if (!file_exists($filePath)) {
            throw new Exception('Uploaded file not found. Please try uploading again.');
        }
        
        // Get form data with validation
        $fileName = isset($_POST['sender']) ? htmlspecialchars(trim($_POST['sender']), ENT_QUOTES, 'UTF-8') : $filePath;
        $accessLimit = isset($_POST['limit']) && is_numeric($_POST['limit']) ? (int)$_POST['limit'] : 5;
        $sessionDuration = isset($_POST['password']) && is_numeric($_POST['password']) ? (int)$_POST['password'] : 600;
        $securityMode = isset($_POST['zhangai']) ? htmlspecialchars(trim($_POST['zhangai']), ENT_QUOTES, 'UTF-8') : 'straight';
        $enableWatermark = isset($_POST['darkmode']) && $_POST['darkmode'] === 'yes' ? 1 : 0;
        $notificationEmail = isset($_POST['mailalert']) ? filter_var($_POST['mailalert'], FILTER_SANITIZE_EMAIL) : '';
        $emailVerification = isset($_POST['enableEmailValidation']) && $_POST['enableEmailValidation'] === 'yes' ? 1 : 0;
        $allowedEmails = isset($_POST['emailAddresses']) ? htmlspecialchars(trim($_POST['emailAddresses']), ENT_QUOTES, 'UTF-8') : '';
        
        // Generate unique identifier (same as in JS)
        $timestamp = time();
        $randomChars = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 6);
        $identifier = base_convert($timestamp, 10, 36) . $randomChars;
        
        // Create expiration date (30 days from now)
        $expireDate = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        // Connect to database
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
        
        // Check connection
        if ($conn->connect_error) {
            throw new Exception('Database connection failed: ' . $conn->connect_error);
        }
        
        // Set UTF-8 charset
        $conn->set_charset("utf8mb4");
        
        // Prepare SQL statement (similar to b.php)
        $sql = "INSERT INTO pdf_links 
                (identifier, file_path, original_filename, access_limit, session_duration, security_mode, 
                watermark_enabled, notification_email, email_verification, allowed_emails, 
                created_at, expire_date, allowed_views, views_count) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, 0, 0)";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Database preparation failed: ' . $conn->error);
        }
        
        // Bind parameters
        $stmt->bind_param(
            "sssiisissss", 
            $identifier, 
            $filePath, 
            $originalFileName, 
            $accessLimit, 
            $sessionDuration, 
            $securityMode, 
            $enableWatermark, 
            $notificationEmail, 
            $emailVerification, 
            $allowedEmails, 
            $expireDate
        );
        
        // Execute statement
        if (!$stmt->execute()) {
            throw new Exception('Database execution failed: ' . $stmt->error);
        }
        
        // Close statement and connection
        $stmt->close();
        $conn->close();
        
        // Generate secure link
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $pdflinkfull = "$protocol://$host/est/$identifier@pdf";
        
        // Log the secure link creation
        $ip = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $logEntry = date('Y-m-d H:i:s') . " | Create | " . $ip . " | " . $originalFileName . " | " . $identifier . " | " . $userAgent . "\n";
        file_put_contents("secure_pdf_log.txt", $logEntry, FILE_APPEND);
        
        // Set form processed flag
        $_SESSION['form_processed'] = true;
        
        // Generate javascript to show results section
        echo "<script>
            window.onload = function() {
                document.getElementById('section2').style.display = 'none';
                document.getElementById('contact').style.display = 'block';
                document.getElementById('step2').classList.remove('active');
                document.getElementById('step3').classList.add('active');
                
                // Generate QR code
                new QRCode(document.getElementById('qrcode'), {
                    text: '$pdflinkfull',
                    width: 128,
                    height: 128,
                    colorDark: '#000000',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.H
                });
                
                // Scroll to results section
                document.getElementById('contact').scrollIntoView({behavior: 'smooth'});
                
                // For feedback to user
                simulateTyping('3step', 'Step 3: Share Your Secure PDF Link', 100);
            };
        </script>";
        
    } catch (Exception $e) {
        // Log error
        error_log("Secure PDF creation error: " . $e->getMessage() . " | User: " . $_SERVER['REMOTE_ADDR']);
        
        // Display error to user
        echo "<script>
            window.onload = function() {
                alert('Error: " . addslashes($e->getMessage()) . "');
            };
        </script>";
    }
}

// Handle file uploads from Dropzone
if(isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    // Set the upload directory
    $year = date("Y");
    $month = date("m");
    $week = date("d");
    $fileplace = "yes/" . $year . "/" . $month . "/" . $week . "/";
    
    // Create directory if it doesn't exist
    if (!file_exists($fileplace)) {
        if (!mkdir($fileplace, 0777, true)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create upload directory', 'success' => false]);
            exit;
        }
    }
    
    // Check if directory is writable
    if (!is_writable($fileplace)) {
        chmod($fileplace, 0777);
        if (!is_writable($fileplace)) {
            http_response_code(500);
            echo json_encode(['error' => 'Upload directory is not writable', 'success' => false]);
            exit;
        }
    }
    
    // Get file info
    $fileName = $_FILES['file']['name'];
    $tmpName = $_FILES['file']['tmp_name'];
    $fileType = $_FILES['file']['type'];
    $fileSize = $_FILES['file']['size'];
    
    // Enhanced file validation
    $validPdfType = false;
    
    // Check file type using mime type
    if ($fileType === 'application/pdf') {
        $validPdfType = true;
    }
    
    // Double-check with file signature (magic numbers)
    $fileHeader = file_get_contents($tmpName, false, null, 0, 4);
    if ($fileHeader === '%PDF') {
        $validPdfType = true;
    }
    
    // Enhanced security check - run virus scan if available
    $isSafe = true;
    if (function_exists('exec')) {
        // Simple file safety check - can be replaced with proper virus scanner
        $output = [];
        @exec("file -b --mime-type " . escapeshellarg($tmpName), $output);
        if (!empty($output) && strpos($output[0], 'pdf') === false) {
            $isSafe = false;
        }
    }
    
    // Validate file size (50MB max)
    $maxFileSize = 50 * 1024 * 1024; // 50MB in bytes
    
    if (!$validPdfType) {
        http_response_code(400);
        echo json_encode(['error' => 'Only PDF files are allowed', 'success' => false]);
        exit;
    }
    
    if (!$isSafe) {
        http_response_code(400);
        echo json_encode(['error' => 'File safety check failed', 'success' => false]);
        
        // Log security event using proper POST request
        $securityData = [
            'event' => 'unsafe_file_upload_attempt',
            'documentId' => 'upload_' . uniqid(),
            'userInfo' => $_SERVER['REMOTE_ADDR']
        ];
        
        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($securityData)
            ]
        ];
        
        $context  = stream_context_create($options);
        file_get_contents('log_security_event.php', false, $context);
        
        exit;
    }
    
    if ($fileSize > $maxFileSize) {
        http_response_code(400);
        echo json_encode(['error' => 'File size exceeds the maximum limit of 50MB', 'success' => false]);
        exit;
    }
    
    // Generate unique filename with original name for better tracking
    $fileNameNoExt = pathinfo($fileName, PATHINFO_FILENAME);
    $safeFileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $fileNameNoExt);
    $newFileName = uniqid() . '_' . $safeFileName . '.pdf';
    $filePath = $fileplace . $newFileName;
    
    // Move uploaded file to destination directory
    if(move_uploaded_file($tmpName, $filePath)) {
        // Store file information in session for next step
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['uploaded_file'] = [
            'name' => $fileName,
            'path' => $filePath,
            'new_name' => $newFileName,
            'upload_time' => date('Y-m-d H:i:s'),
            'file_size' => $fileSize
        ];
        
        // Log the successful upload with IP for security tracking
        $uploadIP = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $logEntry = date('Y-m-d H:i:s') . " | Upload | " . $uploadIP . " | " . $fileName . " | " . $newFileName . " | " . $userAgent . "\n";
        file_put_contents("upload_log.txt", $logEntry, FILE_APPEND);
        
        // Return success response to Dropzone with additional info
        echo json_encode([
            'success' => true,
            'message' => 'File uploaded successfully',
            'fileName' => $newFileName,
            'filePath' => $filePath,
            'originalName' => $fileName,
            'fileSize' => $fileSize,
            'uploadTime' => date('Y-m-d H:i:s')
        ]);
        exit;
    } else {
        // Enhanced error handling
        $errorMessage = 'Failed to move uploaded file: ';
        $errorCode = isset($_FILES['file']['error']) ? $_FILES['file']['error'] : 0;
        
        switch($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                $errorMessage .= 'The file exceeds the upload_max_filesize directive in php.ini';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $errorMessage .= 'The file exceeds the MAX_FILE_SIZE directive in the HTML form';
                break;
            case UPLOAD_ERR_PARTIAL:
                $errorMessage .= 'The file was only partially uploaded';
                break;
            case UPLOAD_ERR_NO_FILE:
                $errorMessage .= 'No file was uploaded';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $errorMessage .= 'Missing a temporary folder';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $errorMessage .= 'Failed to write file to disk';
                break;
            case UPLOAD_ERR_EXTENSION:
                $errorMessage .= 'A PHP extension stopped the file upload';
                break;
            default:
                // Check directory permissions
                if (!is_writable($fileplace)) {
                    $errorMessage .= 'Permission denied: Upload directory is not writable';
                } else {
                    $errorMessage .= 'Unknown upload error. Please try again or contact support.';
                }
        }
        
        // Log upload error for troubleshooting
        error_log("Upload error: " . $errorMessage . " | User: " . $_SERVER['REMOTE_ADDR']);
        
        http_response_code(500);
        echo json_encode(['error' => $errorMessage, 'success' => false, 'errorCode' => $errorCode]);
        exit;
    }
}

// General page setup
$year= date("Y");
$month= date("m");
$week=  date("d");
          
$fileplaceSHOW="/".$year."/".$month."/".$week."/";
$fileplace="yes/".$year."/".$month."/".$week."/";
     // $picplace  = "yes/".$year."/".$month."/".$week."/preview/";
$encryfile='';
  
// 369 行也有 badlist 在上传前检查
if (isset($_COOKIE["shenfen"])){
   if($_COOKIE["shenfen"]=='badd'){exit('Not available to you=== joe@pdfhost.online');}
}

$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
if(strlen($ip)<1){
   $ip = $_SERVER['REMOTE_ADDR'];
}

if (isset($_COOKIE["dc"])){
    session_start();
}else{
    //echo 'mai';
}
     
if (isset($_SESSION["user"])){
    $dengru=$_SESSION["user"];
}else{
    $dengru='wofocibeifox';
    //session_destroy();
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

echo "<script>var dizhi = '$ip';var dengru = '$dengru'; var fileplaceSHOW='$fileplaceSHOW'; </script>";

if(!isset($_SERVER['HTTPS'])){
   $url= 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
   header("Location: $url");
   exit();
}
$br=$_SERVER['HTTP_USER_AGENT'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="author" content="MaiPDF">
  <title>Share PDF with expiration time and restrictions</title>
  <script type="text/javascript" src="qrcode.min.js"></script>
  <meta name="description" content="Share PDFs online securely with features like setting open limits, converting to QR codes, tracking access logs, and preventing PDF forwarding. Protect your documents with ease.">
  <meta name="keywords" content="PDF online sharing, set PDF open limit, convert PDF to QR code, view PDF access logs, prevent PDF forwarding">
  
  <!-- Core Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> 
  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" rel="stylesheet" type="text/css">
  
  <!-- Dropzone for Uploads -->
  <script src="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/dropzone.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/dropzone-amd-module.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/dropzone.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/basic.css">
  
  <!-- First JavaScript block - initialization script for Dropzone -->
  <script>
   document.cookie="uploadedfile=notyet"; 
   var zhuceid=1;
   var bt='xxx';
   var appElement = document.querySelector('div .inmodal');
  </script>
  
  <!-- Custom Styles -->
  <style>
    :root {
      --primary-color: #3b82f6;
      --primary-light: #60a5fa;
      --primary-dark: #1d4ed8;
      --secondary-color: #64748b;
      --success-color: #10b981;
      --info-color: #0ea5e9;
      --warning-color: #f59e0b;
      --danger-color: #ef4444;
      --light-bg: #f8fafc;
      --dark-bg: #1e293b;
      --text-light: #ffffff;
      --text-dark: #0f172a;
      --text-muted: #64748b;
      --border-color: #e2e8f0;
      --border-radius: 0.5rem;
      --border-radius-sm: 0.375rem;
      --border-radius-lg: 0.75rem;
      --box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      --box-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      --transition: all 0.3s ease;
    }
    
    body {
      font-family: 'Inter', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      color: var(--text-dark);
      background-color: var(--light-bg);
      line-height: 1.6;
    }
    
    /* Improved Navbar with Gradient */
    .navbar {
      box-shadow: var(--box-shadow);
      padding: 1rem 0;
      background: linear-gradient(120deg, var(--primary-dark), var(--primary-color), var(--primary-light)) !important;
    }
    
    .navbar-brand {
      font-weight: 700;
      letter-spacing: 0.5px;
      color: var(--text-light) !important;
    }
    
    .brand-name {
      color: var(--text-light);
      font-size: 1.8rem;
      margin: 0;
      font-weight: 800;
      letter-spacing: -0.5px;
      text-shadow: 0px 2px 4px rgba(0,0,0,0.2);
    }
    
    .brand-tagline {
      font-size: 0.9rem;
      opacity: 0.9;
      color: var(--text-light);
      letter-spacing: 0.5px;
    }
    
    /* Enhanced Buttons */
    .btn {
      border-radius: var(--border-radius);
      padding: 0.625rem 1.375rem;
      font-weight: 500;
      transition: var(--transition);
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
      position: relative;
      overflow: hidden;
    }
    
    .btn::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 5px;
      height: 5px;
      background: rgba(255, 255, 255, 0.5);
      opacity: 0;
      border-radius: 100%;
      transform: scale(1, 1) translate(-50%);
      transform-origin: 50% 50%;
    }
    
    .btn:focus:not(:active)::after {
      animation: ripple 1s ease-out;
    }
    
    @keyframes ripple {
      0% {
        transform: scale(0, 0);
        opacity: 0.5;
      }
      20% {
        transform: scale(25, 25);
        opacity: 0.3;
      }
      100% {
        opacity: 0;
        transform: scale(40, 40);
      }
    }
    
    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }
    
    .btn-primary:hover {
      background-color: var(--primary-dark);
      border-color: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: var(--box-shadow);
    }
    
    /* Improved Cards with Hover Effects */
    .card {
      border-radius: var(--border-radius);
      box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.05);
      border: none;
      transition: var(--transition);
      margin-bottom: 1.5rem;
      overflow: hidden;
      background-color: var(--text-light);
      position: relative;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: var(--box-shadow-lg);
    }
    
    .card-header {
      background-color: rgba(0, 0, 0, 0.02);
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      font-weight: 600;
      padding: 1.25rem 1.5rem;
    }
    
    /* Enhanced Section Title */
    .section-title {
      position: relative;
      margin-bottom: 2.5rem;
      font-weight: 700;
      color: var(--primary-color);
      padding-bottom: 0.75rem;
      text-align: center;
    }
    
    .section-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 4rem;
      height: 0.25rem;
      background: linear-gradient(90deg, var(--primary-light), var(--primary-dark));
      border-radius: 1rem;
    }
    
    /* Improved Form Controls */
    .form-control {
      border-radius: var(--border-radius);
      padding: 0.75rem 1rem;
      border: 1px solid var(--border-color);
      transition: var(--transition);
      background-color: #f9fafb;
    }
    
    .form-control:focus {
      box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
      border-color: var(--primary-light);
      background-color: var(--text-light);
    }
    
    /* Enhanced Feature Icons */
    .feature-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 4.5rem;
      height: 4.5rem;
      margin-bottom: 1.25rem;
      color: var(--primary-color);
      background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.2));
      border-radius: 50%;
      transition: var(--transition);
      box-shadow: 0 4px 6px rgba(59, 130, 246, 0.1);
    }
    
    .card:hover .feature-icon {
      transform: scale(1.1);
      background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(37, 99, 235, 0.3));
    }
    
    .feature-icon i {
      font-size: 1.75rem;
    }
    
    /* Improved Dropzone */
    .dropzone {
      border-radius: var(--border-radius);
      border: 2px dashed rgba(59, 130, 246, 0.3) !important;
      transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
      background-color: rgba(59, 130, 246, 0.05) !important;
      min-height: 180px;
      padding: 2.5rem 1.5rem;
      position: relative;
      z-index: 10 !important;
      cursor: pointer !important;
      pointer-events: auto !important;
    }
    
    .dropzone::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      background: linear-gradient(120deg, rgba(59, 130, 246, 0.05), rgba(37, 99, 235, 0.1));
      border-radius: var(--border-radius);
      z-index: -1;
      opacity: 0;
      transition: var(--transition);
    }
    
    .dropzone:hover {
      transform: scale(1.02);
      border-color: rgba(59, 130, 246, 0.6) !important;
    }
    
    .dropzone:hover::before {
      opacity: 1;
    }
    
    .dropzone .dz-message {
      margin: 1.5rem 0;
      pointer-events: none;
    }
    
    .dropzone .dz-message .dz-button {
      font-weight: 600;
      color: var(--primary-color);
      font-size: 1.1rem;
      pointer-events: none;
    }
    
    /* Make sure children don't block clicks */
    .dropzone * {
      pointer-events: none;
    }
    
    /* Enhanced Step Indicator */
    .step-indicator {
      display: flex;
      justify-content: space-between;
      margin-bottom: 2.5rem;
      position: relative;
      padding: 0 2rem;
    }
    
    .step-indicator::before {
      content: '';
      position: absolute;
      top: 1.25rem;
      left: 3rem;
      right: 3rem;
      height: 0.25rem;
      background: linear-gradient(90deg, rgba(59, 130, 246, 0.3), rgba(37, 99, 235, 0.1));
      border-radius: 1rem;
      z-index: 1;
    }
    
    .step {
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      z-index: 2;
      transition: var(--transition);
    }
    
    .step-number {
      width: 2.75rem;
      height: 2.75rem;
      border-radius: 50%;
      background-color: #e9ecef;
      display: flex;
      justify-content: center;
      align-items: center;
      font-weight: bold;
      color: var(--text-muted);
      margin-bottom: 0.75rem;
      transition: var(--transition);
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
      position: relative;
    }
    
    .step.active .step-number {
      background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
      color: var(--text-light);
      transform: scale(1.2);
      box-shadow: 0 0 0 5px rgba(59, 130, 246, 0.15);
    }
    
    .step.active .step-number::after {
      content: '';
      position: absolute;
      width: 100%;
      height: 100%;
      border-radius: 50%;
      background: var(--primary-color);
      z-index: -1;
      opacity: 0.3;
      animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
      0% {
        transform: scale(1);
        opacity: 0.5;
      }
      50% {
        transform: scale(1.4);
        opacity: 0;
      }
      100% {
        transform: scale(1);
        opacity: 0;
      }
    }
    
    .step-title {
      font-size: 0.95rem;
      color: var(--text-muted);
      font-weight: 500;
      transition: var(--transition);
    }
    
    .step.active .step-title {
      color: var(--primary-color);
      font-weight: 600;
    }
    
    /* Enhanced Hero Section */
    .hero-section {
      background: linear-gradient(135deg, #f0f5ff 0%, #eef2ff 50%, #e0eafc 100%);
      padding: 4rem 0;
      margin-bottom: 3rem;
      border-bottom: 1px solid rgba(59, 130, 246, 0.1);
      position: relative;
      overflow: hidden;
    }
    
    .hero-section::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      background-image: url('data:image/svg+xml,%3Csvg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="%233b82f6" fill-opacity="0.05" fill-rule="evenodd"%3E%3Cpath d="M0 40L40 0H20L0 20M40 40V20L20 40"/%3E%3C/g%3E%3C/svg%3E');
      z-index: 0;
    }
    
    .hero-content {
      position: relative;
      z-index: 1;
    }
    
    .hero-image {
      transform: perspective(1000px) rotateY(-10deg);
      box-shadow: var(--box-shadow-lg);
      transition: var(--transition);
      border: 5px solid white;
    }
    
    .hero-image:hover {
      transform: perspective(1000px) rotateY(0deg);
    }
    
    .display-5 {
      font-weight: 800;
      background: linear-gradient(90deg, var(--primary-dark), var(--primary-color));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      letter-spacing: -0.5px;
      line-height: 1.2;
    }
    
    /* Enhanced QR Code */
    #qrcode {
      padding: 1.25rem;
      background-color: white;
      border-radius: var(--border-radius);
      display: inline-block;
      box-shadow: var(--box-shadow);
      position: relative;
      transition: var(--transition);
    }
    
    #qrcode::before {
      content: '';
      position: absolute;
      top: -8px;
      left: -8px;
      right: -8px;
      bottom: -8px;
      border: 2px dashed rgba(59, 130, 246, 0.2);
      border-radius: var(--border-radius);
      z-index: -1;
    }
    
    #qrcode:hover {
      transform: scale(1.05);
      box-shadow: var(--box-shadow-lg);
    }
    
    /* Enhanced Footer */
    footer {
      background: linear-gradient(135deg, #1e293b, #0f172a);
      color: var(--text-light);
      padding: 3rem 0 1.5rem;
      margin-top: 4rem;
      position: relative;
      overflow: hidden;
    }
    
    footer::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM40 4V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
    }
    
    footer a {
      color: rgba(255, 255, 255, 0.7);
      transition: var(--transition);
    }
    
    footer a:hover {
      color: var(--primary-light);
      text-decoration: none;
    }
    
    .social-icons a {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 2.5rem;
      height: 2.5rem;
      border-radius: 50%;
      background-color: rgba(255, 255, 255, 0.1);
      color: var(--text-light);
      transition: var(--transition);
    }
    
    .social-icons a:hover {
      background-color: var(--primary-color);
      transform: translateY(-3px);
    }
    
    /* Animation Effects */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translate3d(0, 20px, 0);
      }
      to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
      }
    }
    
    .fadeInUp {
      animation: fadeInUp 0.6s ease-out;
    }
    
    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }
    
    .fadeIn {
      animation: fadeIn 0.8s ease-out;
    }
    
    @keyframes slideInLeft {
      from {
        transform: translate3d(-40px, 0, 0);
        opacity: 0;
      }
      to {
        transform: translate3d(0, 0, 0);
        opacity: 1;
      }
    }
    
    .slideInLeft {
      animation: slideInLeft 0.8s ease-out;
    }
    
    @keyframes slideInRight {
      from {
        transform: translate3d(40px, 0, 0);
        opacity: 0;
      }
      to {
        transform: translate3d(0, 0, 0);
        opacity: 1;
      }
    }
    
    .slideInRight {
      animation: slideInRight 0.8s ease-out;
    }
    
    /* Enhanced Responsive Design */
    @media (max-width: 768px) {
      .card {
        margin-bottom: 1.5rem;
      }
      
      .step-indicator {
        flex-direction: column;
        align-items: flex-start;
        padding: 0 1rem;
      }
      
      .step-indicator::before {
        display: none;
      }
      
      .step {
        flex-direction: row;
        margin-bottom: 1.25rem;
        width: 100%;
      }
      
      .step-number {
        margin-right: 1rem;
        margin-bottom: 0;
      }
      
      .py-5 {
        padding-top: 2rem !important;
        padding-bottom: 2rem !important;
      }
      
      .display-5 {
        font-size: calc(1.375rem + 1.5vw);
      }
      
      .hero-section {
        padding: 2.5rem 0;
      }
      
      .hero-image {
        margin-top: 2rem;
        transform: none;
      }
    }
    
    /* Skeleton Loading Animation */
    @keyframes skeletonLoading {
      0% { background-position: -200% 0; }
      100% { background-position: 200% 0; }
    }
    
    .skeleton-loading {
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200% 100%;
      animation: skeletonLoading 1.5s infinite;
      border-radius: var(--border-radius);
    }
    
    /* Custom Scrollbar */
    ::-webkit-scrollbar {
      width: 10px;
    }
    
    ::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 5px;
    }
    
    ::-webkit-scrollbar-thumb {
      background: #c5c5c5;
      border-radius: 5px;
      transition: var(--transition);
    }
    
    ::-webkit-scrollbar-thumb:hover {
      background: #a3a3a3;
    }
    
    /* Toast Notifications */
    .toast-container {
      position: fixed;
      bottom: 2rem;
      right: 2rem;
      z-index: 9999;
    }
    
    .toast {
      background: white;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow-lg);
      padding: 1rem 1.5rem;
      margin-top: 1rem;
      display: flex;
      align-items: center;
      animation: toastIn 0.3s forwards;
      border-left: 4px solid var(--primary-color);
    }
    
    @keyframes toastIn {
      from {
        opacity: 0;
        transform: translateX(100%);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }
  </style>
</head>

<body id="page-top">
  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="maipdf.php">
        <div>
          <h1 class="brand-name m-0">MaiPDF</h1>
          <small class="brand-tagline d-block">Secure PDF Sharing Solution</small>
        </div>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <div id="userInfo" class="my-2 my-lg-0"></div>
          </li>
          <li class="nav-item">
            <button type="button" class="btn btn-light mx-1" id="loginBtn">
              <i class="fas fa-sign-in-alt me-2"></i> Login
            </button>
          </li>
          <li class="nav-item">
            <button class="btn btn-outline-light mx-1" id="logoutBtn" style="display:none;">
              <i class="fas fa-sign-out-alt me-2"></i> Log Out
            </button>
          </li>
          <li class="nav-item">
            <a href='../6/list.php' class='btn btn-outline-light mx-1' id="controlpanel" style="display:none;">
              <i class="fas fa-cog me-2"></i> Control Panel
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <div class="hero-section">
    <div class="hero-content container">
      <div class="row align-items-center">
        <div class="col-lg-6 mb-4 mb-lg-0">
          <h1 class="display-5 fw-bold mb-3">Share PDFs Securely</h1>
          <p class="lead mb-4">Control who views your documents with advanced security features, time limits, and access tracking.</p>
          <div class="d-grid gap-2 d-sm-flex">
            <a href="#section1" class="btn btn-primary btn-lg px-4 me-sm-3">
              <i class="fas fa-upload me-2"></i> Get Started
            </a>
            <a href="#features" class="btn btn-outline-primary btn-lg px-4">
              <i class="fas fa-shield-alt me-2"></i> Learn More
            </a>
          </div>
        </div>
        <div class="col-lg-6 text-center">
          <img src="img/bg-masthead2.jpg" class="img-fluid rounded shadow hero-image" alt="Secure PDF Sharing" style="max-height: 300px;">
        </div>
      </div>
    </div>
  </div>

  <!-- Process Steps Indicator -->
  <div class="container my-5">
    <div class="step-indicator">
      <div class="step active" id="step1">
        <div class="step-number">1</div>
        <div class="step-title">Upload File</div>
      </div>
      <div class="step" id="step2">
        <div class="step-number">2</div>
        <div class="step-title">Configure Settings</div>
      </div>
      <div class="step" id="step3">
        <div class="step-number">3</div>
        <div class="step-title">Share PDF</div>
      </div>
    </div>
  </div>

  <!-- Feature Highlights Section -->
  <section id="features" class="py-5 bg-light">
    <div class="container">
      <h2 class="section-title mb-5">Security Features</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="text-center">
            <div class="feature-icon">
              <i class="fas fa-clock"></i>
            </div>
            <h3 class="h5 mb-3">Time-Limited Access</h3>
            <p class="text-muted">Set expiration times and limit the number of times your PDF can be opened.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="text-center">
            <div class="feature-icon">
              <i class="fas fa-lock"></i>
            </div>
            <h3 class="h5 mb-3">Dynamic Watermarking</h3>
            <p class="text-muted">Display viewer's IP address and timestamp directly on the document.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="text-center">
            <div class="feature-icon">
              <i class="fas fa-shield-alt"></i>
            </div>
            <h3 class="h5 mb-3">Email Verification</h3>
            <p class="text-muted">Restrict access to specific email addresses for enhanced security.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Breadcrumbs -->
  <div class="container">
    <ul class="breadcrumb p-0 d-flex justify-content-center border-bottom pb-2">
      <li class="breadcrumb-item"><a class="link-secondary" href="https://maipdf.com/est/k6776416f71665@pdf">FenceView</a></li>
      <li class="breadcrumb-item"><a class="link-secondary" href="https://maipdf.com/est/a677641030889c@pdf">SafeLink</a></li>
      <li class="breadcrumb-item"><a class="link-secondary" href="https://maipdf.com/est/d67764148dd446@pdf">OpenLink</a></li>
    </ul>
  </div>

  <!-- Step 1: Upload Section -->
  <section class="py-5 container" id="section1">
    <div class="row justify-content-center">
      <div class="col-lg-8 col-md-10">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white text-center py-3">
            <h4 class="m-0"><i class="fas fa-cloud-upload-alt text-primary me-2"></i>Step 1: Upload Your PDF</h4>
          </div>
          <div class="card-body p-5">
            <div class="form-group">
              <label class="title mb-3 fw-bold" id="pleaseupload"></label>
              <div id="dropz" class="dropzone"></div>
            </div>
            <input type="hidden" name="file_id" ng-model="file_id" id="file_id"/>
          </div>
        </div>
      </div>
    </div>
  </section>
<script>
  document.cookie="uploadedfile=notyet"; 
var zhuceid=1;
//setInterval(zhucece, 1000);
  function zhucece(){
  
  if(zhuceid==1){
   document.getElementById("zhuce").className = "btn btn-warning btn-xl js-scroll-trigger";
   zhuceid=2;
  }else{
    document.getElementById("zhuce").className = "btn btn-danger btn-xl js-scroll-trigger";
    zhuceid=1;
  }
  }



  var bt='xxx';
  var appElement = document.querySelector('div .inmodal');



    var myDropzone = new Dropzone("#dropz", {
         url: "onlyupload.php",//文件提交地址
        method:"post",  //也可用put
        paramName:"file", //默认为file
        maxFiles:1,//一次性上传的文件数量上限
        maxFilesize: 82, //文件大小，单位：MB
    //chunking: true,
    //forceChunking: true,
    //chunkSize: 256000,
    //retryChunks: true,
       // retryChunksLimit: 3,
      
        acceptedFiles: ".png,.jpg,.jpeg,.gif,image/*,.pdf",
        addRemoveLinks:true,
    //retryChunksLimit: 3,
        parallelUploads: 1,//一次上传的文件数量
        //previewsContainer:"#preview",//上传图片的预览窗口
        //dictDefaultMessage:'拖动文件至此或者点击上传 <br><br><small style="font-style: italic;">建议上传之前自行压缩一下</small>  ',
        dictDefaultMessage:'Choose File<br><small style="font-style: italic;">Or Drop File Here</small> ',
        dictMaxFilesExceeded: "One File！",
        dictResponseError: 'Failed!',
        dictInvalidFileType: "only with *.pdf,*.png,*.jpeg。",
        dictFallbackMessage:"You have an Antique Browser",
        dictFileTooBig:"Reach Size Limit.",
        dictRemoveLinks: "Delete",
        dictCancelUpload: "Cancel",
        timeout: 190000,
        


        init:function(){
            this.on("addedfile", function(file) {
                //上传文件时触发的事件
                document.querySelector('div .dz-default').style.display = 'none';
        if(file.name.endsWith('.PDF')){
          alert('.PDF extention cannot be in Capital');
          return false;
        }
        if(file.name.includes('#')){
          alert('Remove the Special character in File Name');
          simulateTyping("pleaseupload","Remove the Special character in File Name", 180);
          return false;
        }
        //console.log(file.name.length);console.log(file.name);console.log(file.size/1024/1024);
        if(file.size/1024/1024 > 80){
          simulateTyping("pleaseupload","Please Upload Pdf files within 90M", 150);
          return false;
        }
        if(file.name.length== 17 && file.name.startsWith('16458')){
          localStorage.setItem("shenfen", "bad");
          window.location.href = "../bad.html";   
        }
            });
            this.on("success",function(file,data){
        
        //console.log('upload good');
            document.cookie="uploadedfile=success"; 
            //document.getElementById('anquan').click();
      
            var a = fileplaceSHOW + file.name;

            if(file.name=='作品集.pdf' || file.name=='简历.pdf'|| file.name=='作品.pdf'){
          document.getElementById("2step").innerHTML ="该文件名<br>容易与其它文件发生冲突，请尽量修改名字" ;
          document.getElementById("2step").style.color="green";
          simulateTyping("2step","可以将文件重新命名之后进行上传", 100);
          simulateTyping("2step3","系统中以用此文件名命名的文件已经太多了", 100);
         
      
        }
        document.getElementById("name").value = a;
        simulateTyping("pleaseupload","Uploaded Successfully", 180);
        
        // Automatically move to next section
        document.getElementById('section2').style.display = 'block';
        document.getElementById('section1').style.display = 'none';
        
        // Update progress indicators
        document.getElementById('step1').classList.remove('active');
        document.getElementById('step2').classList.add('active');
        
        // Scroll to settings section
        document.getElementById('section2').scrollIntoView({behavior: 'smooth'});
        
        simulateTyping("2step","Uploaded Successfully\nSecond Step: Set Up reading times and each period of length", 180);
            });
            this.on("error",function (file,data) {
                //上传失败触发的事件
                console.log('fail');
                var message = '';
                if (file.accepted){
                    $.each(data,function (key,val) {
                        message = message + val[0] + ';';
                    })
                    alert(message);
                }
            });
            this.on("removedfile",function(file){
                //删除文件时触发的方法
                var file_id = angular.element(appElement).scope().file_id;
                if (file_id){
                    $.post('/admin/del/'+ file_id,{'_method':'DELETE'},function (data) {
                        console.log('Deleted:'+data.message);
                    })
                }
                angular.element(appElement).scope().file_id = 0;
                document.querySelector('div .dz-default').style.display = 'block';
            });

            //return 0;
        }
    });

</script>
  <!-- Step 2: Settings Section -->
  <section class="container" id="section2">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white text-center py-3">
            <h4 class="m-0" id="2step"><i class="fas fa-cog text-primary me-2"></i>Step 2: Configure Security Settings</h4>
          </div>
          <div class="card-body p-4">
            <form role="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
              <div class="text-center mb-4">
                <input type="text" class="form-control text-center w-50 mx-auto" id="name" name="sender" value="File" readonly="readonly">
              </div>
              
              <div class="row g-4" id="2step3">
                <div class="col-md-3 col-sm-6">
                  <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                      <div class="feature-icon mb-3 mx-auto">
                        <i class="fas fa-folder-open"></i>
                      </div>
                      <h5 class="card-title fw-bold mb-4">Access Limit</h5>
                      <div class="form-floating mb-3">
                        <input class="form-control" type="number" id="limit" name="limit" placeholder="Number of Opens">
                        <label for="limit">Number of Opens</label>
                      </div>
                      
                      <div class="form-floating">
                        <input class="form-control" type="number" name="password" placeholder="in (seconds)">
                        <label>Session Duration (seconds)</label>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                  <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                      <div class="feature-icon mb-3 mx-auto">
                        <i class="fas fa-lock"></i>
                      </div>
                      <h5 class="card-title fw-bold mb-4">Security Mode</h5>
                      <div class="form-check mb-3 d-flex align-items-center justify-content-center gap-2">
                        <input class="form-check-input" type="checkbox" name="darkmode" id="darkmode" value="yes">
                        <label class="form-check-label" for="darkmode">Dynamic Watermark</label>
                      </div>
                      
                      <div class="d-flex flex-column gap-2">
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="zhangai" id="secureView" value="straight" checked>
                          <label class="form-check-label" for="secureView">SecureView</label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="zhangai" id="fenceView" value="obstacle">
                          <label class="form-check-label" for="fenceView">FenceView</label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="zhangai" id="unrestricted" value="topen">
                          <label class="form-check-label" for="unrestricted">Unrestricted</label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                  <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                      <div class="feature-icon mb-3 mx-auto">
                        <i class="fas fa-shield-alt"></i>
                      </div>
                      <h5 class="card-title fw-bold mb-4">Email Verification</h5>
                      <div class="form-check mb-3 d-flex align-items-center justify-content-center gap-2">
                        <input class="form-check-input" type="checkbox" id="enableEmailValidation" name="enableEmailValidation" value="yes">
                        <label class="form-check-label" for="enableEmailValidation">Require email verification</label>
                      </div>
                      
                      <div id="emailAddressesInput" style="display: none;">
                        <textarea class="form-control mb-2" name="emailAddresses" placeholder="Enter up to 50 email addresses, separated by commas" rows="4"></textarea>
                        <small class="text-muted">Only these emails will be able to access the document</small>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                  <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                      <div class="feature-icon mb-3 mx-auto">
                        <i class="fas fa-bell"></i>
                      </div>
                      <h5 class="card-title fw-bold mb-4">Notifications</h5>
                      <div class="form-floating mb-4">
                        <input class="form-control" type="text" name="mailalert" placeholder="@ - optional">
                        <label>Email for notifications</label>
                      </div>
                      
                      <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-lock me-2"></i>Create Secure PDF
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </form>
            
            <div class="alert alert-info text-center mt-4" id="cishu">
              <i class="fas fa-info-circle me-2"></i>Unlimited opens will be applied if 'Access-Limit' is over 10k. No access records will be logged.
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  
  <!-- Step 3: Results Section -->
  <section class="container my-5" id="contact">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white text-center py-3">
            <h4 class="m-0" id="3step"><i class="fas fa-share-alt text-primary me-2"></i>Step 3: Share Your Secure PDF</h4>
          </div>
          <div class="card-body p-4">
            <div class="row">
              <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-4">
                    <h5 class="fw-bold mb-4">Direct Link</h5>
                    <div class="input-group mb-3">
                      <input type="text" class="form-control" value="<?php echo $pdflinkfull; ?>" id="myInput" readonly>
                      <button class="btn btn-primary" onclick="myFunction()">
                        <i class="fas fa-copy"></i>
                      </button>
                    </div>
                    <h5 id='Copied' class="text-success"></h5>
                    <div class="mt-4">
                      <div class="alert alert-light border">
                        <p class="mb-1">Password: <strong>"<?php $identifier2='joe'.$identifier; strlen($identifier)<2 ? print 'To Del.MOD Link': print crypt($identifier2,'su');  ?>"</strong></p>
                      </div>
                      <div class="d-flex justify-content-center gap-2 mt-4">
                        <a class="btn btn-sm btn-info" href="https://www.maipdf.com/pdf/hahachange.php" target="_blank">
                          <i class="fas fa-edit me-1"></i> Change File
                        </a>
                        <a class="btn btn-sm btn-outline-dark" href="https://maipdf.com/pdf/haha.php" target="_blank">
                          <i class="fas fa-chart-line me-1"></i> Access Records
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-4">
                    <h5 class="fw-bold mb-4">QR Code</h5>
                    <div class="mb-4">
                      <div id="qrcode" class="mx-auto"></div>
                    </div>
                    <p class="text-muted mb-4">Scan this QR code to access your secure PDF directly from mobile devices.</p>
                    <button class="btn btn-outline-primary" id="downloadQR">
                      <i class="fas fa-download me-2"></i>Download QR Code
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="py-5">
    <div class="container">
      <div class="row mb-4">
        <div class="col-lg-4 mb-4 mb-lg-0">
          <h5 class="text-uppercase mb-4">MaiPDF</h5>
          <p class="mb-4 text-muted">Secure PDF sharing solution with advanced protection features for your important documents.</p>
          <p class="small text-muted">© 2023-<?php echo date('Y'); ?> MaiPDF. All rights reserved.</p>
        </div>
        <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
          <h5 class="text-uppercase mb-4">Links</h5>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="#" class="text-decoration-none">Home</a></li>
            <li class="mb-2"><a href="#features" class="text-decoration-none">Features</a></li>
            <li class="mb-2"><a href="#section1" class="text-decoration-none">Upload</a></li>
            <li class="mb-2"><a href="../6/list.php" class="text-decoration-none">Dashboard</a></li>
          </ul>
        </div>
        <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
          <h5 class="text-uppercase mb-4">Support</h5>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="#" class="text-decoration-none">Help Center</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none">Terms of Service</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none">Privacy Policy</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none">Contact Us</a></li>
          </ul>
        </div>
        <div class="col-lg-4 col-md-6">
          <h5 class="text-uppercase mb-4">Stay Connected</h5>
          <p class="text-muted mb-3">Subscribe to our newsletter for updates</p>
          <div class="input-group mb-3">
            <input type="email" class="form-control" placeholder="Your email address">
            <button class="btn btn-primary" type="button">Subscribe</button>
          </div>
          <div class="social-icons d-flex gap-3 mt-4">
            <a href="#" class="text-decoration-none"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="text-decoration-none"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-decoration-none"><i class="fab fa-linkedin-in"></i></a>
            <a href="#" class="text-decoration-none"><i class="fab fa-instagram"></i></a>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <!-- Login Modal -->
  <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Login to MaiPDF</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <form id="loginForm">
            <div class="form-floating mb-3">
              <input type="email" class="form-control" id="loginEmail" placeholder="name@example.com" required>
              <label for="loginEmail">Email address</label>
            </div>
            <div class="form-floating mb-4">
              <input type="password" class="form-control" id="loginPassword" placeholder="Password" required>
              <label for="loginPassword">Password</label>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="rememberMe">
                <label class="form-check-label" for="rememberMe">Remember me</label>
              </div>
              <a href="#" class="text-primary text-decoration-none">Forgot password?</a>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
          </form>
          <div class="text-center mt-4">
            <p class="mb-0 text-muted">Don't have an account? <a href="#" class="text-primary">Sign up</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script>
    // Initialize Dropzone
    document.addEventListener('DOMContentLoaded', function() {
      // Email validation toggle
      document.getElementById('enableEmailValidation').addEventListener('change', function() {
        document.getElementById('emailAddressesInput').style.display = this.checked ? 'block' : 'none';
      });

      // Login modal
      document.getElementById('loginBtn').addEventListener('click', function() {
        var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
        loginModal.show();
      });

      // Copy to clipboard function
      window.myFunction = function() {
        var copyText = document.getElementById("myInput");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        document.getElementById("Copied").innerHTML = "Link copied!";
        setTimeout(function() {
          document.getElementById("Copied").innerHTML = "";
        }, 2000);
      }

      // QR code download
      document.getElementById('downloadQR').addEventListener('click', function() {
        var qrImage = document.querySelector("#qrcode img");
        if (qrImage) {
          var link = document.createElement('a');
          link.download = 'secure-pdf-qrcode.png';
          link.href = qrImage.src;
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        } else {
          alert('QR code not generated yet');
        }
      });

      // Initialize sections
      // Initially hide section2 and section3 (Results) unless coming from form submission
      var formProcessed = <?php echo isset($_SESSION['form_processed']) && $_SESSION['form_processed'] ? 'true' : 'false'; ?>;
      
      if (!formProcessed) {
        document.getElementById('section2').style.display = 'none';
        document.getElementById('contact').style.display = 'none';
      }
      
      // Email validation toggle (repeated in case nested DOMContentLoaded doesn't fire)
      document.getElementById('enableEmailValidation').addEventListener('change', function() {
        document.getElementById('emailAddressesInput').style.display = this.checked ? 'block' : 'none';
      });

      // Form submission event handler for UX enhancement
      document.querySelector('form[action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"]').addEventListener('submit', function() {
        // Show loading state on submit button
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        submitButton.disabled = true;
      });
    });

    // Add this function for the typing animation effect
    function simulateTyping(elementId, text, speed) {
      let element = document.getElementById(elementId);
      if (!element) return;
      
      element.innerHTML = '';
      let i = 0;
      
      function typeWriter() {
        if (i < text.length) {
          element.innerHTML += text.charAt(i);
          i++;
          setTimeout(typeWriter, speed);
        }
      }
      
      typeWriter();
    }
  </script>
</body>
</html>
