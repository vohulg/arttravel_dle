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
 Файл: ip.php
-----------------------------------------------------
 Назначение: Контроль IP
=====================================================
*/

if(!defined('DATALIFEENGINE') OR !defined( 'LOGGED_IN' ))
{
  die("Hacking attempt!");
}

if($member_id['user_group'] != 1 && (!$galConfig['admin_user_access'] || !$user_group[$member_id['user_group']]['admin_blockip'] || !$user_group[$member_id['user_group']]['admin_iptools'])){ msg("error", $lang['addnews_denied'], $lang['db_denied'], "?mod=twsgallery&act=0"); }

if (isset ($_REQUEST['ip'])) $ip = $db->safesql(htmlspecialchars(strip_tags(trim($_REQUEST['ip'])), ENT_QUOTES, $config['charset'])); else $ip = "";
if (isset ($_REQUEST['name'])) $name = $db->safesql(htmlspecialchars(strip_tags(trim($_REQUEST['name'])), ENT_QUOTES, $config['charset'])); else $name = "";

if ($act == 37){

	include_once ENGINE_DIR.'/classes/parse.class.php';

	$parse = new ParseFilter();
	$parse->safe_mode = true;
	$parse->allow_url = $user_group[$member_id['user_group']]['allow_url'];
	$parse->allow_image = false;

	$banned_descr = $db->safesql($parse->BB_Parse($parse->process($_POST['descr']), false));

	if ((trim($_POST['date']) == "") OR (($_POST['date'] = strtotime($_POST['date'])) === -1))
		$this_time = 0;
	else
		$this_time = $_POST['date'];

	if ($name != ''){

  		$user_id = $db->super_query("SELECT user_id, logged_ip FROM " . USERPREFIX . "_users WHERE name='{$name}'");

		if (!$user_id['user_id']){ msg("error",$lang['cat_error'],$langGal['user_error'], "javascript:history.go(-1)"); }

		$ip = $user_id['logged_ip'];
		$users_id = $user_id['user_id'];
		$search = "users_id = '{$users_id}'";

	} elseif(!$ip){

		msg("error",$lang['cat_error'],$langGal['ip_error'], "javascript:history.go(-1)");

	} else {

		$users_id = 0;
		$search = "users_id = '0' AND ip = '{$ip}'";

	}

	$row = $db->super_query("SELECT id FROM " . USERPREFIX . "_gallery_banned WHERE {$search}");

	if (!$row['id']){

		$db->query("INSERT INTO " . USERPREFIX . "_gallery_banned (descr, date, users_id, ip) values ('{$banned_descr}', '{$this_time}', '{$users_id}', '{$ip}')");

	} else {

		$db->query("UPDATE " . USERPREFIX . "_gallery_banned set descr='{$banned_descr}', users_id='{$users_id}', date='{$this_time}', ip='{$ip}' WHERE id='{$row['id']}'");

	}

	$db->query("UPDATE " . PREFIX . "_gallery_config SET value='1' WHERE name='enable_banned'");

	@unlink(ENGINE_DIR.'/cache/system/gallery_banned.php');
	@unlink(ENGINE_DIR.'/cache/system/gallery_config.php');

	$act = 36;

} elseif ($act == 38){

	$id = (isset($_POST['id'])) ? intval($_POST['id']) : intval($_GET['id']); // исправляем баг $_REQUEST['id'], временно

    if(!$id){

		msg("error",$lang['cat_error'],$langGal['ip_error'], "javascript:history.go(-1)");

	}

	$db->query("DELETE FROM " . USERPREFIX . "_gallery_banned WHERE id = '$id'");

	@unlink(ENGINE_DIR.'/cache/system/gallery_banned.php');

	$count = $db->super_query("SELECT COUNT(*) as count FROM " . USERPREFIX . "_gallery_banned");

	if (!$count['count']){

		$db->query("UPDATE " . PREFIX . "_gallery_config SET value='0' WHERE name='enable_banned'");
		@unlink(ENGINE_DIR.'/cache/system/gallery_config.php');

	}

	$act = 36;

} 

$js_array[] = "engine/skins/tabset.js";
$js_array[] = "engine/skins/calendar.js";

echoheader("", $langGal['menu_ipban']);
galnavigation();

echo "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"engine/skins/calendar-blue.css\" title=\"win2k-cold-1\" />";

echo "<div id=\"dle_tabView1\">";

