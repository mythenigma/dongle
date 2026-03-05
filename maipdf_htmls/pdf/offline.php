






<script>
      var d = new Date();
      document.cookie ="check="+encodeURI(d);  
      document.cookie ="usertime2="+encodeURI(d);d=d+'maipdf';
      var maigua = d.indexOf("0800");
    if(maigua>1){
     // window.location.href = "https://pdf.maitube.com/pdf/offline.php";
    }
      //window.location.href = "https://pdf.maitube.com/pdf/pprevent.php";
     // console.log(usertime2);
       
</script>








<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="keywords" content="pdf,pdf expiration,pdf分享,pdf安全,pdf online share,pdf次数,pdf限制">
  <meta name="description" content="帮助设置PDF文件的阅读次数和阅读时间，并有效控制PDF文件未经允许的传播">
  <meta name="author" content="MaiPDF">

  <title>MaiPDF安全-设置PDF的打开次数和时长-PDF生成链接</title>

  <!-- Font Awesome Icons -->
   <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.13.1/css/all.min.css" rel="stylesheet" type="text/css">

  <!-- Plugin CSS -->
  <link href="vendor/magnific-popup/magnific-popup.css" rel="stylesheet">

  <!-- Theme CSS - Includes Bootstrap -->
  <link href="css/creative.min.css" rel="stylesheet">
<script src="js/dropzone/dropzone.js"></script>
<script src="js/dropzone/dropzone-amd-module.js"></script>
    <link rel="stylesheet" href="js/dropzone/dropzone.css">
<link rel="stylesheet" href="js/dropzone/basic.css">
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
      <a class="navbar-brand js-scroll-trigger" style="color: Blue;" href="../#page-top">MaiPDF</a>
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span style="color: Red;"> <i class="fas  fa-th "></i></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto my-2 my-lg-0">
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="#about">上传文件</a>
          </li>
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" id="anquan" href="#services">安全设置</a>
          </li>
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="#contact">阅读链接</a>
          </li>
             <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="https://www.maipdf.com/qr.php">二维码工具</a>
          </li>
		  <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="https://maipdf.com">English</a>
          </li>
		  
		    <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="https://maipdf.com/read/web/maipdf.html">打印受保护的PDF</a>
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
          <h1 id='diyi' class=" text-white font-weight-bold">Maipdf 提供了两款功能强大的工具，帮助用户设置 PDF 文件的打开次数，确保分享的文档更具控制性和安全性</h1>
		  
          <hr class="divider my-4">
        </div>
        <div class="col-lg-8 align-self-baseline">
          <p class="text-white-75 font-weight-light mb-5">在线分享 PDF：

这款工具允许用户将 PDF 文件在线上传并设置文件的打开次数。每次打开都会扣除一次次数，用户可以控制文件被访问的次数，达到分享权限的精确管理。当设定的次数用尽后，文件将无法继续查看，非常适合需要有限访问权限的共享场景。</p>
           
	
		<p class="text-white-75 font-weight-light mb-5">将 PDF 转换为 HTML 文件：

另一款工具则允许用户将 PDF 文件转换为一个 HTML 文件，并设置文件的查看次数。通过这种方式，用户可以把原始的 PDF 内容嵌入到网页中，设置文件的访问限制，确保文件只能被特定次数的用户查看，避免无权限的访问。
<a class="btn btn-danger btn-xl" href="https://maipdf.cn"  target="_blank">进入工具首页</a>

</p>  
        </div> 
      </div>
    </div>
  </header>

  <!-- About Section <div class="alert alert-danger">德国网站,国内速度慢</div>-->
  <section class="page-section bg-primary" id="about">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
		 <a class="btn btn-danger btn-xl" href="https://maipdf.cn"  target="_blank">进入工具首页</a>
          <h2 class="text-white mt-0">进入首页选择适合的一款工具</h2>
		  
		  
           <hr class="divider my-4">
		  
   

