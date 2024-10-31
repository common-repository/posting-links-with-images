<?php
/*
Plugin Name: posting links with images
Plugin URI: http://radicoreweb.com/
Description: This will helps you to post links with image, title and description...
Version: 1.0
Author: Radicore Softech Web Solutions Pvt Ltd.
Author URI: http://radicoreweb.com/
*/

define('RADICORE_PLUGIN_URL', plugin_dir_url( __FILE__ ));


function Radicore_Styles_Scripts(){

		wp_register_style( 'Radicore.css', RADICORE_PLUGIN_URL . 'radicore.css');
		wp_enqueue_style( 'Radicore.css');

}

function AddFunction()
{
add_action( 'Styles_and_Scripts', 'Radicore_Styles_Scripts' );
?>
<link rel="stylesheet" type="text/css" href="<?php echo RADICORE_PLUGIN_URL.'radicore.css'; ?>">
<script src="<?php echo RADICORE_PLUGIN_URL.'radicore.js'; ?>"></script>

<div style="width: 400px;border: 2px solid;padding: 20px;margin: 20px 3px;border-radius: 3px;background-color: rgba(198, 219, 39, 0.49);">
<div>
<form  action="<?php $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return validate()">
<h3>Enter URL: </h3>  <h4><input name="URL" type="text" id="url"  style="height: 35px;width: 255px;" placeholder=" Enter An Url Here">
<input type="submit" value="Generate Code" style="height: 35px;width: 110px;"></h4><h5>Enter URL Like: 'http://www.example.com' or You can paste it..</h5>
<br>
<h4>Paste This Code Into The Post :-)</h4>
</form>
<?php
function getAbsoluteImageUrl($pageUrl,$imgSrc)
{
    $imgInfo = parse_url($imgSrc);
	// if(stripos($imgInfo,'//')==0){
	// return 'http:'.$imgInfo;
	// }
	//echo $imgInfo;
    if (! empty($imgInfo['host'])) {
        //img src is already an absolute URL
        return $imgSrc;
    }
    else {
        $urlInfo = parse_url($pageUrl);
        $base = $urlInfo['scheme'].'://'.$urlInfo['host'];
        if (substr($imgSrc,0,1) == '/') {
            //img src is relative from the root URL
            return $base . $imgSrc;
        }
        else {
            //img src is relative from the current directory
			$tmpAth = substr($urlInfo['path'],0,strrpos($urlInfo['path'],'/'));
			$return = '';
			if(substr($imgSrc,0,3) == '../'){
				$tmlCo = substr_count($imgSrc,"../");
			 if(strlen($tmpAth)==(strrpos($tmpAth,'/')+1)){
			 $tmpAth=substr($tmpAth,0,strrpos($tmpAth,'/'));
			 }
			 for($i=0;$i<$tmlCo;$i++){
			 $tmpAth=substr($tmpAth,0,strrpos($tmpAth,'/'));
			 }
			$return = $base .$tmpAth.'/'. substr($imgSrc,(3*$tmlCo));
			}
			else{
			$return = $base. substr($urlInfo['path'],0,strrpos($urlInfo['path'],'/')). '/' . $imgSrc;
			}
               return $return;
        }
    }
}
function RadiPlugInnerHTML($element) 
{ 
    $innerHTML = ""; 
    $children = $element->childNodes; 
    foreach ($children as $child) 
    { 
        $tmp_dom = new DOMDocument(); 
        $tmp_dom->appendChild($tmp_dom->importNode($child, true)); 
        $innerHTML.=trim($tmp_dom->saveHTML()); 
    } 
    return $innerHTML; 
} 
function RadiCall($tmpURL){
//$url = $content;
$url = $tmpURL;
//description
$disdes = get_meta_tags($url);

//InnerHTML of Title

	
	//To read HTML page
	$html = file_get_contents($url);
    $doc = new DOMDocument();
    @$doc->loadHTML($html);
	
	
	//To Display Title
	$distitle = $doc->getElementsByTagName('title');
	
	//To display Images
    $disimg = $doc->getElementsByTagName('img');
		$tmp='http://t0.gstatic.com/images?q=tbn:ANd9GcSAiuzjpKpPkP1IsXEXosT-VpITpIxMhiPnBWGTvUXoy3bWmPUlPg';
		$turl = '';
		foreach ($disimg as $tag) {
		$turl = $tag ->getAttribute('src');
			// if(stripos($turl,'//')==0){
			// $turl='http:'.$turl;
			// }
			if(strstr($turl,'http')){
			$tmp = $turl;
			}
			else{
			$tmp = getAbsoluteImageUrl($url,$turl);
			}
			//if($turl!='')
			break;
		}
		$tmp2='';
		foreach ($distitle as $tag) { $tmp2= '<a href="'.$url.'" target="_blank">'.@RadiPlugInnerHTML($tag).'</a>'; break;}
		//$tmp,$tmp2;
		return '<div class="linkpost"><div class="linkimage"><a href="'.$url.'" target="_blank"><img class="DisImg" alt="NYC" src="'.$tmp.'"></a></div><div class="linktitle">'.$tmp2.'</div><div class="linkurl">'.@$url.'</div><div class="linkdes">'.@$disdes['description'].'</div></div>';
}

//$tmpU = @callAble($_POST["URL"]);
if(@$_POST["URL"] == ''){}
else{
$tmpU = @RadiCall($_POST["URL"]);
}
?>
<textarea style="height: 250px;width: 395px;border-radius: 3px;padding: 10px;" placeholder="The Code Will Be Here..."><?php echo @$tmpU;?></textarea>
<br>
<h4>This Will Be Display Like This...</h4>
<div style="width:400px;"><?php echo @$tmpU;?></div>
</div>
</div>
<?php
}

//------------------------------------------------------
//Add To Admin Navigation
//------------------------------------------------------
function Radicore_plugin_setup()
{
	add_submenu_page('plugins.php', 'posting links with images', 'posting links with images', 10, 'posting-links-with-images', 'AddFunction');
}
add_action('admin_menu', 'Radicore_plugin_setup');
?>