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
 Файл: comments.php
-----------------------------------------------------
 Назначение: Управление комментариями
=====================================================
*/

@error_reporting ( E_ALL ^ E_WARNING ^ E_NOTICE );
@ini_set ( 'display_errors', true );
@ini_set ( 'html_errors', false );
@ini_set ( 'error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE );

define('DATALIFEENGINE', true);
define( 'ROOT_DIR', substr( dirname(  __FILE__ ), 0, -20 ) );
define( 'ENGINE_DIR', ROOT_DIR . '/engine' );

include ENGINE_DIR.'/data/config.php';

if( $config['http_home_url'] == "" ) {
	
	$config['http_home_url'] = explode( "engine/gallery/ajax/comments.php", $_SERVER['PHP_SELF'] );
	$config['http_home_url'] = reset( $config['http_home_url'] );
	$config['http_home_url'] = "http://" . $_SERVER['HTTP_HOST'] . $config['http_home_url'];

}

require_once ENGINE_DIR.'/classes/mysql.php';
require_once ENGINE_DIR.'/data/dbconfig.php';
require_once ENGINE_DIR.'/modules/functions.php';

if (version_compare($config['version_id'], "9.6", ">"))
	dle_session();
else
	@session_start();

$_REQUEST['skin'] = trim(totranslit($_REQUEST['skin'], false, false));

if( $_REQUEST['skin'] == "" OR !@is_dir( ROOT_DIR . '/templates/' . $_REQUEST['skin'] ) ) {
	die( "Hacking attempt!" );
}

require_once ENGINE_DIR.'/classes/templates.class.php';

//################# Определение групп пользователей
$user_group = get_vars ("usergroup");

if (!$user_group) {
  $user_group = array ();

  $db->query("SELECT * FROM " . USERPREFIX . "_usergroups ORDER BY id ASC");

  while($row = $db->get_row()){

   $user_group[$row['id']] = array ();

     foreach ($row as $key => $value)
     {
       $user_group[$row['id']][$key] = stripslashes($value);
     }

  }
  set_vars ("usergroup", $user_group);
  $db->free();
}

//####################################################################################################################
//                    Определение забаненных пользователей и IP
//####################################################################################################################
$banned_info = get_vars ( "banned" );

if (! is_array ( $banned_info )) {
	$banned_info = array ();
	
	$db->query ( "SELECT * FROM " . USERPREFIX . "_banned" );
	while ( $row = $db->get_row () ) {
		
		if ($row['users_id']) {
			
			$banned_info['users_id'][$row['users_id']] = array (
																'users_id' => $row['users_id'], 
																'descr' => stripslashes ( $row['descr'] ), 
																'date' => $row['date'] );
		
		} else {
			
			if (count ( explode ( ".", $row['ip'] ) ) == 4)
				$banned_info['ip'][$row['ip']] = array (
														'ip' => $row['ip'], 
														'descr' => stripslashes ( $row['descr'] ), 
														'date' => $row['date']
														);
			elseif (strpos ( $row['ip'], "@" ) !== false)
				$banned_info['email'][$row['ip']] = array (
															'email' => $row['ip'], 
															'descr' => stripslashes ( $row['descr'] ), 
															'date' => $row['date'] );
			else $banned_info['name'][$row['ip']] = array (
															'name' => $row['ip'], 
															'descr' => stripslashes ( $row['descr'] ), 
															'date' => $row['date'] );
		
		}
	
	}
	set_vars ( "banned", $banned_info );
	$db->free ();
}

if ($config["lang_".$_REQUEST['skin']]) { 

	if (file_exists(ROOT_DIR . '/language/' . $config["lang_" . $_REQUEST['skin']] . '/website.lng')){
		@include_once ROOT_DIR . '/language/' . $config["lang_" . $_REQUEST['skin']] . '/website.lng';
	} else die("Language file not found");

	if (file_exists(ROOT_DIR . '/language/' . $config["lang_" . $_REQUEST['skin']] . '/gallery.web.lng')){
		@include_once ROOT_DIR . '/language/' . $config["lang_" . $_REQUEST['skin']] . '/gallery.web.lng';
	} else die("Language file not found");

} else {

    include_once ROOT_DIR.'/language/'.$config['langs'].'/website.lng';
	include_once ROOT_DIR.'/language/'.$config['langs'].'/gallery.web.lng';

}

$config['charset'] = ($lang['charset'] != '') ? $lang['charset'] : $config['charset'];
$is_logged = false;
$member_id = array ();

if ($config['allow_registration'] == "yes") {

	require_once ENGINE_DIR.'/modules/sitelogin.php';

	if (($member_id['user_group'] != '1' && !$user_group[$member_id['user_group']]['allow_offline'] && ($galConfig['off'] == "1" || $config['site_offline'] == "yes"))) die("error");
	if ( isset( $banned_info['ip'] ) ) $blockip = check_ip ( $banned_info['ip'] );  else $blockip = false;
	if (($is_logged AND $member_id['banned'] == "yes") OR $blockip) die("error");

} elseif ($galConfig['off'] == "1" || $config['site_offline'] == "yes") die("error");

if (!$is_logged) {$member_id['user_group'] = 5;}

define('AJAX_ACTION', true);

require_once ENGINE_DIR.'/gallery/functions/web.php';
require_once TWSGAL_DIR.'/classes/comments.php';

if (!$galConfig['allow_comments']) die("error");

$com_result = array();
$buffer = "";
$gzip = true;

if (!in_array($_REQUEST['action'], array('add','list','subscribe','unsubscribe')) && ($dle_login_hash == "" || $_REQUEST['dle_allow_hash'] != $dle_login_hash)) die("error");

switch ($_REQUEST['action']){

case "list" :

	$tpl = new dle_template;
	$tpl->dir = ROOT_DIR.'/templates/'.$_REQUEST['skin'];
	define('TEMPLATE_DIR', $tpl->dir);

	$ID = intval($_REQUEST['id']);
	if (!$ID) die("error");

	$control_info = $db->super_query("SELECT p.picture_id, p.category_id, p.approve, p.comments, p.date, p.picture_user_name, p.user_id, p.allow_comms, c.comment_level, c.view_level, c.edit_level, c.allow_comments, moderators, c.locked FROM " . PREFIX . "_gallery_picturies p, " . PREFIX . "_gallery_category c WHERE c.id=p.category_id AND p.picture_id='$ID'");

	if (!$control_info['picture_id'] || !$control_info['comments'] || !check_gallery_access ("read", $control_info['view_level'], "", $control_info['locked']) || (!($control_info['category_id'] && check_gallery_access ("edit", $control_info['edit_level'], $control_info['moderators'])) && (!$is_logged || $control_info['user_id'] != $member_id['user_id']) && $control_info['approve'] == 0)) die("error");

	$tpl->result['content'] = "<!--dlecomments-->{comments-delimiter}<!--dlenavigationcomments-->";

	$COMMENTS = new UnComments();

	$COMMENTS->url = array("", "");
	$COMMENTS->id = $ID;
	$COMMENTS->comments_num = $control_info['comments'];
	$COMMENTS->comm_nummers = $config['comm_nummers'];
	$COMMENTS->cstart 		= (isset($_REQUEST['cstart']) ? intval($_REQUEST['cstart']) : 0);
	$COMMENTS->template 	= 'gallery/comments';
	$COMMENTS->allow_ajax	= true;
	$COMMENTS->allow_addc	= ($control_info['allow_comms'] && $control_info['allow_comments'] && check_gallery_access ("comms", $control_info['comment_level']) && (!$galConfig['max_comments_days'] || strtotime($control_info['date']) > (TIME - $galConfig['max_comments_days']*3600*24)));
	$COMMENTS->doact		= 'do=gallery&act=3&dle_allow_hash='.$dle_login_hash.'&';
	$COMMENTS->ShowCommentslist(1);

	$buffer = $tpl->result['content'];
	//$buffer = str_replace(array("{", "}"), '', addcslashes($tpl->result['content'], "\v\t\n\r\f\"\\/"));
	//str_ireplace("{comments-delimiter}", "", $tpl->result['comments'])."{comments-delimiter}".str_ireplace("{comments-delimiter}", "", $tpl->result['fastnav']);

break;

case "add" :

	$tpl = new dle_template;
	$tpl->dir = ROOT_DIR.'/templates/'.$_REQUEST['skin'];
	define('TEMPLATE_DIR', $tpl->dir);

	$_POST['name'] = convert_unicode($_POST['name'], $config['charset']);
	$_POST['mail'] = convert_unicode($_POST['mail'], $config['charset']);
	$_POST['comments'] = convert_unicode($_POST['comments'], $config['charset']);
	$_POST['question_answer'] = convert_unicode($_POST['question_answer'], $config['charset']);

	$ID = intval($_POST['id']);
	if (!$ID) die("error");

	$control_info = $db->super_query("SELECT p.picture_id, p.category_id, p.approve, p.date, p.picture_user_name, p.user_id, p.allow_comms, c.comment_level, c.view_level, c.edit_level, c.allow_comments, moderators, c.locked FROM " . PREFIX . "_gallery_picturies p, " . PREFIX . "_gallery_category c WHERE c.id=p.category_id AND p.picture_id='$ID'");

	if (!$control_info['picture_id']) die("error");
	if ($control_info['approve'] != '1') die("error");
	if (!$control_info['allow_comms']) die("error");
	if (!$control_info['allow_comments']) die("error");
	if ($control_info['locked']) die("error");
	if (!check_gallery_access ("comms", $control_info['comment_level'])) die("error");
	if ($galConfig['max_comments_days'] && strtotime($control_info['date']) < (TIME - ($galConfig['max_comments_days'] * 3600 * 24))) die("error");

	$is_admin = check_gallery_access ("edit", $control_info['edit_level'], $control_info['moderators']);

	if (!check_gallery_access ("read", $control_info['view_level'], "") && !$is_admin) die("error");

	$COMMENTS = new UnComments();
	$COMMENTS->id = $ID;
	$COMMENTS->this_id = 'Gal';
	$COMMENTS->doact		= 'do=gallery&act=3&dle_allow_hash='.$dle_login_hash.'&';		
	$COMMENTS->allow_cmod	= $galConfig['comments_mod'];
	$COMMENTS->update_news_sql =  PREFIX . "_gallery_picturies SET comments=comments+1 WHERE picture_id ";

	$com_result = $COMMENTS->AddComment();

	if ($_POST['editor_mode'] == "wysiwyg"){

		if (version_compare($config['version_id'], "9.6", "<") || $config['allow_comments_wysiwyg'] == "2")
			$clear_value = "tinyMCE.execInstanceCommand('comments', 'mceSetContent', false, '', false)";
		else
			$clear_value = "oUtil.obj.focus();oUtil.obj.loadHTML('');";

	} else $clear_value = "form.comments.value = '';";

	if (isset($user_group[$member_id['user_group']]['comments_question']) && $user_group[$member_id['user_group']]['comments_question']){
		$qs = $db->super_query("SELECT id, question FROM " . PREFIX . "_question ORDER BY RAND() LIMIT 1");
		$qs['question'] = htmlspecialchars(stripslashes($qs['question']), ENT_QUOTES, $config['charset']);
		$_SESSION['question'] = $qs['id'];
	}

	if ($com_result === true){

		$COMMENTS->template 	= 'gallery/comments';
		$COMMENTS->ajax_added();

		$buffer = "<div id=\"blind-animation\" style=\"display:none\">".$tpl->result['content']."<div>";

		$buffer .= <<<HTML
<script type="text/javascript">
	var timeval = new Date().getTime();
	var form = document.getElementById('dle-comments-form');
	if ( form.question_answer ) {
	   form.question_answer.value ='';
       jQuery('#dle-question').text('{$qs['question']}');
    }
	{$clear_value}
</script>
HTML;

	} else {

		$com_result = implode('<br /><br />', $com_result);

		$buffer = "<script language=\"JavaScript\" type=\"text/javascript\">\nvar form = document.getElementById('dle-comments-form');\n";

		if (!$where_approve) $buffer .= "
		{$clear_value}
	";

	$buffer .= "\n DLEalert('" . $com_result . "', '". $lang['add_comm']."');\n var timeval = new Date().getTime();\n

	if ( document.getElementById('recaptcha_response_field') ) {
	   Recaptcha.reload(); 
    }

	if (form.question_answer){
	   form.question_answer.value ='';
       jQuery('#dle-question').text('{$qs['question']}');
    }

	if ( document.getElementById('dle-captcha') ) {
		document.getElementById('dle-captcha').innerHTML = '<img src=\"' + dle_root + 'engine/modules/antibot.php?rand=' + timeval + '\"><br /><a onclick=\"reload(); return false;\" href=\"#\">{$lang['reload_code']}</a>';
	}\n </script>";

	}

break;

case "delete" :

	if ($is_logged && isset($_REQUEST['com_id'])){

		$COMMENTS = new UnComments();
		$COMMENTS->update_news_sql =  PREFIX . "_gallery_picturies SET comments=comments-1 WHERE picture_id ";
		if ($galConfig['allow_delete_omcomments']) $COMMENTS->user_mod_id =  "u.user_id as sp_id FROM " . PREFIX . "_gallery_picturies p LEFT JOIN " . USERPREFIX . "_users u ON u.user_id=p.user_id WHERE u.user_group > 2 AND p.picture_id";
		$COMMENTS->delete_comment();

	} else $buffer = "<script language=\"JavaScript\" type=\"text/javascript\">\nDLEalert('" . $langGal['unknown'] . "', '". $lang['add_comm']."');\n </script>";

break;

case "edit" :

	if ($is_logged && isset($_REQUEST['com_id'])){

		$COMMENTS = new UnComments();
		$buffer = $COMMENTS->ajax_edit_comment();
	
	} else $buffer = "<script language=\"JavaScript\" type=\"text/javascript\">\nDLEalert('" . $langGal['unknown'] . "', '". $lang['add_comm']."');\n </script>";

break;

case "do_edit" :

	if ($is_logged && isset($_REQUEST['com_id'])){

		$COMMENTS = new UnComments();
		$COMMENTS->update_news_sql =  PREFIX . "_gallery_picturies SET comments=comments+1 WHERE picture_id ";
		$buffer = $COMMENTS->ajax_do_edit_comment();

	} else $buffer = "<script language=\"JavaScript\" type=\"text/javascript\">\nDLEalert('" . $langGal['unknown'] . "', '". $lang['add_comm']."');\n </script>";

break;

case "subscribe" :
case "unsubscribe" :

	$COMMENTS = new UnComments();

	if ($_REQUEST['action'] == "subscribe")
		$result = $COMMENTS->insert_subscribe($_POST['fid'], $_POST['email'], $_POST['hash'], true);
	elseif ($_REQUEST['action'] == "unsubscribe")
		$result = $COMMENTS->remove_subscribe($_POST['fid'], $_POST['user_id'], $_POST['email'], $_POST['hash']);
	else
		$result = -1;

	$buffer = "{\"status\": \"error\",\"txt1\": \"{$langGal['unsubscribe_err']}\"}";

	if (!is_array($result))
		switch ($result){
		case 4 : $buffer = "{\"status\": \"ok\",\"txt1\": \"{$langGal['unsubscribe_ok']}\"}"; break;
		case 3 : $buffer = "{\"status\": \"ok\",\"txt1\": \"{$langGal['subscribe_ok']}\"}"; break;
		case 2 : $buffer = "{\"status\": \"wait\",\"txt1\": \"{$langGal['subscribe_wait']}\"}"; break;
		case 1 : $buffer = "{\"status\": \"found\",\"hash\": \"{$COMMENTS->url}\",\"txt1\": \"{$langGal['subscribe_alr']}\",\"txt2\": \"{$langGal['unsubscribe_user']}\",\"txt3\": \"{$langGal['unsubscribe_all']}\"}"; break;
		}
	else $buffer = "{\"status\": \"error\",\"txt1\": \"{$result[0]}\"}";

break;

}

$db->close();

$buffer = str_ireplace( '{THEME}', $config['http_home_url'] . 'templates/' . $_REQUEST['skin'], $buffer );

@header("Content-type: text/html; charset=".$config['charset']);
echo $buffer;

if ($gzip && $config['allow_gzip'] == "yes"){
	require_once ENGINE_DIR . '/modules/gzip.php'; // require_once, т.к. часто используем
	$_DOCUMENT_DATE = false;
	GzipOut();
}

exit;

?>