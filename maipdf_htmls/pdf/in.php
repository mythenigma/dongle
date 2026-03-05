<!DOCTYPE html>
<html dir="ltr" mozdisallowselectionprint>
<head>


<script async src="https://www.googletagmanager.com/gtag/js?id=UA-149594131-2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-149594131-2');
</script>
   <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="google" content="notranslate">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.1/dist/jquery.min.js"></script>



<script>
          var d = new Date(); console.log(d);
      document.cookie ="usertime="+encodeURI(d);
      document.cookie ="userjoe="+encodeURI(d);
      var hanren = encodeURI(d);
       var hanren = hanren.indexOf("0800");
      console.log(d);
      console.log('fox');
      var wohenxiangzhidao='maipdf';
          var bt='xxx';

      
</script>

<?php 


if(!isset($_SERVER['HTTPS'])){
  
   $url= 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
   header("Location: $url");
   exit();
}



if(isset($_COOKIE['maigua'])){
    include_once ('/var/www/html/register/sendemail.php');
    $maigua= $_COOKIE['maigua'];  
    //$gua=explode(':', $maigua);
    if($maigua[0]!='q'){
      echo "<div style=\"display:none;\">";
      sendoutemail('admin@maitube.com',$maigua.'*'.$maigua[0]);  
      echo "</div>" ;
    }
      
}
$urljieshu= 'https://maitube.com/read/nofile.html?maipdf';
 $tuichu="<h1 style='color:red;text-align:center;'>You are not authorised to view on<br>Please Contact Author</h1>
 <h2 class='text-center  text-warning>'Why the file is not visible </h2>
 <meta http-equiv='refresh' content='25;url=$urljieshu'>
 <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css'>
<div class='card'>
    <div class='card-body text-center text-info'>Your File may be Obseleted<br>Total/Per viewable may have been reached
    <h5 class='text-center mb-3'>
    <a class='btn btn-info btn-xl' href='https://www.maipdf.com/pdf/hahachange.php' target='_blank'>Renew File</a> 
     </h5>
     <div class='alert alert-warning' style='font-weight:bold;color:#f4623a;'>Please Save your Modification Code
         <br>In order to prevent any copyright issue,We remove historical files once every 5 months,and appreciating your attention
         </div>
</div>
  </div>
";
?>
 