echo <<<HTML
	<div class="tabset" id="tabset">
		<a id="a" class="tab  {content:'cont_1'}">{$langGal['ip_searcm']}</a><a id="b" class="tab  {content:'cont_2'}">{$langGal['ip_searcm2']}</a><a id="c" class="tab  {content:'cont_3'}" >{$langGal['ip_block']}</a><a id="c" class="tab  {content:'cont_4'}" >{$langGal['user_blockm']}</a>
	</div>
<div id="cont_1" style="display:none;">
HTML;

galHeader($langGal['menu_search']);

echo <<<HTML
<form action="{$PHP_SELF}?mod=twsgallery" method="post">
<table width="100%">
    <tr>
        <td style="padding:2px;" height="70">{$langGal['do_ip_search']}<br /><input class="edit" style="width:250px;" type="text" name="ip" value="{$ip}">&nbsp;&nbsp;&nbsp;<input type="submit" value="{$lang['b_find']}" class="edit"><br /><span class=small>{$langGal['opt_ipfe']}</span></td>
    </tr>
</table>
<input type="hidden" name="act" value="39">
<input type="hidden" name="dle_allow_hash" value="{$dle_login_hash}">
</form>
HTML;

galFooter();

echo "</div><div id=\"cont_2\" style=\"display:none;\">";

galHeader($langGal['menu_search']);

echo <<<HTML
<form action="{$PHP_SELF}?mod=twsgallery" method="post">
<table width="100%">
    <tr>
        <td style="padding:2px;" height="70">{$langGal['do_user_search']}<br /><input class="edit" style="width:250px;" type="text" name="name" value="{$name}">&nbsp;&nbsp;&nbsp;<input type="submit" value="{$lang['b_find']}" class="edit"></td>
    </tr>
</table>
<input type="hidden" name="act" value="40">
<input type="hidden" name="dle_allow_hash" value="{$dle_login_hash}">
</form>
HTML;

galFooter();

echo "</div><div id=\"cont_3\" style=\"display:none;\">";


galHeader($langGal['menu_ipadd']);

echo <<<HTML
<form action="" method="post">
<table width="100%">
    <tr>
        <td style="padding:2px;" width="150">{$langGal['ip_add']}</td> 
		<td style="padding:2px;"><input class="edit" style="width:250px;" type="text" name="ip" value="{$ip}">&nbsp;<span class=small>{$langGal['ip_example']}</span></td>
    </tr>
    <tr>
        <td style="padding:2px;" width="150">{$langGal['ban_date']}</td> 
		<td style="padding:2px;"><input type="text" name="date" id="f_date_c" size="20"  class=edit>
<img src="engine/skins/images/img.gif"  align="absmiddle" id="f_trigger_c" style="cursor: pointer; border: 0" title="{$lang['edit_ecal']}"/>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "f_date_c",     // id of the input field
        ifFormat       :    "%Y-%m-%d %H:%M",      // format of the input field
        button         :    "f_trigger_c",  // trigger for the calendar (button ID)
        align          :    "Br",           // alignment
		timeFormat     :    "24",
		showsTime      :    true,
        singleClick    :    true
    });
</script></td>
    </tr>
    <tr>
        <td style="padding:2px;">{$langGal['ban_descr']}</td> 
        <td style="padding:2px;"><textarea class="edit" style="width:250px;height:70px;" name="descr"></textarea></td>
    </tr>
    <tr>
        <td style="padding:2px;">&nbsp;</td> 
		<td style="padding:2px;"><input type="submit" value="{$lang['user_save']}" class="edit"></td>
    </tr>
</table>
<input type="hidden" name="act" value="37">
<input type="hidden" name="dle_allow_hash" value="{$dle_login_hash}">
</form>
HTML;

galFooter();

echo "</div><div id=\"cont_4\" style=\"display:none;\">";

galHeader($langGal['menu_ipadd']);

echo <<<HTML
<form action="" method="post">
<table width="100%">
    <tr>
        <td style="padding:2px;" width="150">{$langGal['ip_addus']}</td> 
		<td style="padding:2px;"><input class="edit" style="width:250px;" type="text" name="name" value="{$name}"></td>
    </tr>
    <tr>
        <td style="padding:2px;" width="150">{$langGal['ban_date']}</td> 
		<td style="padding:2px;"><input type="text" name="date" id="f_date_cs" size="20"  class=edit>
<img src="engine/skins/images/img.gif"  align="absmiddle" id="f_trigger_cs" style="cursor: pointer; border: 0" title="{$lang['edit_ecal']}"/>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "f_date_cs",     // id of the input field
        ifFormat       :    "%Y-%m-%d %H:%M",      // format of the input field
        button         :    "f_trigger_cs",  // trigger for the calendar (button ID)
        align          :    "Br",           // alignment
		timeFormat     :    "24",
		showsTime      :    true,
        singleClick    :    true
    });
