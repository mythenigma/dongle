<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
 <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="keywords" content="pdf,pdf self expiration,pdf self destruction,pdf-drm,pdf online share,pdf encryption,pdf free drm">
  <meta name="description" content="Add free DRM to a PDF file, set the expiration date and limited reading times">
  <meta name="author" content="MaiPDF">

  <title>MaiPDF Offers Free Digital Rights Management (DRM) for PDF Files to Users</title>

<link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.13.1/css/all.min.css" rel="stylesheet" type="text/css">



  <!-- Plugin CSS -->
  <link href="vendor/magnific-popup/magnific-popup.css" rel="stylesheet">

  <!-- Theme CSS - Includes Bootstrap -->
  <link href="css/creative.min.css" rel="stylesheet">
<script src="js/dropzone/dropzone.js"></script>
<script src="js/dropzone/dropzone-amd-module.js"></script>
    <link rel="stylesheet" href="js/dropzone/dropzone.css">
<link rel="stylesheet" href="js/dropzone/basic.css">


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
            <a class="nav-link js-scroll-trigger" href="#about">Upload</a>
          </li>
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" id="anquan" href="#services">DRM Setting</a>
          </li>
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="#contact">YourFile</a>
          </li>
             <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="https://www.maipdf.com/qr.php">QR Tools</a>
          </li>
		  <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="https://maipdf.com/pdf/offline.php">汉</a>
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
          <h1 id='diyi' class=" text-white font-weight-bold">MaiPDF DRM for PDF</h1>
          <hr class="divider my-4">
        </div>
        <div class="col-lg-8 align-self-baseline">
          <p class="text-white-75 font-weight-light mb-5">Encrypted PDF File with Digital Key<br>HTML extention Made it easy on MAC/PC<br>Endable Lifetime PDF Files</p>
           
		  <h2>
	       <a class="btn btn-info btn-xl js-scroll-trigger" style="text-transform: none;" href="#about">Ever used exetuable version of a PDF?<br>Try our Free MaiPDF</a>
		  </h2>

        </div>
 
      </div>

    </div>
  </header>



  <!-- About Section <div class="alert alert-danger">德国网站,国内速度慢</div>-->
  <section class="page-section bg-primary" id="about">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
          <h2 class="text-white mt-0">First Step: Upload your PDF file</h2>  

<a href="http://doc.maitube.club/js/constitution.html.zip" style="color:green">Example1</a>

           <hr class="divider my-4">
		  
   
		<div class="form-group">
    <label class="title" id="pleaseupload">Please Upload Pdf files about 90M</label>
    <div id="dropz" class="dropzone" style="font-weight:900;border-style:dashed;border-width:5px;background-image: linear-gradient(#e66465, #9198e5);"> </div>
</div>
<input type="hidden" name="file_id" ng-model="file_id" id="file_id"/>