<?php
    //get the email equals to variable
  if(isset ($_GET['email'])){
    $email=htmlspecialchars($_GET['email']);

  }elseif(isset ($_GET['e'])){
      $email=htmlspecialchars($_GET['e']);
  }else{
    exit( $tuichu);
  }
  //$conn= new mysqli("213.136.92.253","joe","JOEjoe123","record");
  include_once ('/var/www/html/password.php');
  $conn = new mysqli($servernameMai, $usernameMai, $passwordMai, $dbnameMai);
  if($conn->connect_error){
   die("<meta http-equiv='refresh' content='200;url=$urljieshu'>"); 
   // die("<h1 style=\"color:red;text-align:center;position:fixed;top:50%;right:50%;\";>页面走开了<br>请您重新刷新一下页面</h1>");
  }
  $sql="SELECT * FROM `pdf` WHERE `mdemail`='$email'";
  //echo $sql;
  
  
  $result=mysqli_query($conn,$sql);
  if (mysqli_num_rows($result)>0) {
     $row = mysqli_fetch_assoc($result);
      $limit=$row['limit'];
       $url=$row['url'];
       $period=$row['password'];
       $limit=$limit-1;
       $sqldate=$row['day'];
             $link = 's'.$url;
       $link= str_replace("/preview/","/",$link);
   $verifypdf='/var/www/html/pdf/yes'.$url;
   $verifypdf= str_replace("/preview/","/",$verifypdf);
   $doctitle=explode('/', $verifypdf);
   $doctitle=end($doctitle);
   $fileurl=$doctitle;
   $doctitle=explode('.pdf', $doctitle);
   $doctitle=$doctitle[0];
   echo "<title>".$doctitle."</title>";
   if(!file_exists($verifypdf)){
     $conn->close();
    exit( $tuichu);
   }  
      $verifypdf = (filesize($verifypdf))/2077;
      $br=$_SERVER['HTTP_USER_AGENT'];
    if(stristr('facebook', $br)){
      $limit=97654321;
     }
    $agenter=$br = explode(')',$br);
    $br= $br[0].")";
      $sql2="UPDATE `pdf` SET `limit`=$limit WHERE `mdemail`='$email' ";  
     if( $limit > -3  && $limit<111000){
       $result2 = mysqli_query($conn,$sql2);
      }
    
      //$fileurl= explode('/',$url);
    //print_r($fileurl);
    //exit($fileurl[4]);
      } else {
          
     exit( $tuichu);
      } 
      
   $ip=$_SERVER['REMOTE_ADDR'];
   if(isset($_COOKIE['usertime'])){
     $zmak5=$_COOKIE['usertime'];
   }else{
     $zmak5=date("Y/m/d+H:i:s");
   }
  // $zmak6=$zmak5.rand(7,777);
   //$zmak6=md5($zmak6);

  if($limit<0){
       //$limit = -1* $limit;
       //exit($limit);
     if(abs($limit) < 3){
       $free='Failed';
       $sqlrecord = "INSERT INTO `records`(`email`, `subject`, `mark`,`markopen`,`passcode`,`ip`,`add`) VALUES ('$email','$free','$zmak5','$br',19900101,'$ip',13) ";
       
       $pathpdf = 'yes'.$url;
      // unlink($pathpdf); //标准在sales vector中
       $result2=mysqli_query($conn,$sqlrecord);
       $conn->close();
      exit( $tuichu);
       
     }

     $conn->close();
  exit( $tuichu);
   
  }else{
     $free='Succed';
          $sqlrecord = "INSERT INTO `records`(`email`, `subject`, `mark`,`markopen`,`passcode`,`ip`,`add`) VALUES ('$email','$free','$zmak5','$br',19900101,'$ip',3) ";
         
         $adsensefile=str_replace($fileurl,'',$url);
         $adsensefile= str_replace("/preview/","/",$adsensefile);// 放在主文件夹中
         $adsensefile='/var/www/html/pdf/yes'.$adsensefile.$doctitle.'.txt';
         $adsensedate=date("Y-m-d");

         if (strcmp($adsensedate, $sqldate) !== 0) {
             if (file_exists($adsensefile)){
                $oldadsensedate = file_get_contents($adsensefile);
                $oldadsensedate = substr($oldadsensedate,0,10);
                $origin = new DateTime();
                $target = new DateTime($sqldate);
                $interval = $origin->diff($target);
                $interval=  $interval->days;
               if (strcmp($adsensedate, $oldadsensedate) !== 0) {
                   $adsensedate='joehuang';
               
                if($interval>50 && filesize($adsensefile)>15){
                     file_put_contents($adsensefile,$oldadsensedate);
                    }
                if($interval>90  && filesize($adsensefile)<15){
                     file_put_contents($adsensefile,$oldadsensedate.'#frequent#');
                }
              }
             }else{
                file_put_contents($adsensefile,$adsensedate.$agenter);
             }
             
          }
          echo "<script>var md5='$email';var adsensedate='$adsensedate'; var ip='$ip' ; var br='$br'; var check=$limit; var verifypdf=$verifypdf; var doctitle='$doctitle'; var joehasafile='$link';var period=$period;</script>";
     

     //$result2=mysqli_query($conn,$sqlrecord);
     $conn->close();
          $current=$bt=$fileurl;
          $btofpic='preview/'.$bt.'.jpg';
          $btofpic=str_replace($bt,$btofpic,$url);
          $btofpic=str_replace('/preview/preview','/preview',$btofpic);
          $bttemp='/var/www/html/pdf/yes'.$btofpic; 
          $btofpic='https://doc.maitube.club/pdf/yes'.$btofpic;  
   if(!file_exists($bttemp)){
     $btofpic='https://doc.maitube.club/pdf/images/texture.png';
   }       
   if(file_exists($verifyfile)){
      $current = file_get_contents($verifyfile);
          if(strlen($current)>3){
          $current=explode(';',$current); 
         
          // 在此标注是因为之前 被刷数据库。我可以pass 分钟到 qrcode，这样qrcode检查一下分钟的差距，就可以检查是不是恶意刷单了, 我到现在才发现， doctitle 和 bt其实就是一个变量啊
      }         
    }
  
  echo "<script>   var emaildizhi='$current[0]'; var at='$current[1]';var bt='$bt'; var btofpic='$btofpic';</script>";
        echo "<meta property='og:title' content='$doctitle'>";
        echo "<meta property='og:description' content='$doctitle'>";
        echo "<meta property='og:image' content='$btofpic'> ";         
     
  }
    ?>




    
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.13.1/css/all.min.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@2.2.228/build/pdf.min.js"></script>


    <link rel="stylesheet" href="https://doc.maitube.club/pdf/2021.css">
    <link rel="resource" type="application/l10n" href="https://doc.maitube.club/pdf/locale/locale.properties">
    <script src="https://doc.maitube.club/pdf/maip.js"></script>
    
 </head>
   

 

 <body  id="bigboy" tabindex="1" class="loadingInProgress" oncopy="return false;">
    <div id="outerContainer">

      <div id="sidebarContainer">
        <div id="toolbarSidebar">
          <div class="splitToolbarButton toggled">
            <button id="viewThumbnail" class="toolbarButton toggled" title="Show Thumbnails" tabindex="2" data-l10n-id="thumbs">
               <span data-l10n-id="thumbs_label">Thumbnails</span>
            </button>
            <button id="viewOutline" class="toolbarButton" title="Show Document Outline (double-click to expand/collapse all items)" tabindex="3" data-l10n-id="document_outline">
               <span data-l10n-id="document_outline_label">Document Outline</span>
            </button>
            <button id="viewAttachments" class="toolbarButton" title="Show Attachments" tabindex="4" data-l10n-id="attachments">
               <span data-l10n-id="attachments_label">Attachments</span>
            </button>
          </div>
        </div>
        <div id="sidebarContent">
          <div id="thumbnailView">
          </div>
          <div id="outlineView" class="hidden">
          </div>
          <div id="attachmentsView" class="hidden">
          </div>
        </div>
        <div id="sidebarResizer" class="hidden"></div>
      </div>  <!-- sidebarContainer -->

      <div id="mainContainer">
        <div class="findbar hidden doorHanger" id="findbar">
          <div id="findbarInputContainer">
            <input id="findInput" class="toolbarField" title="Find" placeholder="Find in document…" tabindex="91" data-l10n-id="find_input">
            <div class="splitToolbarButton">
              <button id="findPrevious" class="toolbarButton findPrevious" title="Find the previous occurrence of the phrase" tabindex="92" data-l10n-id="find_previous">
                <span data-l10n-id="find_previous_label">Previous</span>
              </button>
              <div class="splitToolbarButtonSeparator"></div>
              <button id="findNext" class="toolbarButton findNext" title="Find the next occurrence of the phrase" tabindex="93" data-l10n-id="find_next">
                <span data-l10n-id="find_next_label">Next</span>
              </button>
            </div>
          </div>

          <div id="findbarOptionsOneContainer">
            <input type="checkbox" id="findHighlightAll" class="toolbarField" tabindex="94">
            <label for="findHighlightAll" class="toolbarLabel" data-l10n-id="find_highlight">Highlight all</label>
            <input type="checkbox" id="findMatchCase" class="toolbarField" tabindex="95">
            <label for="findMatchCase" class="toolbarLabel" data-l10n-id="find_match_case_label">Match case</label>
          </div>
          <div id="findbarOptionsTwoContainer">
            <input type="checkbox" id="findEntireWord" class="toolbarField" tabindex="96">
            <label for="findEntireWord" class="toolbarLabel" data-l10n-id="find_entire_word_label">Whole words</label>
            <span id="findResultsCount" class="toolbarLabel hidden"></span>
          </div>

          <div id="findbarMessageContainer">
            <span id="findMsg" class="toolbarLabel"></span>
          </div>
        </div>  <!-- findbar -->

        <div id="secondaryToolbar" class="secondaryToolbar hidden doorHangerRight">
          <div id="secondaryToolbarButtonContainer">
            <button id="secondaryPresentationMode" class="secondaryToolbarButton presentationMode visibleLargeView" title="Switch to Presentation Mode" tabindex="51" data-l10n-id="presentation_mode">
              <span data-l10n-id="presentation_mode_label">Presentation Mode</span>
            </button>

            <button id="secondaryOpenFile" class="secondaryToolbarButton openFile visibleLargeView" title="Open File" tabindex="52" data-l10n-id="open_file">
              <span data-l10n-id="open_file_label">Open</span>
            </button>

            <button id="secondaryPrint" class="secondaryToolbarButton print visibleMediumView" title="Print" tabindex="53" data-l10n-id="print">
              <span data-l10n-id="print_label">Print</span>
            </button>

            <button id="secondaryDownload" class="secondaryToolbarButton download visibleMediumView" title="Download" tabindex="54" data-l10n-id="download">
              <span data-l10n-id="download_label">Download</span>
            </button>

            <a href="#" id="secondaryViewBookmark" class="secondaryToolbarButton bookmark visibleSmallView" title="Current view (copy or open in new window)" tabindex="55" data-l10n-id="bookmark">
              <span data-l10n-id="bookmark_label">Current View</span>
            </a>

            <div class="horizontalToolbarSeparator visibleLargeView"></div>

            <button id="firstPage" class="secondaryToolbarButton firstPage" title="Go to First Page" tabindex="56" data-l10n-id="first_page">
              <span data-l10n-id="first_page_label">Go to First Page</span>
            </button>
            <button id="lastPage" class="secondaryToolbarButton lastPage" title="Go to Last Page" tabindex="57" data-l10n-id="last_page">
              <span data-l10n-id="last_page_label">Go to Last Page</span>
            </button>

            <div class="horizontalToolbarSeparator"></div>

            <button id="pageRotateCw" class="secondaryToolbarButton rotateCw" title="Rotate Clockwise" tabindex="58" data-l10n-id="page_rotate_cw">
              <span data-l10n-id="page_rotate_cw_label">Rotate Clockwise</span>
            </button>
            <button id="pageRotateCcw" class="secondaryToolbarButton rotateCcw" title="Rotate Counterclockwise" tabindex="59" data-l10n-id="page_rotate_ccw">
              <span data-l10n-id="page_rotate_ccw_label">Rotate Counterclockwise</span>
            </button>

            <div class="horizontalToolbarSeparator"></div>

            <button id="cursorSelectTool" class="secondaryToolbarButton selectTool toggled" title="Enable Text Selection Tool" tabindex="60" data-l10n-id="cursor_text_select_tool">
              <span data-l10n-id="cursor_text_select_tool_label">Text Selection Tool</span>
            </button>
            <button id="cursorHandTool" class="secondaryToolbarButton handTool" title="Enable Hand Tool" tabindex="61" data-l10n-id="cursor_hand_tool">
              <span data-l10n-id="cursor_hand_tool_label">Hand Tool</span>
            </button>

            <div class="horizontalToolbarSeparator"></div>

            <button id="scrollVertical" class="secondaryToolbarButton scrollModeButtons scrollVertical toggled" title="Use Vertical Scrolling" tabindex="62" data-l10n-id="scroll_vertical">
              <span data-l10n-id="scroll_vertical_label">Vertical Scrolling</span>
            </button>
            <button id="scrollHorizontal" class="secondaryToolbarButton scrollModeButtons scrollHorizontal" title="Use Horizontal Scrolling" tabindex="63" data-l10n-id="scroll_horizontal">
              <span data-l10n-id="scroll_horizontal_label">Horizontal Scrolling</span>
            </button>
            <button id="scrollWrapped" class="secondaryToolbarButton scrollModeButtons scrollWrapped" title="Use Wrapped Scrolling" tabindex="64" data-l10n-id="scroll_wrapped">
              <span data-l10n-id="scroll_wrapped_label">Wrapped Scrolling</span>
            </button>

            <div class="horizontalToolbarSeparator scrollModeButtons"></div>

            <button id="spreadNone" class="secondaryToolbarButton spreadModeButtons spreadNone toggled" title="Do not join page spreads" tabindex="65" data-l10n-id="spread_none">
              <span data-l10n-id="spread_none_label">No Spreads</span>
            </button>
            <button id="spreadOdd" class="secondaryToolbarButton spreadModeButtons spreadOdd" title="Join page spreads starting with odd-numbered pages" tabindex="66" data-l10n-id="spread_odd">
              <span data-l10n-id="spread_odd_label">Odd Spreads</span>
            </button>
            <button id="spreadEven" class="secondaryToolbarButton spreadModeButtons spreadEven" title="Join page spreads starting with even-numbered pages" tabindex="67" data-l10n-id="spread_even">
              <span data-l10n-id="spread_even_label">Even Spreads</span>
            </button>

            <div class="horizontalToolbarSeparator spreadModeButtons"></div>

            <button id="documentProperties" class="secondaryToolbarButton documentProperties" title="Document Properties…" tabindex="68" data-l10n-id="document_properties">
              <span data-l10n-id="document_properties_label">Document Properties…</span>
            </button>
          </div>
        </div>  <!-- secondaryToolbar -->

        <div class="toolbar">
          <div id="toolbarContainer">
            <div id="toolbarViewer">
              <div id="toolbarViewerLeft">
                <button id="sidebarToggle" class="toolbarButton" title="Toggle Sidebar" tabindex="11" data-l10n-id="toggle_sidebar">
                  <span data-l10n-id="toggle_sidebar_label">Toggle Sidebar</span>
                </button>
                <div class="toolbarButtonSpacer"></div>
                <button id="viewFind" class="toolbarButton" title="Find in Document" tabindex="12" data-l10n-id="findbar">
                  <span data-l10n-id="findbar_label">Find</span>
                </button>
                <div class="splitToolbarButton hiddenSmallView">
                  <button class="toolbarButton pageUp" title="Previous Page" id="previous" tabindex="13" data-l10n-id="previous">
                    <span data-l10n-id="previous_label">Previous</span>
                  </button>
                  <div class="splitToolbarButtonSeparator"></div>
                  <button class="toolbarButton pageDown" title="Next Page" id="next" tabindex="14" data-l10n-id="next">
                    <span data-l10n-id="next_label">Next</span>
                  </button>
                </div>
                <input type="number" id="pageNumber" class="toolbarField pageNumber" title="Page" value="1" size="4" min="1" tabindex="15" data-l10n-id="page">
                <span id="numPages" class="toolbarLabel"></span>
              </div>
              <div id="toolbarViewerRight">
                <button id="presentationMode" class="toolbarButton presentationMode hiddenLargeView" title="Switch to Presentation Mode" tabindex="31" data-l10n-id="presentation_mode">
                  <span data-l10n-id="presentation_mode_label">Presentation Mode</span>
                </button>

                <button id="openFile" class="toolbarButton openFile hiddenLargeView" title="Open File" tabindex="32" data-l10n-id="open_file">
                  <span data-l10n-id="open_file_label">Open</span>
                </button>

                <button id="print" class="toolbarButton print hiddenMediumView" title="Print" tabindex="33" data-l10n-id="print">
                  <span data-l10n-id="print_label">Print</span>
                </button>

                <button id="download" class="toolbarButton download hiddenMediumView" title="Download" tabindex="34" data-l10n-id="download">
                  <span data-l10n-id="download_label">Download</span>
                </button>
                <a href="#" id="viewBookmark" class="toolbarButton bookmark hiddenSmallView" title="Current view (copy or open in new window)" tabindex="35" data-l10n-id="bookmark">
                  <span data-l10n-id="bookmark_label">Current View</span>
                </a>

                <div class="verticalToolbarSeparator hiddenSmallView"></div>

                <button id="secondaryToolbarToggle" class="toolbarButton" title="Tools" tabindex="36" data-l10n-id="tools">
                  <span data-l10n-id="tools_label">Tools</span>
                </button>
              </div>
              <div id="toolbarViewerMiddle">
                <div class="splitToolbarButton">
                  <button id="zoomOut" class="toolbarButton zoomOut" title="Zoom Out" tabindex="21" data-l10n-id="zoom_out">
                    <span data-l10n-id="zoom_out_label">Zoom Out</span>
                  </button>
                  <div class="splitToolbarButtonSeparator"></div>
                  <button id="zoomIn" class="toolbarButton zoomIn" title="Zoom In" tabindex="22" data-l10n-id="zoom_in">
                    <span data-l10n-id="zoom_in_label">Zoom In</span>
                   </button>
                </div>
                <span id="scaleSelectContainer" class="dropdownToolbarButton">
                  <select id="scaleSelect" title="Zoom" tabindex="23" data-l10n-id="zoom">
                    <option id="pageAutoOption" title="" value="auto" selected="selected" data-l10n-id="page_scale_auto">Automatic Zoom</option>
                    <option id="pageActualOption" title="" value="page-actual" data-l10n-id="page_scale_actual">Actual Size</option>
                    <option id="pageFitOption" title="" value="page-fit" data-l10n-id="page_scale_fit">Page Fit</option>
                    <option id="pageWidthOption" title="" value="page-width" data-l10n-id="page_scale_width">Page Width</option>
                    <option id="customScaleOption" title="" value="custom" disabled="disabled" hidden="true"></option>
                    <option title="" value="0.5" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 50 }'>50%</option>
                    <option title="" value="0.75" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 75 }'>75%</option>
                    <option title="" value="1" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 100 }'>100%</option>
                    <option title="" value="1.25" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 125 }'>125%</option>
                    <option title="" value="1.5" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 150 }'>150%</option>
                    <option title="" value="2" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 200 }'>200%</option>
                    <option title="" value="3" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 300 }'>300%</option>
                    <option title="" value="4" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 400 }'>400%</option>
                  </select>
                </span>
              </div>
            </div>
            <div id="loadingBar">
              <div class="progress">
                <div class="glimmer">
                </div>
              </div>
            </div>
          </div>
        </div>

        <menu type="context" id="viewerContextMenu">
          <menuitem id="contextFirstPage" label="First Page"
                    data-l10n-id="first_page"></menuitem>
          <menuitem id="contextLastPage" label="Last Page"
                    data-l10n-id="last_page"></menuitem>
          <menuitem id="contextPageRotateCw" label="Rotate Clockwise"
                    data-l10n-id="page_rotate_cw"></menuitem>
          <menuitem id="contextPageRotateCcw" label="Rotate Counter-Clockwise"
                    data-l10n-id="page_rotate_ccw"></menuitem>
        </menu>

        <div id="viewerContainer" tabindex="0" ontouchstart="zongti()" ontouchmove="zongtimove()"  ontouchcancel="zongtifinish()" ontouchend="zongtifinish()" onscroll="yidong()">
     <div id="viewer" class="pdfViewer">
      
      </div>

      <!--   <hr><hr><hr><hr><hr><hr><hr><hr><hr><hr> -->

          <br><h5 id="dibuquanbu" style="text-align:center;color: silver">
            <input type="text" value="---" style="color:#404040;text-align:center;background-color: #404040;border-image:url(https://doc.maitube.club/pdf/images/shadow.png);" id="myInput"><br>
          <span style="font-size:2.0em;opacity:0.12;"> · &nbsp</span>
          <span style="font-size:2.0em;opacity:0.25;"> · &nbsp</span>
          <span style="font-size:2.0em;opacity:0.45;"> · &nbsp</span>
          <span style="font-size:2.0em;opacity:0.65;"> · &nbsp</span>
          <span style="font-size:2.0em;opacity:0.85;"> · &nbsp</span>
          <span style="font-size:2.0em;"> · </span>
                       <a id="dibulink" href="https://maipdf.com?cli" target="_blank" style='color: silver;text-decoration:none;font-weight:bold;'>
                        <sup id="dibutext" style="font-size:1.0em;">&nbsp MaiPDF &nbsp</sup></a>
                      
           <span style="font-size:2.0em;">· </span>
           <span style="font-size:2.0em;opacity:0.85;">&nbsp · &nbsp</span>
           <span style="font-size:2.0em;opacity:0.65;"> · &nbsp</span>
           <span style="font-size:2.0em;opacity:0.45;"> · &nbsp</span>
           <span style="font-size:2.0em;opacity:0.25;"> · &nbsp</span>
           <span style="font-size:2.0em;opacity:0.12;"> · &nbsp</span>
           </h5>
          <h6 style="text-align:center;color: silver">
            <img  id="xiaotu" style="width: 12%;height: 12%;" src="https://doc.maitube.club/pdf/images/texture.png"><br>

                           <a id="dibuad" href="https://www.maitube.com/qr.php?pdf" target="_blank">
                             <img id="dibuimg" class="maiads" src="https://doc.maitube.club/pdf/images/texture.png"><br>
                 </a>
          </h6>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9224406325142860" crossorigin="anonymous"></script>
        <h6  id="ad" style="text-align:center;"> </h6>
        


        

              <br>
        
       <h5 id="dibumiaoshu"  style="text-align:center;color: silver">
                      感谢您的浏览<br>
                             将鼠标移入框内可以展示文档内容
    
      </h5>

        </div>

        <div id="errorWrapper" hidden='true'>
          <div id="errorMessageLeft">
            <span id="errorMessage"></span>
            <button id="errorShowMore" data-l10n-id="error_more_info">
              More Information
            </button>
            <button id="errorShowLess" data-l10n-id="error_less_info" hidden='true'>
              Less Information
            </button>
          </div>
          <div id="errorMessageRight">
            <button id="errorClose" data-l10n-id="error_close">
              Close
            </button>
          </div>
          <div class="clearBoth"></div>
          <textarea id="errorMoreInfo" hidden='true' readonly="readonly"></textarea>
      
        </div>
    
      </div> <!-- mainContainer -->

      <div id="overlayContainer" class="hidden">
        <div id="passwordOverlay" class="container hidden">
          <div class="dialog">
            <div class="row">
              <p id="passwordText" data-l10n-id="password_label">Enter the password to open this PDF file:</p>
            </div>
            <div class="row">
              <input type="password" value="palaword" id="password" class="toolbarField">
            </div>
            <div class="buttonRow">
              <button id="passwordCancel" class="overlayButton"><span data-l10n-id="password_cancel">Cancel</span></button>
              <button id="passwordSubmit" class="overlayButton"><span data-l10n-id="password_ok">OK</span></button>
            </div>
          </div>
        </div>
        <div id="documentPropertiesOverlay" class="container hidden">
          <div class="dialog">
            <div class="row">
              <span data-l10n-id="document_properties_file_name">File name:</span> <p id="fileNameField">-</p>
            </div>
            <div class="row">
              <span data-l10n-id="document_properties_file_size">File size:</span> <p id="fileSizeField">-</p>
            </div>
            <div class="separator"></div>
            <div class="row">
              <span data-l10n-id="document_properties_title">Title:</span> <p id="titleField">-</p>
            </div>
            <div class="row">
              <span data-l10n-id="document_properties_author">Author:</span> <p id="authorField">-</p>
            </div>
            <div class="row">
              <span data-l10n-id="document_properties_subject">Subject:</span> <p id="subjectField">-</p>
            </div>
            <div class="row">
              <span data-l10n-id="document_properties_keywords">Keywords:</span> <p id="keywordsField">-</p>
            </div>
            <div class="row">
              <span data-l10n-id="document_properties_creation_date">Creation Date:</span> <p id="creationDateField">-</p>
            </div>
            <div class="row">
              <span data-l10n-id="document_properties_modification_date">Modification Date:</span> <p id="modificationDateField">-</p>
            </div>
            <div class="row">
              <span data-l10n-id="document_properties_creator">Creator:</span> <p id="creatorField">-</p>
            </div>
            <div class="separator"></div>
            <div class="row">
              <span data-l10n-id="document_properties_producer">PDF Producer:</span> <p id="producerField">-</p>
            </div>
            <div class="row">
              <span data-l10n-id="document_properties_version">PDF Version:</span> <p id="versionField">-</p>
            </div>
            <div class="row">
              <span data-l10n-id="document_properties_page_count">Page Count:</span> <p id="pageCountField">-</p>
            </div>
            <div class="row">
              <span data-l10n-id="document_properties_page_size">Page Size:</span> <p id="pageSizeField">-</p>
            </div>
            <div class="separator"></div>
            <div class="row">
              <span data-l10n-id="document_properties_linearized">Fast Web View:</span> <p id="linearizedField">-</p>
            </div>
            <div class="buttonRow">
              <button id="documentPropertiesClose" class="overlayButton"><span data-l10n-id="document_properties_close">Close</span></button>
            </div>
          </div>
        </div>
        <div id="printServiceOverlay" class="container hidden">
          <div class="dialog">
            <div class="row">
              <span data-l10n-id="print_progress_message">Preparing document for printing…</span>
            </div>
            <div class="row">
              <progress value="0" max="100"></progress>
              <span data-l10n-id="print_progress_percent" data-l10n-args='{ "progress": 0 }' class="relative-progress">0%</span>
            </div>
            <div class="buttonRow">
              <button id="printCancel" class="overlayButton"><span data-l10n-id="print_progress_close">Cancel</span></button>
            </div>
          </div>
        </div>
      </div>  <!-- overlayContainer -->

    </div> <!-- outerContainer -->
    <div id="printContainer"></div>
  <p class="pos_fixed"></p>
  <p class="pos_fixed1"></p>
  <p class="pos_fixed2"></p>
  <p id="joezoombutton">

    <i class="fas fa-plus-circle" id="da" ontouchstart="da()" onclick="da()" ></i> 

    <br>
        
  <br>
    <i class="fas fa-minus-circle" id="xiao" ontouchstart="xiao()" onclick="xiao()"></i>

  </p>
        <div id="pager"><span id="xianzai"></span>/<span id="yigong"></span> </div>
    <p id="pos_fixed3">

        ||||||||||||||||||||||||||||||||||||||||||||<br>
             MaiPDF.COM @ ScreenProtection<br>
       |||||||||||||||||||||||||||||||||||||||||||||<br>

        MaiPDF Shares your File with all the necessary Restrictions<br>
    <br><br>Hover your Mouse within the Content can make the file visible

    
  
    </p>
