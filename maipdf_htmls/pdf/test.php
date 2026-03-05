<script>
          var d = new Date(); console.log(d);
		  document.cookie ="usertime="+encodeURI(d);
		  document.cookie ="userjoe="+encodeURI(d);
		  var hanren = encodeURI(d);
		   var hanren = hanren.indexOf("0800");
		  console.log(d);
		  console.log('fox');
		  var wohenxiangzhidao='what';
      var bt='xxx';

      
</script>
<?php 



if(isset($_COOKIE['maigua'])){
    //include_once ('/var/www/html/register/sendemail.php');
    $maigua= $_COOKIE['maigua'];  
    $gua=explode(':', $maigua);
    if($gua[0]!='qweewq'){
      echo "<div style=\"display:none;\">";
      //sendoutemail('admin@maitube.com',$maigua);  
      echo "</div>" ;
    }
      
}
    
      $cookietime= $_COOKIE['usertime'];  
        $timesqure=$cookietime;
        if((stristr($timesqure,'China'))||(stristr($timesqure,'0800'))||(stristr($timesqure,'中'))){
             
             $urljieshu= 'https://t.maitube.com/pdf/%E4%B8%AD%E5%8D%8E.php?maigua'; 
           //  https://t.maitube.com/pdf/%E4%B8%AD%E5%8D%8E.php 
        }else{
             $urljieshu= 'https://www.maipdf.com?maipdf';
    
        }
?>

