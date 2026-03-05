

<?php 
//echo 'xxxxxxxxxxxxxxxxxxxxxxxxxxx';
 ini_set("display_errors", true);
 ini_set("html_errors", false); 
          $year= date("Y");
          $month= date("m");
          $week=  date("d");
          

  
          $fileplaceSHOW = "/".$year."/".$month."/".$week."/";
          $fileplace     = "yes/".$year."/".$month."/".$week."/";
          $picplace      = "yes/".$year."/".$month."/".$week."/preview/";
          $encryfile      ='';

?>





     
     
<?php
	 



      if(!empty($_FILES) ){
      
         $allowedExts = array("pdf", "htm");
         $temp = explode(".", $_FILES["file"]["name"]);
          $extension = end($temp);     // 获取文件后缀名
         $filename = $_FILES["file"]["name"];
         if (($_FILES["file"]["size"] < 118097152)  && in_array($extension, $allowedExts)){
			   if ($_FILES["file"]["error"] > 0){
       
                  die('File beyond 2M');
                }
				
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
		  //开始上传
		  if (file_exists($fileplace . $_FILES["file"]["name"])){
            //echo $_FILES["file"]["name"] . " File exists ";
            //echo "Not Uploaded". "<br>";
            //exit('Duplicate');
          }else{
			   move_uploaded_file($_FILES["file"]["tmp_name"], $fileplace. $_FILES["file"]["name"]);
	            $im = new Imagick();
                $im->setResolution(75,75);
                $im->readimage($fileplace. $_FILES["file"]["name"].'[0]');
               // $im->readimage($fileplace. $_FILES["file"]["name"].'[1]');
                $im->setImageFormat('jpeg'); 
                $im->setImageCompression(imagick::COMPRESSION_JPEG); 
                $im->setImageCompressionQuality(50);   
                $im->writeImage($picplace.$_FILES["file"]["name"].'.jpg'); 
                $im->clear(); 
                $im->destroy();
				    /*include_once ('/var/www/html/sendemail.php');
                    echo "<div style=\"display:none;\">";
                    sendoutemail('admin@maitube.com',$picplace.$_FILES["file"]["name"].'.jpg');  
                    echo "</div>" ;
					*/
		  }
		  
		}
     
	  }else{
		  echo 'nofile';
	  }
?>

      