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
// $picplace = "yes/".$year."/".$month."/".$week."/preview/";
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

echo "<script>var dizhi = '$ip';var dengru = '$dengru'; var fileplaceSHOW='$fileplaceSHOW'; </script>";

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
        
        if($zhangai != 'topen') {
            $encryfile = 'yes'.$url;
            if(!file_exists($encryfile)) {
                exit('Unknown Reason');
            }
            
            if(filesize($encryfile) < 3797152) {
                try {
                    require_once __DIR__ . '/vendorJiami/autoload.php';
                    $mpdf = new \Mpdf\Mpdf();
                    
                    $pagecount = $mpdf->SetSourceFile($encryfile);
                    
                    for($i = 1; $i <= $pagecount; $i++) {
                        $mpdf->AddPage();
                        $tplId3 = $mpdf->ImportPage($i);
                        $size = $mpdf->getTemplateSize($tplId3);
                        $mpdf->UseTemplate($tplId3, 0, 0, $size['width'], $size['height'], true);
                    }
                    
                    $mpdf->SetProtection(array(), 'guaguashimaimai', 'qweewqer');
                    $mpdf->Output($encryfile, \Mpdf\Output\Destination::FILE);
                } catch(Exception $e) {
                    // Handle exception
                }
            }
        }
        
        $messagebox = 'Your Reading link is Created';
        if(substr($url, -3) == 'pdf') {
            include_once('../password.php');
            $conn = new mysqli($servernameMai, $usernameMai, $passwordMai, $dbnameMai);
            if($conn->connect_error) {
                die("CANNOT INSERT");
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
            }            echo "<script>
                document.getElementById('section2').style.display='none'; 
                document.getElementById('section1').style.display='none';
                var at='".$identifier."';
                var bt='".$identifier."';
                var place='".($url ?? '')."';
                
                // Update step indicators
                document.addEventListener('DOMContentLoaded', function() {
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
            </script>";$result = mysqli_query($conn, $sql);
            $resultres = mysqli_query($conn, $sqlres);
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaiPDF Secure Sharing</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    
    <!-- Bootstrap CSS & JS and jQuery -->
    <link href="https://lf3-cdn-tos.bytecdntp.com/cdn/expire-1-M/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://lf9-cdn-tos.bytecdntp.com/cdn/expire-1-M/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://lf6-cdn-tos.bytecdntp.com/cdn/expire-1-M/bootstrap/5.1.3/js/bootstrap.min.js"></script>
    
    <!-- Dropzone -->
    <script src="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/dropzone.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/dropzone-amd-module.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/dropzone.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/basic.css">
    
    <!-- QR Code -->
    <script type="text/javascript" src="qrcode.min.js"></script>
      <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9224406325142860" crossorigin="anonymous"></script>
    
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
</head>
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
            <form role="form" action="boot.php" method="post">
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
                <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-github"></i></a>
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

<style>
/* Basic Reset & Defaults */
:root {
    --primary: #8e44ad;
    --primary-light: #9b59b6;
    --primary-dark: #7d3c98;
    --secondary: #3498db;
    --accent: #2ecc71;
    --text-dark: #2c3e50;
    --text-light: #ecf0f1;
    --bg-light: #f9f9f9;
    --bg-dark: #2c3e50;
    --shadow-sm: 0 2px 10px rgba(0, 0, 0, 0.08);
    --shadow-md: 0 5px 15px rgba(0, 0, 0, 0.12);
    --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
    --shadow-inset: inset 0 2px 10px rgba(0, 0, 0, 0.1);
    --gradient-primary: linear-gradient(135deg, #2c3e50, #4b7bec);
    --gradient-button: linear-gradient(to right, #114FEE, #C835F8);
    --danger: #e74c3c;
    --transition-fast: all 0.3s ease;
    --transition-medium: all 0.5s ease;
    --transition-slow: all 0.8s ease;
    --border-radius-sm: 5px;
    --border-radius-md: 10px;
    --border-radius-lg: 20px;
    --border-radius-full: 50%;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: var(--text-dark);
    overflow-x: hidden;
    background-color: var(--bg-light);
    scroll-behavior: smooth;
}

.container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Logo */
.logo {
    font-size: 2rem;
    font-weight: bold;
    color: #8e44ad; /* Fallback color */
}
.logo-main {
    margin-bottom: 20px;
    background: linear-gradient(45deg, #3498db, #9b59b6);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    display: inline-block;
}

/* Buttons */
.btn {
    display: inline-block;
    padding: 10px 25px;
    border-radius: 20px;
    text-decoration: none;
    font-weight: bold;
    text-align: center;
    transition: background-color 0.3s ease, color 0.3s ease, transform 0.2s ease, border-color 0.3s ease;
    cursor: pointer;
}

.btn-feature-start {
    background: linear-gradient(to right, #114FEE, #C835F8);
    color: #fff;
    border: 1px solid transparent;
    margin-top: 20px;
    font-size: 0.9em;
    padding: 8px 20px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    box-shadow: 0 4px 12px rgba(114, 53, 248, 0.3);
}

.btn-feature-start:hover {
    background: #fff;
    color: #8e44ad;
    border: 1px solid #8e44ad;
    transform: translateY(-3px);
    box-shadow: 0 8px 15px rgba(114, 53, 248, 0.4);
}

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #2c3e50, #4b7bec);
    color: #fff;
    text-align: center;
    padding: 120px 0 100px 0;
    position: relative;
    overflow: hidden;
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Hero action buttons */
.hero-action-buttons {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 30px;
    flex-wrap: wrap;
}

.btn-hero-primary {
    background: linear-gradient(to right, #2ecc71, #27ae60);
    color: #fff;
    border: 1px solid transparent;
    padding: 12px 28px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: bold;
    font-size: 1.1em;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(46, 204, 113, 0.4);
    display: inline-flex;
    align-items: center;
    position: relative;
    overflow: hidden;
}

.btn-hero-primary:hover {
    background: #fff;
    color: #2ecc71;
    border: 1px solid #2ecc71;
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(46, 204, 113, 0.5);
}

.btn-hero-secondary {
    background: linear-gradient(to right, #3498db, #2980b9);
    color: #fff;
    border: 1px solid transparent;
    padding: 12px 28px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: bold;
    font-size: 1.1em;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
    display: inline-flex;
    align-items: center;
    position: relative;
    overflow: hidden;
}

.btn-hero-secondary:hover {
    background: #fff;
    color: #3498db;
    border: 1px solid #3498db;
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(52, 152, 219, 0.5);
}

.btn-hero-primary::before,
.btn-hero-secondary::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transform: translateX(-100%);
    transition: 0.6s;
}
.btn-hero-tertiary {
    background: linear-gradient(to right, #e74c3c, #c0392b);
    color: #fff;
    border: 1px solid transparent;
    padding: 12px 28px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: bold;
    font-size: 1.1em;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(231, 76, 60, 0.4);
    display: inline-flex;
    align-items: center;
    position: relative;
    overflow: hidden;
}

.btn-hero-tertiary:hover {
    background: #fff;
    color: #e74c3c;
    border: 1px solid #e74c3c;
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(231, 76, 60, 0.5);
}

.btn-hero-tertiary::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transform: translateX(-100%);
    transition: 0.6s;
}

.btn-hero-tertiary:hover::before {
    transform: translateX(100%);
}
.btn-hero-primary:hover::before,
.btn-hero-secondary:hover::before {
    transform: translateX(100%);
}

.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
}

.hero-pattern {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 8%), 
                      radial-gradient(circle at 80% 30%, rgba(255, 255, 255, 0.1) 0%, transparent 8%),
                      radial-gradient(circle at 40% 70%, rgba(255, 255, 255, 0.1) 0%, transparent 8%),
                      radial-gradient(circle at 70% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 8%);
    opacity: 0.6;
}

.animated-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        -45deg,
        rgba(44, 62, 80, 0.8),
        rgba(52, 73, 94, 0.8),
        rgba(75, 123, 236, 0.8),
        rgba(142, 68, 173, 0.8)
    );
    background-size: 400% 400%;
    animation: gradient 15s ease infinite;
    z-index: -1;
}

.floating-elements {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 0;
}

.float-element {
    position: absolute;
    width: 6px;
    height: 6px;
    background: rgba(255, 255, 255, 0.5);
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
    border-radius: 50%;
}

.float-element:nth-child(1) {
    top: 20%;
    left: 10%;
    animation: float-animation 25s infinite ease-in-out;
}

.float-element:nth-child(2) {
    bottom: 30%;
    right: 15%;
    width: 8px;
    height: 8px;
    animation: float-animation 20s infinite ease-in-out reverse;
}

.float-element:nth-child(3) {
    top: 50%;
    left: 70%;
    width: 10px;
    height: 10px;
    animation: float-animation 30s infinite ease-in-out;
}

.float-element:nth-child(4) {
    top: 70%;
    left: 30%;
    width: 4px;
    height: 4px;
    animation: float-animation 15s infinite ease-in-out reverse;
}

.float-element:nth-child(5) {
    top: 25%;
    left: 80%;
    animation: float-animation 18s infinite ease-in-out;
}

@keyframes float-animation {
    0% {
        transform: translate(0, 0) rotate(0deg);
    }
    25% {
        transform: translate(100px, 50px) rotate(90deg);
    }
    50% {
        transform: translate(50px, 100px) rotate(180deg);
    }
    75% {
        transform: translate(-50px, 50px) rotate(270deg);
    }
    100% {
        transform: translate(0, 0) rotate(360deg);
    }
}

@keyframes gradient {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

.hero-section .container {
    position: relative;
    z-index: 1;
}

.hero-section h1 {
    font-size: 2.2rem;
    margin-bottom: 10px;
    font-weight: 600;
    letter-spacing: 1px;
}

.hero-section p {
    font-size: 1.1rem;
    margin-bottom: 30px;
}

/* User Authentication Area */
.user-auth-area {
    margin-top: 20px;
}

.auth-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 10px;
}

/* Scroll Indicator */
.scroll-indicator {
    text-align: center;
    margin-top: 20px;
}

.arrow-down {
    display: block;
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 6px solid #fff;
    margin: 5px auto;
    animation: slide 2s infinite;
}

@keyframes slide {
    0% {
        transform: translateY(0);
        opacity: 0.5;
    }
    50% {
        transform: translateY(10px);
        opacity: 1;
    }
    100% {
        transform: translateY(0);
        opacity: 0.5;
    }
}

.arrow-down + .arrow-down {
    margin-top: 5px;
}

/* Breadcrumb Container */
.breadcrumb-container {
    background-color: #f8f9fa;
    padding: 10px 0;
    border-bottom: 1px solid #e9ecef;
}

.breadcrumb {
    margin-bottom: 0;
    padding: 0;
    list-style: none;
    display: flex;
    flex-wrap: wrap;
}

.breadcrumb-item {
    display: inline-block;
    margin-right: 10px;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "/";
    display: inline-block;
    margin: 0 10px 0 0;
    color: #6c757d;
}

/* Step Progress Indicator */
@keyframes stickyAnimation {
    0% { transform: translateY(-100%); }
    100% { transform: translateY(0); }
}

.step-progress-container {
    background-color: #f8f9fa;
    padding: 20px 0;
    border-bottom: 1px solid #e9ecef;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.step-progress-container.sticky-active {
    padding: 12px 0;
    background-color: rgba(255, 255, 255, 0.98);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    animation: stickyAnimation 0.3s ease-in-out;
}

.step-progress {
    display: flex;
    justify-content: space-between;
    position: relative;
    max-width: 800px;
    margin: 0 auto;
}

.step-progress::before {
    content: '';
    position: absolute;
    top: 24px;
    left: 40px;
    right: 40px;
    height: 2px;
    background: #e9ecef;
    z-index: 1;
    transition: all 0.3s ease;
}

.sticky-active .step-progress::before {
    top: 19px;
}

.step-item {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
}

.step-number {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: #f0f0f0;
    border: 2px solid #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
    margin-bottom: 10px;
    transition: all 0.3s ease;
}

.sticky-active .step-number {
    width: 40px;
    height: 40px;
    font-size: 1rem;
    margin-bottom: 5px;
}

.step-text {
    color: #6c757d;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.sticky-active .step-text {
    font-size: 0.8rem;
}

.step-item.active .step-number {
    background: var(--gradient-button);
    color: white;
    border-color: transparent;
    box-shadow: 0 5px 15px rgba(142, 68, 173, 0.3);
}

.step-item.active .step-text {
    color: var(--primary);
    font-weight: 600;
}

.step-item.completed .step-number {
    background-color: #2ecc71;
    color: white;
    border-color: transparent;
}

/* Features Section - Upload */
.features-section {
    background-color: #f9f9f9;
    padding: 80px 0;
    text-align: center;
    position: relative;
}

.features-section h2,
.why-choose-us-section h2,
.cta-section h2 {
    font-size: 2rem;
    margin-bottom: 40px;
    color: var(--text-dark);
    font-weight: 600;
    position: relative;
    display: inline-block;
}

.features-section h2::after,
.why-choose-us-section h2::after,
.cta-section h2::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: var(--gradient-button);
    border-radius: 3px;
}

.upload-container {
    max-width: 700px;
    margin: 0 auto;
}

/* Dropzone Styling */
.dropzone {
    border: 3px dashed #8e44ad;
    border-radius: 10px;
    background: #f9f9f9;
    padding: 30px;
    text-align: center;
    font-weight: 600;
    font-size: 1.1rem;
    color: #666;
    transition: all 0.3s ease;
}

.dropzone:hover {
    background-color: #f0f0f0;
    border-color: #9b59b6;
}

.dz-message {
    margin: 2em 0;
}

/* Section highlight animation for transitions */
@keyframes highlightSection {
    0% { box-shadow: 0 0 0 0 rgba(142, 68, 173, 0.2); }
    70% { box-shadow: 0 0 0 20px rgba(142, 68, 173, 0); }
    100% { box-shadow: 0 0 0 0 rgba(142, 68, 173, 0); }
}

.highlight-section {
    animation: highlightSection 1.5s ease-in-out;
    border-radius: var(--border-radius-md);
}

/* Settings Section */
.why-choose-us-section {
    background-color: #f4f7fc;
    padding: 80px 0;
    position: relative;
}

.settings-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 30px;
    margin-top: 40px;
}

.setting-box {
    background-color: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: var(--shadow-md);
    text-align: center;
    transition: var(--transition-medium);
    position: relative;
    min-height: 320px; /* Set minimum height for consistency */
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.setting-box:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.setting-icon {
    margin-bottom: 20px;
}

.fa-icon {
    font-size: 2rem;
    color: #8e44ad;
    display: inline-block;
    padding: 15px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(142, 68, 173, 0.1), rgba(155, 89, 182, 0.1));
    margin-bottom: 15px;
}

/* Section Divider */
.section-divider {
    text-align: center;
    padding: 20px 0;
    background-color: #f4f7fc;
    border-top: 1px solid #e9ecef;
    border-bottom: 1px solid #e9ecef;
}

.section-divider p {
    color: #666;
    font-size: 0.9rem;
    margin: 0;
}

/* CTA Section - Results */
.cta-section {
    background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://article.maipdf.com/offlinepages/drag_files_to_upload.png');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    padding: 80px 0;
    text-align: center;
    color: #fff;
    position: relative;
}

.cta-box {
    max-width: 900px;
    margin: 0 auto;
    padding: 40px;
    background-color: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.cta-section h2 {
    color: #fff;
    margin-bottom: 30px;
    font-size: 1.8rem;
    position: relative;
    display: inline-block;
    padding-bottom: 15px;
}

.cta-section h2:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 3px;
    background: linear-gradient(to right, #114FEE, #C835F8);
    border-radius: 3px;
}

.results-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-top: 30px;
}

.result-box {
    padding: 20px;
    background-color: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    text-align: center;
    transition: all 0.3s ease;
}

.result-box h3 {
    font-size: 1.3rem;
    margin-bottom: 15px;
    word-break: break-all;
}

.link-copy-area {
    margin: 20px 0;
}

.link-copy-area input {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 10px;
    width: 100%;
    font-size: 0.9rem;
}

.link-copy-area input:focus {
    outline: none;
    border-color: rgba(255, 255, 255, 0.4);
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
}

.qr-container {
    display: inline-block;
    padding: 12px;
    background-color: white;
    border-radius: 10px;
    margin: 0 auto;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Footer Section */
.footer-section {
    background: linear-gradient(to right, #114FEE, #C835F8);
    color: #ccc;
    padding: 50px 0;
}

.footer-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

.footer-left {
    flex-basis: 60%;
}

.footer-left .logo {
    color: #eee;
    margin-bottom: 15px;
    font-size: 1.8rem;
    background: none;
    -webkit-background-clip: unset;
    background-clip: unset;
}

.footer-left p {
    font-size: 0.9rem;
    line-height: 1.7;
}

.footer-social {
    margin-top: 15px;
    display: flex;
    gap: 15px;
}

.social-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.1);
    color: #fff;
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-icon:hover {
    background-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-3px);
}

.footer-right {
    flex-basis: 30%;
}

.contact-info {
    margin-bottom: 20px;
    background-color: rgba(255, 255, 255, 0.1);
    padding: 20px;
    border-radius: 8px;
}

.contact-info h4 {
    color: #fff;
    font-size: 1.2rem;
    margin-bottom: 15px;
}

.contact-info p {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    color: #fff;
}

.contact-info i {
    margin-right: 10px;
    color: rgba(255, 255, 255, 0.8);
}

.contact-note {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.7) !important;
    margin-top: 10px;
}

/* Back to Top Button */
.back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--gradient-button);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    z-index: 100;
}

