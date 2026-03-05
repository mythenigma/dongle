<!DOCTYPE html>
<html>

<head>

</head>

<body>
<?php

        $q=htmlspecialchars($_GET["q"]);
			$name=explode(".",$q);
			$name= end($name);
		    if(stristr("pdf",$name)){
			echo $q;
		    unlink($q); 
		}
 ?>
 
 
 
 <?php
        $suiyi=rand(1,100);
		if($suiyi<90){
			exit();
		}else{
			$dir    = '/var/www/html/pdf/yes';
			$files1 = scandir($dir);
			print_r($files1);
			sleep(50);
			foreach( $files1 as $value){
			if(stristr($value, '.pdf')){
				$q='yes/'.$value;
				echo '<br>'.$q;
				
				unlink($q); 
			}
			
		   }
		}		
    ?>
</body>

</html>