<input type="hidden" name="file_id" ng-model="file_id" id="file_id"/>



		 


        </div>
      </div>
    </div>

  </section>
  
  <!-- Services Section -->
  <section class="page-section" id="services">
    <div class="container">
	<form   role="form" action="offline.php#contact" method="post">
      <h2 class="text-center mt-0">第二步：设置阅读次数和天数</h2>
	  <h5 class="text-center mt-0"  style="color:blue;">次数和天数先到先生效</h5>
        <h6 class="text-center mt-0"  style="color:blue;">手机可用Adobe打开*电脑可直接打开</h6>

      <hr class="divider my-4">
      <div class="row">
	  
        <div class="col-lg-3 col-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-file-pdf text-primary mb-4"></i>
            <h3 class="h4 mb-2">文件名</h3>
            <p class="text-muted mb-0">	
			<input type="text" class="form-control" id="name" name="sender"
			 value="<?php empty($_FILES)? print "点击上传才行": print "/".$year."/".$month."/".$week."/".$_FILES["file"]["name"]; ?>" readonly="readonly">
			</p>
          </div>
        </div>
        <div class="col-lg-3 col-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-folder-open text-primary mb-4"></i>
            <h3 class="h4 mb-2">可打开的次数</h3>
            <p class="text-muted mb-0"><input class="form-control"  type="number"  name="limit" placeholder="输入整数"></p>
          </div>
        </div>
        <div class="col-lg-3 col-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-hourglass-start text-primary mb-4"></i>
            <h3 class="h4 mb-2">可打开的天数</h3>
            <p class="text-muted mb-0"><input class="form-control"  type="number"  name="password" placeholder="上限1000天"></p>
          </div>
        </div>
        <div class="col-lg-3 col-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-lock text-primary mb-4"></i>
			<h5 class="h4 mb-2" style="font-size:1.2em;font-weight:bold;">所有操作系统</h5>
           
			<p class="text-muted mb-0"><input type="submit" value="生成文件" class="btn btn-success"></p>
          </div>
        </div>
      </div>
	  </form>

  </section>

	<?php
		if(isset ($_POST['limit']) ){
		    $limit=	htmlspecialchars($_POST['limit']);
			if(isset ($_POST['password']) ){
			 $password=htmlspecialchars($_POST['password']);
			// echo $_SESSION["url"];
             if($password<1 || $limit<1){
              
                exit("<script>
                 document.getElementById(\"diyi\").className=\"text-warning\";
               document.getElementById(\"diyi\").innerHTML = 
                \"阅读时间太短了<br>重新上传文件设置时间吧\";</script>");
                }
		     $url = $_POST['sender'];
		     $keyurl = $_POST['sender'];
			 // $zhangai = $_POST['zhangai'];
			$identifier=$url.rand(0,100);
			$identifier=md5($identifier);
			//$identifier=crypt($identifier,'of');
			if($password>1000){
        $password=60;
      }
			
			// 在这里开始写文件。。。。。。 就是验证csv
      $dname='joe'.$url;
			$url=explode('.pdf', $url);
			$downloadname=explode('/', $dname);
     




			//下面开始写我如何修改这个上传的文件
			$keypdf='yes'.$keyurl;
			//echo $keypdf;
			$coder=base64_encode(file_get_contents($keypdf));
      
      $identifier=$coder[150].$coder[170].substr($identifier,2); 
// yidong
      $yanhuo='yes/'.$year.'/'.$month.'/'.$week.'/offline/'.$identifier.'.csv';
      $yanhuopdf='yes'.$url[0].'.html';
      

      $file=fopen($yanhuo,'w+');
      $jieguo[0]=$limit;
      $jieguo[1]=$password;
      $jieguo[2]= date("Y-m-d",strtotime("+$jieguo[1] day"));
      $jieguo[3]=$url[0];
      //echo date("Y/m/d").$jieguo[2];
      //echo "明天:",date("Y-m-d",strtotime("+1 day")),"\n";
      $today=date("Y-m-d");
      if(strtotime($today)<strtotime($jieguo[2])){
      }
      fputcsv($file,$jieguo);
      fclose($file);
// yidong 
			//echo $coder;  //var pdfData = atob('');
      $jsyanhuo=$year.'*'.$month.'*'.$week.'*'.$identifier;





     // echo $jsyanhuo; //" var beforejieguo = '".$jsyanhuo."';";
     // $part0="<script> var beforejieguo = '".$jsyanhuo."'; </script> ";
	   $part0="<script> var beforejieguo = '".$jsyanhuo."'; filenameofthisfile='".$downloadname[5]."';</script> ";
		//	$wholebase= "var pdfData = atob('".$coder."'); ";
      $wholebase= "var pdfdata = '".$coder." '; ";  
			$file=fopen($yanhuopdf,'w');
			$part1=file_get_contents("img/part1.txt");
			$part3=file_get_contents("img/part3.txt");
      fwrite($file, $part0);
			fwrite($file, $part1);
			fwrite($file, $wholebase);
			fwrite($file, $part3);
			fclose($file);
		 }
		}  
	?>
     <!--  <h2 class="mb-4">Your Reading is Created</h2>-->
  <!-- Contact Section -->
  <section class="page-section" id="contact">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
		
		 <a class="btn btn-danger btn-xl" href="https://item.taobao.com/item.htm?spm=a1z10.3-c-s.w4002-22366977349.15.65e44454iQRRDh&id=609902784704"  target="_blank">打赏-资助</a>
		<hr class="divider my-4">
        
	
		
          
          <p class="text-muted mb-5">
		  <h5 id='Copied'></h2>
		
		     
		      <div class="container text-center">
			  <h2 class="mb-4">离线文件已生成 </h2>
			 <h5 class="mb-4">阅读码：<?php	 echo  $identifier; ?></h5>
			  <h5 class="mb-4">修改码：<?php	$identifier2='joe'.$identifier; echo  $year.'*'.$month.'*'.$week.'*'.crypt($identifier2,'ku'); ?></h5>
			  <a class="btn btn-info btn-xl" href="https://maipdf.com/offlinechange.php" target="_blank">修改/查看(阅读设置)</a>
			  <a class="btn btn-info btn-xl" href="<?php	echo $yanhuopdf ?>"  download="">下载您的加密PDF</a>
              </div>
				
		  </p>

        </div>
      </div>
	  
    </div>

  </section>
<script>
	  function myFunction() {
  /* Get the text field */
 
  var copyText = document.getElementById("myInput");
  /* Select the text field */
  copyText.select();
  copyText.setSelectionRange(0, 99999); /*For mobile devices*/

  /* Copy the text inside the text field */
  document.execCommand("copy");

  /* Alert the copied text */
  document.getElementById("Copied").innerHTML = "已复制";
}
</script> 
  <!-- Footer -->
  <footer class="bg-light py-5">
    <div class="container">
      <div class="small text-center text-muted">Copyright &copy; 2026 joe@pdfhost.online</div>
    
    </div>
  </footer>

  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Plugin JavaScript -->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="vendor/magnific-popup/jquery.magnific-popup.min.js"></script>

  <!-- Custom scripts for this template -->
  <script src="js/creative.min.js"></script>


<script type="text/javascript" src="qrcode.min.js"></script>
<script>
var qrcode = new QRCode(document.getElementById("qrcode"), {
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
