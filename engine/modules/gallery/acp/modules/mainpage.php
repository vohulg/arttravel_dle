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
 This file may no be redistributed in whole or significant part.	
 Файл не может быть изменён или использован без прямого согласия автора
 Запрещается использование файла в любых комменрческих целях
=====================================================
 Файл: mainpage.php
-----------------------------------------------------
 Назначение: Главная страница панели управления галереей
=====================================================
*/

if(!defined('DATALIFEENGINE') || !defined('TIME') OR !defined( 'LOGGED_IN' ))
{
  die("Hacking attempt!");
}

if ($member_id['user_group'] == 1)
switch ($act){
case 26 :

	clear_gallery_vars();
	clear_gallery_cache();

	$db->query("UPDATE " . PREFIX . "_gallery_config SET value='-1' WHERE name IN ('statistic_cat','statistic_cat_week','statistic_com','statistic_com_day','statistic_file_day','statistic_file','statistic_file_onmod','statistic_downloads')");

	@unlink(ENGINE_DIR.'/cache/system/gallery_config.php');
	@unlink(ENGINE_DIR.'/cache/system/gallery_banned.php');
	@unlink(ENGINE_DIR.'/cache/system/gallery_profiles.php');
	@unlink(ENGINE_DIR.'/cache/system/gallery_category.php'); // от старых версий на всякий случай

	$tags = array();

	$db->query("SELECT t.id FROM " . PREFIX . "_gallery_tags t LEFT JOIN " . PREFIX . "_gallery_tags_match m ON t.id=m.tag_id WHERE m.tag_id IS NULL");

	while($row = $db->get_row())
		$tags[] = $row['id'];

	$db->free();

	if (count($tags))
		$db->query("DELETE FROM " . PREFIX . "_gallery_tags WHERE id IN (".implode(",",$tags).")");

	$db->query("TRUNCATE TABLE " . PREFIX . "_gallery_search_text");
	$db->query("UPDATE " . PREFIX . "_gallery_search SET actual=0 WHERE actual != 0");

	galExit(false, "<font color=\"green\">".$langGal['stat_clearcache']."</font>");

break;
case 27 :

	$handle = FOTO_DIR .'/temp/test_dirrectory';
	$test_image = FOTO_DIR .'/boxsmall.jpg';

	if (@file_exists($test_image)){

		mkdir($handle, 0777);
		chmod($handle, 0777);
		copy($test_image, $handle.'/boxsmall.jpg');
		chmod($handle.'/boxsmall.jpg', 0666);

		$safemode = (@ini_get('safe_mode') == 1 || @strtolower(@ini_get('safe_mode')) == 'on') ? 0 : 1;

		if (@file_exists($handle.'/boxsmall.jpg') && $safemode)
			$buffer = "<font color=\"green\">".$langGal['stat_test_ok']."</font>";
		else
			$buffer = "<font color=\"red\">".$langGal['stat_test_not_ok']."</font>";

		@unlink($handle.'/boxsmall.jpg');
		@rmdir($handle);

	} else $buffer = "<font color=\"red\">".$langGal['stat_test_not_found']."</font>";

	galExit(false, $buffer);

break;
case 43 :

	$buffer = @file_get_contents("http://inker.wonderfullife.ru/extras/updates.php?script=twsg&dle=".$config['version_id']."&version=".$galConfig['version_gallery']."&host=".$_SERVER['HTTP_HOST']);

	if (!$buffer || !strlen($buffer)) $buffer = $lang['no_update'];
	elseif (strtolower($config['charset']) == "utf-8") $buffer = iconv("windows-1251", "utf-8", $buffer);

	galExit(false, "<font color=\"green\">".$buffer."</font>");

break;
case 57 :

	$db->query("TRUNCATE TABLE " . PREFIX . "_gallery_comments_subscribe");
	$db->query("UPDATE " . USERPREFIX . "_users SET gallery_cs_flag=0");

	galExit(false, "<font color=\"green\">".$langGal['clear_subscribe']."</font>");

break;
}

echoheader("", $langGal['menu_main']);
galnavigation();
$twsg->check_unmoderate();
galHeader($langGal['stats_title']);

