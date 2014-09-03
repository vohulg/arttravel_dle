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
 Файл: lastcomments.php
-----------------------------------------------------
 Назначение: Последние комментарии
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

	require_once TWSGAL_DIR.'/classes/comments.php';

	$_user_id = (isset($_REQUEST['uid'])) ? intval($_REQUEST['uid']) : 0;
	$gal_user = isset($_REQUEST['gal_user']) ? @$db->safesql(strip_tags(str_replace('/', '', urldecode($_GET['gal_user'])))) : '';

	if ($gal_user != ''){

		$sql_user = $db->super_query("SELECT user_id FROM " . PREFIX . "_users WHERE name='{$gal_user}'");
		$_user_id = $sql_user['user_id'];

		if (!$_user_id){

			@header("HTTP/1.0 404 Not Found");
			msgbox ($lang['all_info'], $lang['err_last']."<br /><br /><a href=\"javascript:history.go(-1)\">".$langGal['all_prev']."</a>");
			return;

		}

	}

	$id = (isset($_REQUEST['id'])) ? intval($_REQUEST['id']) : 0;

	$href = $galConfig['PHP_SELF'].'&act=4&uid='.$_user_id.'&id='.$id;

	$sql = array();

	if ($id) $sql[] = "ct.id={$id}";
	if ($_user_id) $sql[] = "c.user_id='{$_user_id}'";

	$sql[] = "(ct.view_level".(check_gallery_access ("read", "", "") ? " IN ('-1','')" : "='-1'")." OR ct.view_level regexp '[[:<:]]({$member_id['user_group']})[[:>:]]')";

	if (!check_gallery_access ("edit", "", "")) $sql[] = "ct.locked=0";

	$sql[] = "p.approve=1";

	$COMMENTS = new UnComments();
	$COMMENTS->allow_cmod = $galConfig['comments_mod'];
	$COMMENTS->this_id = 'Gal';
	$COMMENTS->doact		= 'do=gallery&act=3&dle_allow_hash='.$dle_login_hash.'&';	
	$COMMENTS->comm_nummers = $config['comm_nummers'];
	if ($COMMENTS->allow_cmod) $approve = " AND c.approve=1"; else $approve = "";
	$COMMENTS->count_querry = $COMMENTS->comm_table . " c INNER JOIN " . PREFIX . "_gallery_picturies p ON p.picture_id=c.post_id INNER JOIN " . PREFIX . "_gallery_category ct ON ct.id=p.category_id WHERE " . implode (" AND ",$sql).$approve;
	$COMMENTS->id 			= 0;
	$COMMENTS->cstart 		= $cstart;
	$COMMENTS->template 	= 'gallery/comments';
	$COMMENTS->sort_order	= 'desc';
	$COMMENTS->data_querry 	= "SELECT c.id, c.post_id, c.date, c.autor as gast_name, c.email as gast_email, c.text, c.ip, u.user_id, u.name, u.email, u.news_num, u.comm_num, u.user_group, u.reg_date, u.lastdate, u.signature, u.foto, u.fullname, u.land, u.icq, u.xfields, p.picture_id, p.picture_title, p.image_alt_title, p.category_id, p.picture_alt_name, p.type_upload, p.picture_filname, p.preview_filname, p.full_link, p.media_type, p.text as picture_text, p.thumbnails, ct.cat_alt_name FROM " . $COMMENTS->comm_table . " c LEFT JOIN " . USERPREFIX . "_users u ON c.user_id=u.user_id INNER JOIN " . PREFIX . "_gallery_picturies p ON p.picture_id=c.post_id INNER JOIN " . PREFIX . "_gallery_category ct ON ct.id=p.category_id WHERE " . implode (" AND ",$sql) . $approve . " ORDER BY c.date ".$COMMENTS->sort_order;
	$COMMENTS->url			= array($href.'&cstart={INS}', $href);
	$COMMENTS->allow_addc	= false;
	$COMMENTS->item_url		= $galConfig['mainhref'];
	$COMMENTS->ShowCommentslist(0, true);

	if ($COMMENTS->comments_num == 0) {

		@header("HTTP/1.0 404 Not Found");
		msgbox ($lang['all_info'], $lang['err_last']."<br /><br /><a href=\"javascript:history.go(-1)\">".$langGal['all_prev']."</a>");

	}

	$s_navigation .= " &raquo; <a href=\"{$href}\">".$langGal['menu_title1']."</a>";
	$metatags['title'] = $langGal['menu_title1'];
	if ($cstart > 1) $metatags['title'] .= ' &raquo; '.$lang['news_site'].' '.$cstart;

?>