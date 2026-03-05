<?php 
if(!isset($_SERVER['HTTPS'])){
   $url= 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
   header("Location: $url");
   exit();
}
?>
<!DOCTYPE html>
<link href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.staticfile.org/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<html>

<head>
<meta charset="utf-8">
<title>Track Result</title>

  
   <meta name="keywords" content="ip,track ip">
  
  <meta name="description" content="track ip address">
</head>

<body>
<script type="text/css">
 .table td.fit, 
  .table tr.fit {
    white-space: nowrap;
    width: 12%;
}   
</script> 
<div class="alert alert-danger"><h2 class="text-center">Result for this PDF file</h2></div>
<?php 
header("content-type:text/html; charset=utf-8");
date_default_timezone_set('etc/gmt-8');
$passcodes=htmlspecialchars($_POST['passcode']);
//$subject=htmlspecialchars($_POST['subject']);
if(stristr($passcodes,"maipdf.com") ){
			// echo "<h2>网址最后的 = 后面要输入点字母的，不要乱搜别人的哦</h2><br>";
			 exit("  <h2>Why do you put whole URL to Search;<br> Only the *Reader Code* after Equal sign according to Picute in Previous Page<br>-admin@maitube.com</h2><br> ");
			 
		}
	
   // $zmak5=date("Y-m-d-H-i-s");
	//$ip = $_SERVER['REMOTE_ADDR'];
	  if((strlen($passcodes)<1) or ($passcodes=='%') ){
			// echo "<h2>网址最后的 = 后面要输入点字母的，不要乱搜别人的哦</h2><br>";
			 exit("<h2>Enter the identifer which after = </h2><br>");
		}
    $sql = "SELECT `subject`,`mark`,`markopen`,`ip`,`add` FROM `recordspic` WHERE `email`LIKE '$passcodes' AND `email` != 'pdfcheck' ORDER BY `auto` DESC";  
	$sql2=   "SELECT * FROM `pdf` WHERE `mdemail`LIKE '$passcodes' ";
	//echo $sql;
	//$conn = new mysqli($servername, $username, $password, $dbname);	
	//$conn= new mysqli("127.0.0.1","joe","JOEjoe123","record");
	include_once ('../password.php');
$conn = new mysqli($servernameMai, $usernameMai, $passwordMai, $dbnameMai);
	if ($conn->connect_error) 
	{
    die("Failed: " . $conn->connect_error);
	} 
    
     if($passcodes == '19900101'){
     	$sql= "SELECT * FROM `picture` ORDER BY `day` DESC   ";
     }

     $conn->query("set names 'utf8'");
	 $conn->query("set character_set_client=utf8");
	 $conn->query("set character_set_results=utf8");
	$result = mysqli_query($conn, $sql);
	$result2 = mysqli_query($conn, $sql2);
	$row2 = mysqli_fetch_assoc($result2);
	$a= $row2['limit'];
	$b=$row2['password'];
	echo "<div class=\"alert alert-danger\"><h3 class='text-center'> Your have * $a * left&Per Reading is * $b * (seconds) </h3></div>";
if (mysqli_num_rows($result) > 0) {

	 echo "<table class=\"table table-bordered \">";
	 echo "<caption> PDF Open Result -MaiPDF</caption>";
	 echo "<thead>";
     echo "<tr>";
   
     $fieldcount=0;

	 while ($property = mysqli_fetch_field($result)) {
          if (  $property->name =="mark"){
			  $head="OpenTime";
		  }else if (  $property->name =="markopen"){
			  $head="User-Agent:What devices/browsers are used to read these content";
		  }else{
			   $head=$property->name;
		  }
		  
          echo "<th>".$head; echo "</th>";
          $fieldname[$fieldcount] = $property->name;
          $fieldcount++;

     }
     echo "</tr>";
     echo "</thead>";
	
	echo "<tbody>";
    
	 
     while($row = mysqli_fetch_assoc($result)) {
	   echo "<tr>";
	   $newcount=0;
	  // echo $row['code'];
     $fieldcount=0;
	   foreach($row as $value){
      // echo  $fieldname[$newcount];
	   $property = mysqli_fetch_field($result);
        echo "<td>";
        echo $value;
        // only with first value
        $fieldvalue[$fieldcount]=$value;

        $newcount++;
        $fieldcount = $fieldcount+1;

        echo "</td>";

	   }	   
	   echo "</tr>";

    }
    echo "</tbody>";
    echo "</table>";
     
    
}else{
	echo "Be Patient, Link Not Yet Clicked ";
}

/*	$conn= new mysqli("127.0.0.1","joe","JOEjoe123","record");
	$zmak5=date("Y-m-d-H-i-s");
	$ip = $_SERVER['REMOTE_ADDR'];//123.151.43.110
	$sql = "INSERT INTO `recordspic`(`email`, `subject`, `mark`,`markopen`,`passcode`,`ip`) VALUES ('pdfcheck','$passcodes','$zmak5','$zmak5',19900101,'$ip') ";
	 //$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) 
	{
	die("连接失败: " . $conn->connect_error);
	} 
	
	 $conn->query("set names 'utf8'");
	if ($conn->query($sql) === TRUE) {
	} else {
	}
	 
	
   mysqli_close($conn);					
*/

?> 


</body>
</html>