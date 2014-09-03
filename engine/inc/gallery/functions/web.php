<?PHP
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
 This file may no be redistributed in whole or significant part.	
 Файл не может быть изменён или использован без прямого согласия автора
 Запрещается использование файла в любых комменрческих целях
=====================================================
 Файл: web.php
-----------------------------------------------------
 Назначение: Основные функции
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

if (!defined('TWSGAL_DIR')){

	global $galConfig, $db, $langGal, $config;

	define('FOTO_DIR', ROOT_DIR.'/uploads/gallery');
	define('FOTO_URL', $config['http_home_url'].'uploads/gallery');
	define('TWSGAL_DIR', ENGINE_DIR.'/gallery');
	//define('DEBUG_MODE', true);

	define ('TIME', (time()  + ($config['date_adjust'] * 60)));
	define ('DATETIME', date ("Y-m-d H:i:s", TIME));

	$galConfig = get_vars ("gallery_config");

	if (!is_array($galConfig)){// || defined('DEBUG_MODE')){

		$db->query("SELECT * FROM " . PREFIX . "_gallery_config WHERE type=0", false);

		$galConfig = array();

		while($row = $db->get_row())
			$galConfig[$row['name']] = stripslashes($row['value']);

		$db->free();

		if (!$galConfig['foto_td']) $galConfig['foto_td'] = 4;
		if (!$galConfig['foto_tr']) $galConfig['foto_tr'] = 8;

		$galConfig['extensions'] = unserialize($galConfig['extensions']);

		if (!$galConfig['version_gallery'] or version_compare($galConfig['version_gallery'], "5.2", "<")){
			$jump = 'install';
			include TWSGAL_DIR.'/modules/redirect.php';
		}

		$galConfig['PHP_SELF'] = $config['http_home_url'] . "index.php?do=gallery";

		if ($config['allow_alt_url'] == "yes")
			$galConfig['mainhref'] = $config['http_home_url'].$galConfig['work_postfix'];
		else 
			$galConfig['mainhref'] = $galConfig['PHP_SELF'];

		set_vars ("gallery_config", $galConfig);

	}

	if (version_compare($config['version_id'], "9.5", "<")) $mcache = false;

	if (isset($_REQUEST['jump'])){

		$jump = $_REQUEST['jump'];
		include TWSGAL_DIR.'/modules/redirect.php';

	}

	if (isset($config["lang_".$config['skin']])){
		include_once ROOT_DIR.'/language/'.$config["lang_".$config['skin']].'/gallery.web.lng';
	} else {
		include_once ROOT_DIR.'/language/'.$config['langs'].'/gallery.web.lng';
	}

	$this_album = $this_foto = $_album_id = false;

	if (isset ($_REQUEST['do']) && $_REQUEST['do'] == "gallery" && in_array($_REQUEST['act'], array(1,2))){

		$_foto_id = (isset($_GET['fid'])) ? explode(",", $_GET['fid']) : 0;
		$_album = @$db->safesql(strip_tags($_GET['c']));

		if ($_album != '' && substr($_album, -1, 1) == '/') $_album = substr($_album, 0, -1);

		$_album_id = (isset($_GET['cid'])) ? intval($_GET['cid']) : 0;

		if (intval($_foto_id[0]))
			$this_foto = $db->super_query("SELECT * FROM " . PREFIX . "_gallery_picturies WHERE picture_id='".intval($_foto_id[0])."'");

		if ($config['seo_control'] && $this_foto && strpos($_SERVER['REQUEST_URI'], "subscribe=update") !== false) $config['seo_control'] = 0;
		elseif (!isset($config['seo_control'])) $config['seo_control'] = 1;

		if ($this_foto){

			if (!$config['seo_control'] || count($_foto_id) > 1) $_album_id = $this_foto['category_id'];
			elseif (!$_album_id && $_album == ''){

				$_album_id = $this_foto['category_id'];
				$_GET['seourl'] = '__UNDEFINED__';

			}

		}

		if ($_album_id || $_album != ''){

			$this_album = $db->super_query("SELECT * FROM " . PREFIX . "_gallery_category WHERE ".($_album_id ? "id='{$_album_id}'" : "cat_alt_name='{$_album}'"));

			if ($config['seo_control'] && $this_foto && count($_foto_id) < 2 && ($this_foto['category_id'] != $this_album['id'] || isset($_GET['seourl']) && $this_foto['picture_alt_name'] != $_GET['seourl'] || $config['allow_alt_url'] == "yes" && strpos($_SERVER['REQUEST_URI'], "?") !== false)){
				$jump = 'seo';
				include TWSGAL_DIR.'/modules/redirect.php';
			} elseif ($config['seo_control'] && !$this_album && !$this_foto && strpos($_album, "/") !== false){
				$jump = 'category';
				include TWSGAL_DIR.'/modules/redirect.php';
			}

			$_album_id = $this_album['id'];
			if ($this_album['skin'] != "") $category_skin = $this_album['skin'];
			elseif ($galConfig['skin_name'] != "") $category_skin = $galConfig['skin_name'];

		}

	}

}

