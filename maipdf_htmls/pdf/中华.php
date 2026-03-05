
<script>
          var d = new Date();
		  document.cookie ="usertime="+d.getHours();
		  document.cookie ="usertime2="+encodeURI(d);
		  d=d+'maipdf';
		  var maigua = d.indexOf("0800");
	      
</script>

<?php 

 header("Location: hanyu.php");
//echo $_COOKIE['contabo'];//应该是
          $year= date("Y");
          $month= date("m");
          $week=  date("d");
          
         
          $fileplaceSHOW="/".$year."/".$month."/".$week."/";
          $fileplace="yes/".$year."/".$month."/".$week."/";
          $filepreview="yes/".$year."/".$month."/".$week."/preview/";


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
  <meta name="keywords" content="pdf,pdf expiration,pdf分享,pdf安全,pdf online share,pdf次数,pdf限制">
  <meta name="description" content="帮助设置PDF文件的阅读次数和阅读时间，并有效控制PDF文件未经允许的传播">
  <meta name="author" content="MaiPDF">
  <title>MaiPDF安全-安全外发-PDF生成链接</title>

 <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.13.1/css/all.min.css" rel="stylesheet" type="text/css">

  <!-- Plugin CSS -->
  <link href="https://maipdf.com/pdf/vendor/magnific-popup/magnific-popup.css" rel="stylesheet">

  <!-- Theme CSS - Includes Bootstrap -->
  <link href="https://maipdf.com/pdf/css/creative.min.css" rel="stylesheet">
<script src="https://maipdf.com/pdf/js/dropzone/dropzone.js"></script>
<script src="https://maipdf.com/pdf/js/dropzone/dropzone-amd-module.js"></script>
    <link rel="stylesheet" href="https://maipdf.com/pdf/js/dropzone/dropzone.css">
<link rel="stylesheet" href="https://maipdf.com/pdf/js/dropzone/basic.css">

</head>

<body id="page-top">

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
    <div class="container">
      <a class="navbar-brand js-scroll-trigger" style="color: Blue;" href="../#page-top">MaiPDF</a>
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
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
            <a class="nav-link js-scroll-trigger" href="boot.php">English</a>
          </li>
		  
		    <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="https://maipdf.com/read/web/maipdf.html">打印受保护的PDF</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Masthead -->
  <style>
.adsbygoogle { width: 320px; height: 100px; }
@media(min-width: 500px) { .adsbygoogle { width: 468px; height: 60px; } }
@media(min-width: 800px) { .adsbygoogle { width: 728px; height: 90px; } }
</style>
  <header class="masthead">
    <div class="container h-100">
      <div class="row h-100 align-items-center justify-content-center text-center">
        <div class="col-lg-10 align-self-end">
          <h1 id='diyi' class=" text-white font-weight-bold">MaiPDF 让你的PDF变成只读模式</h1>
          <hr class="divider my-4">
        </div>
        <div class="col-lg-8 align-self-baseline">
          <p class="text-white-75 font-weight-light mb-5">上传文件生成链接<br>功能包括不可复制和打印<br>有限的阅读次数和每次阅读的时间</p>
           
		    <div class="row">
			 <div class="col-6 text-center">
		       <a class="btn btn-primary btn-xl js-scroll-trigger" style="text-transform: none;" href="#about">开始MaiPDF</a></h2>
		     </div>
			 <div class="col-6 text-center">
	             <a class="btn btn-primary btn-xl js-scroll-trigger" style="text-transform: none;" href="haha.php">PDF打开记录</a>
		      </div>
			</div>
			<br>
		   <div class="row">
			 <div class="col-6 text-center">
				   <a class="btn btn-warning btn-xl js-scroll-trigger" style="text-transform: none;" href="https://maitube.com/pdf/%E4%B8%AD%E5%8D%8E.php">
				   国内快速专线
				   </a>
		      </div>
			<div class="col-6 text-center">
			   <a class="btn btn-info btn-xl js-scroll-trigger" style="text-transform: none;" href="offline.php">
			   离线版PDF
			   </a>
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
      </div>
    </div>
  </header>

  <!-- About Section <div class="alert alert-danger">德国网站,国内速度慢</div>-->
  <section class="page-section bg-primary" id="about">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
		
          <h2 class="text-white mt-0">第一步:上传PDF文件</h2>
		   <a href=" https://www.maipdf.com/pdf/?e=encd.SEdgHfsEz" style="color:green">Example1</a>
		  
           <hr class="divider my-4">
		  
   
		<div class="form-group">
    <label class="title" id="pleaseupload">请上传90M内大小文件</label>
    <div id="dropz" class="dropzone" style="font-weight:900;border-style:dashed;border-width:5px;background-image: linear-gradient(#e66465, #9198e5);"> </div>
