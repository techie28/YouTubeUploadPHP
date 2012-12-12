<?php
/*
Created by Himanshu Sharma
techie_28@yahoo.com
feel free to question/suggest any time on the blog.
http://codershelpingcoders.com


*************************************************************************
*   REPLACE YOURE KEY in the code($key).You can obtain the key from google
*   here https://code.google.com/apis/youtube/dashboard/gwt/index.htm
*   It is advised to follow the instructions of the post from the blog
*   If there is a problem,comment again on the post to seek help. 
*   Use at your own risk. :)
*************************************************************************
*/
ob_start();
session_start();
//////////////////////////////////////////YouTube Meta upload section////////////////////////////////////////////////////////// 
if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'metadata'){
  $key = 'XXX';   //get yours from here https://code.google.com/apis/youtube/dashboard/gwt/index.htm
  $xml = '<?xml version="1.0"?>
              <entry xmlns="http://www.w3.org/2005/Atom"
                xmlns:media="http://search.yahoo.com/mrss/"
                xmlns:yt="http://gdata.youtube.com/schemas/2007">
                <media:group>
                  <media:title type="plain">'.$_REQUEST['title'].'</media:title>
                  <media:description type="plain">'.$_REQUEST['desc'].'</media:description>                  
                  <media:category
                    scheme="http://gdata.youtube.com/schemas/2007/categories.cat">'.$_REQUEST['cat'].'
                  </media:category>
                  <media:keywords>'.$_REQUEST['keywrd'].'</media:keywords>
                </media:group>
              </entry>';
  $headers = array('Authorization: AuthSub token="'.trim($_SESSION['AuthSubSessToken']).'"','GData-Version:2','X-GData-Key: key='.trim($key),'Content-length:'.trim(strlen($xml)),'Content-Type:application/atom+xml; charset=UTF-8');
      
      //print('<pre>');print_r($headers);
  $curl = curl_init('http://gdata.youtube.com/action/GetUploadToken');                       
              curl_setopt($curl,CURLOPT_RETURNTRANSFER ,1);                                 
              curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);                                              
              curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);                
              $response = curl_exec($curl);
              curl_close($curl);
              $correctResp = simplexml_load_string($response);
              if(isset($correctResp->url) && isset($correctResp->token)){
                //join url and token as url-token
                //just pass this url as an action of the form and this token as hidden field token
                echo $correctResp->url.';'.$correctResp->token;
              }else{
                echo 'error';
              }
              exit;      
}

$nextUrl      = urlencode('XXX');
if(!isset($_GET['token']) && !isset($_SESSION['AuthSubSessToken'])){ 
    $scope    = urlencode('http://gdata.youtube.com');
    $authUrl  = 'https://www.google.com/accounts/AuthSubRequest?next='.$nextUrl.'&scope='.$scope.'&session=1&secure=0';       
    header('location:'.$authUrl);
}                                                                                         
if(isset($_GET['token']) && !isset($_SESSION['AuthSubSessToken'])){  
    $curl = curl_init();   
    curl_setopt($curl, CURLOPT_URL,"https://www.google.com/accounts/AuthSubSessionToken"); 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FAILONERROR, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);   
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: AuthSub token="'.$_GET["token"].'"'
                    )); 
    $result = curl_exec($curl);
    $arrToken = explode('=',$result);
    curl_close($curl);      
    $_SESSION['AuthSubSessToken'] = $arrToken['1'];
}
?>

<html>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
</head>
<form name="frmMeta" id="frmMeta" action="" method="post">
<div class="input text required">
        <label><strong>Youtube Title:</strong></label>
        <input  style="width:395px;" type="text" name="youtubeTitle" id="youtubeTitle" />
      </div> 
      <div class="input text required">
        <label><strong>Youtube Category:</strong></label>
        <select name="youTubeCategory" id="youTubeCategory">
          <option selected="selected" value="">-- Select a category --</option>
          <option value="Autos &amp; Vehicles">Autos &amp; Vehicles</option>
          <option value="Comedy">Comedy</option>
          <option value="Education">Education</option>
          <option value="Entertainment">Entertainment</option>
          <option value="Film &amp; Animation">Film &amp; Animation</option>
          <!--<option value="Gaming">Gaming</option>-->
          <option value="Howto &amp; Style">Howto &amp; Style</option>
          <option value="Music">Music</option>
          <option value="News &amp; Politics">News &amp; Politics</option>
          <!--<option value="Nonprofits &amp; Activism">Nonprofits &amp; Activism</option>-->
          <option value="People &amp; Blogs">People &amp; Blogs</option>
          <!--<option value="Pets &amp; Animals">Pets &amp; Animals</option>-->
          <!--<option value="Science &amp; Technology">Science &amp; Technology</option>-->
          <option value="Sports">Sports</option>
          <option value="Travel &amp; Events">Travel &amp; Events</option>
      </select>
      </div>                          
      <div class="input text required">
        <label><strong>Youtube Description:</strong></label>          
        <textarea name="youtubeDesc" id="youtubeDesc" rows="10" cols="47"></textarea>
      </div>                             
      <div class="input text required">
        <label><strong>Youtube Keyword:</strong></label>
        <input type="text" style="width:395px;" name="youtubeKeyword" id="youtubeKeyword"/>
        
      </div>  
      <div id="metaDataCheck" style="margin-left:140px">
        <input class="submit_button" type="button" name="updMetaData" id="updMetaData" value="Upload YouTube Metadata">
      </div>
</form>
<form name='frmYoutube' id='frmYoutube' action="" method="post" enctype='multipart/form-data'>
    <input type="hidden" id="youTubeToken" name="token" value=""/>
      <div class="input text required">
        <label><strong>Youtube:</strong></label>
        <input type="file" name="file" id="youtubeVid" />
      </div>
      <input type="submit" name="submit" value="Upload">
    </form>
    <script type="text/javascript">
 $(document).ready(function() {
  $('#updMetaData').click(function(){
      title   = $('#youtubeTitle').val();  
      desc    = $('#youtubeDesc').val(); 
      keywrd  = $('#youtubeKeyword').val();   
      cat     = $('#youTubeCategory').val();
      $.ajax({
          url: "script.php",
          data: 'cat='+cat+'&title='+title+'&desc='+desc+'&keywrd='+keywrd+'&method=metadata',
          success: function(xhr){
            if(xhr !='error'){
              $('#tickMark').remove();
              $('#metaDataCheck').append('<span id="tickMark"><br /><br /><strong style="color:#077A29">Video meta data uploaded successfully!!</strong></span>');
              exploded = xhr.split(';');
              frmAction   = exploded[0]+'?nexturl=<?php echo $nextUrl; ?>';
              $('#frmYoutube').attr('action',frmAction);
              $('#youTubeToken').attr('value',exploded[1]); 
            }else{                
              $('#tickMark').remove();
              $('#metaDataCheck').append('<span id="tickMark"><br /><br /><strong style="color:#FF0000">Video meta data could not be uploaded,Please try again!!</strong></span>');
            }
          }
        });
  });
});
  </script>
</html>