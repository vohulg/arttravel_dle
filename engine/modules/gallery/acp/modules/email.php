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
 Файл: email.php
-----------------------------------------------------
 Назначение: Управление шаблонами email
=====================================================
*/

if(!defined('DATALIFEENGINE') OR !defined( 'LOGGED_IN' ))
{
  die("Hacking attempt!");
}

if($member_id['user_group'] != 1){ msg("error", $lang['addnews_denied'], $lang['db_denied'], "?mod=twsgallery&act=0"); }

if ($act == "42"){

	$find = array ("<", ">");
	$replace = array ("&lt;", "&gt;");

	foreach($_POST['text'] as $id => $data){

		$data = $db->safesql(str_replace($find, $replace, $data));

		$db->query("UPDATE " . PREFIX . "_tws_email set template='{$data}' where id='".intval($id)."'");

	}

	galExit($PHP_SELF."?mod=twsgallery&act=41&dle_allow_hash={$dle_login_hash}");

} elseif ($act == "41"){

	echoheader("", "");
	galnavigation();

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
<form action="$PHP_SELF?mod=twsgallery&act=42&dle_allow_hash={$dle_login_hash}" method="post">
HTML;

	$db->query("SELECT * FROM " . PREFIX . "_tws_email WHERE prefix = '1'");

	while($row = $db->get_row()){

		$row['template'] = stripslashes($row['template']);
		$row['description'] = str_replace("\n", "<br />", $row['description']);
		$row['description'] = stripslashes($row['description']);

echo <<<HTML
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$row['title']}</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td style="padding:2px;">{$row['description']}</td>
    </tr>
    <tr>
        <td style="padding:2px;"><textarea rows="15" style="width:650px;" name="text[{$row['id']}]">{$row['template']}</textarea></td>
    </tr>
</table>
HTML;

	}

	$db->free();

echo <<<HTML
<table width="100%">
    <tr>
		<td>&nbsp;&nbsp;<input type="submit" value="{$lang['user_save']}" class="buttons"></td>
    </tr>
</table>
</form>
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


	$twsg->galsupport50();
	echofooter();

}

?>