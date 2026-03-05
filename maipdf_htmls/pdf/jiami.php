<?php

require_once __DIR__ . '/vendorJiami/autoload.php';
try{
$mpdf = new \Mpdf\Mpdf();

$pagecount = $mpdf->SetSourceFile('1.pdf');

for($i=1;$i<=$pagecount;$i++){
	 $mpdf->AddPage();
     $tplId3 = $mpdf->ImportPage($i);
	 $size=$mpdf->getTemplateSize($tplId3);
     $mpdf->UseTemplate($tplId3,0,0,$size['width'],$size['height'],true);
	
}

$mpdf->SetProtection(array(), 'qweewq', 'asddsa');
//$mpdf->Output('whatever2.pdf');
//$mpdf->Output('yes/2020/mail2.pdf', \Mpdf\Output\Destination::FILE);
$mpdf->Output();
//rename("mail.pdf", "yes/my_file.pdf");
}
catch(Exception $e)
 {
 echo 'Message: ' .$e->getMessage();
 }
?>