function check_gallery_access ($check, $variable = '', $moderators = '', $var1 = '', $var2 = '', $var3 = '', $var4 = '') 
{ global $member_id, $galConfig, $is_logged;

	if ($member_id['user_group'] == 1 && $is_logged) return 2;

	$is_moder = false;

	if ($is_logged && $moderators){
		$mods = explode(',',$moderators);
		if (in_array($member_id['user_id'],$mods)) $is_moder = true;
	}

switch ($check){

	case "read" :

			if ($var1) return 0;
			if (!$variable) $variable = $galConfig['viewlevel'];
			if (!$variable) return 0;
			if ($variable == -1) return 1;

			$c_var = explode(',',$variable);

			if(in_array($member_id['user_group'], $c_var)) return 2;
			return 0;
			break;

	case "edit" :

			if ($is_moder) return 1;
			if (!$variable) $variable = $galConfig['editlevel'];
			if (!$is_logged || !$variable || $member_id['user_group'] == 4) return 0;

			$c_var = explode(',',$variable);

			if(in_array($member_id['user_group'], $c_var)) return 2;
			return 0;
			break;

	case "rate" :

			if (!$galConfig['allow_rating']) return 0;
			$variable = $galConfig['ratelevel'];
			if (!$variable) return 0;
			if ($variable == -1) return 1;

			$c_var = explode(',',$variable);

			if(in_array($member_id['user_group'], $c_var)) return 1;
			return 0;
			break;

	case "comms" :

			if (!$galConfig['allow_comments']) return 0;
			if (isset($member_id['restricted']) and in_array($member_id['restricted'], array(2,3))) return 0;
			if (!$variable) $variable = $galConfig['comlevel'];
			if (!$variable) return 0;
			if ($variable == -1) return 1;

			$c_var = explode(',',$variable);

			if(in_array($member_id['user_group'], $c_var)) return 1;
			return 0;
			break;

	case "addcat" :

			if (!$is_logged) return 0;
			$variable = $galConfig['addlevel'];
			if (!$variable) return 0;
			if ($variable == -1) return 1;

			$c_var = explode(',',$variable);

			if(in_array($member_id['user_group'], $c_var)) return 1;
			return 0;
			break;

	case "upload" : // 1 || 2 : 4

			if ($var2) return 0;
			if ($galConfig['allow_user_admin'] && $is_logged && $var4 && $member_id['name'] == $var3) return 2;
			if (!$variable){
				$variable = $galConfig['uploadlevel'];
				if (!$var1) $var1 = $galConfig['modlevel'];
			}
			if (!$variable) return 0;
			if ($is_moder) return 2;

			$c_var = explode(',',$variable);

			if ($variable == -1 || in_array($member_id['user_group'], $c_var)){ // if can upload
				if (!$var1 || $member_id['user_group'] == 2) return 2; // without moderation
				$c_var = explode(',',$var1);
				if($var1 != -1 && !in_array($member_id['user_group'], $c_var)) return 2; /// without moderation
				return 1;
			}
			return 0;	
			break;
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
	case 56 : $ext = 'gametrailers.com'; break;
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
		case ($priview)	: $player .= "true&previewImageUrl=".htmlspecialchars(stripslashes($priview), ENT_QUOTES, $config['charset']); break;
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

function create_gallery_vars ($file, $data, $cache_id = false){
global $mcache;

	$cache_id = $cache_id ? (totranslit($cache_id, true, false) . "_" . $file) : $file;

	if ($mcache)
		memcache_set($mcache, DBNAME . PREFIX . md5(DBUSER . FOTO_URL) . $cache_id . 'sys', implode('_', $data), MEMCACHE_COMPRESSED, 86400 );
	else {

		file_put_contents (TWSGAL_DIR.'/cache/system/'.$cache_id.'.php', implode('_', $data), LOCK_EX);
		@chmod(TWSGAL_DIR.'/cache/system/'.$cache_id.'.php', 0666);

	}

}

function get_gallery_vars ($file, $cache_id = false){
global $mcache;

	$cache_id = $cache_id ? (totranslit($cache_id, true, false) . "_" . $file) : $file;

	if ($mcache)
		$result = memcache_get($mcache, DBNAME . PREFIX . md5(DBUSER . FOTO_URL) . $cache_id . 'sys');
	else
		$result = @file_get_contents(TWSGAL_DIR.'/cache/system/'.$cache_id.'.php');

	return ($result) ? explode('_', $result) : false;
}

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


function get_gallery_cache($prefix, $cache_id=false, $member_prefix=false)
{ global $is_logged, $member_id, $galConfig, $mcache;

	if (!$galConfig['allow_cache']) return false;

	$cache_id = $prefix.($cache_id ? ("_".totranslit ($cache_id, false, false).($member_prefix ? "_".(($is_logged) ? $member_id['user_group'] : "0") : "")) : "");

	if ($mcache)
		return memcache_get($mcache, DBNAME . PREFIX . md5(DBUSER . FOTO_URL) . $cache_id);
	else
		return @file_get_contents(TWSGAL_DIR."/cache/".$cache_id . ".tmp");
}


function create_gallery_cache($prefix, $cache_text, $cache_id=false, $member_prefix=false)
{ global $is_logged, $member_id, $galConfig, $mcache;

	if (!$galConfig['allow_cache']) return false;

	$cache_id = $prefix.($cache_id ? ("_".totranslit ($cache_id, false, false).($member_prefix ? "_".(($is_logged) ? $member_id['user_group'] : "0") : "")) : "");

	if ($mcache)
		memcache_set($mcache, DBNAME . PREFIX . md5(DBUSER . FOTO_URL) . $cache_id, $cache_text, MEMCACHE_COMPRESSED, 86400 );
	else {

		file_put_contents (TWSGAL_DIR . "/cache/" . $cache_id . ".tmp", $cache_text, LOCK_EX);
		@chmod(TWSGAL_DIR . "/cache/" . $cache_id . ".tmp", 0666);

	}

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


function ShowGalRating ($id, $rating, $vote_num, $allow = true){
global $lang;

	if ($rating AND $vote_num) $rating = round(($rating /  $vote_num), 0)*17; else $rating = 0;

	if (!$allow){

		$rated = <<<HTML
<div class="rating">
		<ul class="unit-rating">
		<li class="current-rating" style="width:{$rating}px;">{$rating}</li>
		</ul>
</div>
HTML;

		return $rated;
	}

	$rated = "<div id='ratig-layer-" . $id . "'>";
	
	$rated .= <<<HTML
<div class="rating">
		<ul class="unit-rating">
		<li class="current-rating" style="width:{$rating}px;">{$rating}</li>
		<li><a href="#" title="{$lang['useless']}" class="r1-unit" onclick="GalRate('1', '{$id}'); return false;">1</a></li>
		<li><a href="#" title="{$lang['poor']}" class="r2-unit" onclick="GalRate('2', '{$id}'); return false;">2</a></li>
		<li><a href="#" title="{$lang['fair']}" class="r3-unit" onclick="GalRate('3', '{$id}'); return false;">3</a></li>
		<li><a href="#" title="{$lang['good']}" class="r4-unit" onclick="GalRate('4', '{$id}'); return false;">4</a></li>
		<li><a href="#" title="{$lang['excellent']}" class="r5-unit" onclick="GalRate('5', '{$id}'); return false;">5</a></li>
		</ul>
</div>
HTML;
	
	$rated .= "</div>";
	
	return $rated;
}

function LoadShortScriprt(){

if (defined('TWS_SCRIPT_LOADED')) return;

define('TWS_SCRIPT_LOADED', true);

global $langGal, $js_array, $is_logged, $member_id, $user_group, $ajax, $galConfig, $js_options, $this_url, $config;

$js_options['mode'] = intval($js_options['mode']);

$version_id = str_replace(".", "", $config['version_id']);

$ajax .= <<<HTML
<script type="text/javascript">
<!--
var gallery_web_root  = '{$galConfig['work_postfix']}';
var gallery_dle_id  = {$version_id};
var gallery_image_url  = 
HTML;
$ajax .= "'".FOTO_URL."';\n";
$ajax .= <<<HTML
var gallery_alt_url = '{$config['allow_alt_url']}';
var gallery_admin_editusers  = '{$user_group[$member_id['user_group']]['admin_editusers']}';
var gallery_mode = {$js_options['mode']};
var gallery_lang_web = {0:'{$langGal['js_menu_allcoms']}',1:'{$langGal['js_menu_allfoto']}',2:'{$langGal['js_menu_allcats']}',3:'{$langGal['js_subscribe_title']}',4:'{$langGal['js_subscribe_text']}'};\n
HTML;
if ($is_logged || $js_options['mode'] == 2) $ajax .= <<<HTML
var gallery_lang_user = {0:'{$langGal['js_menu_delete']}',1:'{$langGal['js_edit_foto_er1']}',2:'{$langGal['js_confirm4']}'};\n
HTML;
if ($js_options['admin']) $ajax .= <<<HTML
var gallery_lang_admin = {0:'{$langGal['js_menu_recount']}',1:'{$langGal['js_menu_open']}',2:'{$langGal['js_menu_approve']}',3:'{$langGal['js_menu_move']}',4:'{$langGal['js_menu_message']}',5:'{$langGal['js_menu_comments']}',6:'{$langGal['js_menu_rating']}',7:'{$langGal['js_menu_disupload']}',8:'{$langGal['js_p_text_d']}',9:'{$langGal['js_confirm1']}',10:'{$langGal['js_p_text']}',11:'{$langGal['js_p_send']}'};\n
HTML;

$ajax .= "//-->
</script>\n";

$ajax .= <<<HTML
<form name="gallery_set_sort" id="gallery_set_sort" method="post" action="{$this_url}"><input type="hidden" name="foto_sort" id="foto_sort" value="" /><input type="hidden" name="foto_msort" id="foto_msort" value="" /></form>\n
HTML;

$js_array[] = "engine/gallery/js/not_logged.js";
if ($is_logged || $js_options['mode'] == 2) $js_array[] = "engine/gallery/js/is_logged.js";

}

function fastpages($count_all, $limit, $page, $url_page, $url_first_page=false, $scr_page="", $fullline = 10, $sector = 5, $usetpl = "navigation.tpl"){
global $tpl;

	$page_count = @ceil($count_all/$limit);	$return = "";

	if ($url_page != "") $url = explode("{INS}", $url_page);
	if ($scr_page != "") $scr = explode("{INS}", $scr_page);

	$prev_page = "";
	$next_page = "";

    if($page == 2 && $url_first_page)
		$prev_page = "<a".(($scr_page != "") ? " onclick=\"".$scr[0].($page-1).$scr[1]."\"" : "")." href=\"{$url_first_page}\">\\1</a>";
	elseif ($page != 1)
		$prev_page = "<a".(($scr_page != "") ? " onclick=\"".$scr[0].($page-1).$scr[1]."\"" : "")." href=\"". (($url_page != "") ? $url[0].($page-1).$url[1] : "javascript:void(0);") ."\">\\1</a>";
 
    if ($page < $page_count) $next_page = "<a".(($scr_page != "") ? " onclick=\"".$scr[0].($page+1).$scr[1]."\"" : "")." href=\"". (($url_page != "") ? $url[0].($page+1).$url[1] : "javascript:void(0);") ."\">\\1</a>";

	if ($page_count <= $fullline){

		for($j=1; $j<=$page_count; $j++){

			if ($j != $page && $j == 1 && $url_first_page)
				$return .= "<a".(($scr_page != "") ? " onclick=\"".$scr[0].$j.$scr[1]."\"" : "")." href=\"{$url_first_page}\">{$j}</a> ";
			elseif ($j != $page)
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

		if ($start >= 2 && $url_first_page)
			$return .= "<a".(($scr_page != "") ? " onclick=\"".$scr[0]. 1 .$scr[1]."\"" : "")." href=\"{$url_first_page}\">1</a> ... ";
		elseif ($start >= 2)
			$return .= "<a".(($scr_page != "") ? " onclick=\"".$scr[0]. 1 .$scr[1]."\"" : "")." href=\"". (($url_page != "") ? $url[0]. 1 .$url[1] : "javascript:void(0);") ."\">1</a> ... ";

		for ($j=$start; $j<=$end; $j++){

			if ($j != $page && $j == 1 && $url_first_page)
				$return .= "<a".(($scr_page != "") ? " onclick=\"".$scr[0].$j.$scr[1]."\"" : "")." href=\"{$url_first_page}\">{$j}</a> ";
			elseif($j != $page)
				$return .= "<a".(($scr_page != "") ? " onclick=\"".$scr[0].$j.$scr[1]."\"" : "")." href=\"". (($url_page != "") ? $url[0].$j.$url[1] : "javascript:void(0);") ."\">{$j}</a> ";
			else
				$return .= "<span>{$j}</span> ";

		}

		if ($page != $page_count)
			$return .= $nav_prefix."<a".(($scr_page != "") ? " onclick=\"".$scr[0].$page_count.$scr[1]."\"" : "")." href=\"". (($url_page != "") ? $url[0].$page_count.$url[1] : "javascript:void(0);") ."\">{$page_count}</a> ";
		else
			$return .= "<span>{$page_count}</span> ";

	}

	if (!$usetpl) return array($prev_page, $return, $next_page);

	$tpl->load_template($usetpl);

	if ($prev_page)
		$tpl->set_block("'\[prev-link\](.*?)\[/prev-link\]'si", $prev_page);
	else
		$tpl->set_block("'\[prev-link\](.*?)\[/prev-link\]'si", "<span>\\1</span>");

	$tpl->set('{pages}', $return);

	if ($next_page)
		$tpl->set_block("'\[next-link\](.*?)\[/next-link\]'si", $next_page);
	else
		$tpl->set_block("'\[next-link\](.*?)\[/next-link\]'si", "<span>\\1</span>");

	$tpl->compile('fastnav');
	$tpl->clear();

}

function gallery_symbols(){

if (($symbols = get_gallery_cache ('symbols')) !== false) return $symbols;

global $db, $config, $galConfig; 

$symbols = array();

$db->query("SELECT symbol FROM " . PREFIX . "_gallery_picturies WHERE approve=1 AND symbol<>'' GROUP BY symbol");

while($row = $db->get_row()){

	$row['symbol'] = stripslashes($row['symbol']);

	if ($config['allow_alt_url'] == "yes")
		$symbols[$row['symbol']] = "<a href=\"{$galConfig['mainhref']}all/symbol-".urlencode($row['symbol'])."/\">".$row['symbol']."</a>";
	else
		$symbols[$row['symbol']] = "<a href=\"{$galConfig['mainhref']}&act=15&p=symbol-".urlencode($row['symbol'])."\">".$row['symbol']."</a>";

}

$db->free();

uksort($symbols, "strnatcmp");
$symbols = implode(" &nbsp; ", $symbols);
create_gallery_cache ('symbols', $symbols);

return $symbols;

}

function foto_sort($this_album = false, $allow_user_sort = true){
global $galConfig;

	$fsort = array('sort' => 'date', 'msort' => 'desc');

	if (isset($_REQUEST['foto_sort']) AND $allow_user_sort AND in_array($_REQUEST['foto_sort'], array("posi","date","rating","file_views","comments","picture_title"))){
		$_SESSION['foto_sort'] = $fsort['sort'] = strtolower($_REQUEST['foto_sort']);
		$fsort['sort_set'] = true;
		define('USER_SET_SORT', true);
	} elseif ($this_album && $this_album['foto_sort'])
		$fsort['sort'] = $this_album['foto_sort'];
	elseif ($galConfig['foto_sort'] != "")
		$fsort['sort'] = $galConfig['foto_sort'];

	if (isset($_SESSION['foto_sort']) AND in_array($_SESSION['foto_sort'], array("posi","date","rating","file_views","comments","picture_title")) && $_SESSION['foto_sort'] != $fsort['sort']){
		$fsort['sort'] = strtolower($_SESSION['foto_sort']);
		$fsort['sort_set'] = true;
	}

	if (isset($_REQUEST['foto_msort']) AND $allow_user_sort AND in_array($_REQUEST['foto_msort'], array("asc","desc"))){
		$_SESSION['foto_msort'] = $fsort['msort'] = strtolower($_REQUEST['foto_msort']);
		$fsort['sort_set'] = true;
		define('USER_SET_SORT', true);
	} elseif ($this_album && $this_album['foto_msort'])
		$fsort['msort'] = $this_album['foto_msort'];
	elseif ($galConfig['foto_msort'] != "")
		$fsort['msort'] = $galConfig['foto_msort'];

	if (isset($_SESSION['foto_msort']) AND in_array($_SESSION['foto_msort'], array("asc","desc")) && $_SESSION['foto_msort'] != $fsort['msort']){
		$fsort['msort'] = strtolower($_SESSION['foto_msort']);
		$fsort['sort_set'] = true;
	}

	return $fsort;
}

function galery_foto_tags ($action, $categories, $show_sub, $template, $aviable, $start, $do, $tr, $td, $cache, $critical_limit, $member_name = "", $media_type = -1){
global $db, $galConfig, $config, $is_logged, $dle_login_hash, $langGal, $lang, $member_id, $smartphone_detected;

	if ($aviable){

		$do = $do ? $do : "main";
		$aviable = explode ('|', $aviable);

		if(!in_array($do, $aviable) and ($aviable[0] != "global")) return "";

	}

	$categories = $db->safesql($categories);
	$template = totranslit($template, true, false);
	$action = totranslit($action, true, false);
	if ($td) $galConfig['foto_td'] = intval($td);
	if ($tr) $galConfig['foto_tr'] = intval($tr);
	$show_sub = intval($show_sub);
	$start = intval($start);
	$content = false;

	$cache_id = md5($config['skin'] . $categories . $member_name . $show_sub . $template . $galConfig['foto_td'] . $galConfig['foto_tr'] . $critical_limit . $start . $media_type . ($is_logged ? $member_id['user_group'] : 0));

	$cat_picturies = false;
	$sql_where = array();

	if ($action != 'random' && $cache == "yes" && ("" != ($content = get_gallery_cache("tag_".$action, $cache_id)))) return $content;
	elseif ($action != 'random' || !($cat_picturies = get_gallery_vars ("tag_random", $cache_id))){

		if ($categories)
			$sql_where[] = ($show_sub == 1) ? "(c.id IN ({$categories}) OR c.p_id IN ({$categories}))" : "c.id IN ({$categories})";

		$sql_where[] = "(c.view_level".(check_gallery_access ("read", "", "") ? " IN ('-1','')" : "='-1'")." OR c.view_level regexp '[[:<:]]({$member_id['user_group']})[[:>:]]')";
		if (!check_gallery_access ("edit", "", "")) $sql_where[] = "c.locked=0";

	}

	if ($media_type != -1){
		$media_type = explode(",", $media_type);
		$sql_where[] = count($media_type) == 1 ? "p.media_type='".intval($media_type[0])."'" : "p.media_type IN (".$db->safesql(implode(",",$media_type)).")";
	}
	if ($member_name && trim($member_name) != '') $sql_where[] = "p.picture_user_name='".$db->safesql($member_name)."'";

	$count_foto = $galConfig['foto_td'] * $galConfig['foto_tr'];

	switch ($action){
	case 'random' :

		if (!$cat_picturies){

			$cat_picturies = array();

			$critical_limit = intval($critical_limit);
			if ($critical_limit > 5000) $critical_limit = 5000;
			elseif ($critical_limit < 1) $critical_limit = 100;

			$db->query("SELECT p.picture_id FROM " . PREFIX . "_gallery_picturies p INNER JOIN " . PREFIX . "_gallery_category c ON c.id=p.category_id WHERE p.approve='1'".(count($sql_where) ? " AND ".implode(" AND ", $sql_where) : "")." ORDER BY picture_id DESC LIMIT {$critical_limit}");
			while($row = $db->get_row())
				$cat_picturies[] = $row['picture_id'];
			$db->free();

			create_gallery_vars ("tag_random", $cat_picturies, $cache_id);

		}

		if (!($found_ids = count($cat_picturies))) return "";

		if ($found_ids < $count_foto) $count_foto = $found_ids;

		$access_array = array();

		if ($found_ids == 1)
			$access_array[] = $cat_picturies[0];
		elseif ($count_foto > 1){

			$array_rands = array_rand($cat_picturies, $count_foto);

			for ($i = 0; $i < $count_foto; $i++){
				$key = $array_rands[$i];
				$access_array[] = $cat_picturies[$key];
			}

		} else $access_array[] = $cat_picturies[array_rand($cat_picturies)];

		$sql_where = array("picture_id IN ('".implode ("','", $access_array)."')");

		unset($cat_picturies, $array_rands, $access_array);

		$fsort = array("sort" => "date", "msort" => "asc");
		$start = 0;

	break;
	default :

		if (!in_array($action, array('date','comments','rating','file_views','downloaded'))) return "Invalid gallery tag action! Check input parametrs!";

		$fsort = array("sort" => $action, "msort" => "desc");
		$sql_where[] = "approve=1";
	
	}

	$_admin = -1;
	$compile = '';
	$search_parametrs = '';

	$fstart = 1;
	$_album_id = 0;
	$main_sql_where = (count($sql_where) ? implode(" AND ", $sql_where) : "1");
	$main_sql_limit = " LIMIT {$start},{$count_foto}";

	$tpl = new dle_template;
	$tpl->dir = TEMPLATE_DIR;

	include ENGINE_DIR.'/gallery/modules/show.foto.php';

	$tpl->result['fotolistrow'] = preg_replace("'<input name=\'si\[\]\' value=\'(.*?)\' type=\'checkbox\'>'", "", $tpl->result['fotolistrow']);

	if ($action != 'random' && $cache == "yes") create_gallery_cache("tag_".$action, $tpl->result['fotolistrow'], $cache_id);

return $tpl->result['fotolistrow'];
}

function gallery_authors($categories, $show_sub, $marker, $aviable, $do, $cache, $limit){
global $db, $config, $langGal, $member_id;

	if ($aviable){

		$do = $do ? $do : "main";
		$aviable = explode ('|', $aviable);

		if(!in_array($do, $aviable) and ($aviable[0] != "global")) return "";

	}

	$categories = $db->safesql($categories);
	$show_sub = intval($show_sub);
	$limit = intval($limit);
	$marker = stripslashes($marker);
	$autors = "";

	$cache_id = md5($categories . $show_sub . $limit . totranslit($marker, false, false));

	if ($cache == "yes")
		$autors = get_gallery_cache("authors", $cache_id);

	if ($autors) return $autors;

	$sql_where = array();
	$sql_where[] = "p.approve=1";
	$sql_where[] = "p.user_id<>0";

	if ($categories)
		$sql_where[] = ($show_sub == 1) ? "(c.id IN ({$categories}) OR c.p_id IN ({$categories}))" : "c.id IN ({$categories})";

	$sql_where[] = "(c.view_level".(check_gallery_access ("read", "", "") ? " IN ('-1','')" : "='-1'")." OR c.view_level regexp '[[:<:]]({$member_id['user_group']})[[:>:]]')";
	if (!check_gallery_access ("edit", "", "")) $sql_where[] = "c.locked=0";

	$db->query("SELECT COUNT(p.picture_id) as count, p.picture_user_name FROM " . PREFIX . "_gallery_picturies p INNER JOIN " . PREFIX . "_gallery_category c ON c.id=p.category_id WHERE ". implode(" AND ", $sql_where)." GROUP BY p.user_id ORDER BY count DESC LIMIT 0,{$limit}");

	while($row = $db->get_row()){

		$row['picture_user_name'] = stripslashes($row['picture_user_name']);
		$encoded = urlencode($row['picture_user_name']);

		if ($config['allow_alt_url'] == "yes")
			$url_user =  $config['http_home_url']."user/".$encoded."/";
		else
			$url_user =  $config['http_home_url']."index.php?subaction=userinfo&amp;user=".$encoded;

		$menu = " onclick=\"ShowProfile('" . $encoded . "', '" . htmlspecialchars( $url_user, ENT_QUOTES, $config['charset'] ) . "', gallery_admin_editusers); return false;\"";
		//$menu = " onclick=\"return dropdownmenu(this, event, GalUserMenu('".htmlspecialchars($url_user, ENT_QUOTES, $config['charset'])."', '".$encoded."'), '220px')\" onMouseout=\"delayhidemenu()\"";
		$autors .= "<a{$menu} href=\"".$url_user."\">".$row['picture_user_name']." (".$row['count'].")</a>";
		$autors .= $marker;

	}

	$db->free();

	if ($autors != "") $autors = dle_substr($autors, 0, (strlen($marker) * -1), $config['charset']);

	if ($cache == "yes")
		create_gallery_cache("authors", $autors, $cache_id);

  return $autors;
}

?>