<script>
var appElement = document.querySelector('div .inmodal');
    var myDropzone = new Dropzone("#dropz", {
        url: "drmpdf.php",//文件提交地址
        method:"post",  //也可用put
        paramName:"file", //默认为file
        maxFiles:1,//一次性上传的文件数量上限
        maxFilesize: 222, //文件大小，单位：MB
		//chunking: true,
		//forceChunking: true,
		//chunkSize: 256000,
		//retryChunks: true,
        //retryChunksLimit: 3,
        acceptedFiles: ".png,.jpg,.jpeg,.gif,image/*,.pdf",
        addRemoveLinks:true,
		addRemoveLinks:true,
		retryChunksLimit: 3,
        parallelUploads: 1,//一次上传的文件数量
        dictDefaultMessage:'Drag or Click to Upload',
        dictMaxFilesExceeded: "One File！",
        dictResponseError: 'Fail!',
        dictInvalidFileType: "with*.pdf,*.png,*.jpeg。",
        dictFallbackMessage:"you are using antiqued Browser",
        dictFileTooBig:"Over Limit",
        dictRemoveLinks: "Del",
        dictCancelUpload: "Cancel",
		timeout: 192000,
        init:function(){
            this.on("addedfile", function(file) {
                //上传文件时触发的事件
                document.querySelector('div .dz-default').style.display = 'none';
            });
            this.on("success",function(file,data){
                //上传成功触发的事件
               // console.log('ok');
				//alert(file.name);
				//$('#about').hide();
				//$('#tou').hide();
				//$('#contact').hide();
				document.getElementById('anquan').click();
				<?php

        ini_set("display_errors", true);
         ini_set("html_errors", true); 
				     $year=  date("Y");
					$month= date("m");
					$week=  date("d");
					$fileplace="/".$year."/".$month."/".$week."/offline/";
				?>
				var a = "<?php echo $fileplace ?>" + file.name;
				document.getElementById("name").value =a ;
				document.getElementById("pleaseupload").innerHTML ="已经上传成功" ;
				document.getElementByClassName("dz-progress").style.opacity="0.1";
				
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
			$identifier='';
			if(!empty($_FILES) ){
			//date_default_timezone_set('etc/gmt-8');
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
					$year= date("Y");
					$month= date("m");
					$week=  date("d");
					$fileplace="yes/".$year."/".$month."/".$week."/"."offline/";
					if (!is_dir($fileplace)){
					  if (!mkdir($fileplace, 0777, true)) {
						  die('Failed to create folders...');
					  }
					}
					//echo $fileplace;

					else
					{
						
	         if (file_exists($fileplace . $_FILES["file"]["name"]))
					{
						unlink($fileplace.$_FILES["file"]["name"]);
					}
						// 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
							move_uploaded_file($_FILES["file"]["tmp_name"], $fileplace. $_FILES["file"]["name"]);
							//echo "文件存储在: " . $fileplace . $_FILES["file"]["name"];
							echo "Upload Successfully, please set up your reading restriction";
							$url=$fileplace.$_FILES["file"]["name"];
							//session_start();
						  // $_SESSION["url"] = $url;
						      require_once __DIR__ . '/vendorJiami/autoload.php';
								try{
								$mpdf = new \Mpdf\Mpdf();

								$pagecount = $mpdf->SetSourceFile($fileplace. $_FILES["file"]["name"]);

								for($i=1;$i<=$pagecount;$i++){
									 $mpdf->AddPage();
									 $tplId3 = $mpdf->ImportPage($i);
									 $size=$mpdf->getTemplateSize($tplId3);
									 $mpdf->UseTemplate($tplId3,0,0,$size['width'],$size['height'],true);								
								}
								$mpdf->SetProtection(array(), 'guaguashimaimai', 'guaguashimaimai');
								$mpdf->Output($fileplace. $_FILES["file"]["name"], \Mpdf\Output\Destination::FILE);						
								}
								catch(Exception $e)
								 {
								 echo 'Message: ' .$e->getMessage();
								// echo "~~~~";
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
    </div>

  </section>
  
  <!-- Services Section -->
  <section class="page-section" id="services">
    <div class="container">

	<form   role="form" action="drmpdf.php#contact" method="post">
      <h2 class="text-center mt-0">Second Step:Add pdf's expiration Date and Times</h2>
	  <h5 class="text-center mt-0"  style="color:blue;">The Restriction settings will be applied<br>When one of the Condition reached</h5>
       <h6 class="text-center mt-0"  style="color:#f4623a;">Directly open on Computer*Using Adobe on Mobile</h6>
      <hr class="divider my-4">
      <div class="row">
	  
        <div class="col-lg-3 col-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-file-pdf text-primary mb-4"></i>
            <h5 class="h4 mb-2" style="font-size:1.2em;font-weight:bold;">File Name</h5>
            <p class="text-muted mb-0">	
			<input type="text" class="form-control" id="name" name="sender"
			 value="<?php empty($_FILES)? print "Click 'Upload' in Step One": print "/".$year."/".$month."/".$week."/".$_FILES["file"]["name"]; ?>" readonly="readonly">
			</p>
          </div>
        </div>
        <div class="col-lg-3 col-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-folder-open text-primary mb-4"></i>
            <h5 class="h4 mb-2" style="font-size:1.2em;font-weight:bold;">Open Limit</h5>
            <p class="text-muted mb-0"><input class="form-control"  type="number"  name="limit" placeholder="Interger"></p>
          </div>
        </div>
        <div class="col-lg-3 col-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-hourglass-start text-primary mb-4"></i>
            
			<h5 class="h4 mb-2" style="font-size:1.2em;font-weight:bold;">Duration Days</h5>
            <p class="text-muted mb-0"><input class="form-control"  type="number"  name="password" placeholder="1000 Max Days"></p>
          </div>
        </div>
        <div class="col-lg-3 col-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-lock text-primary mb-4"></i>
			<h5 class="h4 mb-2" style="font-size:1.2em;font-weight:bold;">All Platforms</h5>
           
			<p class="text-muted mb-0"><input type="submit" value="GenerateFile" class="btn btn-success"></p>
          </div>
        </div>
      </div>
	  </form>

		<?php
if (isset($_POST['limit'])) {
    $limit = htmlspecialchars($_POST['limit']);

    if (isset($_POST['password'])) {
        $password = htmlspecialchars($_POST['password']);

        // If password or limit are less than 1, exit and show warning
        if ($password < 1 || $limit < 1) {
            exit("<script>
                document.getElementById(\"diyi\").className=\"text-warning\";
                document.getElementById(\"diyi\").innerHTML = 
                    \"Too Short for Setting of Each Reading Period<br>Don't Joke with our IT. He's Been MAD\";
            </script>");
        }

        // Get the sender URL and generate an identifier
        $url = $_POST['sender'];
        $keyurl = $_POST['sender'];
        $identifier = $url . rand(0, 100);
        $identifier = md5($identifier);

        // Limit password to a maximum of 999
        if ($password > 1000) {
            $password = 999;
        }

        // File handling logic
        $dname = 'joe' . $url;
        $url = explode('.pdf', $url);
        $downloadname = explode('/', $dname);

        // Start modifying the uploaded file
        $keypdf = 'yes' . $keyurl;
        $coder = base64_encode(file_get_contents($keypdf));
        
        // Substring from the coder
        $firstPartcoder = substr($coder, 0, -399);
        
        // Modify the identifier
        $identifier = $coder[150] . $coder[170] . substr($identifier, 2);
        
        // Define file paths
        $yanhuo = 'yes/' . $year . '/' . $month . '/' . $week . '/offline/' . $identifier . '.csv';
        $yanhuopdf = 'yes' . $url[0] . '.html';
        $yanhuozip = 'yes' . $url[0] . '.zip';
        $yanhuozipname = explode('/', $yanhuopdf);
        $c = count($yanhuozipname) - 1;
        $yanhuozipname = $yanhuozipname[$c];

        // Delete existing files if they exist
        if (file_exists($yanhuopdf)) {
            unlink($yanhuopdf);
        }
        if (file_exists($yanhuozip)) {
            unlink($yanhuozip);
        }

        // Create new CSV file
        $file = fopen($yanhuo, 'w+');
        $jieguo = [];
        $jieguo[0] = $limit;
        $jieguo[1] = $password;
        $jieguo[2] = date("Y-m-d", strtotime("+$jieguo[1] day"));
        $jieguo[3] = $url[0];

        // Get the last 399 characters of the encoded data
        $last399Chars = substr($coder, -399);
        $jieguo[4] = $last399Chars;

        // Insert date validation logic
        $today = date("Y-m-d");
        if (strtotime($today) < strtotime($jieguo[2])) {
            // Add extra logic if necessary
        }

        // Write the data to the CSV
        fputcsv($file, $jieguo);
        fclose($file);

        // Prepare JavaScript data
        $jsyanhuo = $year . '*' . $month . '*' . $week . '*' . $identifier;
        $part0 = "<script> var beforejieguo = '" . $jsyanhuo . "'; filenameofthisfile='" . $downloadname[5] . "';</script>";
        $wholebase = "pdfData = '" . $firstPartcoder . "'; ";

        // Write the data to the PDF file
        $file = fopen($yanhuopdf, 'w');
        $part1 = file_get_contents("img/newpart1.txt");
        $part3 = file_get_contents("img/newpart3.txt");

        try {
            fwrite($file, $part0);
            fwrite($file, $part1);
            fwrite($file, $wholebase);
            fwrite($file, $part3);
            fclose($file);
        } catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
        }

        // Create a zip archive containing the PDF
        $zip = new ZipArchive;
        $zip->open($yanhuozip, ZipArchive::CREATE);
        $zip->addFile($yanhuopdf, $yanhuozipname);
        $zip->close();

        echo $yanhuozipname;
    }
}
?>

	
     <!--  <h2 class="mb-4">Your Reading is Created</h2>-->
  <!-- Contact Section -->
  <section class="page-section" id="contact">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
		<a class="btn btn-danger btn-xl" href="https://paypal.me/maitube"  target="_blank">Donation</a>
		 
		<hr class="divider my-4">
        
	
		
          
          <p class="text-muted mb-5">
		  <h5 id='Copied'></h2>
		
		     
		      <div class="container text-center">
			  <h2 class="mb-4">File Created  </h2>
			 <h5 class="mb-4">Reading Code：<?php	 echo  $identifier; ?></h5>
			  <h5 class="mb-4">Modification Code：<?php	$identifier2='joe'.$identifier; echo  $year.'*'.$month.'*'.$week.'*'.crypt($identifier2,'ku'); ?></h5>
			  <a class="btn btn-info btn-xl" href="https://maipdf.com/enoffline.php" style="text-transform: none;" target="_blank">Change/Check Setting</a>
			   <a class="btn btn-info btn-xl" style="text-transform: none;" href="<?php	echo $yanhuozip ?>"  download="">DownLoad Your File</a>
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
      <div class="small text-center text-muted">Copyright &copy; 2026 - joe@pdfhost.online-</div>
      
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

 <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9224406325142860" crossorigin="anonymous"></script>




</body>

</html>