<span id='mibutton'></span>
   
  </body>






<script>
  







  var gracetouch=1;
  var swapno=1;
  console.log(doctitle);

  if(md5.startsWith("j")){  

     document.getElementById("mibutton").innerHTML = '<button id="kaiguan" type="button"  ontouchcancel="cancelFunction()" ontouchend="endFunction()" ontouchmove="touchFunction()" ontouchstart="startFunction()">MaiPDF</button>';
      document.getElementById("viewer").style.opacity="0.1";  
      $("#pos_fixed3").animate({ height:'+=190px'},5000);
  }else{
      document.getElementById("pos_fixed3").style.display="none";
     document.getElementById("viewer").style.opacity="1.0"; 
  }
  
function isMobilema()
{
    var mobile = navigator.userAgent.match(/iphone|android|phone|mobile|wap|netfront|x11|java|operamobi|operamini|ucweb|windowsce|symbian|symbianos|series|webos|sony|blackberry|dopod|nokia|samsung|palmsource|xda|pieplus|meizu|midp|cldc|motorola|foma|docomo|up.browser|up.link|blazer|helio|hosin|huawei|novarra|coolpad|webos|techfaith|palmsource|alcatel|amoi|ktouch|nexian|ericsson|philips|sagem|wellcom|bunjalloo|maui|smartphone|iemobile|spice|bird|zte-|longcos|pantech|gionee|portalmmm|jig browser|hiptop|benq|haier|^lct|320x320|240x320|176x220/i)!= null;
     console.log(navigator.userAgent);

//var mobile = navigator.userAgent.match(/x86_64)!= null;
    if(navigator.userAgent.match(/x86_64/i)!= null){
        mobile=0;
    }
       var url=location.href.indexOf("http");
      if(url<0){
        $("html").empty();
        window.location.href = "https://maitube.com/read/nofile.html?save";
      }
    return mobile;
}
   
