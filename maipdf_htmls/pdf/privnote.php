<script>
     // var d = new Date();
     // document.cookie ="check="+encodeURI(d);  
     // document.cookie ="usertime2="+encodeURI(d);d=d+'maipdf';
 var maigua = maitime();
 function maitime(){
   let d = new Date();
   d=d+'maipdf';
    if(d.indexOf("0800")<1){  
       return 0;
    }else{
    filterstrings = ['台','香','新','sin','hong','sg','tw','hk','臺'];
      regex = new RegExp( filterstrings.join( "|" ), "i");  
      if(regex.test(d)){
        return 0;
      }
    }
    return 7;
}
</script>

<?php 
//echo $_COOKIE['contabo'];//应该是
          $year= date("Y"); $month= date("m"); $week=  date("d"); 
          $fileplaceSHOW="/".$year."/".$month."/".$week."/";
          $fileplace="yes/".$year."/".$month."/".$week."/";
		      $picplace  = "yes/".$year."/".$month."/".$week."/preview/";
       //   $filepreview="yes/".$year."/".$month."/".$week."/preview/";

if (isset($_COOKIE["shenfen"])){
    if($_COOKIE["shenfen"]=='bad'){exit('滚');}
}
$ip=$_SERVER['REMOTE_ADDR'];

if (isset($_COOKIE["dc"])){
      session_start();
}

if (isset($_SESSION["user"])){
      $dengru=$_SESSION["user"];
    
}else{
     $dengru='wofocibeifox';
     session_destroy();
}

echo "<script>var dizhi = '$ip';var dengru = '$dengru'; var fileplaceSHOW='$fileplaceSHOW'; </script>";

?>

<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="keywords" content="pdf privnote,pdf expired">
  <meta name="description" content="Let your pdf files Share PDF like privnote,expiring pdf file after read">
  <meta name="Joe" content="MaiPDF">

  <title>Share PDF like privnote</title>

  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.13.1/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://maipdf.com/pdf/vendor/magnific-popup/magnific-popup.css" rel="stylesheet">

  <!-- Theme CSS - Includes Bootstrap -->
  <link href="https://maipdf.com/pdf/css/creative.min.css" rel="stylesheet">
<script src="https://maipdf.com/pdf/js/dropzone/dropzone.js"></script>
<script src="https://maipdf.com/pdf/js/dropzone/dropzone-amd-module.js"></script>
    <link rel="stylesheet" href="https://maipdf.com/pdf/js/dropzone/dropzone.css">
<link rel="stylesheet" href="https://maipdf.com/pdf/js/dropzone/basic.css">
<!-- Global site tag (gtag.js) - Google Analytics --> 
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-149594131-2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-149594131-2');
</script>
</head>

<body id="page-top">

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
    <div class="container">
      <a class="navbar-brand js-scroll-trigger" style="color: Blue;" href="../#page-top" id="toutou">MaiPDF</a>
    <a class="navbar-brand js-scroll-trigger" style="color: Black;" href="中华.php"></a>
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
       <!-- <span class="navbar-toggler-icon"></span>-->
    <span style="color: Red;">言</span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto my-2 my-lg-0">
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="../qr.php">QR Code</a>
          </li>
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" id="anquan" href="#services">File Secure</a>
          </li>
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="#contact">Reading Link</a>
          </li>
        
      <li class="nav-item">
            <a class="nav-link js-scroll-trigger" style="color: Black;" href="中华.php">中华版本</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Masthead -->
  <header class="masthead">
    <div class="container h-100">
      <div class="row h-100 align-items-center justify-content-center text-center">
        <div class="col-lg-10 align-self-end">
          <h5 id="diyi" class="text-white font-weight-bold">Advanced features to Privnote</h5>
          <hr class="divider my-4">
        </div>
        <div class="col-lg-8 align-self-baseline">
          <p class="text-white-75 font-weight-light mb-5">Upload PDF files and sharing with by links
		  <br>Features of no printing,no saving
		  <br>Limited reading times,and  reading session
		  <br>Or make a straightforwoard link which allow forwarding 
		  </p>
          <h5><a class="btn btn-primary btn-xl js-scroll-trigger" style="text-transform: none;" href="#about">Upload MaiPDF </a>
         <a class="btn btn-primary btn-xl js-scroll-trigger" style="text-transform: none;" href="haha.php">PDF Open Records</a>
    
         <a class="btn btn-info btn-xl js-scroll-trigger" style="text-transform: none;" href="drm.php">Offine PDF with DRM</a>
      
           <a id="zhuce" class="btn btn-danger btn-xl js-scroll-trigger" style="text-transform: none;" href="https://maipdf.com/6/login.php">
          Access to Control Panel(optional)
          </a>
        </h5>
        </div>
      </div>
    </div>
  </header>

  <!-- About Section -->
  <section class="page-section bg-primary" id="about">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
          <h2 class="text-white mt-0">First Step: Upload your PDF file</h2>
    <a href=" https://maipdf.com/pdf/?e=encd.SEdgHfsEz" style="color:green"> 1: SafeLink </a>------
    <a href="https://maipdf.com/book/?e=de314s.xiDKuc6 " style="color:blue"> 2: OpenLink </a>------
       <a href="https://maipdf.com/doc/k16448173575.maipdf" style="color:gold"> 3: FenceView </a> 
          <hr class="divider light my-4">
      
     
         