function galdirsize($directory) {

	if( ! is_dir( $directory ) ) return - 1;

	$size = 0;

	if( $DIR = opendir( $directory ) ) {
		while ( ($dirfile = readdir( $DIR )) !== false ) {
			if( @is_link( $directory . '/' . $dirfile ) || $dirfile == '.' || $dirfile == '..' ) continue;
			if( @is_file( $directory . '/' . $dirfile ) ) $size += filesize( $directory . '/' . $dirfile );
			elseif( @is_dir( $directory . '/' . $dirfile ) ) {
				$dirSize = galdirsize( $directory . '/' . $dirfile );
				if( $dirSize >= 0 ) $size += $dirSize;
				else return - 1;
			}
		}
		closedir( $DIR );
	}

	return $size;
}

$statistic = array();

$row = $db->super_query("SELECT SUM(size) as count FROM " . PREFIX . "_gallery_picturies");
$statistic['fotos_size'] = formatsize($row['count']);

$db->query("SHOW TABLE STATUS FROM `".DBNAME."`");
	$mysql_size = 0;
	while ($r = $db->get_array()){
	if (strpos($r['Name'], PREFIX."_gallery_") !== false)
	$mysql_size += $r['Data_length'] + $r['Index_length'] ;
	}
$db->free();

$statistic['mysql_size'] = formatsize($mysql_size);
$statistic['cache_size'] = formatsize( galdirsize( "engine/gallery/cache" ) );
	
$row = $db->super_query("SELECT COUNT(id) as count FROM " . PREFIX . "_gallery_category");
$statistic['albums'] = $row['count'];

$row = $db->super_query("SELECT COUNT(picture_id) as count FROM " . PREFIX . "_gallery_picturies");
$statistic['fotos'] = $row['count'];

$db->query("SELECT COUNT(picture_id) as count FROM " . PREFIX . "_gallery_picturies WHERE user_id != '' GROUP BY user_id");
$statistic['users'] = $db->num_rows();

$row = $db->super_query("SELECT COUNT(id) as count FROM " . PREFIX . "_gallery_comments");
$statistic['comments'] = $row['count'];

$row = $db->super_query( "SELECT COUNT(id) as count FROM " . PREFIX . "_gallery_comments_subscribe" );
$statistic['subscribe'] = $row['count'];

$row = $db->super_query("SELECT date as lastdate FROM " . PREFIX . "_gallery_picturies ORDER BY date LIMIT 1");

$days = floor((time () - strtotime($row['lastdate']))/86400);

if ($days){
$statistic['albumsday'] = round($albums/$days, 2);
$statistic['fotosperday'] = round($fotos/$days, 2);
$statistic['comsperday'] = round($comments/$days, 2);
} else {
$statistic['albumsday'] = 0;
$statistic['fotosperday'] = 0;
$statistic['comsperday'] = 0;
}

$maxupload = str_replace('m', '', @strtolower(@ini_get('upload_max_filesize')));
$maxupload_t = formatsize($maxupload*1024*1024);

$comm_edit = ($member_id['user_group'] == 1 || $galConfig['admin_user_access'] && $user_group[$member_id['user_group']]['admin_comments']) ? " [ <a href=\"{$PHP_SELF}?mod=twsgallery&act=32&mode=1\">{$langGal['edit_comment_tit']}</a> ]" : "";