</script>



<style> 

#pos_fixed3
{
  position:fixed;
  top:15%;
  left:30%;
  color:red;
  font-size: 1.5em;
   background-color: silver;
   
    text-align: center;
    border-radius: 6px;
    padding: 5px 0;
}
#kaiguan
{
  background-color:  #FF4500; /* Green */
  border: none;
  color: white;
  padding: 10px 24px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 10px;

  position:fixed;
  top:90%;
  left:10%;

} 
p.pos_fixed
{
  position:fixed;
  top:50%;
  right:50%;
  color:rgb(192,192,192);
  opacity:0.1;
  font-size: 0.2em;
}
p.pos_fixed1
{
  position:fixed;
  top:75%;
  right:50%;
  color:rgb(192,192,192);
  opacity:0.1;
  font-size: 0.2em;
}
p.pos_fixed2
{
  position:fixed;
  top:15%;
  right:50%;
  color:rgb(192,192,192);
  opacity:0.1;
  font-size: 0.2em;
} 
@media print{  
body{display:none}  
}  

body{
-webkit-touch-callout: none;
-webkit-user-select: none;
-khtml-user-select: none;
-moz-user-select: none;
-ms-user-select: none;
user-select: none;

}

#viewer{
 pointer-events: none;
}
.canvasWrapper{
 pointer-events: none;
}
#joezoombutton
{
  position:fixed;
  bottom:35%;
  right:1%;
  color:#f4623a;
  font-size: 2.0em;
  opacity:0.1;
} 


