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
 Файл: main.php
-----------------------------------------------------
 Назначение: Файл распределения запросов и данных
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

	$fstart = isset($_REQUEST['fstart']) ? intval($_REQUEST['fstart']) : 1;
	$cstart = isset($_REQUEST['cstart']) ? intval($_REQUEST['cstart']) : 1;
	unset($_GET['cstart'], $_REQUEST['cstart']);
	if ($fstart < 1) $fstart = 1;
	if ($cstart < 1) $cstart = 1;
	$act = isset($_REQUEST['act']) ? intval($_REQUEST['act']) : 0;

	if ($galConfig['off'] == "1"){
		$config['offline_reason'] = $langGal['offline'];
		include ENGINE_DIR.'/modules/offline.php';
		msgbox ($langGal['all_info'], $langGal['offline_a']);
	}

	$galcron = 0;

	if (date("Y-m-d", $galConfig['last_cron']) != date("Y-m-d", (TIME-3600*3))) $galcron = 2;
	elseif (($galConfig['last_cron'] + (3600*2)) < (TIME-3600*3)) $galcron = 1;

	if ($galcron){
		include TWSGAL_DIR.'/modules/cron.php';
	}

	$js_options = array('admin'=>false,'mode'=>0);
	$main_sql = "";
	$main_sql_where = "";
	$main_sql_limit = "";
	$compile = '';
	$template = '';
	$_admin = 0;
	$fsort = array();
	define('AJAX_ACTION', false);

	$metatags['title'] = "";

	if ($galConfig['description'] != '' || $galConfig['keywords'] != ''){
		$metatags['description'] = $galConfig['description'];
		$metatags['keywords'] = $galConfig['keywords'];
	}

	$s_navigation = "";