<?php
    //get the email equals to variable
  if(isset ($_GET['email'])){
    $email=htmlspecialchars($_GET['email']);

 
	if($email[0]=='l'){
		//是传输进来的长链接
		$email=substr($email,1);
		$longurl=1;
		if (array_key_exists("HTTP_REFERER",$_SERVER)){
			if(!stristr($_SERVER["HTTP_REFERER"],'maipdf')){
				exit('This Incident is very interesting,want to resolve,contact admin');
			}
		}else{
			$url= 'https://'.$_SERVER['HTTP_HOST'].'/pdf/?email='.$email;
            header("Location: $url");
			exit();
		}
	}else{
		 $longurl=2;
	}
	//echo  $longurl;
  }else{
    // exit("you are not authorised to view it");
     exit("<h1 style=\"color:red;text-align:center;\";>You are not authorised to view on<br>Don't know what are you trying to read</h1><meta http-equiv='refresh' content='3;url=$urljieshu'>");
  }
  //$conn= new mysqli("213.136.92.253","joe","JOEjoe123","record");
  include_once ('../password.php');
            $conn = new mysqli($servernameMai, $usernameMai, $passwordMai, $dbnameMai);
  if($conn->connect_error){
   die("<meta http-equiv='refresh' content='2;url=$urljieshu'>"); 
   // die("<h1 style=\"color:red;text-align:center;position:fixed;top:50%;right:50%;\";>页面走开了<br>请您重新刷新一下页面</h1>");
  }
  $sql="SELECT * FROM `pdf` WHERE `mdemail`='$email'";
  //echo $sql;
  
  
  $result=mysqli_query($conn,$sql);
  if (mysqli_num_rows($result)>0) {
      while($row = mysqli_fetch_assoc($result)){
      $limit=$row['limit'];
       $url=$row['url'];
       $period=$row['password'];
       $limit=$limit-1;
       $urloriginal='yes'.$url;
          
      
      $sql2="UPDATE `pdf` SET `limit`=$limit WHERE `mdemail`='$email' ";  
		  if($longurl==2 && $limit > -3){
		  $result2 = mysqli_query($conn,$sql2);
		  }
	  }
	    $fileurl= explode('/',$url);
		//print_r($fileurl);
		//exit($fileurl[4]);
      } else {
          
     // exit("You are not authorised to view on");
      exit("<h1 style=\"color:red;text-align:center;\";>You are not authorised to view on<br>Please Contact Author</h1><meta http-equiv='refresh' content='2;url=$urljieshu'>");
      //exit("<meta http-equiv='refresh' content='0;url=https://pdf.maitube.com'>"); 
      } 
      
   $ip=$_SERVER['REMOTE_ADDR'];
   if(isset($cookietime)){
	   $zmak5=$cookietime;
   }else{
   $zmak5=date("Y/m/d+H:i:s");
   }
   $zmak6=$zmak5.rand(7,777);
   $zmak6=md5($zmak6);
     $br=$_SERVER['HTTP_USER_AGENT'];
     
    $br = explode(')',$br);
    $br= $br[0].")";
  if($limit<0){
       //$limit = -1* $limit;
       //exit($limit);
     if(abs($limit) > 2){
       $free='Failed';
       $sqlrecord = "INSERT INTO `records`(`email`, `subject`, `mark`,`markopen`,`passcode`,`ip`,`add`) VALUES ('$email','$free','$zmak5','$br',19900101,'$ip',13) ";
       
       $pathpdf = 'yes'.$url;
       unlink($pathpdf);
       $result2=mysqli_query($conn,$sqlrecord);
       $conn->close();
       //$limit = -1* $limit;
       //exit('js:'.$limit);
       exit("<h1 style=\"color:Orange;text-align:center;\";>You are not authorised to view on<br>Please Contact Author</h1><meta http-equiv='refresh' content='2;url=$urljieshu'>");
       
     }

     $conn->close();
   //exit("you are not authorised to view this File");
   exit("<h1 style=\"color:red;text-align:center;\";>You are not authorised to view on<br>Please Contact Author</h1><meta http-equiv='refresh' content='2;url=$urljieshu'>");
   
  }else{
     $free='Succed';
          $sqlrecord = "INSERT INTO `records`(`email`, `subject`, `mark`,`markopen`,`passcode`,`ip`,`add`) VALUES ('$email','$free','$zmak5','$br',19900101,'$ip',3) ";
     
          echo "<script>var md5='$email';var ip='$ip' ; var br='$br'; </script>";
     

     //$result2=mysqli_query($conn,$sqlrecord);
     $conn->close();
     $verifyfile='/var/www/html/pdf/yes'.$url.'3.txt';

	 if(file_exists($verifyfile)){
      $current = file_get_contents($verifyfile);
          if(strlen($current)>3){
          $current=explode(';',$current);
          $bt=explode('/', $url);
          $bt=end($bt);
          echo "<script>   var emaildizhi='$current[0]'; var at='$current[1]';var bt='$bt';                                         </script>";
      }         
    }
    $link = 'yes'.$url;
	     if($longurl==2){
	        if (file_exists($link) && stristr($link,'preview')){
				        
				        $pagepreview = 1;
					    $link = 'https://doc.maitube.com/pdf/yes'.$url;
						$link = 's'.$url;
              //$linker= str_replace("/preview/","/",$link);
						$linker='https://doc.maitube.com';
					}else{
					  $pagepreview = 10;
						$link = 'https://doc.maitube.com/pdf/yes'.$url;
						$link = 's'.$url;
				        $link= str_replace("/preview/","/",$link);
                        $linker='https://doc.maitube.com';
					}
		 }else{
			 $pagepreview = 10;
			 $link = 'https://doc.maitube.com/pdf/yes'.$url;
			 $link = 's'.$url;
			 $link= str_replace("/preview/","/",$link);
               $linker='https://doc.maitube.com';
		 }
  }
    ?>


<!DOCTYPE html>

<script>
   var joehasafile='<?php echo $link;?>';
   //alert(joehasafile);
