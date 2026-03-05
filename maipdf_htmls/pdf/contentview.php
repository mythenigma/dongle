
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Business Data</title>
<link rel="stylesheet" href="https://cdn.staticfile.org/font-awesome/4.7.0/css/font-awesome.css">
<script src="https://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
</head>
<style>  
@media print{  
body{display:none}  
}  
</style> 
<body id="isbody">
<?php
    //get the email equals to variable
    $email=htmlspecialchars($_GET['email']);
	//$conn= new mysqli("192.168.1.133","zben","HAOhao123","record");
	$conn= new mysqli("127.0.0.1","root","JOEjoe123","record");
	if($conn->connect_error){
		die("contact it for issue");
	}
	$sql="SELECT * FROM `pdf` WHERE `mdemail`='$email'";
	//echo $sql;
	$result=mysqli_query($conn,$sql);
	if (mysqli_num_rows($result)>0) {
		  while($row = mysqli_fetch_assoc($result)){
		 // $weblink=$row['weblink'];
		  $limit=$row['limit'];
		  $url=$row['url'];
		  $limit=$limit-1;	
		  $sql2="UPDATE `zbenemail` SET `limit`=$limit WHERE `mdemail`='$email' "; 	
		   //echo $sql2;
		  $result2=mysqli_query($conn,$sql2);
		  }
      } else {
          //header("Location: https://www.z-ben.com");
		  exit("you are not authorised to view");
      } 
      	// $sql="UPDATE"; 
         $conn->close();
    
	$ip=$_SERVER['REMOTE_ADDR'];
    $datime=date("Y.m.d.H.i.s");
    $useragent=$_SERVER['HTTP_USER_AGENT'];
    $content=$email."*URL*".$ip."***".$datime.'-'.$useragent;
  //  $file=fopen("../upload/record.txt","a+");
   //fwrite($file,"$content \r\n");
   // fclose($file);
    if($limit>0){
		  echo "<div id=\"target\" >";
		  	$homepage = file_get_contents($contentFile.'.txt');
			$homepage= str_replace($weblink,"http://192.168.1.133/email/redirect.php?email=$email",$homepage);
            echo $homepage;
		  echo "</div>";
	}else{
		 exit("you are not authorised to view");
	} 
    ?>
<script>
$(function () { 
	$( "#target" ).contextmenu(function() {
		return false;
	});
	$( "#target" ).keydown(function() {
		//return false;
		//alert('111111111111111qqqokkkkk');
	});
	document.getElementById("target").addEventListener('contextmenu', function(){
    alert("your are right clicking!"); return false;
    });
	document.getElementById("isbody").addEventListener('keydown', function(e){
			      if (66 < e.keyCode && e.ctrlKey)
						{
							//alert('qqqokkkkk');
							$("#isbody").empty();
							alert('This is a CopyRight Proteced Content');
							return false;
						}
			 // alert(e.keyCode);
		    //alert("keyboard command not working !"); 
		     return false;
	});
})
</script>	
</body>
</html>