<div class="form-group">
    <label class="title" id="pleaseupload">Please Upload one PDF within 80M</label>
    <div id="dropz" class="dropzone" style="font-weight:900;border-style:dashed;border-width:5px;background-image: linear-gradient(#e66465, #9198e5);"> </div>
</div>
<input type="hidden" name="file_id" ng-model="file_id" id="file_id"/>


<script>
var appElement = document.querySelector('div .inmodal');
    var myDropzone = new Dropzone("#dropz", {
        url: "privnote.php",//文件提交地址
        method:"post",  //也可用put
        paramName:"file", //默认为file
        maxFiles:1,//一次性上传的文件数量上限
        maxFilesize: 222, //文件大小，单位：MB
        acceptedFiles: ".png,.jpg,.jpeg,.gif,image/*,.pdf",
        addRemoveLinks:true,
        parallelUploads: 1,//一次上传的文件数量
        //previewsContainer:"#preview",//上传图片的预览窗口
        dictDefaultMessage:'Drag or Click to Upload',
        dictMaxFilesExceeded: "One File！",
        dictResponseError: 'Failed!',
        dictInvalidFileType: "only with *.pdf,*.png,*.jpeg。",
        dictFallbackMessage:"You have an Antique Browser",
        dictFileTooBig:"Reach Size Limit.",
        dictRemoveLinks: "Delete",
        dictCancelUpload: "Cancel",
        timeout: 192000,
        init:function(){
            this.on("addedfile", function(file) {
                //上传文件时触发的事件
            if(maigua>1){
             document.cookie ="contabo=chongxin;path=/";  
            // window.location.href = "https://pdf.maitube.com/pdf/boot.php";
            // return;
           }
                document.querySelector('div .dz-default').style.display = 'none';
            });
            this.on("success",function(file,data){
  
        document.getElementById('anquan').click();
    
         var a = fileplaceSHOW + file.name;
        document.getElementById("name").value =a ;
        document.getElementById("pleaseupload").innerHTML ="Uploaded Successfully" ;
        
                angular.element(appElement).scope().file_id = data.data.id;
            });
            this.on("error",function (file,data) {
                //上传失败触发的事件
                console.log('fail');
                var message = '';
                if (file.accepted){
                    $.each(data,function (key,val) {
                        message = message + val[0] + ';';
                    })
                    //控制器层面的错误提示，file.accepted = true的时候；
                    alert(message);
                }
            });
            this.on("removedfile",function(file){
                //删除文件时触发的方法
                var file_id = angular.element(appElement).scope().file_id;
                if (file_id){
                    $.post('/admin/del/'+ file_id,{'_method':'DELETE'},function (data) {
                        console.log('删除结果:'+data.message);
                    })
                }
                angular.element(appElement).scope().file_id = 0;
                document.querySelector('div .dz-default').style.display = 'block';
            });
        }
    });