</script></td>
    </tr>
    <tr>
        <td style="padding:2px;">{$langGal['ban_descr']}</td> 
        <td style="padding:2px;"><textarea class="edit" style="width:250px;height:70px;" name="descr"></textarea></td>
    </tr>
    <tr>
        <td style="padding:2px;">&nbsp;</td> 
		<td style="padding:2px;"><input type="submit" value="{$lang['user_save']}" class="edit"></td>
    </tr>
</table>
<input type="hidden" name="act" value="37">
<input type="hidden" name="dle_allow_hash" value="{$dle_login_hash}">
</form>
HTML;

galFooter();

echo "</div></div>";

echo <<<HTML
<script type="text/javascript">
		$(function(){
			$("#tabset").buildMbTabset({
				sortable:false,
				position:"left"
			});
		});
</script>
HTML;

echo <<<JSCRIPT
<script language='JavaScript' type="text/javascript">
<!--
function IPMenu( m_ip, l1, l2, l3 ){

var menu=new Array();

menu[0]='<a href="http://www.nic.ru/whois/?ip=' + m_ip + '" target="_blank">' + l1 + '</a>';
menu[1]='<a href="{$PHP_SELF}?mod=twsgallery&act=39&ip=' + m_ip + '&dle_allow_hash={$dle_login_hash}">' + l2 + '</a>';
menu[2]='<a href="{$PHP_SELF}?mod=twsgallery&act=10&ip=' + m_ip + '" target="_blank">' + l3 + '</a>';

return menu;
};
//-->
</script>
JSCRIPT;