echo <<<HTML
<table width="100%">
    <tr>
        <td width="265" style="padding:2px;">{$langGal['stat_fotos']}</td>
        <td>{$statistic['fotos']}</td>
    </tr>
    <tr>
        <td style="padding:2px;">{$langGal['stat_fotoscount']}</td>
        <td>{$statistic['fotosperday']}</td>
    </tr>
    <tr>
        <td style="padding:2px;">{$langGal['stat_thems']}</td>
        <td>{$statistic['albums']}</td>
    </tr>
    <tr>
        <td style="padding:2px;">{$langGal['stat_albcount']}</td>
        <td>{$statistic['albumsday']}</td>
    </tr>
    <tr>
        <td style="padding:2px;">{$langGal['stat_coms']}</td>
        <td>{$statistic['comments']}{$comm_edit}</td>
    </tr>
    <tr>
        <td style="padding:2px;">{$langGal['stat_comsday']}</td>
        <td>{$statistic['comsperday']}</td>
    </tr>
    <tr>
        <td style="padding:2px;">{$langGal['stat_comssubscribe']}</td>
        <td><span id="subscribenum">{$statistic['subscribe']}</span></td>
    </tr>
    <tr>
        <td style="padding:2px;">{$langGal['stat_act_us']}</td>
        <td>{$statistic['users']}</td>
    </tr>
    <tr>
        <td style="padding:2px;">{$langGal['stat_dirsize']}</td>
        <td>{$statistic['fotos_size']}</td>
    </tr>
    <tr>
        <td style="padding:2px;">{$langGal['stat_dbsize']}</td>
        <td>{$statistic['mysql_size']}</td>
    </tr>
    <tr>
        <td style="padding:2px;">{$lang['cache_size']}</td>
        <td><span id="cachesize">{$statistic['cache_size']}</span></td>
    </tr>
    <tr>
        <td style="padding:2px;">{$langGal['stat_maxfile']}</td>
        <td>{$maxupload_t}</td>
    </tr>
    <tr>
        <td style="padding:2px;">TWSG Version:</td>
        <td>{$galConfig['version_gallery']}</td>
    </tr>