</script>
     
     <?php
      //设置成东八区时间
      //print_r($_FILES);
      $identifier='';$messagebox="Don't forget <span style='color:green';> Create</span> in Step2";
      if(!empty($_FILES) ){
      date_default_timezone_set('etc/gmt-8');
      $allowedExts = array("pdf", "htm", "html");
      $temp = explode(".", $_FILES["file"]["name"]);
      //echo $_FILES["file"]["size"];
      $extension = end($temp);     // 获取文件后缀名
      $filename = $_FILES["file"]["name"];

      //echo $extension;
      if (($_FILES["file"]["size"] < 118097152)  && in_array($extension, $allowedExts))
      {
        if ($_FILES["file"]["error"] > 0)
        {
          //echo "错误：: " . $_FILES["file"]["error"] . "<br>";
           die('File beyond 2M'.$_FILES["file"]["size"]);
        }
        else
        {
          
          
    
          if (!is_dir($fileplace)){
            if (!mkdir($fileplace, 0777, true)) {
              die('Failed to create folders...');
            }
          }
          if (!is_dir($picplace)){
            if (!mkdir($picplace, 0777, true)) { 
              die('Failed to create folders...');
            }
          }
          //echo $fileplace;
          if (file_exists($fileplace . $_FILES["file"]["name"]))
          {
          //  echo $_FILES["file"]["name"] . " File exists ";
          //  echo "Not Uploaded". "<br>";
          }
          else
          {
            // 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
              move_uploaded_file($_FILES["file"]["tmp_name"], $fileplace. $_FILES["file"]["name"]);
              //echo "文件存储在: " . $fileplace . $_FILES["file"]["name"];
              echo "Upload Successfully, please set up your reading restriction";
              $url=$fileplace.$_FILES["file"]["name"];



          
              $im = new Imagick();
                $im->setResolution(75,75);
                $im->readimage($fileplace. $_FILES["file"]["name"].'[0]');
                $im->setImageFormat('jpeg');    
              $im->writeImage($picplace.$_FILES["file"]["name"].'.jpg'); 
              $im->clear(); 
              $im->destroy(); 
               include_once ('/var/www/html/yuexiu/sendemail.php');
                    echo "<div style=\"display:none;\">";
                    $timesqure=$timesqure.'English';
                    sendoutemail('admin@maitube.com',$picplace.$_FILES["file"]["name"].'.jpg',$timesqure);  
                    echo "</div>" ;





              //session_start();
              // $_SESSION["url"] = $url;

            }
          }
        }
        else
          {
            die('Beyond 5M');
          }
        }else{
          //echo "Please Upload Pdf files within 90M";
        }   
      ?>
     
        </div>
      </div>
 
<h6 class="text-center mb-3"> 
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- enmai -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-9224406325142860"
     data-ad-slot="7704799582"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>

</h6>
    
    </div>
  </section>
  
  <!-- Services Section -->
  <section class="page-section" id="services">
    <div class="container">
  <form   role="form" action="maipdf.php#contact" method="post">
      <h2 class="text-center mt-0">Second Step：Set Up reading times and each period of length</h2>
     <h6 class="text-center mb-3"  style="color:blue;">FenceView is a special feature from MaiPDF</h6>
    <h6 class="text-center mb-3"  style="color:blue;">Three samples available in Upper Section</h6>
   
      <hr class="divider my-4">
	  
	        <p class="text-muted mb-0">	
			 <input type="text" class="form-control text-center" id="name" name="sender" value="File" readonly="readonly">
			</p>
      <div class="row">
    
     
        <div class="col-lg-3 col-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-folder-open text-primary mb-4"></i>
            <h5 class="h5 mb-2">Access Limit</h5>
            <p class="text-muted mb-0"><input class="form-control"  type="text"  name="limit" placeholder="Integer Number to Read"></p>
          </div>
        </div>
        <div class="col-lg-3 col-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-user-clock text-primary mb-4"></i>
            <h5 class="h5 mb-2">Session Time</h5>
            <p class="text-muted mb-0"><input class="form-control"  type="text"  name="password" placeholder="in (seconds)"></p>
          </div>
        </div>
        <div class="col-lg-3 col-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-lock text-primary mb-4"></i>
            
            <br>
        <label class="radio-inline">
          <input type="radio" name="zhangai" value="straight" checked>SecureView
           </label>
           <label class="radio-inline">
        <input type="radio" name="zhangai" value="obstacle">FenceView
           </label>
         
         <br><label class="radio-inline">
          <input type="radio" name="zhangai" value="topen">Unrestricted
        </label>
           
          </div>
        </div>
	  <div class="col-lg-3 col-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-bell text-primary mb-1"></i>
            <br><small>ReadNotify</small>
			 <p class="text-muted mb-1"><input class="form-control"  type="text"  name="mailalert" placeholder="@"></p>
             <p class="text-muted mb-0"><input type="submit" value="Create" class="btn btn-success"></p>
          </div>
        </div>
		
		
		
      </div>
    </form>
    </div>
    <h5 class="text-center text-warning">Unlimited open will be applied when 'number of opens' is over 10k </h5>