if ($act == 36){

	galHeader($langGal['menu_blocked']);

echo <<<HTML
<table width="100%">
    <tr>
        <td width="200" style="padding:2px;">IP:</td>
		<td width="150" style="padding:2px;">{$langGal['ban_user_name']}</td>
        <td width="190">{$langGal['ban_date']}</td>
        <td width="250">{$langGal['ban_descr']}</td>
        <td>&nbsp;</td>
    </tr>
	<tr><td colspan="5"><div class="hr_line"></div></td></tr>
HTML;

	$db->query("SELECT b.*, u.name FROM " . USERPREFIX . "_gallery_banned b LEFT JOIN " . USERPREFIX . "_users u ON b.users_id=u.user_id ORDER BY id DESC");

	$i = 0;

	while($row = $db->get_row()){

        $i++;

		if ($row['date'])
			$endban = langdate("j M Y H:i", $row['date']);
		else
			$endban = $langGal['banned_info'];

		if ($row['name']){
			$row['name'] = stripslashes($row['name']);
			$user_name = "<a class=list href=\"{$PHP_SELF}?mod=twsgallery&act=10&author=".$row['name']."\">".$row['name']."</a>";
		} else {
			$user_name = "--";
		}

        echo"
        <tr>
        <td style=\"padding:3px\">
		<a onclick=\"return dropdownmenu(this, event, IPMenu('".$row['ip']."', '".$langGal['ip_info']."', '".$langGal['ip_tools']."', '".$langGal['ip_ban']."'), '190px')\" onMouseout=\"delayhidemenu()\" href=\"http://www.nic.ru/whois/?ip={$row['ip']}\" target=\"_blank\">{$row['ip']}</a>
        </td>
		<td style=\"padding:3px\">
        ".$user_name."
        </td>
        <td style=\"padding:3px\">
        {$endban}
        </td>
        <td style=\"padding:3px\">
        ".stripslashes($row['descr'])."
        </td>
        <td>
        [<a href=\"$PHP_SELF?mod=twsgallery&act=38&id={$row['id']}&dle_allow_hash={$dle_login_hash}\">{$langGal['ip_unblock']}</a>]</td>
        </tr>
	</tr><tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=5></td></tr>
        ";
}

if($i == 0){     
echo"<tr>
     <td height=\"18\" colspan=\"5\">
       <p align=\"center\"><br><b>{$langGal['ip_empty']}<br><br></b>
    </tr>"; }


echo "</table>";

galFooter();

} elseif ($act == 39){

	galHeader($langGal['menu_find2']);

	echopopupedituser();

	echo <<<HTML
<script language="javascript" type="text/javascript">
<!--
function cdelete(id){
DLEconfirm( '{$lang['comm_alldelconfirm']}', '{$lang['p_confirm']}', function () {
			document.location='?mod=iptools&action=find&ip={$ip}&doaction=dodelcomments&user_hash={$dle_login_hash}&id=' + id + '';
		} );
}
function MenuBuild( m_id ){

var menu=new Array()

menu[0]='<a href="{$config['http_home_url']}index.php?do=lastcomments&userid=' + m_id + '" target="_blank">{$lang['comm_view']}</a>';
menu[1]='<a onclick="javascript:cdelete(' + m_id + '); return(false)" href="?mod=iptools&action=find&ip={$ip}&doaction=dodelcomments&id=' + m_id + '" >{$lang['comm_del']}</a>';

return menu;
}
//-->
</script>
<table width="100%">
    <tr>
        <td width="170" style="padding:2px;">{$lang['user_name']}</td>
        <td width="110" style="padding:2px;">IP</td>
        <td width="130">{$lang['user_reg']}</td>
        <td width="130">{$lang['user_last']}</td>
        <td width="60">{$lang['user_news']}</td>
        <td width="120" align="center">{$lang['user_coms']}</td>
        <td>{$lang['user_acc']}</td>
    </tr>
	<tr><td colspan="7"><div class="hr_line"></div></td></tr>
HTML;

	$db->query("SELECT u.news_num, u.name, u.comm_num, u.user_id, u.banned, u.user_group, u.reg_date, u.lastdate, p.ip FROM " . USERPREFIX . "_users u, " . PREFIX . "_gallery_picturies p WHERE u.user_id=p.user_id AND p.ip LIKE '{$ip}%' GROUP BY u.name");

	$i = 0;

	while($row = $db->get_row()){ $i++;

		if ($row[news_num] == 0)
			$news_link = $row['news_num'];
		else
			$news_link = "[<a href=\"{$config['http_home_url']}index.php?subaction=userinfo&user=".urlencode($row['name'])."\" target=\"_blank\">".$row[news_num]."</a>]";

		if ($row[comm_num] == 0)
			$comms_link = $row['comm_num'];
		else
			$comms_link = "[<a onclick=\"return dropdownmenu(this, event, MenuBuild('".$row['user_id']."'), '150px')\" href=\"#\" >".$row[comm_num]."</a>]";

		if ($row['banned'] == 'yes')
			$group = "<font color=\"red\">".$lang['user_ban']."</font>";
		else
			$group = $user_group[$row['user_group']]['group_name'];

		$user_edit = $user_group[$member_id['user_group']]['admin_editusers'] ? "<a class=maintitle onclick=\"javascript:popupedit('$row[user_id]'); return(false)\" href=\"#\">{$row['name']}</a>" : $row['name'];

		echo"<tr>
		<td style=\"padding:3px\">{$user_edit}</td>
		<td><a onclick=\"return dropdownmenu(this, event, IPMenu('".$row['ip']."', '".$langGal['ip_info']."', '".$langGal['ip_tools']."', '".$langGal['ip_ban']."'), '190px')\" onMouseout=\"delayhidemenu()\" href=\"http://www.nic.ru/whois/?ip={$row['ip']}\" target=\"_blank\">{$row['ip']}</a></td>
		<td>".langdate("d/m/Y - H:i",$row['reg_date'])."</td>
		<td>".langdate('d/m/Y - H:i',$row['lastdate'])."</td>
		<td align=\"center\">".$news_link."</td>
		<td align=\"center\">".$comms_link."</td>
		<td>".$group."</td>
		</tr>
		<tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=7></td></tr>";

	}

	$db->free();

	if($i == 0){ echo"<tr><td height=18 colspan=7><p align=center><br><b>{$langGal['ip_empty']}<br><br></b></td></tr>"; }

	echo "</table>";

	galFooter();


	galHeader($langGal['menu_find1']);

echo <<<HTML
<table width="100%">
    <tr>
        <td width="170" style="padding:2px;">{$lang['user_name']}</td>
        <td width="110" style="padding:2px;">IP</td>
        <td width="130">{$lang['user_reg']}</td>
        <td width="130">{$lang['user_last']}</td>
        <td width="60">{$lang['user_news']}</td>
        <td width="120" align="center">{$lang['user_coms']}</td>
        <td>{$lang['user_acc']}</td>
    </tr>
	<tr><td colspan="7"><div class="hr_line"></div></td></tr>
HTML;

	$db->query("SELECT u.news_num, u.name, u.comm_num, u.user_id, u.banned, u.user_group, u.reg_date, u.lastdate, c.ip FROM " . USERPREFIX . "_users u, " . PREFIX . "_gallery_comments c WHERE u.user_id=c.user_id AND c.ip LIKE '{$ip}%' GROUP BY u.user_id");

	$i = 0;

	while($row = $db->get_row()){ $i++;

		if ($row[news_num] == 0)
			$news_link = $row['news_num'];
		else
			$news_link = "[<a href=\"{$config['http_home_url']}index.php?subaction=userinfo&user=".urlencode($row['name'])."\" target=\"_blank\">".$row[news_num]."</a>]";

		if ($row[comm_num] == 0)
			$comms_link = $row['comm_num'];
		else
			$comms_link = "[<a onclick=\"return dropdownmenu(this, event, MenuBuild('".$row['user_id']."'), '150px')\" href=\"#\" >".$row[comm_num]."</a>]";

		if ($row['banned'] == 'yes')
			$group = "<font color=\"red\">".$lang['user_ban']."</font>";
		else
			$group = $user_group[$row['user_group']]['group_name'];

		$user_edit = $user_group[$member_id['user_group']]['admin_editusers'] ? "<a class=maintitle onclick=\"javascript:popupedit('$row[user_id]'); return(false)\" href=\"#\">{$row['name']}</a>" : $row['name'];

		echo"<tr>
		<td style=\"padding:3px\">{$user_edit}</td>
		<td><a onclick=\"return dropdownmenu(this, event, IPMenu('".$row['ip']."', '".$langGal['ip_info']."', '".$langGal['ip_tools']."', '".$langGal['ip_ban']."'), '190px')\" onMouseout=\"delayhidemenu()\" href=\"http://www.nic.ru/whois/?ip={$row['ip']}\" target=\"_blank\">{$row['ip']}</a></td>
		<td>".langdate("d/m/Y - H:i",$row['reg_date'])."</td>
		<td>".langdate('d/m/Y - H:i',$row['lastdate'])."</td>
		<td align=\"center\">".$news_link."</td>
		<td align=\"center\">".$comms_link."</td>
		<td>".$group."</td>
		</tr>
		<tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=7></td></tr>";

	}

	$db->free();

	if($i == 0){ echo"<tr><td height=18 colspan=7><p align=center><br><b>{$langGal['ip_empty']}<br><br></b></td></tr>"; }

	echo "</table>";

	galFooter();


	galHeader($langGal['menu_find3']);

echo <<<HTML
<table width="100%">
    <tr>
        <td width="170" style="padding:2px;">{$lang['user_name']}</td>
        <td width="110" style="padding:2px;">IP</td>
        <td width="130">{$lang['user_reg']}</td>
        <td width="130">{$lang['user_last']}</td>
        <td width="60">{$lang['user_news']}</td>
        <td width="120" align="center">{$lang['user_coms']}</td>
        <td>{$lang['user_acc']}</td>
    </tr>
	<tr><td colspan="7"><div class="hr_line"></div></td></tr>
HTML;

	$db->query("SELECT u.news_num, u.name, u.comm_num, u.user_id, u.banned, u.user_group, u.reg_date, u.lastdate, c.ip FROM " . USERPREFIX . "_users u, " . PREFIX . "_comments c WHERE u.user_id=c.user_id AND c.ip LIKE '{$ip}%' GROUP BY u.user_id");

	$i = 0;

	while($row = $db->get_row()){ $i++;

		if ($row[news_num] == 0)
			$news_link = $row['news_num'];
		else
			$news_link = "[<a href=\"{$config['http_home_url']}index.php?subaction=userinfo&user=".urlencode($row['name'])."\" target=\"_blank\">".$row[news_num]."</a>]";

		if ($row[comm_num] == 0)
			$comms_link = $row['comm_num'];
		else
			$comms_link = "[<a onclick=\"return dropdownmenu(this, event, MenuBuild('".$row['user_id']."'), '150px')\" href=\"#\" >".$row[comm_num]."</a>]";

		if ($row['banned'] == 'yes')
			$group = "<font color=\"red\">".$lang['user_ban']."</font>";
		else
			$group = $user_group[$row['user_group']]['group_name'];

		$user_edit = $user_group[$member_id['user_group']]['admin_editusers'] ? "<a class=maintitle onclick=\"javascript:popupedit('$row[user_id]'); return(false)\" href=\"#\">{$row['name']}</a>" : $row['name'];

		echo"<tr>
		<td style=\"padding:3px\">{$user_edit}</td>
		<td><a onclick=\"return dropdownmenu(this, event, IPMenu('".$row['ip']."', '".$langGal['ip_info']."', '".$langGal['ip_tools']."', '".$langGal['ip_ban']."'), '190px')\" onMouseout=\"delayhidemenu()\" href=\"http://www.nic.ru/whois/?ip={$row['ip']}\" target=\"_blank\">{$row['ip']}</a></td>
		<td>".langdate("d/m/Y - H:i",$row['reg_date'])."</td>
		<td>".langdate('d/m/Y - H:i',$row['lastdate'])."</td>
		<td align=\"center\">".$news_link."</td>
		<td align=\"center\">".$comms_link."</td>
		<td>".$group."</td>
		</tr>
		<tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=7></td></tr>";

	}

	$db->free();

	if($i == 0){ echo"<tr><td height=18 colspan=7><p align=center><br><b>{$langGal['ip_empty']}<br><br></b></td></tr>"; }

	echo "</table>";

	galFooter();


} elseif ($act == 40){

	galHeader($langGal['menu_find2']);

echo <<<HTML
<table width="100%">
    <tr>
        <td style="padding:2px;" height="70">
HTML;

   $row = $db->super_query("SELECT user_id, name, logged_ip FROM " . USERPREFIX . "_users WHERE name='".$name."'");

   if (!$row['user_id']) {

		echo "<center><b>".$lang['user_nouser']."</b></center>";

   } else {

		echo $lang['user_name']." <b>".$row['name']."</b><br /><br />".$langGal['opt_iptoollast']." <b><a onclick=\"return dropdownmenu(this, event, IPMenu('".$row['logged_ip']."', '".$langGal['ip_info']."', '".$langGal['ip_tools']."', '".$langGal['ip_ban']."'), '190px')\" onMouseout=\"delayhidemenu()\" href=\"http://www.nic.ru/whois/?ip={$row['logged_ip']}\" target=\"_blank\">{$row['logged_ip']}</a></b><br /><br />".$langGal['opt_iptoolcall3']." <b>";

	    $db->query("SELECT ip FROM " . PREFIX . "_gallery_picturies WHERE picture_user_name = '{$name}' AND ip != '' GROUP BY ip");

		$ip_list = array();

		while($row = $db->get_row()){
			$ip_list[] = "<a onclick=\"return dropdownmenu(this, event, IPMenu('".$row['ip']."', '".$langGal['ip_info']."', '".$langGal['ip_tools']."', '".$langGal['ip_ban']."'), '190px')\" onMouseout=\"delayhidemenu()\" href=\"http://www.nic.ru/whois/?ip={$row['ip']}\" target=\"_blank\">{$row['ip']}</a>";
		}

		$db->free();

		echo implode (", ", $ip_list);


		echo "</b><br /><br />".$langGal['opt_iptoolcall2']." <b>";

	    $db->query("SELECT ip FROM " . PREFIX . "_gallery_comments WHERE user_id = '{$row['user_id']}' AND ip != '' GROUP BY ip");

		$ip_list = array();

		while($row = $db->get_row()){
			$ip_list[] = "<a onclick=\"return dropdownmenu(this, event, IPMenu('".$row['ip']."', '".$langGal['ip_info']."', '".$langGal['ip_tools']."', '".$langGal['ip_ban']."'), '190px')\" onMouseout=\"delayhidemenu()\" href=\"http://www.nic.ru/whois/?ip={$row['ip']}\" target=\"_blank\">{$row['ip']}</a>";
		}

		$db->free();

		echo implode (", ", $ip_list);


		echo "</b><br /><br />".$langGal['opt_iptoolcall']." <b>";

	    $db->query("SELECT ip FROM " . PREFIX . "_comments WHERE user_id = '{$row['user_id']}' AND ip != '' GROUP BY ip");

		$ip_list = array();

		while($row = $db->get_row()){
			$ip_list[] = "<a onclick=\"return dropdownmenu(this, event, IPMenu('".$row['ip']."', '".$langGal['ip_info']."', '".$langGal['ip_tools']."', '".$langGal['ip_ban']."'), '190px')\" onMouseout=\"delayhidemenu()\" href=\"http://www.nic.ru/whois/?ip={$row['ip']}\" target=\"_blank\">{$row['ip']}</a>";
		}

		$db->free();

		echo implode (", ", $ip_list);

   }

echo <<<HTML
	</b></td>
    </tr>
</table>
HTML;

	galFooter();

}

$twsg->galsupport50();
echofooter();

?>