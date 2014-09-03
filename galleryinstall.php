<?PHP
/*
=====================================================
 DataLife Engine - by SoftNews Media Group
 TWS Gallery - by Al-x
-----------------------------------------------------
 http://inker.wonderfullife.ru/
-----------------------------------------------------
 Copyright (c) 2007-2011 TWS
=====================================================
 ������ ��� ������� ���������� �������
 This file may no be redistributed in whole or significant part.	
 ���� �� ����� ���� ������ ��� ����������� ��� ������� �������� ������
 ����������� ������������� ����� � ������ ������������� �����
=====================================================
 ����: galleryinstall.php
-----------------------------------------------------
 ����������: ������� ��������
=====================================================
*/

error_reporting(E_ALL ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
@ini_set('error_reporting', E_ALL ^ E_NOTICE);

define('DATALIFEENGINE', true);
define('ROOT_DIR', dirname (__FILE__));
define('ENGINE_DIR', ROOT_DIR.'/engine');
define('FOTO_DIR', ROOT_DIR.'/uploads/gallery');
$_D = ROOT_DIR; $_F = ENGINE_DIR;

include ENGINE_DIR . '/data/config.php';

$js_array = array ();

require_once(ROOT_DIR.'/language/Russian/adminpanel.lng');
require_once(ENGINE_DIR.'/inc/include/functions.inc.php');
require_once(ENGINE_DIR.'/skins/default.skin.php');
require_once(ENGINE_DIR.'/classes/mysql.php');
require_once(ENGINE_DIR.'/data/dbconfig.php');
require_once(ENGINE_DIR.'/gallery/acp/functions.admin.php');

extract($_REQUEST, EXTR_SKIP);

$new_version = "5.2";

$db->query("SHOW TABLES LIKE '" . PREFIX . "_gallery_config'");
$found = false;
while ($row = $db->get_row()){ $found = true; }

if (@file_exists(ENGINE_DIR.'/data/gallery.config.php') || $found) { 

echoheader("", "");
galHeader("��������� ������� ������������� �������������");

echo <<<HTML
<form method=POST action="">
<table width="100%">
    <tr>
        <td style="padding:2px;">��������, �� ������� ���������� ��� ������������� ����� TWS Gallery. ���� �� ������ ��� ��� ���������� ��������� �������, �� ��� ���������� ������� ������� ���� <b>/engine/data/gallery.config.php</b> (���� �� ����������), ��������� FTP ��������, � ��� �� ������� ������� <b>_gallery_config</b> �� ���� ������ (���� ��� ����������). ��� ���� ��� ������������ ������ ����� ����������.<br /><br /></td>
    </tr>
    <tr>
        <td style="padding:2px;"><input class=buttons type=submit value=" �������� "></td>
    </tr>
</table>
</form>
HTML;

galFooter();
echofooter();

die ();
}

if (!$_REQUEST['action']){

// ********************************************************************************
// �����������
// ********************************************************************************
echoheader("", "");
galHeader("������ ��������� �������");

$last_news = @file_get_contents("http://inker.wonderfullife.ru/extras/updates.php?script=twsg&install=1&dle=".$config['version_id']."&version=".$new_version."&host=".$_SERVER['HTTP_HOST']);

echo <<<HTML
<form method=POST action="{$PHP_SELF}">
<table width="100%">
    <tr>
        <td style="padding:2px;">����� ���������� � ������ ��������� <b>TWS Gallery {$new_version}</b>. ������ ������ ������� ��� ���������� ������ ����� �� ���� �����. ������, �� ������ �� ���, �� ������������ ����������� ��� ������������ � ������������� �� ��� ���������, ������� ������������ ������ �� ��������.<br><br>
������ ��� ������ ��������� ���������, ��� ��� ����� ������������ ��������� �� ������, � ����� ���������� ����������� ����� ������� ��� ����� � ������. ����� ����������� ������ ������ � ������������ � ����������� ��� ������ �������� ���� ����� ������ ������������ � ������.<br><br>
<font color="red">��������: ���� ����� ��������� �������, ��� �� ����������� ��� �������� �� ��������� - <b>�� ����� ����� � ����� ��������� ���� galleryinstall.php</b>, �.�. ������ ���� ������������ ������ ��� �������� ������ ������� � ���� ������! ��� �������, ������������������� ������� ��������� ���������� ������ ������. �� ������ ������ ������� �� ��������� ������� �� ������ ��������� inker.wonderfullife.ru!</font><br><br>
�������� ��� ������,<br><br>
Al-x by TWS<br><br></td>
    </tr>
    <tr>
        <td style="padding:2px;"><input type=hidden name=action value="eula"><input class=buttons type=submit value="������ ���������"></td>
    </tr>
</table>
</form>
HTML;

galFooter();
echofooter();

} elseif($_REQUEST['action'] == "eula"){

echoheader("", "");

galHeader("������������ ����������");

echo <<<HTML
<form id="check-eula" method="post" action="">
<script language='javascript'>
check_eula = function()
{
	if( document.getElementById( 'eula' ).checked == true )
	{
		return true;
	}
	else
	{
		alert( '�� ������ ������� ������������ ����������, ������ ��� ���������� ���������.' );
		return false;
	}
}
document.getElementById( 'check-eula' ).onsubmit = check_eula;
HTML;
echo "</script>";

$lic_text = <<<HTML
<!--sizestart:3--><span style="font-size:12pt;line-height:100%"><!--/sizestart-->������������ ���������������� ����������<!--sizeend--></span><!--/sizeend--><br /><br /><b>������� ������������� ����������</b><br /><br />��������� ���������� ������������� ���������� �������� ����� �� ������������� ����� ������������ ����� ������������ �������� <b>TWS Gallery</b> ��������� � <b>DataLife Engine</b>, � ������� � �� ��������, ������������� ��������� �����������. ���� �� �� �������� � ��������� ������� ����������, �� �� ������ ������������ ������ �������. ��������� � ������������� �������� �������� ���� ������ �������� �� ����� �������� ���������� ����������.<br /><br /><b>������ ������������� ����������</b><br /><br />������������ ���������� �� ������ <b>TWS Gallery</b> ���������� �� ������ ������������� ���������� �� ����������� ������� <b>DataLife Engine</b>, �������������� �� ����������� ����� <b>DataLife Engine</b>. ������������ ���������� �� <b>TWS Gallery</b> �� ������ � ������������ � ������������ ����������� �� <b>DataLife Engine</b> � ����� �� �������� ���� ���������� ���������� <b>SoftNews Media Group</b>. ������ � ���� ������������ ���������� �� <b>TWS Gallery</b> �������� ��������������� ����������� � ����� �� ������� �� <b>SoftNews Media Group</b> � ������ ����������. ���������� ������� ������������� ���������� �������� ��������.<br /><br /><b>���������� ��������</b><br /><br />���� ������������ ������� � ������� ������������ ����� ������������ ����� ������������ �������� <b>TWS Gallery</b> ��������� � <b>DataLife Engine</b> �� ������� �������� ����� <b>������ ����</b>. ���� �� ��������� ����� ������������, �� ������ �� ���������� ��� ��������, ��� ����������� ������� ����� ��������������� � ������ ������, �� ��� ����� ����������� ��������� � ��� �������������� ����� ������ �������, �� ����������� ����������� ���������� ������������ �������, � ��� �� ��� �������� ������������� � ������ �������� <b>DataLife Engine</b>.<br /><br />� ������ ������������ � ������������� ������� �������� �� ������, ������ ����� ����� ������ �� ����������� ������� ����� ����� ������ ����������� ��������� TWS Gallery.  � ����������� ����� ������: �������������� �������������, ����� ������ �������, ����������� ���������� �������. ������������ �������������� ����������� ������������ �� ������� ��������� �� �������������. ������ �������� ���������� ������������ �� ������ TWS Gallery �� ����� ������ ���������. ��� �� ������� �������� ������������� ������������ ������� ������ �� ���� <b>wonderfullife.ru</b> �� ������� �������� �������. ��� ��������� ���������� �������������� <a href="http://wonderfullife.ru/14-tehnicheskaya-podderzhka.html" >����������� ���������</a> �� ������� ��� �������� �������������� ������, ������������� ���������� ���������� ����������� ��������, ���������� � ���� ������ ����� ����� ������ ����������� ���������, ���� ����� ����������� ���������� ������ ����������� ���������.<br /><br />�� ��������� �� ����� ����� ����������� ������ ��������� ������������� ����� ����������� ���������. �� ��������� �� ����� ����� � ����� ����� �������� ������� ������� ��������, �� ������ �������� �� ����� �������� ����. ���������� � ����� ���������� ���������� �������� ����� ���������� ������������� �� ����������� ����� �� ������, ��������� ��� ������������ �������.<br /><br /><b>������������ �������������</b><br /><br />���������� �������� �� ����������� ������� <b>TWS Gallery</b>, �� ������ �����, ��� ������������ ������ <b>����� �� �������������</b> ������������ ��������, �� �� ��������� ����� �� ����. ��������� ����� �� ����������� ������� ����������� �������������� � ��������� �������� ������� <a href="http://wonderfullife.ru" >wonderfullife.ru</a> � �������� �������. �������� �� ����������� ������� <b>TWS Gallery</b> ������������ ������������� <b>TWS Gallery</b> �� ������������ ���-����� (����� ����� ������) � ��� ����������, ������������� ��� ��� ������ �������. ��� ������������� ������� �� ������ �����, ��� ���������� ����������� �������� ��������. ����������� ����������� ������� ������� �����, ���� �� �� ��������� ����������� � ������������ ������� ���������� �� ��� ����, �� ������� ���������� ����� �������� � ������ ������������ �����������. �� �� ���� ������� ������������ �� �� ����������� ��������� � ������������. ������ ����� ������ ��������� ������������� ������ ��� ���, ��� �������� � ���� ��������, � �� ����� ������� ���.  <br /><br /><b>����� � ����������� ������</b><br /><br /><b>���������� ������:</b><br /><br /><!--dle_list--><ul><li>������������ � ������������ ������������ ������������, ������������ ��� ����������� ���������������� �������, � ��������������, ��� ��������� ���������� ���������.</li></ul><!--dle_list_end--><br /><b>���������� ����� �����:</b><br /><br /><!--dle_list--><ul><li>�������� ������ � ��������� ������������ ���� � ������������ �� ������ ��������� � ��� ����� ������ �����.<br /></li><!--dle_li--><li>��������� � �������������� ���������� �� ����� ������������ ����� �������� � �������� ������, ��� �������, ��� � ����������� ����� �������������� �������� �� ������������� ������������ TWS Gallery, � ����������� ���������������� ����. ��� ���������� � ���������, �������� ���� � ��� ������� ��������������, �� �������� �������������� TWS, ���� �� �������� ����������� ���� ��������������� �������.<br /></li><!--dle_li--><li>��������� ������, ����������� � ����������������� � ������ ������������ ������, ������ � ���������, ��� ��� ���� ����������, ������������ ������� � ��������������� �� ��� ������ � ����������� ��������� ����� �� ���.<br /></li><!--dle_li--><li>���������� ����������� ������� �� ������ ���� ����� ���������������� � ������������� ����������� ��� � ��������, � ����� ������� �������� ������� � ����������� �����.</li></ul><!--dle_list_end--><br /><b>���������� �� ����� �����:</b><br /><br /><!--dle_list--><ul><li>���������� ����� �� ������������� ������������ �������� ������� �����.<br /></li><!--dle_li--><li>�������� ��������� ����������� �����, ������� ��������� � ����� �������� ��� ��������������� ����������� ���������.<br /></li><!--dle_li--><li>��������� ��������� ��������������� ��������, ������������ �� ����������� ���� ������� TWS Gallery. <br /></li><!--dle_li--><li>������������ ����� ������������ �������� TWS Gallery �� ����� �������� �� ����� ��� ����� ����� (����� ����� ������ � ��� ����������)<br /></li><!--dle_li--><li>�������������, ��������� ��� �����������, �������������� ��� ������������� ��������������� �������������� ����� ������������ �������� TWS Gallery � DataLife Engine<br /></li><!--dle_li--><li>������� ����������� ����������, �������������� �������� ������� �� ����� ������������ �������� �� ������������� �������.</li></ul><!--dle_list_end--><br /><b>����������� ����������� ������������</b><br /><br />���������� ������������, ������������� �� <b>TWS Gallery</b>, ����� ��� �����������. � �������� �� ��, ��� �� ��������� ������������ ������ �� ����������� ������������ �������, �� ������ ��������, ��� ���������� ������ �� ������ ������ ����� �� ����������. ���� �������� � ����������� ��������� �� ���������������� ����� �� ����������� �������� � ����� ��������� <b>TWS Gallery</b>, ������������� ����������� �������� ��� ������� ��������, ������� ��������� ������������ ����, �����, �������� �������. ���� �������� - ���� ������������. �� �������� - ����������� ������� ������, ������������ � ��� ������ � ������� ����� ������ ���������. ����������� ������� <b>TWS Gallery</b> �� �������� �������� ��� ������ ��-�� ���������� ��������, ���������� ����������� ������� �� �����������, � ��� �� ��-�� �������������� ����� �������� � �� ����������� ����������� �����������, ���������� <a href="http://wonderfullife.ru/11-sistemnye-trebovaniya.html" >�� ������ ������</a> � ����������� ��� ����������� ���������������� �������.<br /><br /><b>��������� ���������</b><br /><br />����������� ������� <b>TWS Gallery</b> ����� ��������� �������� ������������ � ����������� ��������. �� ������� ��������� �������� ���������������� ������� �����������. ��� ���������� ��������������� ���� ������������� ����� (��������� ��� ������������ ��������) � ��������������� ���� � ���������������� ������. ��������� ��������� <b>TWS Gallery</b> ����� ������������� � ������ ������ �������������� ����� ��� ���������� �������, ������ ��������� ���� ������ <b>��</b> ������� � ������ ����� �� ������������� ��������. <b>��� ���������� ��������� ����������� ������� ��������� �� ��� �����, ��� ������������ ��������� TWS Gallery.</b> � ������ ������������� ��������� ����� �������� ������������� �������� �������� � ������ �������� ���������� � ��������������� ������������, ������� ������������� ��������.<br /><br /><b>����� �� ���������������� �������������</b><br /><br />������������ <b>TWS Gallery</b>, � ����� �������� � ������ ������� �������, �������� �������������� <b>TWS</b>, �� ����������� �������, ����� ��� ���������� ������� ����������� ������ ��� ��������. ����������� ������� ������� �������. ����� ����������� ������������ ���������, ����������� �� ���� ������������ ���� ������ �������, �������� �������������� �������������� � ��������� �������� ������� <a href="http://wonderfullife.ru" >wonderfullife.ru</a> � �������� �������. <b>TWS</b> �� ����� ������� ��������������� �� ���������� ������, ����������� ������������� ������� <b>TWS Gallery</b>.<br /><br /><b>��������� ����������� ���������� ������������</b><br /><br />���������� ������������ ������������� ��� ��������� ������� ������� �������� ��� ������ �� ���������� ������������ �� ������������ ��������. ������������ ���������� ����� ���� ����������� ���� � ������������� �������, � ������ ������������ ������ ��������� ������� ������� ������������� ����������. � ������ ���������� ����������� ��������, �� ���������� ������� ��� ���� ����� ������ ������������ �������� � ������� 3 ������� ���� � ������� ��������� ���������������� �����������.
HTML;

$lic_text = str_replace("SoftNews Media Group", "SoftNews&nbsp;Media&nbsp;Group", $lic_text);
$lic_text = str_replace("DataLife Engine", "DataLife&nbsp;Engine", $lic_text);
$lic_text = str_replace("TWS Gallery", "TWS&nbsp;Gallery", $lic_text);
$lic_text = str_replace("  ", " ", $lic_text);
$lic_text = str_replace("<a", "<a target=\"_blank\"", $lic_text);

$lic_text = explode(" ", $lic_text);
$lic_text_count = count($lic_text);
$lic_text_str = "";

for ($i=0;$i<$lic_text_count;$i++){
	$lic_text_str .= $lic_text[$i];
	$lic_text_str .= (strlen($lic_text[$i]) < 4 && strpos($lic_text[$i], "<") === false && strpos($lic_text[$i], ">") === false) ? "&nbsp;" : " ";
}

echo <<<HTML
<table width="100%">
    <tr>
        <td style="padding:2px;">����������, ����������� ���������� � ������� ���������������� ���������� �� ������������� TWS Gallery ��������� � DataLife Engine.<br /><br /><div style="height: 300px; border: 1px solid #76774C; background-color: #FDFDD3; padding: 5px; overflow: auto;">{$lic_text_str}</div>
		<input type='checkbox' name='eula' id='eula'><label for="eula"><b>� �������� ������ ����������</b></label>
		<br />
</td>
    </tr>
    <tr>
        <td style="padding:2px;"><input type=hidden name=action value="version_check"><input class=buttons type=submit value=" ���������� >> "></td>
    </tr>
</table>
</form>
HTML;

} elseif ($_REQUEST['action'] == "version_check"){

// ********************************************************************************
// �������� ������ ������
// ********************************************************************************

	if (!in_array($config['version_id'], array("9.2", "9.3", "9.4", "9.5", "9.6", "9.7", "9.8"))){

		echoheader("", "");
		galHeader("�������� ������ CMS");

		echo <<<HTML
<form method=POST action="{$PHP_SELF}">
<table width="100%">
	<tr>
		<td style="padding:2px;"><br><br><font color="red"><b>��������: ������ CMS DLE �� ������������ ��������� ��� ���������� ������ �������! �� ������ ���������� ��������� �� ���� ����� � ����, ������ ����������������� ������� �� �������������! ��� �� ������������ ������� ������� �� ���������� �������� ������ ��� ��������� ���������� ������ �� ����� ������ cms. � ������, ���� ������������ �������, ��� ��������������� ������ ������� ����� �������� �� ����� ������ ������, ����������, �������� �� ���� ������ �������������!</b></font><br><br>
�������� ��� ������,<br><br>
Al-x by TWS</td>
	</tr>
	<tr>
		<td style="padding:2px;"><input type=hidden name=action value="chmod_check"><input class=buttons type=submit value="���������� ���������"></td>
	</tr>
</table>
</form>
HTML;

		galFooter();
		echofooter();

	} else {

		@header("Location: ".$PHP_SELF."?action=chmod_check");
		die();

	}

} elseif ($_REQUEST['action'] == "chmod_check"){

echoheader("", "");
galHeader("�������� �� ������ � ������ ������ �������");

echo <<<HTML
<form method=POST action="$PHP_SELF">
<table width="100%">
HTML;

echo"<tr>
<td height=\"25\">&nbsp;�����/����
<td width=\"100\" height=\"25\">&nbsp;CHMOD
<td width=\"100\" height=\"25\">&nbsp;������</tr><tr><td colspan=3><div class=\"hr_line\"></div></td></tr>";
 
$important_files = array(
'./engine/data/',
'./engine/gallery/cache/',
'./engine/gallery/cache/system/',
'./uploads/',
'./uploads/gallery/',
'./uploads/gallery/caticons/',
'./uploads/gallery/comthumb/',
'./uploads/gallery/main/',
'./uploads/gallery/thumb/',
'./uploads/gallery/temp/',
);


$chmod_errors = 0;
$not_found_errors = 0;
    foreach($important_files as $file){

        if(!file_exists($file)){
            $file_status = "<font color=red>�� ������!</font>";
            $not_found_errors ++;
        }
        elseif(is_writable($file)){
            $file_status = "<font color=green>���������</font>";
        }
        else{
            @chmod($file, 0777);
            if(is_writable($file)){
                $file_status = "<font color=green>���������</font>";
            }else{
                @chmod("$file", 0755);
                if(is_writable($file)){
                    $file_status = "<font color=green>���������</font>";
                }else{
                    $file_status = "<font color=red>���������</font>";
                    $chmod_errors ++;
                }
            }
        }
        $chmod_value = @decoct(@fileperms($file)) % 1000;

    echo"<tr>
         <td height=\"22\" class=\"tableborder main\">&nbsp;$file</td>
         <td>&nbsp; $chmod_value</td>
         <td>&nbsp; $file_status</td>
         </tr><tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=3></td></tr>";
    }
if($chmod_errors == 0 and $not_found_errors == 0){
$status_report = '�������� ������� ���������! ������ ���������� ���������!';
}else{
if($chmod_errors > 0){
$status_report = "<font color=red>��������!!!</font><br /><br />�� ����� �������� ���������� ������: <b>$chmod_errors</b>. ��������� ������ � ����.<br />�� ������ ��������� ��� ����� CHMOD 777, ��� ������ CHMOD 666, ��������� ���-������.<br /><br /><font color=red><b>������������ �� �������������</b></font> ���������� ���������, ���� �� ����� ����������� ���������.<br />";
}
if($not_found_errors > 0){
$status_report .= "<font color=red>��������!!!</font><br />�� ����� �������� ���������� ������: <b>$not_found_errors</b>. ����� �� �������!<br /><br /><font color=red><b>�� �������������</b></font> ���������� ���������, ���� �� ����� ����������� ���������.<br />";
}
}

echo"<tr><td colspan=3><div class=\"hr_line\"></div></td></tr><tr><td height=\"25\" colspan=3>&nbsp;&nbsp;��������� ��������</td></tr><tr><td style=\"padding: 5px\" colspan=3>$status_report</td></tr><tr><td colspan=3><div class=\"hr_line\"></div></td></tr>";    

echo <<<HTML
     <tr>
     <td height="40" colspan=3 align="right">&nbsp;&nbsp;
     <input class=buttons type=submit value="���������� >>">&nbsp;&nbsp;<input type=hidden name="action" value="function_check">
     </tr>
</table>
</form>
HTML;

galFooter();
echofooter();

} elseif ($_REQUEST['action'] == "function_check"){

echoheader("", "");
galHeader("�������� �������� �������");

	echo <<<HTML
<form method=POST action="$PHP_SELF">
<table width="100%">
HTML;

	echo"<tr>
<td height=\"25\" width=\"250\">&nbsp;����������� ���������� �������
<td height=\"25\" colspan=2>&nbsp;������� ��������
<tr><td colspan=3><div class=\"hr_line\"></div></td></tr>";

	$status = ini_get('file_uploads') ? '<font color=green><b>��������</b></font>' : '<font color=red><b>���������</b></font>';

	echo"<tr>
      <td height=\"22\" class=\"tableborder main\">&nbsp;�������� ������</td>
      <td>&nbsp;$status</td>
      </tr><tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=2></td></tr>";

	$status = (ini_get('safe_mode') == 1 || ini_get('safe_mode') == 'on') ? '<font color=red><b>��������</b></font>' : '<font color=green><b>���������</b></font>';

	echo"<tr>
      <td height=\"22\" class=\"tableborder main\">&nbsp;Safe Mode</td>
      <td>&nbsp;$status</td>
      </tr><tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=2></td></tr>";

	$handle = FOTO_DIR .'/temp/test_dirrectory';
	$test_image = FOTO_DIR .'/boxsmall.jpg';
	$error = false;

	if (@file_exists($test_image)){

		@mkdir($handle, 0777);
		@chmod($handle, 0777);
		@copy($test_image, $handle.'/boxsmall.jpg');
		@chmod($handle.'/boxsmall.jpg', 0666);

		$status = (@file_exists($handle.'/boxsmall.jpg') && !(@ini_get('safe_mode') == 1 || @ini_get('safe_mode') == 'on')) ? '<font color=green><b>�������</b></font>' : '<font color=red><b>������!</b> �� ������� ������� ����� � ����������� ����!</font>';

		@unlink($handle.'/boxsmall.jpg');
		@rmdir($handle);

	} else $status = '<font color=red><b>������!</b> �� ������ ���� /uploads/gallery/boxsmall.jpg</font>';

	echo"<tr>
      <td height=\"22\" class=\"tableborder main\">&nbsp;�������� ��������</td>
      <td>&nbsp;$status</td>
      </tr><tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=2></td></tr>";

	$status = !function_exists('gzopen') ? '<font color=red><b>���������</b></font>' : '<font color=green><b>��������</b></font>';;

	echo"<tr>
      <td height=\"22\" class=\"tableborder main\">&nbsp;Gzopen</td>
      <td>&nbsp;$status</td>
      </tr><tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=2></td></tr>";

	$maxupload = str_replace(array('M','m'), '', @ini_get('upload_max_filesize'));
	$maxupload = intval($maxupload);

	$status = ($maxupload && $maxupload < 2) ? '<font color=red><b>����������� �������� (����� 2 ��������)</b></font>' : '<font color=green><b>� ����� (����� 2 ��������)</b></font>';;

	echo"<tr>
      <td height=\"22\" class=\"tableborder main\">&nbsp;upload_max_filesize</td>
      <td>&nbsp;$status</td>
      </tr><tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=2></td></tr>";

	echo"<tr>
         <td colspan=2 class=\"navigation\"><br />���� ����� �� ���� ������� ������� �������, �� ���������� ��������� �������� ��� ����������� ���������. � ������ ������������ ����������� ���������� ������� �������� ��� ������������ ������ � �������.<br /><br /></td>
         </tr>";

	echo <<<HTML
     <tr>
     <td height="40" colspan=3 align="right">&nbsp;&nbsp;
     <input class=buttons type=submit value="���������� >>">&nbsp;&nbsp;<input type=hidden name="action" value="set_config">
     </tr>
	</table>
	</form>
HTML;

galFooter();
echofooter();

} elseif ($_REQUEST['action'] == "set_config"){

echoheader("", "");
galHeader("��������� ������������ �������");

echo <<<HTML
<form method=POST action="$PHP_SELF">
���� �� ������ ������� ��������� ���������, ������� ����� ������������� ��������� ��� ��������� �������. ��� ��������� ���� ����� ���� ������������ �������� � ���������� �������, �.�. ��� �������������, �� ������� ������� �� ������ ��� ���������, ������� �����, ��� ��������������� ���������. ��� ��������� ���� ���� ������ ��� ��������� �������� ���������, ������� ��� ����, ����� ��������� �������� ����� (��� ����� �������� ����) <b>�� �����</b> ��������� ������ ���������� �����.<br /><br />

<div class="unterline"></div>
<table width="100%">
HTML;

echo'<tr>
<td style="padding: 5px;">������� ������� ���������:
<td style="padding: 5px;">
<select class=rating name="create_cats"><option value="1">��</option><option value="0">���</option></select>&nbsp;&nbsp;<span class="navigation">E��� �� ������, ����� ������ ������ ��������� ��������� � �������� �������, ������ �������� ������ �����. ����� ������� ��������������������� �� �������� ��������� � ������� ����������� (� �������� ������� ��� ����� ������� ���������).</span>
</tr>
<tr>
<td style="padding: 5px;">�������� ��������� ����������� � ����:
<td style="padding: 5px;">
<select class=rating name="allow_video"><option value="1">��</option><option value="0">���</option></select>&nbsp;&nbsp;<span class="navigation">E��� �� ������, ����� ������ �������� �������� media ������, ������ �������� ������ �����. ���� ��������� �������� ������� ���������, �� ����� ������� ������ ��������� ��� �������� media ������.</span>
</tr>
<tr>
<td style="padding: 5px;">�������� ��������� �����������:
<td style="padding: 5px;">
<select class=rating name="allow_audio"><option value="1">��</option><option value="0">���</option></select>&nbsp;&nbsp;<span class="navigation">E��� �� ������, ����� ������ �������� �������� audio ������, ������ �������� ������ �����. ���� ��������� �������� ������� ���������, �� ����� ������� ������ ��������� ��� �������� audio ������.</span>
</tr>
<tr>
<td style="padding: 5px;">�������� ��������� rar � zip �������:
<td style="padding: 5px;">
<select class=rating name="allow_rarzip"><option value="1">��</option><option value="0">���</option></select>&nbsp;&nbsp;<span class="navigation">E��� �� ������, ����� ������ �������� �������� �������� ������, ������ �������� ������ �����. ���� ��������� �������� ������� ���������, �� ����� ������� ������ ��������� ��� �������� �������� ������.</span>
</tr>
<tr>
<td style="padding: 5px;">�������� �������� � �������� youtube.com, rutube.ru, video.mail.ru, vimeo.com, smotri.com, gametrailers.com:
<td style="padding: 5px;">
<select class=rating name="allow_youtube"><option value="1">��</option><option value="0">���</option></select>&nbsp;&nbsp;<span class="navigation">E��� �� ������, ����� ������ �������� �������� ������ � ��������� ��������, ������ �������� ������ �����. ���� ��������� �������� ������� ���������, �� ����� ������� ������ ��������� ��� ������ � ������ ��������.</span>
</tr>
<tr>
<td style="padding: 5px;">��������� ������������� ��������� ������������ � ��������� ����������:
<td style="padding: 5px;">
<select class=rating name="allow_user_create"><option value="1">��</option><option value="0">���</option></select>&nbsp;&nbsp;<span class="navigation">E��� �� ������, ����� ������������ ����� ��������� ��������� ��������� � ������������ �� ��������, ������ �������� ������ �����. � ���� ������ ����� ������� ��������� �������� ��������� ��� ������������� ����� ������.</span>
</tr>
<tr>
<td style="padding: 5px;">��������� ������������� ��������� ����� � ��������� ���������:
<td style="padding: 5px;">
<select class=rating name="allow_user_upload"><option value="1">��</option><option value="0">���</option></select>&nbsp;&nbsp;<span class="navigation">E��� �� ������, ����� ������������ ����� ��������� ����� � ��������� ���������, ������ �������� ������ �����.</span>
</tr>
<tr>
<td style="padding: 5px;">������������ ������������� ��� ������, ������� ��������� ������������ � ��������� ���������:
<td style="padding: 5px;">
<select class=rating name="moderate"><option value="1">��</option><option value="0">���</option></select>&nbsp;&nbsp;<span class="navigation">E��� �� ������, ����� ����������� ������������� ����� �������� �� ��������� ���������������, ������ �������� ������ �����.</span>
</tr>
<tr>
<td style="padding: 5px;">��������� ������������� ������� ��������� ������ ������������:
<td style="padding: 5px;">
<select class=rating name="allow_user_own"><option value="1">��</option><option value="0">���</option></select>&nbsp;&nbsp;<span class="navigation">E��� �� ������, ����� ������������ ��� ������� ��������� ������ ���������, ���� ����������� ��� ���������, � ��� �� ��������� � ��� ����� ��� �������������, ������ �������� ������ �����. � ���� ������ ����� ������ ������������� ������� ���������. ��������� ������ ����� <b>��</b> ����������� ����������� ������������� ��������� ������� � ��������� ����������, ���� ���� ������������ ����� �������� �������� ����� ���������.</span>
</tr>
<tr><td style="padding: 5px;">������������� ���������� ������ ��������� ��� ������ ������������:<td style="padding: 5px;"><input class="edit" type=text size="10" value="1" name="max_users_cats"></tr>
<tr>
<td style="padding: 5px;">��������� ��������������� � ������� ������:
<td style="padding: 5px;">
<select class=rating name="allow_comrate"><option value="1">��</option><option value="0">���</option></select>&nbsp;&nbsp;<span class="navigation">E��� �� ������, ����� ������������ ����� �������������� ����� � ���������� �������, ������ �������� ������ �����.</span>
</tr>
';

echo <<<HTML
     <tr>
     <td height="40" colspan=3 align="right">&nbsp;&nbsp;
     <input class=buttons type=submit value="���������� >>">&nbsp;&nbsp;<input type=hidden name="action" value="doinstall">
     </tr>
</table>
</form>
HTML;

galFooter();
echofooter();

} elseif ($_REQUEST['action'] == "doinstall"){

$tableSchema = array();

$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_gallery_banned";

$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_banned (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `users_id` mediumint(8) NOT NULL DEFAULT '0',
  `descr` text NOT NULL DEFAULT '',
  `date` varchar(20) NOT NULL DEFAULT '',
  `ip` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `user_id` (`users_id`),
  KEY `ip` (`ip`)
  ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";


$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_gallery_category";

$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_category (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `p_id` smallint(5) NOT NULL DEFAULT '0',
  `cat_title` varchar(255) NOT NULL DEFAULT '',
  `cat_short_desc` text NOT NULL DEFAULT '',
  `metatitle` varchar(255) NOT NULL DEFAULT '',
  `meta_descr` varchar(255) NOT NULL DEFAULT '',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `position` int(10) NOT NULL DEFAULT '0',
  `cat_alt_name` varchar(255) NOT NULL DEFAULT '',
  `user_name` varchar(40) NOT NULL DEFAULT '',
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `disable_upload` tinyint(1) NOT NULL DEFAULT '0',
  `reg_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_cat_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `images` mediumint(8) NOT NULL DEFAULT '0',
  `cat_images` int(10) NOT NULL DEFAULT '0',
  `all_time_images` mediumint(8) NOT NULL DEFAULT '0',
  `view_level` varchar(100) NOT NULL DEFAULT '',
  `upload_level` varchar(100) NOT NULL DEFAULT '',
  `comment_level` varchar(100) NOT NULL DEFAULT '',
  `edit_level` varchar(100) NOT NULL DEFAULT '',
  `mod_level` varchar(100) NOT NULL DEFAULT '',
  `moderators` varchar(100) NOT NULL DEFAULT '',
  `foto_sort` varchar(18) NOT NULL DEFAULT '',
  `foto_msort` varchar(5) NOT NULL DEFAULT '',
  `allow_rating` tinyint(1) NOT NULL DEFAULT '1',
  `allow_comments` tinyint(1) NOT NULL DEFAULT '1',
  `allow_watermark` tinyint(1) NOT NULL DEFAULT '1',
  `icon` varchar(100) NOT NULL DEFAULT '',
  `icon_picture_id` int(10) NOT NULL DEFAULT '0',
  `icon_max_size` varchar(10) NOT NULL DEFAULT '',
  `subcats_td` smallint(4) NOT NULL DEFAULT '0',
  `subcats_tr` smallint(4) NOT NULL DEFAULT '0',
  `foto_td` smallint(4) NOT NULL DEFAULT '0',
  `foto_tr` smallint(4) NOT NULL DEFAULT '0',
  `auto_resize` tinyint(1) NOT NULL DEFAULT '1',
  `skin` varchar(50) NOT NULL DEFAULT '',
  `subcatskin` varchar(50) NOT NULL DEFAULT '',
  `maincatskin` varchar(50) NOT NULL DEFAULT '',
  `smallfotoskin` varchar(50) NOT NULL DEFAULT '',
  `bigfotoskin` varchar(50) NOT NULL DEFAULT '',
  `width_max` smallint(4) NOT NULL DEFAULT '0',
  `height_max` smallint(4) NOT NULL DEFAULT '0',
  `com_thumb_max` varchar(10) NOT NULL DEFAULT '',
  `thumb_max` varchar(10) NOT NULL DEFAULT '',
  `size_factor` smallint(3) NOT NULL DEFAULT '0',
  `allowed_extensions` varchar(250) NOT NULL DEFAULT '',
  `exprise_delete` smallint(4) NOT NULL DEFAULT '0',
  `allow_user_admin` tinyint(1) NOT NULL DEFAULT '0',
  `sub_cats` mediumint(8) NOT NULL DEFAULT '0',
  `allow_carousel` tinyint(1) NOT NULL DEFAULT '1',
  `uploadskin` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `view_level` (`view_level`),
  KEY `p_id` (`p_id`,`view_level`),
  KEY `position` (`position`),
  KEY `cat_title` (`cat_title`),
  KEY `cat_alt_name` (`cat_alt_name`)
  ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";


$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_gallery_comments";

$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_comments (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL DEFAULT '0',
  `user_id` mediumint(8) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `autor` varchar(40) NOT NULL DEFAULT '',
  `email` varchar(40) NOT NULL DEFAULT '',
  `text` text NOT NULL DEFAULT '',
  `ip` varchar(16) NOT NULL DEFAULT '',
  `approve` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`)
  ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";


$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_gallery_comments_subscribe";

$tableSchema[] = "CREATE TABLE IF NOT EXISTS " . PREFIX . "_gallery_comments_subscribe (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL  DEFAULT '0',
  `user_id` mediumint(8) NOT NULL  DEFAULT '0',
  `gast_email` varchar(50) NOT NULL  DEFAULT '',
  `flag` tinyint(1) NOT NULL DEFAULT '1',
  `date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `file_id` (`file_id`,`flag`),
  KEY `user_id` (`user_id`)
  ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";

$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_gallery_config";

$tableSchema[] = "CREATE TABLE IF NOT EXISTS " . PREFIX . "_gallery_config (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(75) NOT NULL DEFAULT '',
  `value` text NOT NULL DEFAULT '',
  `type` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `type` (`type`)
  ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";

$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_gallery_flood";

$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_flood (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_key` int(12) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `member_key` (`member_key`,`date`)
  ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";


$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_gallery_logs";

$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_logs (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pic_id` int(10) NOT NULL DEFAULT '0',
  `member_key` varchar(12) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pic_id` (`pic_id`,`member_key`)
  ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";

$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_gallery_picture_views";

$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_picture_views (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `picture_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
  ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";

$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_gallery_picturies";

$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_picturies (
  `picture_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `picture_title` varchar(255) NOT NULL DEFAULT '',
  `picture_alt_name` varchar(50) NOT NULL DEFAULT '',
  `image_alt_title` varchar(255) NOT NULL DEFAULT '',
  `text` text NOT NULL DEFAULT '',
  `tags` varchar(255) NOT NULL DEFAULT '',
  `posi` int(10) NOT NULL DEFAULT '1',
  `picture_filname` varchar(100) NOT NULL DEFAULT '',
  `preview_filname` varchar(100) NOT NULL DEFAULT '',
  `media_type` tinyint(2) NOT NULL DEFAULT '0',
  `md5_hash` varchar(32) NOT NULL DEFAULT '',
  `full_link` text NOT NULL DEFAULT '',
  `type_upload` tinyint(2) NOT NULL DEFAULT '0',
  `size` int(10) NOT NULL DEFAULT '0',
  `width` smallint(6) NOT NULL DEFAULT '0',
  `height` smallint(6) NOT NULL DEFAULT '0',
  `picture_user_name` varchar(40) NOT NULL DEFAULT '',
  `user_id` mediumint(8) NOT NULL DEFAULT '0',
  `email` varchar(50) NOT NULL DEFAULT '',
  `ip` varchar(16) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `category_id` smallint(5) NOT NULL DEFAULT '0',
  `file_views` mediumint(8) NOT NULL DEFAULT '0',
  `allow_comms` tinyint(1) NOT NULL DEFAULT '1',
  `allow_rate` tinyint(1) NOT NULL DEFAULT '1',
  `comments` smallint(5) NOT NULL DEFAULT '0',
  `rating` smallint(5) NOT NULL DEFAULT '0',
  `vote_num` smallint(5) NOT NULL DEFAULT '0',
  `approve` tinyint(1) NOT NULL DEFAULT '1',
  `symbol` varchar(10) NOT NULL DEFAULT '',
  `has_text` tinyint(1) NOT NULL DEFAULT '0',
  `logs` text NOT NULL DEFAULT '',
  `edit_reason` varchar(250) NOT NULL DEFAULT '',
  `editor` varchar(40) NOT NULL DEFAULT '',
  `session_id` varchar(32) NOT NULL DEFAULT '',
  `downloaded` mediumint(8) NOT NULL DEFAULT '0',
  `thumbnails` varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`picture_id`),
  KEY `posi` (`posi`),
  KEY `picture_user_name` (`picture_user_name`),
  KEY `date` (`date`),
  KEY `category_id` (`category_id`),
  KEY `approve` (`approve`),
  KEY `symbol` (`symbol`),
  KEY `user_id` (`user_id`),
  FULLTEXT KEY `picture_title` (`picture_title`,`text`)
  ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";


$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_gallery_profiles";

$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_profiles (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `profile_name` varchar(100) NOT NULL DEFAULT '',
  `allow_user` tinyint(1) NOT NULL DEFAULT '1',
  `p_id` smallint(5) NOT NULL DEFAULT '0',
  `view_level` varchar(100) NOT NULL DEFAULT '',
  `upload_level` varchar(100) NOT NULL DEFAULT '',
  `comment_level` varchar(100) NOT NULL DEFAULT '',
  `edit_level` varchar(100) NOT NULL DEFAULT '',
  `mod_level` varchar(100) NOT NULL DEFAULT '',
  `moderators` varchar(100) NOT NULL DEFAULT '',
  `foto_sort` varchar(18) NOT NULL DEFAULT '',
  `foto_msort` varchar(5) NOT NULL DEFAULT '',
  `allow_rating` tinyint(1) NOT NULL DEFAULT '1',
  `allow_comments` tinyint(1) NOT NULL DEFAULT '1',
  `allow_watermark` tinyint(1) NOT NULL DEFAULT '1',
  `icon_max_size` varchar(10) NOT NULL DEFAULT '',
  `subcats_td` smallint(4) NOT NULL DEFAULT '0',
  `subcats_tr` smallint(4) NOT NULL DEFAULT '0',
  `foto_td` smallint(4) NOT NULL DEFAULT '0',
  `foto_tr` smallint(4) NOT NULL DEFAULT '0',
  `auto_resize` tinyint(1) NOT NULL DEFAULT '1',
  `skin` varchar(50) NOT NULL DEFAULT '',
  `subcatskin` varchar(50) NOT NULL DEFAULT '',
  `maincatskin` varchar(50) NOT NULL DEFAULT '',
  `smallfotoskin` varchar(50) NOT NULL DEFAULT '',
  `bigfotoskin` varchar(50) NOT NULL DEFAULT '',
  `allow_carousel` tinyint(1) NOT NULL DEFAULT '1',
  `width_max` smallint(4) NOT NULL DEFAULT '0',
  `height_max` smallint(4) NOT NULL DEFAULT '0',
  `com_thumb_max` varchar(10) NOT NULL DEFAULT '',
  `thumb_max` varchar(10) NOT NULL DEFAULT '',
  `size_factor` smallint(3) NOT NULL DEFAULT '0',
  `allowed_extensions` varchar(250) NOT NULL DEFAULT '',
  `exprise_delete` smallint(4) NOT NULL DEFAULT '0',
  `allow_user_admin` tinyint(1) NOT NULL DEFAULT '0',
  `alt_name_tpl` varchar(100) NOT NULL DEFAULT '',
  `uploadskin` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
  ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";


$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_gallery_search";

$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_search (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `search_code` varchar(32) NOT NULL DEFAULT '',
  `search_num` int(10) NOT NULL DEFAULT '0',
  `ip` varchar(16) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` mediumint(8) NOT NULL DEFAULT '0',
  `actual` tinyint(1) NOT NULL DEFAULT '1',
  `symbol` varchar(10) NOT NULL DEFAULT '',
  `user` varchar(40) NOT NULL DEFAULT '',
  `story` varchar(255) NOT NULL DEFAULT '',
  `cat` mediumint(8) NOT NULL DEFAULT '0',
  `tag` varchar(100) NOT NULL DEFAULT '',
  `search_sort` varchar(18) NOT NULL DEFAULT '',
  `search_msort` varchar(5) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `search_code` (`search_code`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";

$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_gallery_search_text";

$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_search_text (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `search_id` int(10) NOT NULL DEFAULT '0',
  `search_page` smallint(5) NOT NULL DEFAULT '0',
  `find_files` text NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `search_id` (`search_id`,`search_page`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";


$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_gallery_tags";

$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_tags (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag_name` (`tag_name`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";


$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_gallery_tags_match";

$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_tags_match (
  `mid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(10) NOT NULL DEFAULT '0',
  `file_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mid`),
  KEY `tag_id` (`tag_id`),
  KEY `file_id` (`file_id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";

$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_gallery_temp_files";

$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_temp_files (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` text NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` mediumint(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";


$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_gallery_users_views";

$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_users_views (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) NOT NULL DEFAULT '0',
  `user_id` mediumint(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `file_id` (`file_id`,`user_id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";

$tableSchema[] = "ALTER TABLE `" . PREFIX . "_users` ADD `gallery_cs_flag` tinyint(1) NOT NULL DEFAULT '0'";

$insert_config_table = array(
'off' => '0',
'allow_cache' => '2',
'work_postfix' => 'gallery/',
'description' => '��� tws ������� ��������',
'keywords' => '������ �������, ��������, ����, tws',
'main_cat_td' => '2',
'main_cat_tr' => '5',
'foto_td' => '4',
'foto_tr' => '8',
'category_sort' => 'position',
'category_msort' => 'asc',
'foto_sort' => 'posi',
'foto_msort' => 'asc',
'max_title_lenght' => '40',
'autowrap_foto' => '20',
'global_max_foto_width' => '1024',
'global_max_foto_height' => '768',
'full_res_type' => '0',
'comms_foto_size' => '550x450',
'comm_res_type' => '5',
'max_thumb_size' => '150x120',
'thumb_res_type' => '5',
'allow_foto_resize' => '1',
'min_watermark' => '150',
'resize_quality' => '90',
'rewrite_mode' => '1',
'allow_check_double' => '1',
'allow_watermark' => '1',
'max_icon_size' => '120',
'watermark_light' => 'dleimages/watermark_light.png',
'watermark_dark' => 'dleimages/watermark_dark.png',
'allow_edit_picture' => '1',
'allow_delete_picture' => '0',
'dinamic_symbols' => '1',
'allow_comments' => '1',
'allow_rating' => '1',
'show_statistic' => '1',
'comments_mod' => '0',
'mail_comments' => '1',
'mail_foto' => '1',
'extensions' => 'a:30:{s:3:\\"jpg\\";a:3:{s:1:\\"s\\";i:5000;s:1:\\"p\\";i:1;s:1:\\"m\\";i:0;}s:4:\\"jpeg\\";a:3:{s:1:\\"s\\";i:5000;s:1:\\"p\\";i:1;s:1:\\"m\\";i:0;}s:3:\\"jpe\\";a:3:{s:1:\\"s\\";i:5000;s:1:\\"p\\";i:1;s:1:\\"m\\";i:0;}s:3:\\"png\\";a:3:{s:1:\\"s\\";i:500;s:1:\\"p\\";i:1;s:1:\\"m\\";i:0;}s:3:\\"gif\\";a:3:{s:1:\\"s\\";i:250;s:1:\\"p\\";i:1;s:1:\\"m\\";i:0;}s:3:\\"psd\\";a:3:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:0;s:1:\\"m\\";i:0;}s:3:\\"mp3\\";a:3:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:8;s:1:\\"m\\";i:0;}s:3:\\"cue\\";a:3:{s:1:\\"s\\";i:2048;s:1:\\"p\\";i:0;s:1:\\"m\\";i:0;}s:3:\\"m3u\\";a:3:{s:1:\\"s\\";i:1;s:1:\\"p\\";i:0;s:1:\\"m\\";i:0;}s:3:\\"mp4\\";a:3:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:4;s:1:\\"m\\";i:0;}s:3:\\"swf\\";a:3:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:6;s:1:\\"m\\";i:0;}s:3:\\"m4v\\";a:3:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:4;s:1:\\"m\\";i:0;}s:3:\\"m4a\\";a:3:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:4;s:1:\\"m\\";i:0;}s:3:\\"mov\\";a:3:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:4;s:1:\\"m\\";i:0;}s:3:\\"3gp\\";a:3:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:4;s:1:\\"m\\";i:0;}s:3:\\"f4v\\";a:3:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:4;s:1:\\"m\\";i:0;}s:3:\\"mkv\\";a:3:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:3;s:1:\\"m\\";i:0;}s:4:\\"divx\\";a:3:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:3;s:1:\\"m\\";i:0;}s:3:\\"avi\\";a:3:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:3;s:1:\\"m\\";i:0;}s:3:\\"wmv\\";a:3:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:7;s:1:\\"m\\";i:0;}s:3:\\"mpg\\";a:3:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:7;s:1:\\"m\\";i:0;}s:11:\\"youtube.com\\";a:3:{s:1:\\"s\\";i:0;s:1:\\"p\\";i:9;s:1:\\"m\\";i:0;}s:9:\\"rutube.ru\\";a:3:{s:1:\\"s\\";i:0;s:1:\\"p\\";i:10;s:1:\\"m\\";i:0;}s:13:\\"video.mail.ru\\";a:3:{s:1:\\"s\\";i:0;s:1:\\"p\\";i:13;s:1:\\"m\\";i:0;}s:9:\\"vimeo.com\\";a:3:{s:1:\\"s\\";i:0;s:1:\\"p\\";i:12;s:1:\\"m\\";i:0;}s:10:\\"smotri.com\\";a:3:{s:1:\\"s\\";i:0;s:1:\\"p\\";i:11;s:1:\\"m\\";i:0;}s:16:\\"gametrailers.com\\";a:3:{s:1:\\"s\\";i:0;s:1:\\"p\\";i:14;s:1:\\"m\\";i:0;}s:3:\\"flv\\";a:3:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:4;s:1:\\"m\\";i:0;}s:3:\\"rar\\";a:3:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:0;s:1:\\"m\\";i:0;}s:3:\\"zip\\";a:3:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:0;s:1:\\"m\\";i:0;}}',
'viewlevel' => '-1',
'comlevel' => '-1',
'uploadlevel' => '-1',
'modlevel' => '-1',
'editlevel' => '1,2',
'ratelevel' => '-1',
'addlevel' => '-1',
'adminaccess' => '1,2',
'allowed_extensions' => 'jpg,jpeg,jpe,png,gif',
'skin_name' => '',
'version_gallery' => $new_version,
'max_comments_days' => '0',
'allow_check_update' => '1',
'jw_flv_mp_full_width' => '420',
'jw_flv_mp_full_height' => '320',
'jw_flv_mp_width' => '150',
'jw_flv_mp_height' => '150',
'jw_flv_mp_mp3_full_width' => '475',
'jw_flv_mp_mp3_full_height' => '20',
'jw_flv_mp_mp3_width' => '175',
'jw_flv_mp_mp3_height' => '20',
'divx_wp_full_width' => '420',
'divx_wp_full_height' => '320',
'divx_wp_width' => '150',
'divx_wp_height' => '150',
'cms_fp_full_width' => '420',
'cms_fp_full_height' => '320',
'cms_fp_width' => '150',
'cms_fp_height' => '150',
'cms_fp_mp3_full_width' => '420',
'cms_fp_mp3_full_height' => '27',
'cms_fp_mp3_width' => '475',
'cms_fp_mp3_height' => '27',
'cms_flp_full_width' => '420',
'cms_flp_full_height' => '320',
'cms_flp_width' => '150',
'cms_flp_height' => '150',
'cms_ftp_full_width' => '420',
'cms_ftp_full_height' => '320',
'cms_ftp_width' => '150',
'cms_ftp_height' => '150',
'progressBarColor' => '0xFFFFFF',
'play' => '1',
'flv_watermark' => '1',
'advance_default' => '1',
'disable_advance_upload' => '0',
'max_once_upload' => '20',
'allow_user_admin' => '1',
'max_user_categories' => '1',
'icon_type' => '1',
'timestamp_active' => 'j F H:i',
'allow_recycle' => '1',
'allow_recycle_own' => '0',
'files_on_moderation' => '5',
'file_title_control' => '1',
'disable_select_upload' => '0',
'remotelevel' => '-1',
'allow_download' => '1',
'empty_title_template' => '���� {%i%}',
'allow_ajax_comments' => '1',
'yrt_full_width' => '420',
'yrt_full_height' => '320',
'yrt_width' => '150',
'yrt_height' => '150',
'yrt_tube_related' => '0',
'flv_watermark_pos' => 'left',
'flv_watermark_al' => '1',
'youtube_q' => 'hd720',
'startframe' => '1',
'preview' => '0',
'autohide' => '0',
'buffer' => '3',
'fullsizeview' => '1',
'last_cron' => 0,
'statistic_file_onmod' => '-1',
'statistic_file' => '-1',
'statistic_file_day' => '-1',
'statistic_cat' => '-1',
'statistic_cat_week' => '-1',
'statistic_com_day' => '-1',
'statistic_com' => '-1',
'statistic_downloads' => '-1',
'enable_banned' => '0',
'tags_len' => '3-40',
'convert_png_thumb' => '1',
'allow_delete_omcomments' => '0',
'tags_num' => '5',
'file_views' => '1',
'whois_view_file' => '1',
'whois_view_file_day' => '90',
'no_main_watermark' => '0',
'random_filename' => '0',
'comsubslevel' => '-1',
'thumbs_offset' => '1',
'show_in_fullimage' => '1',
'thumbs_mousewheel' => '1',
'buffer_in_fullimage' => '15',
'thumbs_template' => '<a href="{url}"><img src="{image}" alt="{alt-title}" title="{alt-title}" /></a><br /><div><b>{title}</b></div>',
'thumbs_fx' => 'scroll',
);

$set_create_cats = intval($_POST['create_cats']);
$set_allow_video = intval($_POST['allow_video']);
$set_allow_audio = intval($_POST['allow_audio']);
$set_allow_rarzip = intval($_POST['allow_rarzip']);
$set_allow_youtube = intval($_POST['allow_youtube']);
$set_allow_user_upload = intval($_POST['allow_user_upload']);
$set_moderate = intval($_POST['moderate']);
$set_allow_comrate = intval($_POST['allow_comrate']);
$set_max_users_cats = intval($_POST['max_users_cats']);
$set_allow_user_create = intval($_POST['allow_user_create']);
$set_allow_user_own = intval($_POST['allow_user_own']);

if ($set_allow_comrate == 1){
$insert_config_table['allow_comments'] = 1;
$insert_config_table['allow_rating'] = 1;
} else {
$insert_config_table['allow_comments'] = 0;
$insert_config_table['allow_rating'] = 0;
}

$insert_config_table['max_user_categories'] = $set_max_users_cats;
$insert_config_table['allow_user_admin'] = $set_allow_user_own;

$insert_config_table['allowed_extensions'] = explode(',',$insert_config_table['allowed_extensions']);
if ($set_allow_video == 1){
$insert_config_table['allowed_extensions'][] = 'avi';
$insert_config_table['allowed_extensions'][] = 'wmv';
$insert_config_table['allowed_extensions'][] = 'flv';
$insert_config_table['allowed_extensions'][] = 'mp4';
$insert_config_table['allowed_extensions'][] = 'swf';
$insert_config_table['allowed_extensions'][] = 'mov';
$insert_config_table['allowed_extensions'][] = 'mkv';
$insert_config_table['allowed_extensions'][] = 'divx';
$insert_config_table['allowed_extensions'][] = 'mpg';
}
if ($set_allow_audio == 1){
$insert_config_table['allowed_extensions'][] = 'mp3';
$insert_config_table['allowed_extensions'][] = '3gp';
}
if ($set_allow_rarzip == 1){
$insert_config_table['allowed_extensions'][] = 'rar';
$insert_config_table['allowed_extensions'][] = 'zip';
}
if ($set_allow_youtube == 1){
$insert_config_table['allowed_extensions'][] = 'youtube.com';
$insert_config_table['allowed_extensions'][] = 'rutube.ru';
$insert_config_table['allowed_extensions'][] = 'video.mail.ru';
$insert_config_table['allowed_extensions'][] = 'vimeo.com';
$insert_config_table['allowed_extensions'][] = 'smotri.com';
$insert_config_table['allowed_extensions'][] = 'gametrailers.com';
}
$insert_config_table['allowed_extensions'] = implode(',',$insert_config_table['allowed_extensions']);

if ($set_allow_user_upload == 1){
$insert_config_table['uploadlevel'] = '-1';
if ($set_moderate == 1) $insert_config_table['modlevel'] = '-1';
else $insert_config_table['modlevel'] = '';
} else {
$insert_config_table['modlevel'] = '';
$insert_config_table['uploadlevel'] = '';
}

if ($set_allow_user_create == 1){
$insert_config_table['addlevel'] = '-1';
} else {
$insert_config_table['addlevel'] = '';
}

foreach ($insert_config_table as $config_name => $config_value){
	$tableSchema[] = "INSERT IGNORE INTO " . PREFIX . "_gallery_config (name, value, type) VALUES ('{$config_name}', '{$config_value}', 0)";
}

$insert_config_table = array(
'check_update' => '',
'key' => '',
'admin_num_files' => '50',
'admin_num_cats' => '50',
'admin_user_access' => '0',
);

foreach ($insert_config_table as $config_name => $config_value){
	$tableSchema[] = "INSERT IGNORE INTO " . PREFIX . "_gallery_config (name, value, type) VALUES ('{$config_name}', '{$config_value}', 1)";
}

$db->query("SHOW TABLES LIKE '" . PREFIX . "_tws_email'");

$found = false;

while ($row = $db->get_row()){ $found = true; }

if (!$found){

  $tableSchema[] = "CREATE TABLE " . PREFIX . "_tws_email (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `prefix` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL DEFAULT '',
  `template` text NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE (`name`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";

}

 $tableSchema[] = "INSERT IGNORE INTO `" . PREFIX . "_tws_email` (`prefix`, `name`, `title`, `description`, `template`) VALUES (1, 'gallery_newcomment', 'E-Mail ���������, ������� ���������� ��� ���������� ������ ����������� � �����', '��� ��������� ������� ��� ������� ��������� �� ������ ������������ ��������� ����:\r\n<b>{%site%}</b> - �������� �����, � �������� ���������� �����������\r\n<b>{%username_to%}</b> - ���������� ���������\r\n<b>{%username%}</b> - ����� �����������\r\n<b>{%date%}</b> - ���� ���������\r\n<b>{%link%}</b> - ������ �� ����, � �������� ��� �������� ����������� � ���������� ����������� ��������\r\n<b>{%ip%}</b> - IP ����� ������\r\n<b>{%text%}</b> - ����� �����������\r\n<b>{%unsubscribe%}</b> - ������ �� ������ �������� �� ����������� � ������ �������', '��������� ����������,\r\n\r\n���������� ��� � ���, ��� �� ���� {%site%} ��� �������� �����������.\r\n\r\n------------------------------------------------\r\n������� ���������� � �����������\r\n------------------------------------------------\r\n\r\n�����: {%username%}\r\n���� ����������: {%date%}\r\n������ �� ����������: {%link%}\r\nIP �����: {%ip%}\r\n------------------------------------------------\r\n����� �����������\r\n------------------------------------------------\r\n\r\n{%text%}\r\n\r\n------------------------------------------------\r\n\r\n��� ����������� �������� �� ����������� � ������� ����� ���������� ��������� ��� ����������� �������������� �����������, ������� �� ������ {%link%}\r\n\r\n���� �� �� ������ ������ �������� ����������� � ����� ������������ � ������ �������, �� ����������� �� ������ ������: {%unsubscribe%}\r\n\r\n� ���������,\r\n������������� {%site%}')";
 $tableSchema[] = "INSERT IGNORE INTO `" . PREFIX . "_tws_email` (`prefix`, `name`, `title`, `description`, `template`) VALUES (1, 'gallery_newfoto', 'E-Mail ���������, ������� ���������� ��� �������� ����� ���������� ��������������, ��������� ���������', '��� ��������� ������� ��� ������� ��������� �� ������ ������������ ��������� ����:\r\n<b>{%name%}</b> - ��� ������������, �������� ���������� �����������\r\n<b>{%site%}</b> - �������� �����, � �������� ���������� �����������\r\n<b>{%username%}</b> - ��� ������������\r\n<b>{%ip%}</b> - IP ����� ������������ (�������� ������ ���������������)\r\n<b>{%date%}</b> - ���� ��������\r\n<b>{%category%}</b> - ��� ���������, � ������� ���� ��������� �����\r\n<b>{%images%}</b> - ���������� ����� ������\r\n<b>{%link%}</b> - ������ ��� ������������� ������', '��������� {%name%}, \r\n\r\n���������� ��� � ���, ��� � ������� �� ����� {%site%} ���� ��������� ����� ����������, ��������� ���������.\r\n\r\n------------------------------------------------\r\n������� ���������� � ������\r\n------------------------------------------------\r\n\r\n�����: {%username%}{%ip%}\r\n���� ����������: {%date%}\r\n���������: {%category%}\r\n���������� ������: {%images%}\r\n------------------------------------------------\r\n\r\n�������� ����������������� ���������� �� ������, ������� �� ������ ����\r\n{%link%}\r\n\r\n� ���������,\r\n������������� {%site%}')";
 $tableSchema[] = "INSERT IGNORE INTO `" . PREFIX . "_tws_email` (`prefix`, `name`, `title`, `description`, `template`) VALUES (1, 'gallery_editfoto', 'PM ���������, ������� ���������� ��� ������� ����������� ������������', '��� ��������� ������� ��� ������� ��������� �� ������ ������������ ��������� ����:\r\n<b>{%name%}</b> - ��� ������������, �������� ���������� �����������\r\n<b>{%site%}</b> - �������� �����, � �������� ���������� �����������\r\n<b>{%username%}</b> - ��� ��������������\r\n<b>{%usergroup%}</b> - ������ ��������������\r\n<b>{%date%}</b> - ���� ��������������\r\n<b>{%fileslist%}</b> - ������ ����������������� ������ � ������ �� ���\r\n<b>{%action%}</b> - ��������, ������� �������� �������������\r\n<b>{%notice%}</b> - ����� ���������, ������� ������ ��������������', '��������� {%name%},\r\n\r\n���������� ��� � ���, ��� ��������� ����������� ���� ����� ���� ��������������� � �������:\r\n\r\n{%fileslist%}\r\n\r\n[action]��������: {%action%}[/action]\r\n[notice]��������� �������: {%notice%}[/notice]\r\n\r\n�������������: {%username%}\r\n������: {%usergroup%}\r\n����: {%date%}')";
 $tableSchema[] = "INSERT IGNORE INTO `" . PREFIX . "_tws_email` (`prefix`, `name`, `title`, `description`, `template`) VALUES (1, 'gallery_subscribe', 'E-Mail ���������, ������� ���������� ��� ������������� ��������', '��� ��������� ������� ��� ������� ��������� �� ������ ������������ ��������� ����:\r\n<b>{%site%}</b> - �������� �����, � �������� ���������� �����������\r\n<b>{%subscribe%}</b> - ������ �� ������������� �������� �� �����������', '��������� ����������,\r\n\r\n�� ������������� �� �������� ����� ������������ � ������� �� ����� {%site%}.\r\n\r\n��� ������������� �������� �������� �� ��������� ������: {%subscribe%}\r\n\r\n���� �� ������� �� ������ �� ����� ����� � �� ������������� �� ����������� - �� ��������� �������� �� ������ ��������� - �������� ���-�� ������ ������ ��� ����� e-mail ������. ���� ��������� ����� ��������� ��������� - ����������, ���������� � �������������� ����� - �� ����������� ������ ���� ��������.')";

$tableSchema[] = "INSERT IGNORE INTO " . PREFIX . "_admin_sections (name, title, descr, icon, allow_groups) VALUES ('twsgallery', '�����������', '����� ���������, ��������� � ����������� �������', 'iPhoto.png', 'all')";

foreach($tableSchema as $table) {
	$db->query($table);
}

$p_id_array = array();
$insert_table = array();
$last_date = date ("Y-m-d H:i:s", time());
$posi = 1;

$member_name = $db->super_query("SELECT name FROM " . USERPREFIX . "_users WHERE user_id=1");
$member_name = $member_name['name'];

if ($set_create_cats){

	$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
		 VALUES (0, '���� ������ ������', '������������ ������, ���� ������ ������ �� ������ ����������, ��������� ����������� � ������ �������', '', '', '', '{$posi}', 'city', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '1', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, '', 0, 0, 0, 1, '')");

	$posi++;
	$p_id_array[0] = $db->insert_id();

	$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
		 VALUES ({$p_id_array[0]}, '������', '������ ������ ������', '', '', '', '{$posi}', 'city/buildings', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, 'jpg,jpeg,jpe,png,gif', 0, 0, 0, 1, '')");

	$posi++;

	$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
		 VALUES ({$p_id_array[0]}, '�����', '����� ������ ������', '', '', '', '{$posi}', 'city/streets', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, 'jpg,jpeg,jpe,png,gif', 0, 0, 0, 1, '')");

	$posi++;

	if ($set_allow_user_create){

		$db->query("INSERT INTO " . PREFIX . "_gallery_profiles (p_id, allow_user, profile_name, skin, moderators, upload_level, allowed_extensions, allow_user_admin, allow_watermark, allow_rating, allow_comments, auto_resize, alt_name_tpl) 
			VALUES ({$p_id_array[0]}, 1, '���� ������ ������', '', '', '', 'jpg,jpeg,jpe,png,gif', 0, 1, 1, 1, 1, '{%category%}/')");

	}

	if ($set_allow_video){

		$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
			 VALUES (0, '����� ������� ������ ������', '������������� ������ ����, ��������������� �� ������ ������ �� ������ ���������� � ���� �������', '', '', '', '{$posi}', 'videcity', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '1', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, '', 0, 0, 0, 1, '')");

		$posi++;
		$p_id_array[1] = $db->insert_id();

		$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
			 VALUES ({$p_id_array[1]}, '������� ��������', '�������������� �� ������', '', '', '', '{$posi}', 'videcity/videostreets', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, 'avi,wmv,flv,mp4,swf,mov,mkv,divx,mpg', 0, 0, 0, 1, '')");

		$posi++;

		$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
			 VALUES ({$p_id_array[1]}, '������ �����', '����� ������ ������, ������������ �� ����� � ����� �����', '', '', '', '{$posi}', 'videcity/videonightstreets', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, 'avi,wmv,flv,mp4,swf,mov,mkv,divx,mpg', 0, 0, 0, 1, '')");

		$posi++;

		if ($set_allow_user_create){

			$db->query("INSERT INTO " . PREFIX . "_gallery_profiles (p_id, allow_user, profile_name, skin, moderators, upload_level, allowed_extensions, allow_user_admin, allow_watermark, allow_rating, allow_comments, auto_resize, alt_name_tpl) 
				VALUES ({$p_id_array[1]}, 1, '����� ������ ������', '', '', '', 'avi,wmv,flv,mp4,swf,mov,mkv,divx,mpg', 0, 1, 1, 1, 1, '{%category%}/')");

		}

	}

	if ($set_allow_audio){

		$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
			 VALUES (0, '������', '����������� ������ ������ �����', '', '', '', '{$posi}', 'music', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '1', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, '', 0, 0, 0, 1, '')");

		$posi++;
		$p_id_array[2] = $db->insert_id();

		$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
			 VALUES ({$p_id_array[2]}, '������������ ������', '', '', '', '', '{$posi}', 'music/dance', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, 'mp3,3gp', 0, 0, 0, 1, '')");

		$posi++;

		$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
			 VALUES ({$p_id_array[2]}, '������������� ������', '', '', '', '', '{$posi}', 'music/relax', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, 'mp3,3gp', 0, 0, 0, 1, '')");

		$posi++;

		if ($set_allow_user_create){

			$db->query("INSERT INTO " . PREFIX . "_gallery_profiles (p_id, allow_user, profile_name, skin, moderators, upload_level, allowed_extensions, allow_user_admin, allow_watermark, allow_rating, allow_comments, auto_resize, alt_name_tpl) 
				VALUES ({$p_id_array[2]}, 1, '����������� ������������', '', '', '', 'mp3,3gp', 0, 1, 1, 1, 1, '{%category%}/')");

		}

	}

	if ($set_allow_rarzip){

		$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
			 VALUES (0, '�������� �����', '����� �������', '', '', '', '{$posi}', 'files', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '1,2,3', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, 'rar,zip', 0, 0, 0, 1, '')");

		$posi++;

	}

	if ($set_allow_youtube == 1){

		$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
			 VALUES (0, '����� � youtube.com', '� ��� ��������� ����� ��������� ����� �� ��������� ������: youtube.com, rutube.ru, video.mail.ru, vimeo.com, smotri.com, gametrailers.com.', '', '', '', '{$posi}', 'youtube', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, 'youtube.com,rutube.ru,video.mail.ru,vimeo.com,smotri.com,gametrailers.com', 0, 0, 0, 1, '')");

		$posi++;

	}

}

if ($set_allow_user_own){

	$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
		 VALUES (0, '������ ������', '� ������ ������� ������ ������������ ����� ������� ���� ������ ������', '', '', '', '{$posi}', 'users', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '1', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, '', 0, 0, 0, 1, '')");

	$posi++;
	$p_id_array[3] = $db->insert_id();

	$db->query("INSERT INTO " . PREFIX . "_gallery_profiles (p_id, allow_user, profile_name, skin, moderators, upload_level, allowed_extensions, allow_user_admin, allow_watermark, allow_rating, allow_comments, auto_resize, alt_name_tpl) 
		 VALUES ({$p_id_array[3]}, 1, '������ ������', '', '', '1', 'jpg,jpeg,jpe,png,gif', 1, 1, 1, 1, 1, '{%user%}/')");

}

$sql = $db->query("SELECT COUNT(*) as count, p_id FROM " . PREFIX . "_gallery_category WHERE p_id > 0 GROUP BY p_id");

while ($row = $db->get_row($sql))
	$db->query( "UPDATE " . PREFIX . "_gallery_category SET sub_cats={$row['count']} WHERE id={$row['p_id']}");

$db->free($sql);


$l=@file_get_contents("http://inker.wonderfullife.ru/extras/updates.php?script=twsg&install=2&dle=".$config['version_id']."&version=".$new_version."&host=".$_SERVER['HTTP_HOST']); // ������ ������� ������� ���������� � ���, ��� ��������� ������� ��������� � ������ � ����������� �� ����������
unset($l);

echoheader("", "");
galHeader("��������� ������� ���������");

echo <<<HTML
<table width="100%">
    <tr>
        <td style="padding:2px;"><br>����������� ���, TWS Gallery {$new_version} ���� ������� ����������� �� ��� ������. �� ������ ������ ����������� ������� <a href="/index.php?do=gallery">�������� ����� �������</a> � ������� ����������� �������. ���� �� ������ <a href="/{$config['admin_path']}?mod=twsgallery&act=1">�����</a> � ������ ���������� � �������� ������ ��������� �������. 
<br><br><font color="red">��������: ��� ��������� ������� ��������� ��������� ���� ������, � ����� ������������� �������� ��������� �������, ������� ����� �������� ��������� ������� ���� <b>galleryinstall.php</b> �� ��������� ��������� ��������� �������!</font><br><br>
�������� ��� ������<br><br>
Al-x by TWS<br><br></td>
    </tr>
</table>
HTML;

galFooter();
echofooter();

}


?>