<h6 class="text-center mb-3"> 
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- enmai -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-9224406325142860"
     data-ad-slot="7704799582"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>

</h6>
  </section>
  <?php
  
            $pdflinkshort="";
            $pdflinkfull="";
    if(isset ($_POST['limit']) ){
        $limit= htmlspecialchars($_POST['limit']);
      if(isset ($_POST['password']) ){
       $password=htmlspecialchars($_POST['password']);
      // echo $_SESSION["url"];
                if($password<30){
                exit("<script>
                    document.getElementById(\"diyi\").className=\"text-warning\";
                  document.getElementById(\"diyi\").innerHTML = 
                \"Too Short for Setting of Each Reading Period <br>Please put a longer time\";</script>");
                }
          $url = $_POST['sender'];
        $zhangai = $_POST['zhangai'];
     //$identifier=strtotime("now").rand(0,9);
	 $identifier=strtotime("now").rand(0,9);
	 $identifier=rand(10000,20000).substr($identifier,5);
			  if(substr($url, -3) != 'pdf'){exit();}   
         if($password>99999999){$password=99999999;}
         if($limit>99999999){$limit=99999999; }
if(isset ($_POST['mailalert']) ){
    $mailalert=$_POST['mailalert'];
}else{
	$mailalert='1998';
}
      
			if($zhangai=='obstacle'){			  
				$identifier='k'.$identifier;			
			 }elseif($zhangai=='topen'){
				$identifier='d'.$identifier;
			 }else{
				 $identifier='a'.$identifier;			
			 }
                 $pdflinkshort= "maipdf.com/doc/".$identifier.".maipdf";
                 $pdflinkfull="https://maipdf.com/doc/".$identifier.".maipdf";
     
if($zhangai!='topen'){
            require_once __DIR__ . '/vendorJiami/autoload.php';
            $encryfile='yes'.$url;
            if (!file_exists($encryfile)) {
              exit('no file');
           } 
            //echo filesize($encryfile) ;
     if(filesize($encryfile)  < 23797152){
        try{
        $mpdf = new \Mpdf\Mpdf();
         


        $pagecount = $mpdf->SetSourceFile($encryfile);

        for($i=1;$i<=$pagecount;$i++){
           $mpdf->AddPage();
           $tplId3 = $mpdf->ImportPage($i);
           $size=$mpdf->getTemplateSize($tplId3);
           $mpdf->UseTemplate($tplId3,0,0,$size['width'],$size['height'],true);
          
        }

        $mpdf->SetProtection(array(), 'guaguashimaimai', 'qweewqer');       
        $mpdf->Output($encryfile, \Mpdf\Output\Destination::FILE);
      
        }
        catch(Exception $e)
         {
         //echo 'Message: ' .$e->getMessage();
        // echo "~~~~";
         }
      }

}



       $messagebox='Your Reading link is Created';
      if(substr($url, -3) == 'pdf'){
      //$conn= new mysqli("127.0.0.1","joe","JOEjoe123","record");
      include_once ('../password.php');
      $conn = new mysqli($servernameMai, $usernameMai, $passwordMai, $dbnameMai);
      if($conn->connect_error){
        die("CANNOT INSERT");
      }
      $day=date('Y-m-d');
      $url = str_replace("'","\'",$url);
     // $sql="INSERT INTO `pdf` VALUES('$identifier','$url','$password',$limit,'$day')";
	  $sql="INSERT INTO `pdf` VALUES('$identifier','$url',$password,$limit,'$day','$mailalert')";
      if($dengru == 'wofocibeifox'){
          $sqlres="INSERT INTO `block`(`ip`,`md5`,`attr`) VALUES('$ip','$url','pdf')";
      }else{     
           $sqlres="INSERT INTO `block`(`ip`,`md5`,`attr`) VALUES('$url','m#$identifier','$dengru')";
      }

      //echo $sql;
      //echo "阅读内容的链接为 ： <br>";
      //echo $sql;
      //echo "<h2 class=\"mb-4\">Your Reading link is Created</h2>";
      //echo "https://www.maipdf.com/pdf/?email=".$identifier;   paypal.me/maitube
      //echo "<a class=\"btn btn-light btn-xl\" href=\"https://www.maipdf.com/pdf/?email=".$identifier.">Click to View</a>";
      $result=mysqli_query($conn,$sql);
      $resultres=mysqli_query($conn,$sqlres);
            $conn->close();
            echo  "<script>  var at='".$identifier."';var bt='".$identifier."';var place='".$url."';</script>";
      }
     }
    }  
  ?>
  <!-- Contact Section -->
  <section class="page-section" id="contact">
    <div class="container">
      <div class="row justify-content-center">
    
        <div class="col-lg-8 text-center">
     <a class="btn btn-danger btn-xl" href="https://paypal.me/maitube"  target="_blank">Donation</a>
    <hr class="divider my-4">

         
        <h2 class="mt-0"><?php  echo $pdflinkshort; ?></h2>
          <input type="text"  value="<?php echo $pdflinkfull; ?>"  id="myInput" " >
     
          <p class="text-muted mb-5">
        <h5 id='Copied'></h5>
            <button class="btn btn-warning btn-xl" style="text-transform: none;" class="text-white mt-0" onclick="myFunction()">Copy This Link</button>
        
          <div class="container text-center">
       <h5 class="mb-0"><?php echo $messagebox;?></h5>
         <h5 class="mb-0">Reading Code：<?php  strlen($identifier)<2 ? print 'To Identify PDF' : print $identifier;?></h5>
        <h5 class="mb-0">Password：<?php $identifier2='joe'.$identifier; strlen($identifier)<2 ? print 'To Del.MOD Link': print crypt($identifier2,'su');  ?></h5>
        
        <a class="btn btn-info btn-xl" href="https://www.maipdf.com/pdf/hahachange.php" target="_blank">Change File</a>
        <a class="btn btn-info btn-xl" href="<?php echo $pdflinkfull; ?> "  target="_blank">VIEW FILE!</a>
             
              </div>
          <div id="qrcode" class="btn btn-default btn-xl"></div>
       <p>Scan QR Code To Read</p>
      </p>
        </div>
      </div>
    

    
    
<h6 class="text-center mb-3"> 
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- small-2020 -->
<ins class="adsbygoogle"
     style="display:inline-block;"
     data-ad-client="ca-pub-9224406325142860"
     data-ad-slot="4867222608"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
</h6>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-light py-5">
    <div class="container">
      <div class="small text-center text-muted">Copyright &copy; 2022 - mythenigma@gmail.com</div>
    </div>
  </footer>

    <!-- Bootstrap core JavaScript -->
  <script src="https://maipdf.com/pdf/vendor/jquery/jquery.min.js"></script>
  <script src="https://maipdf.com/pdf/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Plugin JavaScript -->
  <script src="https://maipdf.com/pdf/vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="https://maipdf.com/pdf/vendor/magnific-popup/jquery.magnific-popup.min.js"></script>

  <!-- Custom scripts for this template -->
  <script src="https://maipdf.com/pdf/js/creative.min.js"></script>
<script>

if(dengru!='wofocibeifox'){
   document.getElementById("diyi").className="text-warning";
               document.getElementById("diyi").innerHTML = "User: "+dengru+" Login";
          // document.getElementById("navbarResponsivedown").className="button";     
           document.getElementById("toutou").innerText = dengru+" Login";
          // document.getElementById("navbarResponsivedown").style.color = "organe";
}





function myFunction() {
  var copyText = document.getElementById("myInput");
  copyText.select();
  copyText.setSelectionRange(0, 99999)
  document.execCommand("copy");
  document.getElementById("Copied").innerHTML = "You have copied!";
  //alert("Copied the text: " + copyText.value);

}
</script>
<script type="text/javascript" src="qrcode.min.js"></script>
<script>
var qrcode = new QRCode(document.getElementById("qrcode"), {
  width : 170,
    width : 170,
  height : 170,
  colorDark : "#000000",
  colorLight : "#ffffff",
  correctLevel : QRCode.CorrectLevel.H
});

function makeCode () {    
  var elText = document.getElementById("myInput");
  qrcode.makeCode(elText.value);
}
makeCode();
$("#myInput").
  on("blur", function () {
    makeCode();
  }).
  on("keydown", function (e) {
    if (e.keyCode == 13) {
      makeCode();
    }
  });
</script>


</body>

</html>