</script>
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
    <title>MaiPDF Secure PDF</title>
    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.1/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="https://doc.maitube.com/pdf/viewer.css">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.13.1/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="resource" type="application/l10n" href="https://doc.maitube.com/pdf/locale/locale.properties">
    <script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@2.0.943/build/pdf.min.js"></script>
    <script src="https://doc.maitube.com/pdf/v2.js"></script>
    
 </head>
   

 

 <body  id="bigboy" tabindex="1" class="loadingInProgress">
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

        <div id="viewerContainer" tabindex="0" ontouchstart="zongti()" ontouchmove="zongtimove()"  ontouchcancel="zongtifinish()" ontouchend="zongtifinish()">
		 <div id="viewer" class="pdfViewer">
			
		  </div>
		  <?php 
		  echo "<meta http-equiv=\"Refresh\" content=\"$period;url=$urljieshu\">";
		     if($pagepreview==1){
				 $gourl= 'https://'.$_SERVER['HTTP_HOST'].'/pdf/?e=l'.$email;
           
		      echo "<a href='$gourl' target='_blank'><button id='button3'>+More</button></a>";
			 }
         ?>
		  <!--   <hr><hr><hr><hr><hr><hr><hr><hr><hr><hr> -->

        <br><h5 style="text-align:center;color: silver">
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
              <br><br>

        <h6 style="text-align: center;"> 
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

              <br><br>
        </h5>
       <h5 id="dibumiaoshu"  style="text-align:center;color: silver">
                       MaiTube提供的PDF文件安全分享是免费易用的民间好工具<br>
                             将鼠标移入框内可以展示文档内容
        <style>
        .adsbygoogle { width: 320px; height: 120px; }
        @media(min-width: 500px) { .adsbygoogle { width: 468px; height: 120px; } }
        @media(min-width: 800px) { .adsbygoogle { width: 528px; height: 120px; } }
        </style>
        
 
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
	<p class="pos_fixed"><?php echo $ip ?></p>
  <p class="pos_fixed1"><?php echo $ip ?></p>
  <p class="pos_fixed2"><?php echo $ip ?></p>
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
      *Please Click the Button at bottom left corner*<br>
             *IP address as your watermark*<br>
         |||||||||||||||||||||| ||||||||||||||||||||||||||||||||<br>
        
       <br> Please Click the Button at bottom left corner<br>
        IP address as your watermark<br>
    
  
    </p>
  <?php 
  if(substr($email,0,1)=='j'){  
     echo "<button id=\"kaiguan\" type=\"button\"  ontouchcancel=\"cancelFunction()\" ontouchend=\"endFunction()\" ontouchmove=\"touchFunction()\" ontouchstart=\"startFunction()\">MaiPDF</button>"; 
  }
   ?>
   
  </body>
  <script>
document.getElementById("outerContainer").addEventListener('contextmenu', function(){
	//document.getElementById("viewer").style.display = "none";
    //alert("Protected by *MaiPDF* ! Hidden WaterMark Protected"); 
    //return false;
	//document.getElementById('xiao').click();document.getElementById('da').click();return false;
});
document.getElementById("outerContainer").addEventListener('keydown', function(e){
  
  
    //alert("keyboard command not working !"); 
  //return false;
});
</script>





<script>
  







  



   <?php 
    if(substr($email,0,1)=='j'){  
          echo "document.getElementById(\"viewerContainer\").style.opacity=\"0.1\";   $(\"#pos_fixed3\").animate({ height:'+=190px'},5000);"; 

    
  }else{
    echo "document.getElementById(\"pos_fixed3\").style.display=\"none\";"; 
    //echo "document.getElementById(\"kaiguan\").style.display=\"none\";"; 
    echo "document.getElementById(\"viewerContainer\").style.opacity=\"1.0\";"; 
  }
   ?>
  
function isMobilema()
{
    var mobile = navigator.userAgent.match(/iphone|android|phone|mobile|wap|netfront|x11|java|operamobi|operamini|ucweb|windowsce|symbian|symbianos|series|webos|sony|blackberry|dopod|nokia|samsung|palmsource|xda|pieplus|meizu|midp|cldc|motorola|foma|docomo|up.browser|up.link|blazer|helio|hosin|huawei|novarra|coolpad|webos|techfaith|palmsource|alcatel|amoi|ktouch|nexian|ericsson|philips|sagem|wellcom|bunjalloo|maui|smartphone|iemobile|spice|bird|zte-|longcos|pantech|gionee|portalmmm|jig browser|hiptop|benq|haier|^lct|320x320|240x320|176x220/i)!= null;
     console.log(navigator.userAgent);

//var mobile = navigator.userAgent.match(/x86_64)!= null;
    if(navigator.userAgent.match(/x86_64/i)!= null){
        mobile=0;
    }

    return mobile;
}
   