#pager
{
  position:fixed;
  color:#f4623a;
  top:10%;
  left:7%;
  font-size: 1.2em;
  font-weight:bold;
} 
 
        

        .maiads { width: 320px; height: 120px; }
        @media(min-width: 500px) { .maiads { width: 388px; height: 119px; } }
        @media(min-width: 800px) { .maiads { width: 438px; height: 129px; } }


</style>


<script >


  



          function random(min, max) {
           return Math.floor(Math.random() * (max - min)) + min;
          }
          
        function quickads(){
             
            document.getElementById("ad").innerHTML='<ins class="adsbygoogle" style="display:block; text-align:center;" data-ad-layout="in-article" data-ad-format="fluid" data-ad-client="ca-pub-9224406325142860" data-ad-slot="4978213873"></ins>';  
               (adsbygoogle = window.adsbygoogle || []).push({}); 
  
          }
         function showads(){
                nowpage= PDFViewerApplication.pdfViewer.currentPageNumber;
                 allpage= PDFViewerApplication.pdfViewer.pagesCount;
                if (adsensedate=='joehuang' && swapno>2 && gracetouch>2 && allpage>nowpage) {
               document.getElementById("ad").innerHTML='<br><ins class="adsbygoogle" style="display:inline-block;" data-ad-client="ca-pub-9224406325142860"  data-ad-slot="1399762953"></ins>';
                adsensedate='zhanshi'; console.log(adsensedate);
               (adsbygoogle = window.adsbygoogle || []).push({});

                }
                adsensedate='laiguo';
           
          }

