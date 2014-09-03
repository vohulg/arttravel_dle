<?php

$galConfig['version_gallery'] = "4.1";
$galConfig['extensions'] = "a:12:{s:3:\\\"jpg\\\";a:2:{s:1:\\\"s\\\";i:250;s:1:\\\"p\\\";i:1;}s:4:\\\"jpeg\\\";a:2:{s:1:\\\"s\\\";i:250;s:1:\\\"p\\\";i:1;}s:3:\\\"jpe\\\";a:2:{s:1:\\\"s\\\";i:250;s:1:\\\"p\\\";i:1;}s:3:\\\"png\\\";a:2:{s:1:\\\"s\\\";i:250;s:1:\\\"p\\\";i:1;}s:3:\\\"gif\\\";a:2:{s:1:\\\"s\\\";i:200;s:1:\\\"p\\\";i:1;}s:3:\\\"mp3\\\";a:2:{s:1:\\\"s\\\";i:6144;s:1:\\\"p\\\";i:2;}s:3:\\\"avi\\\";a:2:{s:1:\\\"s\\\";i:10240;s:1:\\\"p\\\";i:3;}s:3:\\\"wmv\\\";a:2:{s:1:\\\"s\\\";i:10240;s:1:\\\"p\\\";i:4;}s:3:\\\"flv\\\";a:2:{s:1:\\\"s\\\";i:10240;s:1:\\\"p\\\";i:2;}s:3:\\\"rar\\\";a:2:{s:1:\\\"s\\\";i:10240;s:1:\\\"p\\\";i:5;}s:3:\\\"zip\\\";a:2:{s:1:\\\"s\\\";i:10240;s:1:\\\"p\\\";i:5;}s:3:\\\"doc\\\";a:2:{s:1:\\\"s\\\";i:2048;s:1:\\\"p\\\";i:5;}}";

$handler = @fopen(ENGINE_DIR.'/data/gallery.config.php', "w") or die("���������� �������� ������ � ���� engine/data/gallery.config.php. ���������� �� ���� ����� 666 � ��������� �������");

fwrite($handler, "<?PHP \n\n//System Configurations\n\n\$galConfig = array (\n\n");
foreach($galConfig as $name => $value)
{
	fwrite($handler, "'{$name}' => \"{$value}\",\n\n");
}
fwrite($handler, ");\n\n?>");
fclose($handler);

$tableSchema = array();

$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies_text` ADD `logs` TEXT NOT NULL ";

$db->query("SHOW TABLES LIKE '" . PREFIX . "_tws_email'");

$found = false;

while ($row = $db->get_row()){ $found = true; }

if (!$found){

  $tableSchema[] = "CREATE TABLE " . PREFIX . "_tws_email (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `prefix` tinyint(1) NOT NULL default '0',
  `name` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `template` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";

} else {

 $tableSchema[] = "DELETE FROM `" . PREFIX . "_tws_email` WHERE prefix='1'";

}

 $tableSchema[] = "INSERT INTO `" . PREFIX . "_tws_email` (`prefix`, `name`, `title`, `description`, `template`) VALUES (1, 'gallery_newfoto', 'E-Mail ���������, ������� ���������� ��� �������� ����� ���������� ��������������, ��������� ���������', '��� ��������� ������� ��� ������� ��������� �� ������ ������������ ��������� ����:\r\n<b>{%name%}</b> - ��� ������������, �������� ���������� �����������\r\n<b>{%site%}</b> - �������� �����, � �������� ���������� �����������\r\n<b>{%username%}</b> - ��� ������������\r\n<b>{%ip%}</b> - IP ����� ������������ (�������� ������ ���������������)\r\n<b>{%date%}</b> - ���� ��������\r\n<b>{%category%}</b> - ��� ���������, � ������� ���� ��������� �����\r\n<b>{%images%}</b> - ���������� ����� ������\r\n<b>{%link%}</b> - ������ ��� ������������� ������', '��������� {%name%}, \r\n\r\n���������� ��� � ���, ��� � ������� �� ����� {%site%} ���� ��������� ����� ����������, ��������� ���������.\r\n\r\n------------------------------------------------\r\n������� ���������� � ������\r\n------------------------------------------------\r\n\r\n�����: {%username%}{%ip%}\r\n���� ����������: {%date%}\r\n���������: {%category%}\r\n���������� ������: {%images%}\r\n------------------------------------------------\r\n\r\n�������� ����������������� ���������� �� ������, ������� �� ������ ����\r\n{%link%}\r\n\r\n� ���������,\r\n������������� {%site%}')";
 
 $tableSchema[] = "INSERT INTO `" . PREFIX . "_tws_email` (`prefix`, `name`, `title`, `description`, `template`) VALUES (1, 'gallery_newcomment', 'E-Mail ���������, ������� ���������� ��� ���������� ������ ����������� � �����', '��� ��������� ������� ��� ������� ��������� �� ������ ������������ ��������� ����:\r\n<b>{%site%}</b> - �������� �����, � �������� ���������� �����������\r\n<b>{%username%}</b> - ����� �����������\r\n<b>{%date%}</b> - ���� ���������\r\n<b>{%link%}</b> - ������ �� ����, � �������� ��� �������� �����������\r\n<b>{%ip%}</b> - IP ����� ������\r\n<b>{%text%}</b> - ����� �����������', '��������� �������������,\r\n\r\n���������� ��� � ���, ��� �� ���� {%site%} ��� �������� �����������.\r\n\r\n------------------------------------------------\r\n������� ���������� � �����������\r\n------------------------------------------------\r\n\r\n�����: {%username%}\r\n���� ����������: {%date%}\r\n������ �� ����������: {%link%}\r\nIP �����: {%ip%}\r\n------------------------------------------------\r\n����� �����������\r\n------------------------------------------------\r\n\r\n{%text%}\r\n\r\n� ���������,\r\n������������� {%site%}')";


foreach($tableSchema as $table){
	$db->query ($table);
}

@unlink(ENGINE_DIR.'/cache/system/gallery_category.php');
@unlink(ENGINE_DIR.'/cache/system/gallery_cron.php');
@unlink(ENGINE_DIR.'/cache/system/gallery_banned.php');
clear_gallery_cache();
clear_gallery_vars();

msgbox("info","����������", "<form action=\"index.php\" method=\"GET\">���������� ���� ������ � ������ <b>4.0.7</b> �� ������ <b>4.1</b> ������� ���������.<br />������� ����� ��� ����������� �������a ���������� �������<br /><br /><input type=\"hidden\" name=\"next\" value=\"4.1\"><input class=\"edit\" type=\"submit\" value=\"����� ...\"></form>");

?>