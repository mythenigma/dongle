<?php 
$target = 'maipdf2026.html';
if (!empty($_SERVER['QUERY_STRING'])) {
    $target .= '?' . $_SERVER['QUERY_STRING'];
}
header('Location: ' . $target, true, 302);
exit;

// Preserve all PHP functionality at the top
ini_set("display_errors", true);
ini_set("html_errors", false); 

// Set PHP charset and encoding
ini_set('default_charset', 'utf-8');
mb_internal_encoding('UTF-8');

// Set HTTP response header encoding
header('Content-Type: text/html; charset=utf-8');



// Initialize variables to prevent undefined variable warnings
$identifier = '';
$messagebox = "Don't forget <span style='color:green';> Create</span> in Step2";
$pdflinkshort = "";
$pdflinkfull = "";
$formSubmitted = false; // Flag to track if form has been submitted

$year = date("Y");
$month = date("m");
$week = date("d");

//英文版只有一个地址
$fileplaceSHOW = "/".$year."/".$month."/".$week."/";
$fileplace = "yes/".$year."/".$month."/".$week."/";
$picplace = "yes/".$year."/".$month."/".$week."/preview/";
$encryfile = '';

// 369 行也有 badlist 在上传前检查
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
        
        $url = $_POST['sender'];
        $zhangai = $_POST['zhangai'];
        $identifier = uniqid();
        
        if($_COOKIE["uploadedfile"] == "notyet") {
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
        
        if(isset($_POST['mailalert'])) {
            $mailalert = $_POST['mailalert'];
            $injectionChars = array('(', ')', '/', '*', '%', '&', "'", '#');
            foreach($injectionChars as $char) {
                if(strpos($mailalert, $char) !== false) {
                    $mailalert = '1998';
                }
            }
        } else {
            $mailalert = '1998';
        }
        
        // Email addresses handling
        $emailAddresses = $_POST['emailAddresses'] ?? '';
        $emailAddresses = trim($emailAddresses);
        $emailAddresses = str_replace('，', ',', $emailAddresses);
        
        $byteLength = strlen($emailAddresses);
        if($byteLength > 3500) {
            echo "输入的邮箱列表过长，不能超过 3500 字节。";
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
        
        if($zhangai == 'obstacle') {
            $identifier = 'k'.$identifier;
        } elseif($zhangai == 'topen') {
            $identifier = 'd'.$identifier;
        } else {
            $identifier = 'a'.$identifier;
        }
        
        $pdflinkshort = "maipdf.com/file/".$identifier."@pdf";
        $pdflinkfull = "https://maipdf.com/file/".$identifier."@pdf";
        
        // ========== 统一的 R2 上传处理逻辑 ==========
        // 优先使用 preview 目录中的处理后文件，如果不存在则使用原文件
        $previewFile = $picplace . basename($url); // preview目录中的文件
        $originalFile = 'yes'.$url; // 原始文件
        
        // 判断使用哪个文件进行 R2 上传
        if (file_exists($previewFile) && filesize($previewFile) > 3072) {
            $finalFileForR2 = $previewFile;
            echo "<script>console.log('📂 Using processed file from preview directory: " . addslashes($finalFileForR2) . " (" . number_format(filesize($previewFile)) . " bytes)');</script>";
        } else {
            $finalFileForR2 = $originalFile;
            if (file_exists($previewFile) && filesize($previewFile) <= 3072) {
                echo "<script>console.log('⚠️ Preview file too small (" . filesize($previewFile) . " bytes), using original file instead');</script>";
            }
            echo "<script>console.log('📂 Using original file: " . addslashes($finalFileForR2) . "');</script>";
        }
        
        if(!file_exists($finalFileForR2)) {
            echo "<script>console.error('❌ Source file not found for R2 upload: " . addslashes($finalFileForR2) . "');</script>";
        } else {
            echo "<script>console.log('📂 Found source file for R2 upload: " . addslashes($finalFileForR2) . " (" . number_format(filesize($finalFileForR2)) . " bytes)');</script>";
        }
        
        if($zhangai != 'topen') {
            // ========== 需要加密的情况 ==========
            // 注意：如果使用的是preview文件，我们需要对其进行加密
            $fileToEncrypt = $finalFileForR2;  // 保存原始文件路径，用于加密失败时回退
            
            if(filesize($fileToEncrypt) < 3797152) {
                try {
                    // 使用 qpdf 进行加密（替换mPDF方案）
                    $encryptedTempFile = $fileToEncrypt . '.encrypted';
                    
                    // qpdf 加密命令 - 使用256位加密，避免弱加密算法警告
                    $qpdfEncryptCommand = "qpdf --encrypt " . 
                                         escapeshellarg('guaguashimaimai') . " " . 
                                         escapeshellarg('qweewqer') . " " . 
                                         "256 --print=none --modify=none --extract=n -- " . 
                                         escapeshellarg($fileToEncrypt) . " " . 
                                         escapeshellarg($encryptedTempFile) . " 2>&1";
                    
                    exec($qpdfEncryptCommand, $qpdfOutput, $qpdfReturnCode);
                    
                    if ($qpdfReturnCode === 0 && file_exists($encryptedTempFile)) {
                        // ✅ 加密成功：使用加密后的文件替换原文件
                        if (rename($encryptedTempFile, $fileToEncrypt)) {
                            echo "<script>console.log('✅ PDF encryption successful using qpdf');</script>";
                            $finalFileForR2 = $fileToEncrypt; // 使用加密后的文件进行 R2 上传
                        } else {
                            throw new Exception("Failed to replace file with encrypted version");
                        }
                    } else {
                        throw new Exception("qpdf encryption failed: " . implode(' ', $qpdfOutput));
                    }
                    
                } catch(Exception $e) {
                    // ❌ 加密失败处理：保持使用原始上传的文件
                    echo "<script>console.error('❌ PDF encryption failed: " . addslashes($e->getMessage()) . "');</script>";
                    echo "<script>console.warn('⚠️ Continuing with unencrypted PDF for R2 upload');</script>";
                    // 【重要】不修改 $finalFileForR2，继续使用原始文件（$fileToEncrypt）
                    // $finalFileForR2 保持原值不变，即使加密失败也能正常上传原文件
                }
            } else {
                // 文件太大，跳过加密，直接使用原文件
                echo "<script>console.warn('⚠️ File too large for encryption, uploading unencrypted to R2');</script>";
                // $finalFileForR2 保持原值，继续使用未加密的原文件
            }
        } else {
            // ========== 不需要加密的情况 ==========
            echo "<script>console.log('📄 File set to unrestricted mode, uploading original file to R2');</script>";
            // $finalFileForR2 保持原值，使用原文件进行 R2 上传
        }
        
        // ========== 统一的 R2 上传逻辑 ==========
        try {
            echo "<script>console.log('🚀 Starting R2 upload process...');</script>";
            
            // 读取最终文件内容（可能是加密后的，也可能是原文件）
            $fileContentForR2 = file_get_contents($finalFileForR2);
            if ($fileContentForR2 !== false) {
                
                // 获取文件名，如果是preview目录的文件，需要计算正确的上传路径
                if (strpos($finalFileForR2, 'preview/') !== false) {
                    // 如果是preview目录的文件，使用原始文件名
                    $uploadFileName = basename($finalFileForR2);
                    // 计算没有preview的服务器路径用于R2存储
                    $serverPathForR2 = $fileplace; // 不包含preview的路径
                    echo "<script>console.log('📁 Using preview file, server path: " . addslashes($serverPathForR2) . "');</script>";
                } else {
                    // 如果是原始文件，使用正常路径
                    $uploadFileName = basename($finalFileForR2);
                    $serverPathForR2 = $fileplace;
                    echo "<script>console.log('📁 Using original file, server path: " . addslashes($serverPathForR2) . "');</script>";
                }
                
                // 设置 R2 Worker URL
                $workerUrl = 'https://grabb.site/upload';
                
                // 动态检测当前域名并设置合适的Origin头
                $currentDomain = $_SERVER['HTTP_HOST'] ?? 'maipdf.com';
                $originHeader = "https://" . $currentDomain;
                
                // 确保使用已知的允许域名
                $allowedDomains = ['maipdf.com', 'www.maipdf.com', 'privnote.maipdf.com'];
                if (!in_array($currentDomain, $allowedDomains)) {
                    $originHeader = "https://maipdf.com"; // 默认使用主域名
                }
                
                // 创建 multipart/form-data 请求
                $boundary = uniqid();
                $postData = '';
                
                // 添加文件数据
                $postData .= "--$boundary\r\n";
                $postData .= "Content-Disposition: form-data; name=\"file\"; filename=\"$uploadFileName\"\r\n";
                $postData .= "Content-Type: application/pdf\r\n\r\n";
                $postData .= $fileContentForR2 . "\r\n";
                
                // 添加服务器路径参数
                $postData .= "--$boundary\r\n";
                $postData .= "Content-Disposition: form-data; name=\"server_path\"\r\n\r\n";
                $postData .= $serverPathForR2 . "\r\n";
                
                // 添加时间戳参数
                $postData .= "--$boundary\r\n";
                $postData .= "Content-Disposition: form-data; name=\"server_timestamp\"\r\n\r\n";
                $postData .= time() . "\r\n";
                
                // 添加文件类型标识
                $postData .= "--$boundary\r\n";
                $postData .= "Content-Disposition: form-data; name=\"file_type\"\r\n\r\n";
                $fileType = ($zhangai != 'topen') ? 'encrypted_pdf' : 'standard_pdf';
                $postData .= $fileType . "\r\n";
                
                // 添加处理标识
                $postData .= "--$boundary\r\n";
                $postData .= "Content-Disposition: form-data; name=\"processed_by\"\r\n\r\n";
                $postData .= "maipdf_unified\r\n";
                
                $postData .= "--$boundary--\r\n";
                
                // 发送请求到 R2 Worker
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
                
                echo "<script>console.log('📤 Sending file to R2: " . addslashes($uploadFileName) . " (" . strlen($fileContentForR2) . " bytes)');</script>";
                
                // 执行上传请求
                $response = file_get_contents($workerUrl, false, $context);
                
                if ($response !== false) {
                    $workerResult = json_decode($response, true);
                    if ($workerResult && $workerResult['status'] === 'upload_success') {
                        // 文件成功上传到 R2
                        echo "<script>console.log('✅ File successfully uploaded to R2: " . addslashes($uploadFileName) . "');</script>";
                        echo "<script>console.log('🌐 File now available on both local server and Cloudflare R2');</script>";
                    } else {
                        // 上传失败，记录日志但不中断流程
                        $errorMsg = $workerResult['message'] ?? 'Unknown error';
                        echo "<script>console.warn('❌ Failed to upload file to R2: " . addslashes($errorMsg) . "');</script>";
                        echo "<script>console.log('💾 File remains available on local server');</script>";
                    }
                } else {
                    echo "<script>console.error('❌ Failed to connect to R2 Worker');</script>";
                    echo "<script>console.log('💾 File remains available on local server');</script>";
                }
            } else {
                echo "<script>console.error('❌ Failed to read file content for R2 upload');</script>";
            }
        } catch (Exception $r2Exception) {
            // R2上传失败不影响主流程，只记录错误
            echo "<script>console.error('❌ R2 upload exception: " . addslashes($r2Exception->getMessage()) . "');</script>";
            echo "<script>console.log('💾 File processing completed, available on local server');</script>";
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
      <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.7/js/bootstrap.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.7/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Dropzone -->
    <script src="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/dropzone.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/dropzone-amd-module.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/dropzone.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/basic.css">
    
    <!-- QR Code -->
    <script type="text/javascript" src="qrcode.min.js"></script>
    
    <!-- Google AdSense -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9224406325142860" crossorigin="anonymous"></script>
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="maipdf.css">
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
                <i class="fas fa-cogs me-1"></i> ALT PDF Settings
            </a>
            <a href="../watermark.html" class="btn btn-hero-tertiary" target="_blank">
                <i class="fas fa-search me-1"></i> Query Watermark
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
            <form role="form" action="maipdf.php" method="post">
                <h2 id="2step">2: Set Up Reading Times and Each Period of Length</h2>
                
                <input type="text" class="form-control text-center" id="name" name="sender" value="File" readonly="readonly">
                
                <div class="settings-grid" id="2step3">
                    <div class="setting-box">
                        <div class="setting-icon">
                            <i class="fas fa-folder-open fa-icon"></i>
                        </div>
                        <h3>Access Limit</h3>
                        <input class="form-control" type="number" id="limit" name="limit" placeholder="Number of Opens">
                        
                        <div class="mt-4">
                            <i class="fas fa-user-clock fa-icon"></i>
                            <h3>Each Session</h3>
                            <input class="form-control" type="number" name="password" placeholder="in (seconds)">
                        </div>
                    </div>
                    
                    <div class="setting-box">
                        <div class="setting-icon">
                            <i class="fas fa-lock fa-icon"></i>
                        </div>
                        <h3>Protection Type</h3>
                        <div class="protection-options">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="darkmode" value="yes">
                                <label class="form-check-label">Dynamowatermark</label>
                            </div>
                            
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="zhangai" value="straight" checked>
                                    <label class="form-check-label">SecureView</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="zhangai" value="obstacle">
                                    <label class="form-check-label">FenceView</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="zhangai" value="topen">
                                    <label class="form-check-label">Unrestricted</label>
                                </div>
                            </div>
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
                    </div>
                    
                    <!-- Notification Section -->
                    <div class="setting-box">
                        <div class="setting-icon">
                            <i class="fas fa-bell fa-icon"></i>
                        </div>
                        <h3>Read Notification</h3>
                        <input class="form-control mb-3" type="text" name="mailalert" placeholder="Email for notifications (optional)">
                        
                        <button type="submit" class="btn btn-feature-start">Create Secure Link</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
    
    <div class="section-divider">
        <p id="cishu">↡Unlimited open will be applied if 'Access-Limit' is over 10k, and no access record will be logged↡</p>
    </div>
    
    <!-- Results Section - Only shown after form submission -->
    <section class="cta-section" id="contact" style="<?php echo ($formSubmitted && isset($pdflinkfull) && strlen($pdflinkfull) > 0) ? '' : 'display:none;'; ?>">
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
                        <h6 class="mt-3"><?php echo isset($messagebox) ? $messagebox : ''; ?></h6>                        <h5 class="mb-3">Password: "<?php $identifier2 = isset($identifier) ? 'joe'.$identifier : 'joe'; echo (strlen(isset($identifier) ? $identifier : '') < 2) ? 'To Del.MOD Link' : crypt($identifier2,'su'); ?>"</h5>                        <div class="action-buttons-container">
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
                    <h5 class="modal-title">访问优化建议</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>检测到您可能位于中国大陆，推荐访问中文站点获得更快的访问速度！</p>
                    <p>当前站点：国际站（全球加速）</p>
                    <p>推荐站点：中文站（中国大陆优化）</p>
                </div>
                <div class="modal-footer">
                    <a href="https://maipdf.cn/maifile.php" class="btn btn-danger" target="_blank" rel="noopener">
                        🇨🇳 立即访问中文站
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        继续留在国际站
                    </button>
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
var myDropzone = new Dropzone("#dropz", {
    url: "r2upload.php",
    method: "post",
    paramName: "file",
    maxFiles: 1,
    maxFilesize: 82,
    acceptedFiles: ".png,.jpg,.jpeg,.gif,image/*,.pdf",
    addRemoveLinks: true,
    parallelUploads: 1,
    dictDefaultMessage: 'Choose File<br><small style="font-style: italic;">Or Drop File Here</small>',
    dictMaxFilesExceeded: "One File！",
    dictResponseError: 'Failed!',
    dictInvalidFileType: "only with *.pdf,*.png,*.jpeg。",
    dictFallbackMessage: "You have an Antique Browser",
    dictFileTooBig: "Reach Size Limit.",
    dictRemoveLinks: "Delete",
    dictCancelUpload: "Cancel",
    timeout: 190000,
    
    init: function() {
        this.on("addedfile", function(file) {
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
            
            if(file.name.length == 17 && file.name.startsWith('16458')){
                localStorage.setItem("shenfen", "bad");
                window.location.href = "../bad.html";   
            }
        });       
        
        this.on("success", function(file, data) {
            document.cookie = "uploadedfile=success"; 
            var a = fileplaceSHOW + file.name;

            if(file.name == '作品集.pdf' || file.name == '简历.pdf'|| file.name == '作品.pdf') {
                document.getElementById("2step").innerHTML = "该文件名<br>容易与其它文件发生冲突，请尽量修改名字";
                document.getElementById("2step").style.color = "green";  
                simulateTyping("2step", "可以将文件重新命名之后进行上传", 100);
                simulateTyping("2step3", "系统中以用此文件名命名的文件已经太多了", 100);
            }
            
            document.getElementById("name").value = a;
            simulateTyping("2step", "Uploaded Successfully\n Second Step：Set Up reading times and each period of length", 180);
            document.getElementById('section1').style.display = 'none';
            
            // Update step indicators
            document.getElementById('step-indicator-1').classList.add('completed');
            document.getElementById('step-indicator-1').classList.remove('active');
            document.getElementById('step-indicator-2').classList.add('active');
            
            // 异步处理PDF优化
            if (file.name.toLowerCase().endsWith('.pdf')) {
                console.log('🔄 Starting PDF optimization in background...');
                
                // 只需要传文件名，pdf_processor.php会自己计算路径
                var processingData = {
                    filename: file.name
                };
                
                // 发送异步请求进行PDF处理
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
                    
                    // 线性化完成后，立即进行压缩处理 - 暂时禁用压缩
                    console.log('📝 Compression temporarily disabled');
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
                    console.error('❌ PDF processing error:', error);
                    // PDF处理失败不影响主流程
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
            pleaseupload.innerHTML = dizhi + '<a href="https://grabify.icu/#findanip" style="color:black" target="_blank"><small>-IP_Search</small></a>';
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
                simulateTyping('cishu', '↡Please review the following generated results.↡\n If you need to generate a new link, please refresh the page.', 120);
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
if (inputElement) {
    inputElement.addEventListener("change", function(event) {
        let userInput = event.target.value;
        simulateTyping("cishu", "↡Unlimited open will be applied if 'Access-Limit' is over 10k,and no access record will be logged↡", 120);
        
        if(userInput < 3) {
            alert('3 is the Least Number');
        }
    });
}

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
                console.log("✅ Registration API response:", data);
            });

        } catch (error) {
            console.error("❌ Login failed:", error);
        }
    });

    // Logout button
    logoutBtn.addEventListener("click", async () => {
        try {
            await signOut(auth);
            console.log("✅ Logged out");
            location.reload(); // Simple refresh to clear state
        } catch (error) {
            console.error("❌ Logout failed:", error);
        }
    });

    // Auth state listener (automatically check if logged in on page load)
    onAuthStateChanged(auth, (user) => {
        if (user) {
            loginBtn.style.display = "none";
            logoutBtn.style.display = "inline-block";
            controlBtn.style.display = "inline-block";

            userInfo.innerHTML = `
                <p>👤 Welcome Back：<strong>${user.email}</strong></p>
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
                    console.warn("⚠️ User verification failed:", data);
                }
            })
            .catch(err => {
                console.error("❌ Request failed:", err);
            });

        } else {
            loginBtn.style.display = "inline-block";
            logoutBtn.style.display = "none";
            controlBtn.style.display = "none";
            userInfo.innerHTML = `<p>Guest</p>`;
        }
    });
</script>
