<?php
/*
=====================================================
 TWS Gallery - by Al-x
-----------------------------------------------------
 Version TWS Gallery 5.2
 Powered by http://wonderfullife.ru/
 Support by http://wonderfullife.ru/, http://inker.wonderfullife.ru/
-----------------------------------------------------
 Copyright (c) 2007,2012 TWS
=====================================================
 Данный код защищен авторскими правами
=====================================================
 Файл: functions.admin.php
-----------------------------------------------------
 Назначение: Функции администрирования
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

function get_extension_icon ($ext, $media_type){ // 03.03.2012

	switch ($media_type){
	case 51 : return 'youtube.com.png';
	case 52 : return 'rutube.ru.png';
	case 53 : return 'video.mail.ru.png';
	case 54 : return 'vimeo.com.png';
	case 55 : return 'smotri.com.png';
	case 56 : return 'gametrailers.com.png';
	case 1  :	case 12  : return 'audio.png';
	case 9  :	case 5  : return 'flash.png';
	case 2  :	case 3  :	case 4  :	case 6  :	case 7  :	case 8  :	case 11 :	case 13 :	case 14 :	case 15 : return 'video.png';
	default :
		$temp_arr = explode('.',$ext);
		$ext = end($temp_arr);
		$ext = strtolower($ext);
		return $ext.".png";
	}

}

function players($url, $media_type, $fullsize=false, $priview='', $alt=''){ // 08.02.2013
global $galConfig, $config;

	switch ($media_type){
	case 51 : $ext = 'youtube.com'; break;
	case 52 : $ext = 'rutube.ru'; break;
	case 53 : $ext = 'video.mail.ru'; break;
	case 54 : $ext = 'vimeo.com'; break;
	case 55 : $ext = 'smotri.com'; break;
	case 56 : $ext = 'gametrailers.com.png'; break;
	default:
		$temp_arr = explode('.',$url);
		$ext = end($temp_arr);
	}

	if ($media_type == 10 || !$galConfig['extensions'][$ext]['m'] && !$fullsize)
		return "<img src=\"".($priview ? $priview : "{THEME}/gallimages/extensions/".get_extension_icon ($ext, $media_type))."\" />";

	if ($galConfig['extensions'][$ext]['p'] > 8 && $galConfig['extensions'][$ext]['p'] < 15){

		if (!$fullsize){
			$width = $galConfig['yrt_width'];
			$height = $galConfig['yrt_height'];
		} else {
			$width = $galConfig['yrt_full_width'];
			$height = $galConfig['yrt_full_height'];
		}

	}

	switch ($galConfig['extensions'][$ext]['p']){
	case '2' : // JW FLV MEDIA PLAYER 4.1

		if ($fullsize){
			$width = $galConfig['jw_flv_mp_full_width'];
			$height = $galConfig['jw_flv_mp_full_height'];
			if ($ext == 'mp3'){ $width = $galConfig['jw_flv_mp_mp3_full_width']; $height = $galConfig['jw_flv_mp_mp3_full_height']; }
		} else {
			$width = $galConfig['jw_flv_mp_width'];
			$height = $galConfig['jw_flv_mp_height'];
			if ($ext == 'mp3'){ $width = $galConfig['jw_flv_mp_mp3_width']; $height = $galConfig['jw_flv_mp_mp3_height']; }
		}

		return "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" \"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0\" width=\"{$width}\" height=\"{$height}\" align=\"middle\">
<param name=\"movie\" value=\"".$config['http_home_url']."engine/gallery/classes/flashplayer/player.swf?file={$url}&showdigits=true&autostart=false&overstretch=false&showfsbutton=true&allowfullscreen=false&backcolor=0xBACFEB&lightcolor=0xF1F6FC&volume=50\" />
<param name=\"allowFullScreen\" value=\"true\" />
<param name=\"quality\" value=\"high\" />
<param name=\"bgcolor\" value=\"#000000\" />
<param name=\"wmode\" value=\"opaque\" />
<embed src=\"".$config['http_home_url']."engine/gallery/classes/flashplayer/player.swf\" width=\"{$width}\" height=\"{$height}\" allowfullscreen=\"true\" flashvars=\"file={$url}&showdigits=true&autostart=false&overstretch=false&showfsbutton=true&allowfullscreen=false&backcolor=0xBACFEB&lightcolor=0xF1F6FC&volume=50\" />
</object>";
	
	break;
	case '3' : // DivX Web Player

		if (!$fullsize){
			$width = $galConfig['divx_wp_width'];
			$height = $galConfig['divx_wp_height'];
		} else {
			$width = $galConfig['divx_wp_full_width'];
			$height = $galConfig['divx_wp_full_height'];
		}

		$url = htmlspecialchars(trim($url), ENT_QUOTES, $config['charset']);
		$auto_play = ($fullsize && $galConfig['play']) ? 1 : 0;

		if ($priview) $priview = htmlspecialchars(stripslashes($priview), ENT_QUOTES, $config['charset']);

		return "<object classid=\"clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616\" width=\"{$width}\" height=\"{$height}\" codebase=\"http://go.divx.com/plugin/DivXBrowserPlugin.cab\">
<param name=\"custommode\" value=\"none\" />
<param name=\"mode\" value=\"zero\" />
<param name=\"autoPlay\" value=\"{$auto_play}\" />
<param name=\"minVersion\" value=\"2.0.0\" />
<param name=\"src\" value=\"{$url}\" />
<param name=\"previewImage\" value=\"{$priview}\" />
<embed type=\"video/divx\" src=\"{$url}\" custommode=\"none\" width=\"{$width}\" height=\"{$height}\" mode=\"zero\"  autoPlay=\"{$auto_play}\" previewImage=\"{$priview}\" minVersion=\"2.0.0\" pluginspage=\"http://go.divx.com/plugin/download/\">
</embed>
</object>";
	
	break;
	case '4' : // Плеер cms для видео (v.9.3)

		$player = "&autoHideNav=".(($galConfig['autohide']) ? "true&autoHideNavTime=3" : "false")."&showWatermark=".(($galConfig['flv_watermark']) ? "true&watermarkPosition={$galConfig['flv_watermark_pos']}&watermarkMargin=0&watermarkAlpha={$galConfig['flv_watermark_al']}&watermarkImageUrl={THEME}/dleimages/flv_watermark.png" : "false")."&showPreviewImage=";

		switch (true){
		case ($fullsize && $galConfig['play']) : $player .= "false&autoPlays=true"; break;
		case ($priview) : $player .= "true&previewImageUrl=".htmlspecialchars(stripslashes($priview), ENT_QUOTES, $config['charset']); break;
		case ($galConfig['startframe']) : $player .= "false"; break;
		case ($galConfig['preview']) : $player .= "true&previewImageUrl={THEME}/dleimages/videopreview.jpg"; break;
		default : $player .= "true&previewImageUrl=";
		}

		$id_player = md5(microtime());

		if (!$fullsize){
			$width = $galConfig['cms_fp_width'];
			$height = $galConfig['cms_fp_height'];
		} else {
			$width = $galConfig['cms_fp_full_width'];
			$height = $galConfig['cms_fp_full_height'];
		}

		$galConfig['fullsizeview'] = intval($galConfig['fullsizeview']);
		if (!$galConfig['fullsizeview']) $galConfig['fullsizeview'] = 3;

		if (version_compare($config['version_id'], "9.7", ">") && $media_type == 51){

			return "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0\" width=\"{$width}\" height=\"{$height}\" id=\"Player-{$id_player}\">
<param name=\"movie\" value=\"" . $config['http_home_url'] . "engine/classes/flashplayer/youtube.swf?stageW={$width}&stageH={$height}&contentType=video&showYouTubeHD=true&videoUrl=http://www.youtube.com/watch?v={$url}{$player}&isYouTube=true&youTubePlaybackQuality={$galConfig['youtube_q']}&rollOverAlpha=0.5&contentBgAlpha=0.8&progressBarColor={$galConfig['progressBarColor']}&defaultVolume=1&fullSizeView={$galConfig['fullsizeview']}&showRewind=false&showInfo=false&showFullscreen=true&showScale=true&showSound=true&showTime=true&showCenterPlay=true&videoLoop=false&defaultBuffer={$galConfig['buffer']}\" />
<param name=\"allowFullScreen\" value=\"true\" />
<param name=\"scale\" value=\"noscale\" />
<param name=\"quality\" value=\"high\" />
<param name=\"bgcolor\" value=\"#000000\" />
<param name=\"wmode\" value=\"opaque\" />
<embed src=\"" . $config['http_home_url'] . "engine/classes/flashplayer/youtube.swf?stageW={$width}&stageH={$height}&contentType=video&showYouTubeHD=true&videoUrl=http://www.youtube.com/watch?v={$url}{$player}&isYouTube=true&youTubePlaybackQuality={$galConfig['youtube_q']}&rollOverAlpha=0.5&contentBgAlpha=0.8&progressBarColor={$galConfig['progressBarColor']}&defaultVolume=1&fullSizeView={$galConfig['fullsizeview']}&showRewind=false&showInfo=false&showFullscreen=true&showScale=true&showSound=true&showTime=true&showCenterPlay=true&videoLoop=false&defaultBuffer={$galConfig['buffer']}\" quality=\"high\" bgcolor=\"#000000\" wmode=\"opaque\" allowFullScreen=\"true\" width=\"{$width}\" height=\"{$height}\" align=\"middle\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\"></embed>
</object>";

		}

		if ($media_type == 51) // youtube.com
			$url = "http://www.youtube.com/watch?v=" . $url;

		return "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0\" width=\"{$width}\" height=\"{$height}\" id=\"Player-{$id_player}\">
<param name=\"movie\" value=\"" . $config['http_home_url'] . "engine/classes/flashplayer/media_player_v3.swf?stageW={$width}&stageH={$height}&contentType=video&videoUrl={$url}{$player}&isYouTube=".($media_type != 51 ? "false" : "true&youTubePlaybackQuality={$galConfig['youtube_q']}")."&rollOverAlpha=0.5&contentBgAlpha=0.8&progressBarColor={$galConfig['progressBarColor']}&defaultVolume=1&fullSizeView={$galConfig['fullsizeview']}&showRewind=false&showInfo=false&showFullscreen=true&showScale=true&showSound=true&showTime=true&showCenterPlay=true&videoLoop=false&defaultBuffer={$galConfig['buffer']}\" />
<param name=\"allowFullScreen\" value=\"true\" />
<param name=\"scale\" value=\"noscale\" />
<param name=\"quality\" value=\"high\" />
<param name=\"bgcolor\" value=\"#000000\" />
<param name=\"wmode\" value=\"opaque\" />
<embed src=\"" . $config['http_home_url'] . "engine/classes/flashplayer/media_player_v3.swf?stageW={$width}&stageH={$height}&contentType=video&videoUrl={$url}{$player}&isYouTube=".($media_type != 51 ? "false" : "true")."&youTubePlaybackQuality={$galConfig['youtube_q']}&rollOverAlpha=0.5&contentBgAlpha=0.8&progressBarColor={$galConfig['progressBarColor']}&defaultVolume=1&fullSizeView={$galConfig['fullsizeview']}&showRewind=false&showInfo=false&showFullscreen=true&showScale=true&showSound=true&showTime=true&showCenterPlay=true&videoLoop=false&defaultBuffer={$galConfig['buffer']}\" quality=\"high\" bgcolor=\"#000000\" wmode=\"opaque\" allowFullScreen=\"true\" width=\"{$width}\" height=\"{$height}\" align=\"middle\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\"></embed>
</object>";

	break;
	case '6' : // Флэш плеер cms (v.9.3)

		if (!$fullsize){
			$width = $galConfig['cms_flp_width'];
			$height = $galConfig['cms_flp_height'];
		} else {
			$width = $galConfig['cms_flp_full_width'];
			$height = $galConfig['cms_flp_full_height'];
		}

		return "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' width='{$width}' height='{$height}'><param name='movie' value='$url'><param name='wmode' value='transparent' /><param name='play' value='true'><param name='loop' value='true'><param name='quality' value='high'><param name='allowscriptaccess' value='never'><embed AllowScriptAccess='never' src='{$url}' width='{$width}' height='{$height}' play='true' loop='true' quality='high' wmode='transparent'></embed></object>";

	break;
	case '7' : // Простой плеер cms (v.9.3)

		if (!$fullsize){
			$width = $galConfig['cms_ftp_width'];
			$height = $galConfig['cms_ftp_height'];
		} else {
			$width = $galConfig['cms_ftp_full_width'];
			$height = $galConfig['cms_ftp_full_height'];
		}

		$url = htmlspecialchars(trim($url), ENT_QUOTES, $config['charset']);
		$auto_play = ($fullsize && $galConfig['play']) ? 1 : 0;

		return "<object id=\"mediaPlayer\" width=\"{$width}\" height=\"{$height}\" classid=\"CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6\" standby=\"Loading Microsoft Windows Media Player components...\" type=\"application/x-oleobject\">
<param name=\"url\" VALUE=\"{$url}\" />
<param name=\"autoStart\" VALUE=\"{$auto_play}\" />
<param name=\"showControls\" VALUE=\"true\" />
<param name=\"TransparentatStart\" VALUE=\"false\" />
<param name=\"AnimationatStart\" VALUE=\"true\" />
<param name=\"StretchToFit\" VALUE=\"true\" />
<embed pluginspage=\"http://www.microsoft.com/Windows/Downloads/Contents/MediaPlayer/\" src=\"{$url}\" width=\"{$width}\" height=\"{$height}\" type=\"application/x-mplayer2\" autorewind=\"1\" showstatusbar=\"1\" showcontrols=\"1\" autostart=\"{$auto_play}\" allowchangedisplaysize=\"1\" volume=\"70\" stretchtofit=\"1\"></embed>
</object>";

	break;
	case '8' : //Плеер cms для аудио (v.9.3)

		$id_player = md5(microtime());
		$auto_play = ($fullsize && $galConfig['play']) ? 1 : 0;

		if (!$fullsize){
			$width = $galConfig['cms_fp_mp3_width'];
			$height = $galConfig['cms_fp_mp3_height'];
		} else {
			$width = $galConfig['cms_fp_mp3_full_width'];
			$height = $galConfig['cms_fp_mp3_full_height'];
		}

		return "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0\" width=\"{$width}\" height=\"{$height}\" id=\"Player-{$id_player}\">
<param name=\"movie\" value=\"" . $config['http_home_url'] . "engine/classes/flashplayer/media_player_v3.swf?stageW={$width}&stageH={$height}&contentType=audio&videoUrl={$url}&showWatermark=false&showPreviewImage=true&previewImageUrl=&autoPlays={$auto_play}&isYouTube=false&rollOverAlpha=0.5&contentBgAlpha=0.8&progressBarColor={$galConfig['progressBarColor']}&defaultVolume=1&fullSizeView=2&showRewind=false&showInfo=false&showFullscreen=true&showScale=false&showSound=true&showTime=true&showCenterPlay=false&autoHideNav=false&videoLoop=false&defaultBuffer={$galConfig['buffer']}\" />
<param name=\"allowFullScreen\" value=\"false\" />
<param name=\"scale\" value=\"noscale\" />
<param name=\"quality\" value=\"high\" />
<param name=\"bgcolor\" value=\"#000000\" />
<param name=\"wmode\" value=\"opaque\" />
<embed src=\"" . $config['http_home_url'] . "engine/classes/flashplayer/media_player_v3.swf?stageW={$width}&stageH={$height}&contentType=audio&videoUrl={$url}&showWatermark=false&showPreviewImage=true&previewImageUrl=&autoPlays={$auto_play}&isYouTube=false&rollOverAlpha=0.5&contentBgAlpha=0.8&progressBarColor={$galConfig['progressBarColor']}&defaultVolume=1&fullSizeView=2&showRewind=false&showInfo=false&showFullscreen=true&showScale=false&showSound=true&showTime=true&showCenterPlay=false&autoHideNav=false&videoLoop=false&defaultBuffer={$galConfig['buffer']}\" quality=\"high\" bgcolor=\"#000000\" wmode=\"opaque\" allowFullScreen=\"false\" width=\"{$width}\" height=\"{$height}\" align=\"middle\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\"></embed>
</object>";

	break;

	case '9' : //Плеер youtube.com

		return '<iframe title="YouTube video player" width="'.$width.'" height="'.$height.'" src="http://www.youtube.com/embed/'.$url.'?rel='.$galConfig['yrt_tube_related'].'&amp;wmode=transparent" frameborder="0" allowfullscreen></iframe>';

	break;

	case '10' : //Плеер rutube.ru

		return '<object width="'.$width.'" height="'.$height.'"><param name="movie" value="http://video.rutube.ru/'.$url.'"></param><param name="wmode" value="transparent" /></param><param name="allowFullScreen" value="true"></param><embed src="http://video.rutube.ru/'.$url.'" type="application/x-shockwave-flash" wmode="transparent" width="'.$width.'" height="'.$height.'" allowFullScreen="true" ></embed></object>';

	break;

	case '11' : //Плеер smotri.com

		return '<object id="smotriComVideoPlayer" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="'.$width.'" height="'.$height.'"><param name="movie" value="http://pics.smotri.com/player.swf?file='.$url.'&bufferTime=3&autoStart=false&str_lang=rus&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" /><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="true" /><param name="wmode" value="opaque" /><embed src="http://pics.smotri.com/player.swf?file='.$url.'&bufferTime=3&autoStart=false&str_lang=rus&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" quality="high" allowscriptaccess="always" allowfullscreen="true" wmode="opaque"  width="'.$width.'" height="'.$height.'" type="application/x-shockwave-flash"></embed></object>';

	break;

	case '12' : //Плеер vimeo.com

			return '<iframe width="'.$width.'" height="'.$height.'" src="http://player.vimeo.com/video/'.$url.'" frameborder="0" allowfullscreen></iframe>';

	break;

	case '13' : //Плеер video.mail.ru

			return '<object width="'.$width.'" height="'.$height.'"><param name="allowScriptAccess" value="always" /><param name="movie" value="http://img.mail.ru/r/video2/player_v2.swf?movieSrc='.$url.'" /><param name="wmode" value="transparent" /><embed src="http://img.mail.ru/r/video2/player_v2.swf?movieSrc='.$url.'" type="application/x-shockwave-flash" wmode="transparent" width="'.$width.'" height="'.$height.'" allowScriptAccess="always"></embed></object>';

	break;

	case '14' : //Плеер gametrailers.com

			return '<embed src="http://media.mtvnservices.com/mgid:moses:video:gametrailers.com:'.$url.'" width="'.$width.'" height="'.$height.'" type="application/x-shockwave-flash" allowFullScreen="true" allowScriptAccess="always" base="." flashVars="" wmode="opaque"></embed>';

	break;

	}

}

function thumb_path($thumbnails, $find){ // 22.08.2011

	$thumb_path = 'main';

	if ($thumbnails){

		$thumbnails = explode('||', $thumbnails);
		$thumbnails[0] = explode('|', $thumbnails[0]);
		$thumbnails[1] = explode('|', $thumbnails[1]);
		$thumb_found = false;

		foreach ($thumbnails[0] as $i => $file)
			if ($file == $find || $thumb_found && $thumbnails[1][$i]){
				if ($thumbnails[1][$i]){ $thumb_found = $file; break; }
				$thumb_found = true;
			}

		switch ($thumb_found){
			case 't' : $thumb_path = 'thumb'; break;
			case 'i' : $thumb_path = 'caticons'; break;
			case 'c' : $thumb_path = 'comthumb'; break;
			default :
				if ($find == 'i' && !isset($thumbnails[0]['i']))
					 $thumb_path = 'thumb';
		}

	}

  return $thumb_path;
}

if (version_compare($config['version_id'], "9.5", "<")) $mcache = false;

function clear_gallery_vars($cache_areas = false){
global $mcache;

	if ($mcache){

		if (defined('MEMCACHE_GALLERY_CLEARED')) return;

		define('MEMCACHE_GALLERY_CLEARED', true);
		memcache_flush($mcache);
		return;

	}

	if ($cache_areas && !is_array($cache_areas)) $cache_areas = array($cache_areas);
	$fdir = opendir(TWSGAL_DIR.'/cache/system/');
    while ($file = @readdir($fdir)){
		if ($file != '.' and $file != '..' and $file != '.htaccess' and $file != 'system'){
			if ($cache_areas) {
				foreach($cache_areas as $cache_area){ if (strpos($file, $cache_area) !== false) @unlink(TWSGAL_DIR.'/cache/system/'.$file); }
			} else {
				@unlink(TWSGAL_DIR.'/cache/system/'.$file);
			}
		}
  	}
	@closedir($fdir);

}

function clear_gallery_cache($cache_areas = false){
global $mcache;

	if ($mcache){

		if (defined('MEMCACHE_GALLERY_CLEARED')) return;

		define('MEMCACHE_GALLERY_CLEARED', true);
		memcache_flush($mcache);
		return;

	}

	if ($cache_areas && !is_array($cache_areas)) $cache_areas = array($cache_areas);
	$fdir = opendir(TWSGAL_DIR.'/cache/');
    while ($file = @readdir($fdir)){
        if ($file != '.' and $file != '..' and $file != '.htaccess' and $file != 'system'){
			if ($cache_areas){
				foreach($cache_areas as $cache_area){ if( strpos( $file, $cache_area ) !== false ) @unlink(TWSGAL_DIR.'/cache/'.$file); }
			} else {
	            @unlink(TWSGAL_DIR.'/cache/'.$file);
			}
      	  }
  	}
	@closedir($fdir);

}

function makeDropDownGallery($options, $name, $selected){
global $langGal;
    $output = "";
    foreach($options as $value => $description)
		$output .= "<option value=\"{$value}\"".((!is_array($selected) && $selected == (string)$value || is_array($selected) && in_array($value, $selected)) ? " selected=\"selected\" " : "")."".($langGal['sys_global'] == $description ? " style=\"color:green;\"" : "").">{$description}</option>\n";
    return "<select name=\"{$name}\" id=\"{$name}\">\r\n" . $output . "</select>";
}

function radiomenu($options, $name, $selected){

	$output = "";
	foreach($options as $key => $value){
		$output .= "<input type=\"radio\" value=\"".$key."\"";
		if(is_array($selected) && in_array($key, $selected)) $output .= " checked=\"checked\" ";
		elseif($selected == $key) $output .= " checked=\"checked\" ";
		$output .= " name=\"{$name}\" id=\"{$key}{$name}\"> <label for=\"{$key}{$name}\">".$value."</label>\n";
	}
  return $output;
}

function fastpages($count_all, $limit, $page, $url_page, $scr_page="", $fullline = 10, $sector = 5){
global $lang;

	$page_count = @ceil($count_all/$limit);	$return = "";

	if ($url_page != "") $url = explode("{INS}", $url_page);
	if ($scr_page != "") $scr = explode("{INS}", $scr_page);

	$prev_page = "";
	$next_page = "";

	if ($page != 1)	$prev_page = "<a".(($scr_page != "") ? " onclick=\"".$scr[0].($page-1).$scr[1]."\"" : "")." href=\"". (($url_page != "") ? $url[0].($page-1).$url[1] : "javascript:void(0);") ."\" title=\"{$lang['edit_prev']}\">&lt;&lt;</a>";
     if ($page < $page_count) $next_page = "<a".(($scr_page != "") ? " onclick=\"".$scr[0].($page+1).$scr[1]."\"" : "")." href=\"". (($url_page != "") ? $url[0].($page+1).$url[1] : "javascript:void(0);") ."\" title=\"{$lang['edit_next']}\">&gt;&gt;</a>";

	if ($page_count <= $fullline){

		for($j=1; $j<=$page_count; $j++){

			if ($j != $page)
				$return .= "<a".(($scr_page != "") ? " onclick=\"".$scr[0].$j.$scr[1]."\"" : "")." href=\"". (($url_page != "") ? $url[0].$j.$url[1] : "javascript:void(0);") ."\">{$j}</a> ";
			else
				$return .= "<span>{$j}</span> ";

		}

	} else {

		$start = 1;	$end = $fullline; $nav_prefix = "... ";

		if ($page > $sector) {

			$start = $page + 1 - $sector;
			$end = $start + 8;

			if ($end >= $page_count){
				$start = $page_count - 9;
				$end = $page_count - 1;
				$nav_prefix = "";
			} else $nav_prefix = "... ";

		}

		if ($start >= 2)
			$return .= "<a".(($scr_page != "") ? " onclick=\"".$scr[0]. 1 .$scr[1]."\"" : "")." href=\"". (($url_page != "") ? $url[0]. 1 .$url[1] : "javascript:void(0);") ."\">1</a> ... ";

		for ($j=$start; $j<=$end; $j++){

			if($j != $page)
				$return .= "<a".(($scr_page != "") ? " onclick=\"".$scr[0].$j.$scr[1]."\"" : "")." href=\"". (($url_page != "") ? $url[0].$j.$url[1] : "javascript:void(0);") ."\">{$j}</a> ";
			else
				$return .= "<span>{$j}</span> ";

		}

		if ($page != $page_count)
			$return .= $nav_prefix."<a".(($scr_page != "") ? " onclick=\"".$scr[0].$page_count.$scr[1]."\"" : "")." href=\"". (($url_page != "") ? $url[0].$page_count.$url[1] : "javascript:void(0);") ."\">{$page_count}</a> ";
		else
			$return .= "<span>{$page_count}</span> ";

	}

	return array($prev_page, $return, $next_page);
}

function galMessage($title = "", $text = "", $height = 100, $align = "center"){
galHeader($title);
echo <<<HTML
<table width="100%">
    <tr>
        <td height="{$height}" align="{$align}">{$text}</td>
    </tr>
</table>
HTML;
galFooter();
}

function galHeader($title){
if (is_array($title)){
	$title_right = $title[1];
	$title = $title[0];
}
echo <<<HTML
<div style="padding-top:5px;padding-bottom:2px;">
<table width="100%">
    <tr>
        <td width="4"><img src="engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
        <td background="engine/skins/images/tl_oo.gif"><img src="engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
        <td width="6"><img src="engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
    </tr>
    <tr>
        <td background="engine/skins/images/tl_lb.gif"><img src="engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
        <td style="padding:5px;" bgcolor="#FFFFFF">
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$title}</div></td>
		<td bgcolor="#EFEFEF" height="29" style="padding:5px;" align="right">{$title_right}</td>
    </tr>
</table>
<div class="unterline"></div>
HTML;
}

function galFooter(){
echo <<<HTML
</td>
        <td background="engine/skins/images/tl_rb.gif"><img src="engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
    </tr>
    <tr>
        <td><img src="engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
        <td background="engine/skins/images/tl_ub.gif"><img src="engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
        <td><img src="engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
    </tr>
</table>
</div>
HTML;
}

function galExit($location = false, $echo = false, $ajax = false){
global $db, $config;

	$db->close ();

	if ($location && !$ajax){

		@header("Location: {$location}");
		echo "Please visit <a href=\"/{$location}\">{$location}</a>";
		exit;

	} elseif ($echo !== false){

		@header("Content-type: text/html; charset=".$config['charset']);
		echo $echo;

	}

	GzipOut ();
	exit; // Для логического завершения. Функцию exit есть в GzipOut

}

function galnavigation(){
global $langGal, $config, $PHP_SELF, $member_id, $user_group, $galConfig;

$menu =	array(
array ('name' => $langGal['menu_conf'], 'url' => "{$PHP_SELF}?mod=twsgallery&act=4", 'image' => "config.png", 'access' => "admin", "target" => ""),
array ('name' => $langGal['menu_email'], 'url' => "{$PHP_SELF}?mod=twsgallery&act=41", 'image' => "email.png", 'access' => "admin", "target" => ""),
array ('name' => $langGal['menu_cats'], 'url' => "{$PHP_SELF}?mod=twsgallery&act=1", 'image' => "category.png", 'access' => ($galConfig['admin_user_access'] && $user_group[$member_id['user_group']]['admin_categories']), "target" => ""),
array ('name' => $langGal['menu_cats_cr'], 'url' => "{$PHP_SELF}?mod=twsgallery&act=2", 'image' => "newcategory.png", 'access' => ($galConfig['admin_user_access'] && $user_group[$member_id['user_group']]['admin_categories']), "target" => ""),
array ('name' => $langGal['menu_cats_pr'], 'url' => "{$PHP_SELF}?mod=twsgallery&act=28", 'image' => "profiles.png", 'access' => "admin", "target" => ""),
array ('name' => $langGal['menu_foto'], 'url' => "{$PHP_SELF}?mod=twsgallery&act=10", 'image' => "foto.png", 'access' => ($galConfig['admin_user_access'] && $user_group[$member_id['user_group']]['admin_editnews']), "target" => ""),
array ('name' => $langGal['menu_add'], 'url' => "{$PHP_SELF}?mod=twsgallery&act=12", 'image' => "addfoto.png", 'access' => "1", "target" => ""),
array ('name' => $langGal['menu_tags'], 'url' => "{$PHP_SELF}?mod=twsgallery&act=53", 'image' => "tags.png", 'access' => ($galConfig['admin_user_access'] && $user_group[$member_id['user_group']]['admin_tagscloud']), "target" => ""),
array ('name' => $langGal['menu_ipban'], 'url' => "{$PHP_SELF}?mod=twsgallery&act=36", 'image' => "ban.png", 'access' => ($galConfig['admin_user_access'] && $user_group[$member_id['user_group']]['admin_blockip'] && $user_group[$member_id['user_group']]['admin_iptools']), "target" => ""),
array ('name' => $langGal['menu_main'], 'url' => "{$PHP_SELF}?mod=twsgallery&act=0", 'image' => "stat.png", 'access' => "1", "target" => ""),
array ('name' => $langGal['menu_view'], 'url' => "{$config['http_home_url']}index.php?do=gallery", 'image' => "watch.png", 'access' => "1", "target" => "target=\"_blank\""),
);

galHeader($langGal['menu_nav']);

echo <<<HTML
<div style="padding-top:5px;padding-bottom:2px;">
<table width="100%">
    <tr>
HTML;

foreach ($menu as $item)
	if ($member_id['user_group'] == 1 || $item['access'] && $item['access'] !== "admin")
		echo <<<HTML
		<td align="center"><a class="list" href="{$item['url']}" title="{$item['name']}" {$item['target']}><img src="engine/gallery/acp/skins/images/{$item['image']}" border="0" align="center"></a><br /><span class=small>{$item['name']}</span></td>		
HTML;

echo <<<HTML
    </tr>
</table>
</div>
HTML;

galFooter();

}


function get_gal_groups($id = 0, $first = 0, $all = true, $except = array()){
global $user_group, $langGal;

	if (!$first) $returnstring = "";
	elseif ($first && ($id == "" || (is_numeric($id) && !intval($id)) || (is_array($id) && (count($id) < 1 || $id[0] == "" || in_array("0",$id))))) $returnstring = "<option value=\"\" style=\"color: green;\" SELECTED>$langGal[sys_global]</option>";
	else $returnstring = "<option value=\"\" style=\"color: green;\">$langGal[sys_global]</option>";

	if ($all) $returnstring .= "<option value=\"-1\" style=\"color:blue;\"".(($id === -1 || (isset($id[0]) && $id[0] == -1)) ? " SELECTED" : "").">$langGal[edit_all]</option>";

	foreach ($user_group as $group){

		if (in_array($group['id'], $except)) continue;

		$returnstring .= '<option value="'.$group['id'].'" ';
		if (is_array ($id) && in_array($group['id'], $id) || $id == $group['id']) $returnstring .= 'SELECTED';
		$returnstring .= ">".$group['group_name']."</option>\n";

	}

return $returnstring;
}


function save_group_info($level){
global $db;

	if (!is_array($level)){
		$level = intval($level);
		return (!$level) ? "" : $level;
	}

	$uviewlevel = array();

	foreach ($level as $lev)
		$uviewlevel[] = intval($lev);

	if (in_array("0", $uviewlevel) || count($uviewlevel) < 1) return "";
	if (in_array("-1", $uviewlevel)) return "-1";

	return implode(',', $uviewlevel);
}

function initTabs1($array, $id = "dle_tabView1"){

	$array = implode("','", $array);

	return <<<HTML
<script type="text/javascript">
<!--
$(function(){
	initTabs('{$id}',Array('{$array}'),0, '100%');
});
-->
</script>
HTML;

}

function echopopupheader($title){
global $config, $lang;

echo <<<HTML
<html>
<head>
<meta content="text/html; charset={$config['charset']}" http-equiv="content-type" />
<title>{$title}</title>

<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="engine/skins/calendar-blue.css" title="win2k-cold-1" />

<!-- main calendar program -->
<link rel="stylesheet" type="text/css" href="engine/skins/default.css">
<link rel="stylesheet" type="text/css" href="engine/skins/jquery-ui.css">
<script type="text/javascript" src="engine/classes/js/jquery.js"></script>
<script type="text/javascript" src="engine/classes/js/jqueryui.js"></script>
<script type="text/javascript" src="engine/skins/calendar.js"></script>
<script type="text/javascript" src="engine/skins/default.js"></script>
<script type="text/javascript" src="engine/skins/tabs.js"></script>
<script type="text/javascript" src="engine/classes/js/bbcodes.js"></script>
<script type="text/javascript" src="engine/skins/autocomplete.js"></script>
<script type="text/javascript" src="engine/gallery/js/not_logged.js"></script>
</head>
<body>
HTML;

}

function echopopupedituser(){
global $config, $lang, $PHP_SELF, $dle_login_hash;

	echo <<<HTML
<script language="javascript" type="text/javascript">
<!--
function popupedit( id, user ){
HTML;

	if (version_compare($config['version_id'], "9.5", "<")){

		echo <<<HTML
window.open('?mod=editusers&action=edituser&id='+id+'&user='+user,'User','toolbar=0,location=0,status=0, left=0, top=0, menubar=0,scrollbars=yes,resizable=0,width=540,height=500');
HTML;

	}else{

		echo <<<HTML
		var rndval = new Date().getTime();
		var req_user = id ? "id="+id : "user="+user;

		$('body').append('<div id="modal-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #666666; opacity: .40;filter:Alpha(Opacity=40); z-index: 999; display:none;"></div>');
		$('#modal-overlay').css({'filter' : 'alpha(opacity=40)'}).fadeIn('slow');

		$("#dlepopup").remove();
		$("body").append("<div id='dlepopup' title='{$lang['user_edhead']}' style='display:none'></div>");

		$('#dlepopup').dialog({
			autoOpen: true,
			width: 560,
			height: 500,
			dialogClass: "modalfixed",
			buttons: {
				"{$lang['user_can']}": function() { 
					$(this).dialog("close");
					$("#dlepopup").remove();							
				},
				"{$lang['user_save']}": function() { 
					document.getElementById('edituserframe').contentWindow.document.getElementById('saveuserform').submit();							
				}
			},
			open: function(event, ui) { 
				$("#dlepopup").html("<iframe name='edituserframe' id='edituserframe' width='100%' height='400' src='{$PHP_SELF}?mod=editusers&action=edituser&" + req_user + "&rndval=" + rndval + "' frameborder='0' marginwidth='0' marginheight='0' allowtransparency='true'></iframe>");
			},
			beforeClose: function(event, ui) { 
				$("#dlepopup").html("");
			},
			close: function(event, ui) {
					$('#modal-overlay').fadeOut('slow', function() {
			        $('#modal-overlay').remove();
			    });
			 }
		});

		if ($(window).width() > 830 && $(window).height() > 530 ) {
			$('.modalfixed.ui-dialog').css({position:"fixed"});
			$('#dlepopup').dialog( "option", "position", ['0','0'] );
		}

		return false;
HTML;

}

	echo <<<HTML
}
//-->
</script>
HTML;

}


?>