function jilu(){
    if (window.XMLHttpRequest){
        // IE7+, Firefox, Chrome, Opera, Safari 浏览器执行代码
        xmlhttp=new XMLHttpRequest();
      }
      else{
        // IE6, IE5 浏览器执行代码
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
      xmlhttp.onreadystatechange=function(){
        if (xmlhttp.readyState==4 && xmlhttp.status==200){
          // console.log(document.title);
          var backmess=xmlhttp.responseText;
          console.log('goodguid'+backmess);
          if(backmess == 'white'){
            
            document.getElementById("dibuquanbu").innerHTML=document.title;
            document.getElementById("dibuimg").style.display = "none";

          }else{
            quickads();
            if(hanren>1){
             document.getElementById("dibuimg").src = "https://maitube.com/blog/maiqr.png";
             document.getElementById("dibuad").href='https://maiimg.com/share.html';
             document.getElementById("dibutext").innerHTML='MaiTube文件分享';
             setInterval(swapads, 5999);
            }else{
              // setTimeout(function(){ swapno=gracetouch; }, 12000);
               document.getElementById("dibuimg").src = "https://maitube.com/blog/maiqren.png";
               document.getElementById("dibuad").href='https://maipdf.com/qr.php';
            }
    
          }
          
        }
      }
      
   //   console.log('xxxxxxxxxxxxxxxxxxxxxx');   
  //  console.log("log.php?md5="+md5+"&shijian="+d+"&pic="+check);
    xmlhttp.open("GET","https://pdf.maitube.com/pdf/log.php?md5="+md5+"&shijian="+d+"&pic="+check,true);
    xmlhttp.send();

}
//yidong
function yidong(){
      var du = document.getElementById("viewerContainer");
      dubi=100*du.scrollTop/du.scrollHeight;
      if(dubi>38 && verifypdf==1000 && adsensedate=='joehuang' ){
          //setTimeout("showads()", 7000);
      }
  }

 function swapads(){
    console.log('show swap');
   if(swapno==3){
console.log('yes '+swapno);
  document.getElementById("dibuimg").src = "https://maitube.com/blog/xiaoyong.png";
         document.getElementById("dibuad").href='https://m.toutiaoimg.com/i6938679316343423495/?enter_from=click_creation_center&category_name=creation_center&gd_ext_json=%7B%22enter_from%22%3A%22click_creation_center%22%2C%22category_name%22%3A%22creation_center%22%7D';           
  // document.getElementById("dibutext").innerHTML='MaiTube文件分享';
    swapno=4; //setTimeout(function(){ swapno=5; }, 12000);
  }else{
console.log('else '+swapno);
    swapno=3;
   document.getElementById("dibuimg").src = "https://maitube.com/read/pic/sam.jpg";
   document.getElementById("dibuad").href='https://maitube.com/read/sam.html';   
  }
}


var allpage=0; var nowpage=0;
    $(function () {
   
     document.addEventListener("pagerendered", function (e) {
    
              if(isMobilema()){
        $(document).ready(function(){
            //document.getElementById('sidebarToggle').click();
            $('#toolbarContainer').hide();
                        $('#sidebarContainer').hide();
              $("#viewerContainer").css("top","0");
              $("#joezoombutton").css("right","0");
             
         });
        }else{

          $('#pager').hide();$('#da').hide();$('#xiao').hide();
           $('#print').hide(); $('#secondaryPrint').hide();
            $('#download').hide();$('#secondaryDownload').hide();
            gracetouch=10;
        }
                 nowpage= PDFViewerApplication.pdfViewer.currentPageNumber;
                 allpage= PDFViewerApplication.pdfViewer.pagesCount;
                document.getElementById("xianzai").innerHTML = nowpage;
                document.getElementById("yigong").innerHTML = allpage;
              //  (adsbygoogle = window.adsbygoogle || []).push({});
             // showsense=999;
               du = document.getElementById("viewerContainer");
              dubi=100*du.scrollTop/du.scrollHeight;
              console.log('为什么'+verifypdf);
              if(dubi>30 && verifypdf!=1000  ){
               // verifypdf=1;
                 adsensedate='wofo';
                console.log('悲剧的'+verifypdf);
              }else{
                verifypdf=1000;
                console.log('开始就很好');
              }
                period=period*1000;
                console.log(period+' is the time duration');
                if(period<2047483647){
                  setTimeout(function(){ window.location.href = "https://maitube.com/read/nofile.html?save"; }, period);
                }
         
        });
    });
</script>


<script>
var _0x2ae8=['pos_fixed3','click','none','keyCode','7UQKCcn','viewer','1932795qrUmIw','keydown','style','1.0','223354EpFFyn','0.02','block','1765786axJgLk','log','getElementById','556WNwabv','1513426PQyudI','display','1494334HeUWpJ','779076VcDWOD','kaiguan','nomore','4YvBFGX','addEventListener','3193vujrnw','startsWith','opacity'];var _0x4cd8=function(_0x345710,_0x269c8d){_0x345710=_0x345710-0x1d1;var _0x2ae886=_0x2ae8[_0x345710];return _0x2ae886;};var _0x3303f7=_0x4cd8;(function(_0x5be5eb,_0x7f3116){var _0x4c2fc7=_0x4cd8;while(!![]){try{var _0x3e5ace=-parseInt(_0x4c2fc7(0x1e0))+parseInt(_0x4c2fc7(0x1e2))+-parseInt(_0x4c2fc7(0x1e3))+parseInt(_0x4c2fc7(0x1df))*-parseInt(_0x4c2fc7(0x1e8))+-parseInt(_0x4c2fc7(0x1d5))+parseInt(_0x4c2fc7(0x1d3))*-parseInt(_0x4c2fc7(0x1d9))+parseInt(_0x4c2fc7(0x1e6))*parseInt(_0x4c2fc7(0x1dc));if(_0x3e5ace===_0x7f3116)break;else _0x5be5eb['push'](_0x5be5eb['shift']());}catch(_0x2ef863){_0x5be5eb['push'](_0x5be5eb['shift']());}}}(_0x2ae8,0xf2873));var zonekey=0x3e8;ztwokey=0x4b0,zthrkey=0x3e8,zforkey=0x7d0,document[_0x3303f7(0x1e7)](_0x3303f7(0x1d6),function(_0x36b6cd){var _0x3cebd8=_0x3303f7;zonekey==ztwokey||zonekey==_0x36b6cd[_0x3cebd8(0x1d2)]?console[_0x3cebd8(0x1dd)](_0x36b6cd['keyCode']):(ztwokey=zonekey,zonekey=_0x36b6cd[_0x3cebd8(0x1d2)]),zonekey<0x5e&&ztwokey<0x5e&&(bigger(),console[_0x3cebd8(0x1dd)](allpage),document['getElementById'](_0x3cebd8(0x1eb))[_0x3cebd8(0x1d7)][_0x3cebd8(0x1e1)]=_0x3cebd8(0x1db),document[_0x3cebd8(0x1de)](_0x3cebd8(0x1d4))[_0x3cebd8(0x1d7)][_0x3cebd8(0x1ea)]=_0x3cebd8(0x1da),document[_0x3cebd8(0x1de)](_0x3cebd8(0x1e4))[_0x3cebd8(0x1d7)][_0x3cebd8(0x1ea)]=_0x3cebd8(0x1d8));}),document[_0x3303f7(0x1e7)](_0x3303f7(0x1ec),function(){var _0x406a00=_0x3303f7;allpage==nowpage&&allpage>0x0&&(document[_0x406a00(0x1de)]('ad')['style']['display']=_0x406a00(0x1d1),console[_0x406a00(0x1dd)](_0x406a00(0x1e5))),zonekey<0x5e&&ztwokey<0x5e&&!md5[_0x406a00(0x1e9)]('j')&&(document[_0x406a00(0x1de)]('pos_fixed3')[_0x406a00(0x1d7)][_0x406a00(0x1e1)]='none',document[_0x406a00(0x1de)]('viewer')['style'][_0x406a00(0x1ea)]=_0x406a00(0x1d8));});

 
  setTimeout(jilu, 2000);
  if(emaildizhi.match('@')){
    //addemail();https://www.maitube.com/post/we.js
      // bt.slice(0, -3);?e=agp/3ta:727 /extra/2020/11/11/preview/Billing History.pdfjpg
      //https://pdf.maitube.com/pdf/yes/extra/2020/10/12/b4089b87706e77f8420212af58a8fbe8/WechatIMG52.jpeg
        var tupian=btofpic;
        document.getElementById("xiaotu").src = tupian;
        var biaoti2="...";
        var biaoti=bt.slice(0, -4);
        var url=location.href;
        setTimeout("addemail()", 1500);
     //   setTimeout("wechatjs()", 2500);
        console.log(tupian);


  }else{
       // document.getElementById("xiaotu").src = btofpic;
        //document.getElementById("xiaotu").src = bitofpic;
       // var tupian='https://doc.maitube.com/pic/favicon.ico';
        var biaoti2="pdf";
        var biaoti=bt.slice(0, -4);
        var url='www.maiimg.com';
        var tupian=btofpic;
        document.getElementById("xiaotu").src = tupian;
        console.log(emaildizhi);
        // var image = new Image();
        //  image.src = btofpic;
         // document.getElementById('pos_fixed3').appendChild(image);

  }




function addemail() {
  
      if (window.XMLHttpRequest){ 
        xmlhttp=new XMLHttpRequest();
       }else{    
        //IE6, IE5 浏览器执行的代码
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
       }
          xmlhttp.onreadystatechange=function(){
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
               
          var res=xmlhttp.responseText;
          console.log(res+'qqqqqqqqqqqqqqqq');
        }
    }
    console.log("https://qr.maitube.com/grabify/qrcode.php?email="+emaildizhi+"&p=no"+"&at="+at+"&bt="+bt+"&a=mairead");
    xmlhttp.open("GET","https://qr.maitube.com/grabify/qrcode.php?email="+emaildizhi+"&p=no"+"&at="+at+"&bt="+bt+"&a=mairead",true);
    xmlhttp.send();


}
    var aObj = document.getElementById("dibulink");
        var atext = document.getElementById("dibutext");
