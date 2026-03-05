<?php

















  $year= date("Y"); $month= date("m"); $week=  date("d"); 
  $fileplace="yes/".$year."/".$month."/".$week."/";
  $files = scandir($fileplace);
  $numFiles = count($files);
  


  $filename  = $year.$month.$week.$numFiles;
  $filename = intval($filename) + 123456789;

  //echo $filename;
  //echo $filename-123456789;
if(isset($_POST["textareaValue"])) {
  // The "myVariable" value was posted
  $textareaValue = $_POST["textareaValue"];


  $confirmask = $_POST["confirmask"];
  $expiretime = $_POST["expiretime"];
  $pass1 = $_POST["pass1"];
  $emailnoti = $_POST["emailnoti"];
  $refename  = $_POST["refename"];

}


$firstline = $expiretime.'æ'.$confirmask.'æ'.$pass1.'æ'.$emailnoti.'æ'.$refename.'æ'.time();

$firstline = $firstline .  PHP_EOL .$textareaValue;
$fileplace = $fileplace.$filename.'.txt';
file_put_contents($fileplace, $firstline);
echo $filename; 
//echo $firstline;
//echo $fileplace;
exit();	
?>