switch ($act){

	case 0 :
		include TWSGAL_DIR.'/modules/mainpage.php';
		break;

	case 1 :
		include TWSGAL_DIR.'/modules/category.php';
		break;

	case 2 :
		include TWSGAL_DIR.'/modules/fullimage.php';
		break;

	case 3 :

		$metatags['title'] = $langGal['menu_title5'];

		if ($dle_login_hash != "" && $_REQUEST['dle_allow_hash'] == $dle_login_hash && $is_logged && isset($_REQUEST['com_id']) && (in_array($subaction, array('comm_edit','comm_del','do_comm_edit','comm_combine')))){

			include_once TWSGAL_DIR.'/classes/comments.php';

			$COMMENTS = new UnComments();

			if ($subaction == "comm_del"){

				$COMMENTS->update_news_sql =  PREFIX . "_gallery_picturies SET comments=comments-1 WHERE picture_id ";
				if ($galConfig['allow_delete_omcomments']) $COMMENTS->user_mod_id =  "u.user_id as sp_id FROM " . PREFIX . "_gallery_picturies p LEFT JOIN " . USERPREFIX . "_users u ON u.user_id=p.user_id WHERE u.user_group > 2 AND p.picture_id";
				$result = $COMMENTS->delete_comment(false);

				if ($result !== true) msgbox ($lang['comm_err_2'], $result);

			} elseif ($subaction == "comm_edit"){

				$COMMENTS->template 	= 'gallery/addcomments';
				$COMMENTS->edit_comment();

			} elseif ($subaction == "do_comm_edit"){

				$COMMENTS->update_news_sql =  PREFIX . "_gallery_picturies SET comments=comments+1 WHERE picture_id ";
				$COMMENTS->do_edit_comment();

			} elseif ($subaction == "comm_combine"){

				$COMMENTS->update_news_sql =  PREFIX . "_gallery_picturies SET comments=comments-1 WHERE picture_id ";
				$result = $COMMENTS->comm_combine(false);

				if ($result !== true) msgbox ($lang['comm_err_2'], $result);

			}

		} else msgbox ( $lang['comm_err_2'], $lang['comm_err_5'] );

		break;

	case 4 :
		include TWSGAL_DIR.'/modules/lastcomments.php';
		break;

	case 15 :
	case 16 :
		include TWSGAL_DIR.'/modules/newfoto.php';
		break;

	case 22 :
		include_once TWSGAL_DIR.'/classes/tagscloud.php';
		$tagscloud = new gallery_tags_cloud();
		echo $tagscloud->tags_page();
		break;

	case 28 :
		include TWSGAL_DIR.'/modules/categories.php';
		break;

	case 19 :
	case 20 :
	case 21 :
	case 24 :
	case 25 :
	case 27 :

		include_once TWSGAL_DIR.'/classes/editfile.php';
		include_once TWSGAL_DIR.'/classes/editcategory.php';
		include TWSGAL_DIR.'/modules/editcategory.php';

		break;

	case 5 :
	case 6 :
	case 7 :
	case 8 :
	case 9 :
	case 10 :
	case 11 :
	case 12 :
	case 13 :
	case 14 :
	case 17 :
	case 18 :

		include_once TWSGAL_DIR.'/classes/editfile.php';
		include_once TWSGAL_DIR.'/classes/editcategory.php';
		include TWSGAL_DIR.'/modules/editfile.php';
		break;

	case 26 :

		include_once TWSGAL_DIR.'/classes/editfile.php';
		include_once TWSGAL_DIR.'/classes/editcategory.php';
		include_once TWSGAL_DIR.'/classes/upload.php';
		include_once TWSGAL_DIR.'/classes/thumbnailer.php';
		include_once TWSGAL_DIR.'/classes/upload.action.php';

		$upload = new gallery_upload_action($_REQUEST['cat']);
		$upload->gallery_upload_init($_REQUEST['ap']);

		break;

	case 23 :

		$buffer = $langGal['unsubscribe_err'];

		include_once TWSGAL_DIR.'/classes/comments.php';

		$COMMENTS = new UnComments();

		if ($_GET['action'] == "subscribe")
			$result = $COMMENTS->insert_subscribe($_GET['fid'], $_GET['email'], $_GET['hash'], true);
		elseif ($_GET['action'] == "unsubscribe")
			$result = $COMMENTS->remove_subscribe($_GET['fid'], $_GET['user_id'], $_GET['email'], $_GET['hash']);
		else
			$result = -1;

		if (!is_array($result))
			switch ($result){
			case 1 : $buffer = $langGal['subscribe_alr']; break;
			case 3 : $buffer = $langGal['subscribe_ok']; break;
			case 4 : $buffer = $langGal['unsubscribe_ok']; break;
			}
		else $buffer = $result[0];

		msgbox($lang['all_info'],  $buffer);

		break;

	default :

		msgbox ($langGal['all_err_1'], $langGal['unknown']);

}

if ($galConfig['work_postfix']){
	if ($metatags['title'] != ''){
		if ($s_navigation == "") $s_navigation = " &raquo; " . $metatags['title'];
		$metatags['title'] .= ' &raquo; ';
	}
	$s_navigation = "<a href=\"{$galConfig['mainhref']}\">".$langGal['galleryname']."</a>" . $s_navigation;
	$metatags['title'] .= $langGal['galleryname'] . ' &raquo; ';
} else {
	if ($metatags['title'] != ''){
		if ($s_navigation == "") $s_navigation = $metatags['title'];
		$metatags['title'] .= ' &raquo; ';
	}
	if (substr($s_navigation, 0, 9) == ' &raquo; ') $s_navigation = substr($s_navigation, 9, (strlen($s_navigation)-9));
}

$metatags['title'] .= $config['home_title'];

LoadShortScriprt();

$allow_groups = $galConfig['adminaccess'] ? explode(',', $galConfig['adminaccess']) : array();
if ($is_logged AND ($member_id['user_group'] == '1' || in_array($member_id['user_group'], $allow_groups))){
$tpl->result['content'] .= <<<HTML
<div align="center" style="padding:0px;font-size:11px;color:#666666;" class="slink"><a href="{$config['http_home_url']}{$config['admin_path']}?mod=twsgallery" target="_blank">Admin Control Panel</a></div><br />
HTML;
}

?>