.back-to-top.show {
    opacity: 1;
    visibility: visible;
}

.back-to-top:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
}

/* Responsive Design */
@media (max-width: 992px) {
    .settings-grid {
        grid-template-columns: 1fr;
    }
    
    .results-grid {
        grid-template-columns: 1fr;
    }
    
    .cta-box {
        padding: 30px 20px;
    }
    
    .footer-content {
        flex-direction: column;
        text-align: center;
    }
    
    .footer-left, .footer-right {
        flex-basis: 100%;
        margin-bottom: 20px;
        text-align: center;
    }
    
    .footer-right {
        margin-top: 20px;
        text-align: center;
    }
    
    .footer-social, .language-switcher {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .hero-section h1 {
        font-size: 1.8rem;
    }
    
    .hero-section p {
        font-size: 1rem;
    }
    
    .fa-icon {
        font-size: 1.8rem;
        padding: 12px;
    }
    
    .why-choose-us-section {
        padding: 60px 0;
    }
      /* Responsive styling for hero buttons */
    .hero-action-buttons {
        flex-direction: column;
        align-items: center;
        gap: 12px;
        width: 100%;
        padding: 0 15px;
    }
      /* Make hero buttons consistent on mobile */
    .btn-hero-primary,
    .btn-hero-secondary,
    .btn-hero-tertiary {
        width: 100%;
        max-width: 280px;
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 1rem;
        display: flex;
        justify-content: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
      /* Ensure copy button has consistent styling on mobile */
    .btn-copy {
        width: 100%;
        max-width: 280px;
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 1rem;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    /* Make feature buttons consistent on mobile */
    .btn-feature-start {
        width: 100%;
        max-width: 280px;
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 1rem;
        display: flex;
        justify-content: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    /* Adjust action buttons container for mobile */
    .action-buttons-container {
        padding: 12px 10px;
    }
    
    .action-buttons {
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }
    
    .action-title {
        font-size: 1rem;
    }
    
    .btn-hero-primary,
    .btn-hero-secondary {
        width: 100%;
        max-width: 280px;
        padding: 10px 20px;
        font-size: 1rem;
    }
    
    /* Responsive step indicator */
    .step-number {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .step-text {
        font-size: 0.8rem;
    }
    
    .sticky-active .step-number {
        width: 35px;
        height: 35px;
        font-size: 0.9rem;
    }
    
    .sticky-active .step-text {
        font-size: 0.7rem;
    }
    
    .step-progress::before {
        top: 19px;
    }
    
    .sticky-active .step-progress::before {
        top: 17px;
    }
}

@media (max-width: 576px) {
    .hero-section {
        padding: 80px 0 60px;
    }
    
    .hero-section h1 {
        font-size: 1.6rem;
    }
    
    .auth-buttons {
        flex-direction: column;
        gap: 10px;
    }
    
    .hero-action-buttons {
        margin-top: 20px;
    }
    
    .btn-hero-primary,
    .btn-hero-secondary {
        font-size: 0.9rem;
        padding: 8px 16px;
    }
    
    .features-section h2, 
    .why-choose-us-section h2, 
    .cta-section h2 {
        font-size: 1.7rem;
    }
    
    .back-to-top {
        bottom: 20px;
        right: 20px;
    }
}

/* Action Buttons Styling in Results Section */
.action-buttons-container {
    margin-top: 25px;
    padding: 15px;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.action-title {
    color: #fff;
    margin-bottom: 15px;
    font-size: 1.1rem;
    font-weight: 600;
    text-align: center;
    letter-spacing: 0.5px;
    position: relative;
    padding-bottom: 10px;
}

.action-title:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 50px;
    height: 2px;
    background: rgba(255, 255, 255, 0.5);
}

.action-buttons {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 10px;
    flex-wrap: wrap;
}

.action-buttons .btn {
    min-width: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
}

@media (max-width: 576px) {
    .action-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .action-buttons .btn {
        width: 100%;
        margin: 5px 0;
    }
    
    .cta-box {
        padding: 20px 15px;
    }
    
    .cta-section h2 {
        font-size: 1.5rem;
    }
    
    .result-box {
        padding: 15px 10px;
    }
    
    .result-box h3 {
        font-size: 1.1rem;
        word-break: break-all;
    }
      .link-copy-area input {
        font-size: 0.85rem;
        padding: 10px;
        width: 100%;
        max-width: 100%;
        margin-bottom: 10px;
    }
    
    .link-copy-area {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }
}

/* Custom styling for the copy button */
.btn-copy {
    background: linear-gradient(to right, #3498db, #2980b9);
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: auto;
    min-width: 200px;
    margin: 0 auto;
}

.btn-copy:hover {
    background: linear-gradient(to right, #2980b9, #3498db);
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(52, 152, 219, 0.5);
}

#Copied {
    color: #2ecc71;
    font-weight: 600;
    margin-top: 10px;
    opacity: 0;
    animation: fadeInOut 2s ease forwards;
}

@keyframes fadeInOut {
    0% { opacity: 0; }
    25% { opacity: 1; }
    75% { opacity: 1; }
    100% { opacity: 0; }
}
</style>

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
        });        this.on("success", function(file, data) {
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