</script>



<style> 
#button3 {
    background-color: orange; /* Green */
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 18px;
    margin: 4px 2px;
    cursor: pointer;
	  padding-left: 0;
    padding-right: 0;
    width: 100%;
    margin-left:12px;margin-right:10px;
}
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
  bottom:15%;
  right:1%;
  color:black;
  font-size: 2.0em;
  opacity:0.1;
} 


#pager
{
  position:fixed;
  color:red;
  top:10%;
  left:7%;
  font-size: 1.2em;
  font-weight:bold;
} 


</style>


<script >




    $(function () {
		$("#button3").hide();
        document.addEventListener("pagerendered", function (e) {
			$("#button3").show();
              if(isMobilema()){
				$(document).ready(function(){
            //document.getElementById('sidebarToggle').click();
						$('#toolbarContainer').hide();
            $('#sidebarContainer').hide();
						  $("#viewerContainer").css("top","0");
						  $("#joezoombutton").css("right","0");
						 
				 });
				}else{
					$('#pager').hide();
					 $('#print').hide(); $('#secondaryPrint').hide();
						$('#download').hide();$('#secondaryDownload').hide();
				}
               var nowpage= PDFViewerApplication.pdfViewer.currentPageNumber;
                var allpage= PDFViewerApplication.pdfViewer.pagesCount;
                document.getElementById("xianzai").innerHTML = nowpage;
                document.getElementById("yigong").innerHTML = allpage;
              //  (adsbygoogle = window.adsbygoogle || []).push({});

            if (window.XMLHttpRequest)
      {
        // IE7+, Firefox, Chrome, Opera, Safari 浏览器执行代码
        xmlhttp=new XMLHttpRequest();
      }
      else
      {
        // IE6, IE5 浏览器执行代码
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
      xmlhttp.onreadystatechange=function()
      {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
          //document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
        }
      }
          
    console.log("https://pdf.maitube.com/pdf/log.php?md5="+md5+"&shijian="+d);
    xmlhttp.open("GET","https://pdf.maitube.com/pdf/log.php?md5="+md5+"&shijian="+d,true);
    xmlhttp.send();
            document.cookie ="userread=yes";
           var user = getCookie("userread");
         
        });
    });
</script>
<script>
  if(bt!='xxx'){
    //addemail();
    setTimeout("addemail()", 3500);
  }
  console.log(bt);
function addemail () {
  
      if (window.XMLHttpRequest){ 
        xmlhttp=new XMLHttpRequest();
       }else{    
        //IE6, IE5 浏览器执行的代码
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
       }
          xmlhttp.onreadystatechange=function(){
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
               
          //var res=xmlhttp.responseText;
        }
    }
   // console.log("https://qr.maitube.com/grabify/qrcode.php?email="+emaildizhi+"&p=no"+"&at="+at+"&bt="+bt+"&a=read");
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
		  	atext.innerHTML='MaiTube 文件分享';
		  	  //  aObj.innerText = "MaiPDF提供";
		  }else{
		  	var hanzu='蛮夷';
		  	aObj.href = "https://maipdf.com?bottom";
		  	shubiaomiaoshu.innerHTML='MaiPDF Shares your File with all the necessary Restrictions';
		  	   // aObj.innerText = "MaiPDF";
		  }
		  console.log(hanzu);
      //console.log(password);
      console.log(wohenxiangzhidao);
  //var whatjoe = document.getElementById("errorMoreInfo").value;
  //console.log(whatjoe);
  document.getElementById("errorShowMore").style.display = "none";
 

</script>


</html>