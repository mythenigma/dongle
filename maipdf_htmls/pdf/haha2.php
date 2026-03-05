<?php 
if(!isset($_SERVER['HTTPS'])){
   $url= 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
   header("Location: $url");
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"> 
  <title>Maipdf-Result Check</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
   
   <meta name="keywords" content="track ip address,find ip address">
  
  <meta name="description" content="find people ip address">
   <link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css">  
  <script src="https://cdn.staticfile.org/jquery/2.1.1/jquery.min.js"></script>
  <script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
  
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-149594131-2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-149594131-2');
</script>
</head>
<h2 class="text-center">MaiPDF-Picture-Result Check  </h2>
<body>
<div class="container">
   <div class="jumbotron">
     <label>言</label> 
     <select id="bcc" name="bcc" onchange="myFunction()">
     <option value="colleague">Whatever</option>
     <option value="cn">中文</option>
     <option value="en" selected>English</option> 
     </select>

     <h2 class="text-center" id="shuoming">
	    We can check who read what at when!
	 </h2>
     <form class="form-horizontal"  role="form" action="result2.php" method="post">
    
    <div class="col-sm-10">
      <input type="text" class="form-control" id="name" name="passcode" placeholder="identifier which after =;（eg:XXX） " >  						
						 
	  <h2 class="text-center"><button type="submit" class="btn btn-primary btn-lg" value="Search"  >Search</button></h2>
	</div>  
	
    
      
      <img src="iden.JPG" class="img-responsive" alt="MaiPDF" > 
      <br><h2><a href="https://www.maipdf.com"><button type="button" id="niu" class="btn btn-success">HomePage </button> </a></h2>	
	  <div class="alert alert-warning">Trackers Are Not to be Searched in this page,But <a href="https://www.maipdf.com/#about"><button type="button"  class="btn btn-info">Here
	  </button></a>
	  </div>
	 
	  
</form>


<script>
function myFunction(){
   var x=document.getElementById("bcc").value;
    if(x=='cn'){
        document.getElementById("shuoming").innerHTML="将等号后面的阅读码输入搜索，可以查看这个pdf文件的阅读记录";
        document.getElementById("niu").innerHTML="回到首页";
       }else{
        document.getElementById("shuoming").innerHTML="We can check who read what at when!<br>By searching the identifier after = sign";
       }
    }
</script>

<?php 
$urls = array(
    'https://www.maipdf.com/boot.php',
	'https://www.maipdf.com/pdf/maipdf.php',
	'https://www.maipdf.com/7/hahamaimai.php',
	'https://www.maipdf.com/img/guide/',
	'https://www.maipdf.com/js/blog/ipapi.html',
	'https://www.maipdf.com/js/blog/55.php',
	'https://www.maipdf.com/js/blog/campaign.php',
	'https://www.maipdf.com/js/blog/weichat.php',
	'https://www.maipdf.com/js/blog/getip.php',
	'https://www.maipdf.com/file2.php',
  
);
$api = 'http://data.zz.baidu.com/urls?site=maipdf.com&token=aabLMM6xehuHKKjz';
$ch = curl_init();
$options =  array(
    CURLOPT_URL => $api,
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POSTFIELDS => implode("\n", $urls),
    CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
);
curl_setopt_array($ch, $options);
$result = curl_exec($ch);
?>

<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- maitube-auto-size -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-9224406325142860"
     data-ad-slot="4867222608"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- maitube-auto-size -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-9224406325142860"
     data-ad-slot="4867222608"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- maitube-auto-size -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-9224406325142860"
     data-ad-slot="4867222608"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>

 </div>
</div>

</body>
</html>