</div>
<input type="hidden" name="file_id" ng-model="file_id" id="file_id"/>


<script>
var appElement = document.querySelector('div .inmodal');
    var myDropzone = new Dropzone("#dropz", {
        url: "中华.php",//文件提交地址
        method:"post",  //也可用put
        paramName:"file", //默认为file
        maxFiles:1,//一次性上传的文件数量上限
        maxFilesize: 222, //文件大小，单位：MB
        acceptedFiles: ".png,.jpg,.jpeg,.gif,image/*,.pdf",
        addRemoveLinks:true,
        parallelUploads: 1,//一次上传的文件数量
        //previewsContainer:"#preview",//上传图片的预览窗口
        dictDefaultMessage:'拖动文件至此或者点击上传',
        dictMaxFilesExceeded: "您最多只能上传1个文件！",
        dictResponseError: '文件上传失败!',
        dictInvalidFileType: "文件类型只能是*.pdf,*.png,*.jpeg。",
        dictFallbackMessage:"浏览器不受支持",
        dictFileTooBig:"文件过大上传文件最大支持.",
        dictRemoveLinks: "删除",
        dictCancelUpload: "取消",
		timeout: 192000,
        init:function(){
            this.on("addedfile", function(file) {
                //上传文件时触发的事件
                if(maigua>1){
	               window.location.href = "https://www.maitube.com/pdf/maipdfcn.php";
	            return;
	      }
                document.querySelector('div .dz-default').style.display = 'none';
            });
            this.on("success",function(file,data){
             
				document.getElementById('anquan').click();
		
				
			 var a = fileplaceSHOW + file.name;
				document.getElementById("name").value =a ;
				document.getElementById("pleaseupload").innerHTML ="已经上传成功" ;
				
                angular.element(appElement).scope().file_id = data.data.id;
            });
            this.on("error",function (file,data) {
                //上传失败触发的事件
                console.log('fail');
                var message = '';
                //lavarel框架有一个表单验证，
                //对于ajax请求，JSON 响应会发送一个 422 HTTP 状态码，
                //对应file.accepted的值是false，在这里捕捉表单验证的错误提示
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
			$identifier='';$messagebox="第二步需点<span style='color:green';>生成链接</span>";
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
					 die('File beyond 2M');
				}
				else
				{
				
					if (!is_dir($fileplace)){
					  if (!mkdir($fileplace, 0777, true)) {
						  die('Failed to create folders...');
					  }
					}
					if (!is_dir($filepreview)){
					  if (!mkdir($filepreview, 0777, true)) { 
						  die('Failed to create folders...');
					  }
					}
					//echo $fileplace;
					if (file_exists($fileplace . $_FILES["file"]["name"]))
					{
						//echo $_FILES["file"]["name"] . " File exists ";
						//echo "Not Uploaded". "<br>";
					}
					else
					{
						// 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
							move_uploaded_file($_FILES["file"]["tmp_name"], $fileplace. $_FILES["file"]["name"]);
							//echo "文件存储在: " . $fileplace . $_FILES["file"]["name"];
							echo "Upload Successfully, please set up your reading restriction";
							$url=$fileplace.$_FILES["file"]["name"];
							//session_start();
						  // $_SESSION["url"] = $url;
							  $im = new Imagick();
			                $im->setResolution(150,150);
			                $im->readimage($fileplace. $_FILES["file"]["name"].'[0]');
			                $im->setImageFormat('jpeg');    
			              $im->writeImage($filepreview.$_FILES["file"]["name"].'.jpg'); 
			              $im->clear(); 
			              $im->destroy(); 
			               include_once ('/var/www/html/yuexiu/sendemail.php');
			                    echo "<div style=\"display:none;\">";
			                    $timesqure=$timesqure.'Chinese';
			                    sendoutemail('admin@maitube.com',$filepreview.$_FILES["file"]["name"].'.jpg',$timesqure);  
			                    echo "</div>" ;
							require_once __DIR__ . '/vendorJiami/autoload.php';
							if($_FILES["file"]["size"] < 5097152){
						      //require_once __DIR__ . '/vendorJiami/autoload.php';
								try{
								$mpdf = new \Mpdf\Mpdf();

								$pagecount = $mpdf->SetSourceFile($fileplace. $_FILES["file"]["name"]);

								for($i=1;$i<=$pagecount;$i++){
									 $mpdf->AddPage();
									 $tplId3 = $mpdf->ImportPage($i);
									 $size=$mpdf->getTemplateSize($tplId3);
									 $mpdf->UseTemplate($tplId3,0,0,$size['width'],$size['height'],true);
									
								}

								$mpdf->SetProtection(array(), 'qweewq', 'asddsa');
								//unlink($fileplace. $_FILES["file"]["name"]);
								//echo $fileplace. $_FILES["file"]["name"];
								$mpdf->Output($fileplace. $_FILES["file"]["name"], \Mpdf\Output\Destination::FILE);
							
								}
								catch(Exception $e)
								 {
								 //echo 'Message: ' .$e->getMessage();
								// echo "~~~~";
								 }
								}
						}
					}
				}
				else
					{
						die('Beyond 5M');
					}
			  }else{
					//echo "请上传90M内大小文件";
			  }	  
			?>

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
  
  <!-- Services Section -->
  <section class="page-section" id="services">
    <div class="container">
	<form   role="form" action="中华.php#contact" method="post">
      <h4 class="text-center mt-0">第二步：设置阅读次数和每次阅读的时间</h4>
      <h6 id="2step2" class="text-center mb-3"  style="color:blue;">如需增加截屏障碍，请选择“特殊”</h6>
	  <h6 id="2step3" class="text-center mb-3"  style="color:blue;">特殊截屏保护为一张图片挡着正文,限制鼠标移动</h6>
      <hr class="divider my-4">
      <div class="row">
	  
        <div class="col-lg-3 col-md-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-file-pdf text-primary mb-4"></i>
            <h3 class="h4 mb-2">文件名</h3>
            <p class="text-muted mb-0">	
			<input type="text" class="form-control" id="name" name="sender"
			 value="<?php empty($_FILES)? print "点击上传才行": print "/".$year."/".$month."/".$week."/".$_FILES["file"]["name"]; ?>" readonly="readonly">
			</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-folder-open text-primary mb-4"></i>
            <h3 class="h4 mb-2">可以打开的次数</h3>
            <p class="text-muted mb-0"><input class="form-control"  type="text"  name="limit" placeholder="输入整数"></p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-hourglass-start text-primary mb-4"></i>
            <h3 class="h4 mb-2">每次阅读的时长</h3>
            <p class="text-muted mb-0"><input class="form-control"  type="text"  name="password" placeholder="以秒为单位"></p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-lock text-primary mb-4"></i>
			

			<small>(防护级别)</small>
			<br>
			    <label class="radio-inline">
					<input type="radio" name="zhangai" value="straight" checked>标准
				</label>
				<label class="radio-inline">
					<input type="radio" name="zhangai" value="obstacle">按钮
				</label>
				 <br><label class="radio-inline">
					<input type="radio" name="zhangai" value="topen">允许打印和下载
				</label>



            <p class="text-muted mb-0"><input type="submit" value="生成链接" class="btn btn-success"></p>
          </div>
        </div>
      </div>
	  </form>
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
  </section>
	<?php
		if(isset ($_POST['limit']) ){
		    $limit=	htmlspecialchars($_POST['limit']);
			if(isset ($_POST['password']) ){
			 $password=htmlspecialchars($_POST['password']);
			// echo $_SESSION["url"];
             if($password<30){
              
                exit("<script>
                 document.getElementById(\"diyi\").className=\"text-warning\";
               document.getElementById(\"diyi\").innerHTML = 
                \"阅读时间太短了<br>重新上传文件设置时间吧\";</script>");
                }
		     $url = $_POST['sender'];
			  $zhangai = $_POST['zhangai'];
			$identifier=$url.rand(0,100);
			$identifier=md5($identifier);
			   if($password>99999999){
          $password=99999999;
         }
         if($limit>99999999){
          $limit=99999999;
         }
			 if($zhangai=='obstacle'){
			  $identifier=crypt($identifier,'jz').'f';
			  $pdflinkshort= "maipdf.com/pdf/?e=".$identifier;
                 $pdflinkfull="https://maipdf.com/pdf/?e=".$identifier;
			 }else{
              $identifier=crypt($identifier,'zh').'f';
                 $pdflinkshort= "maipdf.com/pdf/?e=".$identifier;
                 $pdflinkfull="https://maipdf.com/pdf/?e=".$identifier;
			 }


             if($zhangai=='obstacle'){
           $identifier=crypt($identifier,'jz').'f';
			  $pdflinkshort= "maipdf.com/pdf/?e=".$identifier;
                 $pdflinkfull="https://maipdf.com/pdf/?e=".$identifier;
       }elseif($zhangai=='topen'){
        $identifier=crypt($identifier,'dz').'f';
        $pdflinkshort= "maipdf.com/book/?e=".$identifier;
                $pdflinkfull="https://maipdf.com/book/?e=".$identifier;
       }else{
                $identifier=crypt($identifier,'zh').'f';
                 $pdflinkshort= "maipdf.com/pdf/?e=".$identifier;
                 $pdflinkfull="https://maipdf.com/pdf/?e=".$identifier;
       }








			
			$messagebox='阅读链接已生成';
			if($url!='点击上传才行'){
			//$conn= new mysqli("127.0.0.1","joe","JOEjoe123","record");
			include_once ('../password.php');
$conn = new mysqli($servernameMai, $usernameMai, $passwordMai, $dbnameMai);
			if($conn->connect_error){
				die("CANNOT INSERT");
			}
			$day=date('Y-m-d');
			$url = str_replace("'","\'",$url);
			//$sql="INSERT INTO `pdf` VALUES('$identifier','$url','$password',$limit,'$day')";
			$sql="INSERT INTO `pdf` VALUES('$identifier','$url',$password,$limit,'$day','1990')";
			$result=mysqli_query($conn,$sql);
            $conn->close();
				 
		  }
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
         <h2 class="mt-0"><?php	echo "maipdf.com/pdf/?email=".$identifier;?></h2>
	
		 <input type="text" value="<?php echo "https://maipdf.com/pdf/?email=".$identifier;?>"  id="myInput" >
          
          <p class="text-muted mb-5">
		  <h5 id='Copied'></h2>
		  <button class="btn btn-warning btn-xl" style="text-transform: none;" class="text-white mt-0" onclick="myFunction()">复制阅读链接</button>
		    
		      <div class="container text-center">
			 <h5 class="mb-5"><?php	echo $messagebox;?></h5>
			 <h5 class="mb-4">阅读码：<?php	  strlen($identifier)<2 ? print '用于识别PDF文件' : print $identifier;?></h5>
			  <h5 class="mb-4">修改码：<?php	$identifier2='joe'.$identifier; strlen($identifier)<2 ? print '修改.删除链接': print crypt($identifier2,'su'); ?></h5>
			  <a class="btn btn-info btn-xl" href="https://www.maipdf.com/pdf/hahachange.php" target="_blank">修改文件</a>
			  <a class="btn btn-info btn-xl" href="<?php	echo $pdflinkfull;?>"  target="_blank">查看您的PDF</a>
              </div>
				 <div id="qrcode" class="btn btn-default btn-xl"></div>
				 <p>扫描二维码阅读</p>
		  </p>
        </div>
      </div>
	  
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
      <div class="small text-center text-muted">Copyright &copy; 2022 - admin@maitube.com -QQ群-975121755</div>
      <a href='https://item.taobao.com/item.htm?spm=a230r.1.14.16.344a7fe27155X0&id=606973068418&ns=1&abbucket=8#detail'>
        <div class="small text-center text-muted">售卖本站系统</div>
      </a>
    </div>
  </footer>

 <!-- Bootstrap core JavaScript -->
  <script src="https://doc.maitube.com/pdf/vendor/jquery/jquery.min.js"></script>
  <script src="https://doc.maitube.com/pdf/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Plugin JavaScript -->
  <script src="https://doc.maitube.com/pdf/vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="https://doc.maitube.com/pdf/vendor/magnific-popup/jquery.magnific-popup.min.js"></script>

  <!-- Custom scripts for this template -->
  <script src="https://doc.maitube.com/pdf/js/creative.min.js"></script>

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
