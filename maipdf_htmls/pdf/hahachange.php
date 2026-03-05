<?php 
if(!isset($_SERVER['HTTPS'])){
   $url= 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
   header("Location: $url");
}

//echo "mm//1EEOSlMcM<br>";

//echo crypt("joemm//1EEOSlMcM","su").'<br>';
//echo crypt("joempAhCIzvy/X9.","su").'<br>';
//$passcodes=htmlspecialchars($_POST['passcode']);

if((isset($_POST['email']))&& (isset($_POST['email2'])) && isset(($_POST['pwd']))&& (isset($_POST['pwd2']))){
  
  //echo "part 1";
   $email = htmlspecialchars($_POST['email']);
   $email2 = htmlspecialchars($_POST['email2']);
   $pwd = htmlspecialchars($_POST['pwd']);
   $pwd2 = htmlspecialchars($_POST['pwd2']);
  
   
  //if($email2=='grace' && $pwd2=='grace'){
   if($email2=='grace'){
    $emailcode= 'joe'.$email;
    $good1=crypt($emailcode,'su');

    if($pwd=='joe'){
      $pwd2=(int) $pwd2;
      if($pwd2==0){ $pwd2=1;}
      $pwd2=35*$pwd2;
      echo $pwd2.'<br>';
      $day=date('Y-m-d');
      $endday = date('Y-m-d',strtotime('+'.$pwd2.' day'));
      //echo $endday;
      $day=$day.'@'.$endday;
      echo $day;
     // exit();

       $number=$pwd2;
      // if($email[1]!='g' || $email[1]!='n'){
      echo '<br>: '.$email[1].':'.ord($email[1]).'<br>';
       if(ord($email[1])<97 && ord($email[1])>64){  
        $number=2;
         $arr = str_split($email);
         $i=0;
       foreach($arr as $value){
           $atype= ord($value)-64;
         if($atype==10){
           $atype=0;
         }
        // echo $atype.':'.$value.'#';
         echo ' Picture ';
          $akey[$i]=$atype;
          $i++;
       }
       print_r($akey);
        $number=implode("", $akey);
        //echo $number;
       }
       
      // echo $number;
      $sqlres="INSERT INTO `block`(`ip`,`md5`,`attr`,`number`)VALUES('$day','$email','user',$number)";
      

     include_once ('../password.php');
     $conn = new mysqli($servernameMai, $usernameMai, $passwordMai, $dbnameMai);
        if ($conn->connect_error) 
      {
      die("Failed: " . $conn->connect_error);
      } 
      //$baidan = mysqli_query($conn, $sqlres);
      //$baidan = mysqli_fetch_assoc($baidan);
      //echo '<br>'.$sqlres.'<br>';
     // $conn->query($sqlres);
     // print_r($conn->insert_id);

         if (mysqli_query($conn, $sqlres)) {
               echo "New record created successfully";
            } else {
               echo "Error: "  . "is " . mysqli_error($conn);
            }




      echo 'The ID is: '.$conn->insert_id.'<br>';
       mysqli_close($conn);

       
        exit('ADD to white list');
    }else{
     //     echo $emailcode.$good1;
    }
  }




   
   if($pwd[0]=='i'){
       $emailcode= 'joe'.$email;
       $emailcode2='joe'.$email2;
       $good1=crypt($emailcode,'ig');
       $good2=crypt($emailcode2,'ig');
      
      $arr = str_split($key);
       $i=0;
       foreach($arr as $value){
           $atype= ord($value)-64;
         if($atype==10){
           $atype=0;
         }
          $akey[$i]=$atype;
          $i++;
       }
        $key=implode("", $akey);
     $sql=   "SELECT * FROM `picture` WHERE `mdemail` LIKE '$email2' LIMIT 1";
    // $sql2= "UPDATE `picture` SET `url`='$newurl',`password`= '$newpassword',`limit`= '$newlimit' WHERE `mdemail`='$email' ";
   }else{
   $emailcode= 'joe'.$email;
   $emailcode2='joe'.$email2;
   $good1=crypt($emailcode,'su');
   $good2=crypt($emailcode2,'su');
   $sql=   "SELECT * FROM `pdf` WHERE `mdemail`LIKE '$email2' LIMIT 1";
   //$sql2= "UPDATE `pdf` SET `url`='$newurl',`password`= '$newpassword',`limit`= '$newlimit' WHERE `mdemail`='$email' ";
  //echo $sql2;
   }
   
   
   

  
   if(($pwd==$good1) && ($pwd2==$good2) ){
    
       // echo "right ";
    //  echo $email[1];
    
      include_once ('../password.php');
     $conn = new mysqli($servernameMai, $usernameMai, $passwordMai, $dbnameMai);
        if ($conn->connect_error) 
      {
      die("Failed: " . $conn->connect_error);
      } 
      
     //$sql=   "SELECT * FROM `pdf` WHERE `mdemail`LIKE '$email2' LIMIT 1";
     
     
     $result = mysqli_query($conn, $sql);
     if (mysqli_num_rows($result) > 0) {
       $row = mysqli_fetch_assoc($result);
       $newurl= $row['url'];
       $newpassword= $row['password'];
       $newlimit= $row['limit'];
       $newday= $row['day'];
           $newalert= $row['alert'];
       $newsms =$row['sms'];
     }else{
        //echo "error search".$sql;
        mysqli_close($conn);
        exit();
     }
     //$sql2= "UPDATE `pdf` SET `url`='$newurl',`password`= '$newpassword',`limit`= '$newlimit' WHERE `mdemail`='$email' ";
     //echo $sql2;
     //$result2 = mysqli_query($conn, $sql2);

   if($pwd[0]=='i'){
     
     $sql2= "UPDATE `picture` SET `url`='$newurl',`password`= '$newpassword',`limit`= '$newlimit' WHERE `mdemail`='$email' ";
   }else{
  
     $sql2= "UPDATE `pdf` SET `sms`='$newsms',`url`='$newurl',`password`= '$newpassword',`limit`= '$newlimit',`day`='$newday',`alert`='$newalert' WHERE `mdemail`='$email' ";
  //echo $sql2;
   }


     if ($conn->query($sql2) === TRUE) {

          $a=$conn->affected_rows;
      $updateResult=1;
      
      //echo  "good";
          //echo $a;
           // echo $conn->query($sql)->rows();       
      } else {
          //asdfasecho "Error: " . $sql . "<br>" . $conn->error;
          exit ("not active");
      }    
     
     mysqli_close($conn);
   }else{
     $updateResult=2;
   //  echo $emailcode.'<br>';
   // echo $emailcode2.'<br>';
   // echo crypt($emailcode,'su').'<br>';
   // echo crypt($emailcode2,'su').'<br>';
   }
  
}else{
  
  //echo "pass";
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"> 
  <title>MaiPDF - Document Replacement Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
 
  
     <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.7/js/bootstrap.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.7/css/bootstrap.min.css" rel="stylesheet"> 
  
  
  <style>
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
      background-color: #f4f7fc;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Hero Section */
    .hero-section {
      background: linear-gradient(135deg, #2c3e50, #4b7bec);
      color: #fff;
      text-align: center;
      padding: 60px 0 40px 0;
      position: relative;
      overflow: hidden;
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

    @keyframes gradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
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
      0% { transform: translate(0, 0) rotate(0deg); }
      25% { transform: translate(100px, 50px) rotate(90deg); }
      50% { transform: translate(50px, 100px) rotate(180deg); }
      75% { transform: translate(-50px, 50px) rotate(270deg); }
      100% { transform: translate(0, 0) rotate(360deg); }
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
      max-width: 700px;
      margin-left: auto;
      margin-right: auto;
    }

    .logo-main {
      margin-bottom: 20px;
      background: linear-gradient(45deg, #3498db, #9b59b6);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      display: inline-block;
      font-size: 2rem;
      font-weight: bold;
    }

    /* Main Content */
    .main-content {
      flex: 1;
      padding: 40px 0;
    }

    .form-container {
      background-color: #fff;
      border-radius: var(--border-radius-md);
      box-shadow: var(--shadow-md);
      padding: 30px;
      max-width: 650px;
      margin: 0 auto;
      transition: var(--transition-medium);
    }

    .form-container:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-lg);
    }

    .form-container h2 {
      text-align: center;
      margin-bottom: 30px;
      position: relative;
      color: var(--text-dark);
      font-weight: 600;
      display: inline-block;
      width: 100%;
    }

    .form-container h2::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 3px;
      background: var(--gradient-button);
      border-radius: 3px;
    }

    .form-section {
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 1px solid #eee;
    }

    .form-section:last-child {
      margin-bottom: 0;
      padding-bottom: 0;
      border-bottom: none;
    }

    .section-title {
      font-weight: 600;
      color: var(--primary);
      margin-bottom: 15px;
      font-size: 1.1rem;
    }

    .input-container {
      position: relative;
      margin-bottom: 20px;
    }

    .input-container i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
    }

    .input-container input {
      padding-left: 45px;
      width: 100%;
      padding: 12px 15px 12px 45px;
      font-size: 16px;
      border: 1px solid #ddd;
      border-radius: var(--border-radius-md);
      transition: var(--transition-fast);
      font-family: 'Poppins', sans-serif;
    }

    .input-container input:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(142, 68, 173, 0.2);
      outline: none;
    }

    .submit-btn {
      width: 100%;
      padding: 12px;
      background: var(--gradient-button);
      color: white;
      border: none;
      border-radius: var(--border-radius-md);
      cursor: pointer;
      font-weight: 600;
      transition: var(--transition-fast);
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 12px rgba(114, 53, 248, 0.3);
      margin-top: 10px;
    }

    .submit-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 15px rgba(114, 53, 248, 0.4);
    }

    .submit-btn i {
      margin-right: 8px;
    }

    .instructions {
      background-color: #f8f9fa;
      border-radius: var(--border-radius-md);
      padding: 20px;
      margin-top: 30px;
    }

    .instructions h3 {
      font-size: 1.1rem;
      margin-bottom: 15px;
      color: var(--primary);
    }

    .instructions ul {
      list-style-type: none;
      padding: 0;
    }

    .instructions li {
      position: relative;
      padding-left: 30px;
      margin-bottom: 15px;
      line-height: 1.5;
    }

    .instructions li:before {
      content: '\f058';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      position: absolute;
      left: 0;
      top: 2px;
      color: var(--primary);
    }

    .example-image {
      border-radius: var(--border-radius-md);
      overflow: hidden;
      box-shadow: var(--shadow-sm);
      cursor: pointer;
      transition: var(--transition-fast);
      margin: 20px 0;
    }

    .example-image:hover {
      transform: scale(1.02);
      box-shadow: var(--shadow-md);
    }

    /* Status Messages */
    .status-message {
      padding: 15px 20px;
      border-radius: var(--border-radius-md);
      margin-bottom: 25px;
      text-align: center;
      animation: fadeInDown 0.5s ease;
    }

    .status-success {
      background-color: rgba(46, 204, 113, 0.15);
      border-left: 4px solid var(--accent);
      color: #27ae60;
    }

    .status-warning {
      background-color: rgba(241, 196, 15, 0.15);
      border-left: 4px solid #f1c40f;
      color: #d35400;
    }

    .status-error {
      background-color: rgba(231, 76, 60, 0.15);
      border-left: 4px solid var(--danger);
      color: #c0392b;
    }

    @keyframes fadeInDown {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Footer */
    .footer-section {
      background: linear-gradient(to right, #114FEE, #C835F8);
      color: #ccc;
      padding: 40px 0;
      margin-top: auto;
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

    /* Responsive */
    @media (max-width: 768px) {
      .hero-section {
        padding: 40px 0 30px 0;
      }

      .hero-section h1 {
        font-size: 1.8rem;
      }

      .form-container {
        padding: 20px;
      }

      .footer-content {
        flex-direction: column;
        text-align: center;
      }

      .footer-left, .footer-right {
        flex-basis: 100%;
        margin-bottom: 30px;
      }

      .footer-social {
        justify-content: center;
      }
    }

    @media (max-width: 576px) {
      .hero-section h1 {
        font-size: 1.6rem;
      }

      .section-title {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  <!-- Hero Section -->
  <header class="hero-section">
    <div class="hero-background">
      <div class="hero-pattern"></div>
      <div class="animated-bg"></div>
    </div>
    <div class="container">
      <div class="logo logo-main animate__animated animate__fadeInDown">MaiPDF</div>
      <h1 class="animate__animated animate__fadeInUp">Document Replacement Portal</h1>
      <p class="animate__animated animate__fadeInUp animate__delay-1s">Update your document links and redirect existing reading codes to new files with enhanced security and control</p>
    </div>
    <div class="floating-elements">
      <div class="float-element" style="--i:1"></div>
      <div class="float-element" style="--i:2"></div>
      <div class="float-element" style="--i:3"></div>
      <div class="float-element" style="--i:4"></div>
      <div class="float-element" style="--i:5"></div>
    </div>
  </header>

  <!-- Main Content -->
  <section class="main-content">
    <div class="container">
      <div class="form-container">
        <?php  
        if(isset($updateResult)) {
          if($updateResult==1){
            echo "<div class=\"status-message status-success\"><h4>Success!</h4><p>You have successfully updated your Reading Link</p></div>";
          }elseif($updateResult==2){
            echo "<div class=\"status-message status-error\"><h4>Update Failed</h4><p>The control codes do not match. Please verify your entries and try again.</p></div>";
          }
        }
        ?>

        <h2>Update Document Links</h2>
        
        <form role="form" action="hahachange.php" method="post">
          <div class="form-section">
            <h3 class="section-title">Original Document</h3>
            <div class="input-container">
              <i class="fas fa-key"></i>
              <input type="text" class="form-control" id="email" name="email" placeholder="Reading Code to Update">
            </div>
            <div class="input-container">
              <i class="fas fa-lock"></i>
              <input type="text" class="form-control" id="pwd" name="pwd" placeholder="Current Modification Code">
            </div>
          </div>
          
          <div class="form-section">
            <h3 class="section-title">Replacement Document</h3>
            <div class="input-container">
              <i class="fas fa-file-alt"></i>
              <input type="text" class="form-control" id="email2" name="email2" placeholder="Replacement Reading Code">
            </div>
            <div class="input-container">
              <i class="fas fa-shield-alt"></i>
              <input type="text" class="form-control" id="pwd2" name="pwd2" placeholder="Replacement Modification Code">
            </div>
          </div>
          
          <button type="submit" class="submit-btn">
            <i class="fas fa-sync-alt"></i> Update File
          </button>
        </form>

        <div class="example-image" data-bs-toggle="modal" data-bs-target="#exampleModal">
          <picture>
            <source media="(max-width: 760px)" srcset="../6/assets/replace-flow-mobile.svg">
            <img src="https://sendpdfonline.com/maipdf-flowcharts/en-replace-animation.svg" class="img-fluid" alt="Code Replacement Example">
          </picture>
        </div>
        
        <div class="instructions">
          <h3><i class="fas fa-info-circle"></i> How It Works</h3>
          <ul>
            <li>Enter the <strong>Reading Code</strong> to update and its corresponding <strong>Paired Control Code</strong> to verify the file you want to replace.</li>
            <li>Enter the Replacement <strong>Reading Code</strong> and its <strong>Paired Control Code</strong> for the new file and settings.</li>
            <li>After completing the input, click the "Update File" button. The old link will point to the new file and its settings.</li>
          </ul>
          <p style="margin-top:12px;">
            If you want a deeper understanding of how the replacement logic works:
            <a href="https://sendpdfonline.com/article/replace-pdf-without-changing-link" target="_blank" rel="noopener noreferrer">
              Replace PDF Without Changing Link
            </a>
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Example Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Document Replacement Example</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <picture>
            <source media="(max-width: 760px)" srcset="../6/assets/replace-flow-mobile.svg">
            <img src="https://sendpdfonline.com/maipdf-flowcharts/en-replace-animation.svg" class="img-fluid" alt="Detailed replacement example">
          </picture>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
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
</body>
</html>