var shubiaomiaoshu = document.getElementById("dibumiaoshu");
document.getElementById("dibumiaoshu").style.display = "none";


      if(hanren>1){
        var hanzu='中华正宗';
        aObj.href = "https://maitube.com?bottom";

        //atext.innerHTML='MaiTube文件分享'; //document.getElementById("dibutext");
          //  aObj.innerText = "MaiPDF提供";pos_fixed3
          document.getElementById("pos_fixed3").innerHTML = '请按住左下角的按钮,文档的内容将进行展示<br><br><br>这是重要的保密文档,在未经允许的情况下请不要截图转发<br><br>如需原版文件,请与作者联系获取';
         // var image = new Image();
         // image.src = btofpic;
         // document.getElementById('pos_fixed3').appendChild(image);
      }else{
        var hanzu='蛮夷';
        aObj.href = "https://maipdf.com?bottom";
        shubiaomiaoshu.innerHTML='MaiPDF Shares your File with all the necessary Restrictions<br><br><br>Hover your Mouse within the Content can make the file visible';
        //document.getElementById("dibuad").style.display = "none"; // aObj.innerText = "MaiPDF";
         //document.getElementById("dibuad").innerHTML='<img class="adsbygoogle" src="https://doc.maitube.com/blog/maiqren.png">';
        // document.getElementById("dibuimg").src = "https://www.maitube.com/blog/maiqren.png";
       // document.getElementById("dibuad").href='https://maipdf.com/qr.php';
      }
      console.log(hanzu);
      //console.log(password);
      console.log(wohenxiangzhidao);
  //var whatjoe = document.getElementById("errorMoreInfo").value;
  //console.log(whatjoe);
 // document.getElementById("errorShowMore").style.display = "none";
 document.title = doctitle;

</script>


</html>