</table>
HTML;

	if ($member_id['user_group'] == 1){

	echo <<<HTML
<div style="padding-left:4px;"><br /><input id="check_updates" name="check_updates" class="edit" style="width:220px;" type="button" value="{$lang['dle_udate']}">&nbsp;<input id="clearbutton" name="clearbutton" class="edit" style="width:150px;" type="button" value="{$lang['btn_clearcache']}">&nbsp;<input id="testupload" name="testupload" class="edit" style="width:150px;" type="button" value="{$langGal['menu_test']}">&nbsp;<input id="clearsubscribe" name="clearsubscribe" class="edit" style="width:300px;" type="button" value="{$langGal['btn_clearsubscribe']}">
<br /><br /><div id="main_box"></div></div>
<script type="text/javascript">
			$(function(){
	
				$.ajaxSetup({
					cache: false
				});
	
				$('#clearsubscribe').click(function() {
					DLEconfirm( '{$lang['confirm_action']}', '{$lang['p_confirm']}', function () {
						$('#main_box').html('{$lang['dle_updatebox']}');
						$.get("{$PHP_SELF}?mod=twsgallery&a=1&act=57&dle_allow_hash={$dle_login_hash}", function( data ){
							$('#subscribenum').html('0');
							$('#main_box').html(data);
						});
					} );
					return false;
				});
				$('#clearbutton').click(function() {
					$('#main_box').html('{$lang['dle_updatebox']}');
					$.get("{$PHP_SELF}?mod=twsgallery&a=1&act=26&dle_allow_hash={$dle_login_hash}", function( data ){
						$('#cachesize').html('0 b');
						$('#main_box').html(data);
					});
					return false;
				});
				$('#check_updates').click(function() {
					$('#main_box').html('{$lang['dle_updatebox']}');
					$.get("{$PHP_SELF}?mod=twsgallery&a=1&act=43&dle_allow_hash={$dle_login_hash}", function( data ){
						$('#main_box').html(data);
					});
					return false;
				});
				$('#testupload').click(function() {
					$('#main_box').html('{$lang['dle_updatebox']}');
					$.get("{$PHP_SELF}?mod=twsgallery&a=1&act=27&dle_allow_hash={$dle_login_hash}", function( data ){
						$('#main_box').html(data);
					});
					return false;
				});
			});
		</script>
HTML;

	if ($lic_gal_tr){

		echo "<br /><form action=\"{$PHP_SELF}?mod=twsgallery\" method=\"post\">
	<table width=\"100%\"align=center>
		<tr>
			<td style='padding:3px; border:1px dashed rgb(190,190,190); background-color:lightyellow;'>
			<div>".str_replace( 'dle-news.ru', 'wonderfullife.ru', $lang['trial_info'] )."
			<br /><br /><b>{$lang['trial_key']}</b>
			<span style=\"padding-left:7px;\"><input class=\"edit bk\" type=\"text\" size=\"45\" name=\"site_k_ey2\"> <input class=\"edit\" type=\"submit\" value=\"{$lang['trial_act']}\"></span><div id=\"result_info\" style=\"color:red;\"><br /><span class=\"navigation\">{$lang['key_format']} <b>XXXXX-XXXXX-XXXXX-XXXXX-XXXXX</b></span></div></div>
			</td>
		</tr>
	</table>
	</form>";

	}

	if( @file_exists( "galleryinstall.php" ) ) {
		echo "<br><table width=\"100%\" align=center><tr><td>
		  <div class=\"ui-state-error ui-corner-all\" style=\"padding:10px;\">$langGal[stat_install]</div>
			 </td></tr><tr><td>&nbsp;</td></tr></table>";
	}

	if( @ini_get('safe_mode') == 1 || @strtolower(@ini_get('safe_mode')) == 'on') {
		echo "<br><table width=\"100%\" align=center><tr><td>
		  <div class=\"ui-state-error ui-corner-all\" style=\"padding:10px;\">$langGal[safe_mode_on]</div>
			 </td></tr><tr><td>&nbsp;</td></tr></table>";
	}

	if( $maxupload && $maxupload < 2) {
		echo "<br><table width=\"100%\" align=center><tr><td>
		  <div class=\"ui-state-error ui-corner-all\" style=\"padding:10px;\">$langGal[stat_test_not_sizemax]</div>
			 </td></tr><tr><td>&nbsp;</td></tr></table>";
	}

	if( !@extension_loaded('zlib') ) {
		echo "<br><table width=\"100%\" align=center><tr><td>
		 <div class=\"ui-state-error ui-corner-all\" style=\"padding:10px;\">{$langGal['stat_not_min']} <b>ZLib</b></div>
		 </td></tr><tr><td>&nbsp;</td></tr></table>";
	}

	if( !$galConfig['allow_cache']) {
		echo "<br><table width=\"100%\" align=\"center\"><tr><td style='padding:3px; border:1px dashed red; background-color:lightyellow;' class=main>
			  $langGal[c_allow_cache_warn]
				  </td></tr><tr><td>&nbsp;</td></tr></table>";
	}

	if( !is_writable(ENGINE_DIR."/gallery/cache/") || !is_writable(ENGINE_DIR."/gallery/cache/system/") || !is_writable(FOTO_DIR."/") || !is_writable(FOTO_DIR."/main/") || !is_writable(FOTO_DIR."/temp/") || !is_writable(FOTO_DIR."/thumb/") || !is_writable(FOTO_DIR."/caticons/")) {
		echo "<br><table width=\"100%\" align=\"center\"><tr><td style='padding:3px; border:1px dashed red; background-color:lightyellow;' class=main>
			  $langGal[stat_cache]
				  </td></tr><tr><td>&nbsp;</td></tr></table>";
	}

	$check_files = array(
		ENGINE_DIR."/gallery/cache/.htaccess",
		ENGINE_DIR."/gallery/cache/system/.htaccess",
		FOTO_DIR."/.htaccess",
	);

	foreach ($check_files as $file) {

		if( is_writable($file) ) {
			echo "<br><table width=\"100%\" align=center><tr><td>
	       <div class=\"ui-state-error ui-corner-all\" style=\"padding:10px;\">".str_replace("{file}", str_replace(ROOT_DIR, '', $file), $lang['stat_secfault_4'])."</div>
	          </td></tr><tr><td>&nbsp;</td></tr></table>";
		}

		if( !file_exists($file ) ) {
			echo "<br><table width=\"100%\" align=center><tr><td>
	       <div class=\"ui-state-error ui-corner-all\" style=\"padding:10px;\">".str_replace("{folder}", $file, $lang['stat_secfault_2'])."</div>
	          </td></tr><tr><td>&nbsp;</td></tr></table>";
		}

	}

}

galFooter();
$twsg->galsupport50();
echofooter();

?>