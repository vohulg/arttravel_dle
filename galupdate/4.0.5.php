<?php

$galConfig['version_gallery'] = "4.0.7";

$handler = @fopen(ENGINE_DIR.'/data/gallery.config.php', "w") or die("���������� �������� ������ � ���� engine/data/gallery.config.php. ���������� �� ���� ����� 666 � ��������� �������");

fwrite($handler, "<?PHP \n\n//System Configurations\n\n\$galConfig = array (\n\n");
foreach($galConfig as $name => $value)
{
	fwrite($handler, "'{$name}' => \"{$value}\",\n\n");
}
fwrite($handler, ");\n\n?>");
fclose($handler);

$tableSchema = array();

$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_profiles` ADD `exprise_delete` SMALLINT( 4 ) NOT NULL DEFAULT '0';";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` ADD `exprise_delete` SMALLINT( 4 ) NOT NULL DEFAULT '0';";

foreach($tableSchema as $table){
	$db->query ($table);
}

@unlink(ENGINE_DIR.'/cache/system/gallery_category.php');
@unlink(ENGINE_DIR.'/cache/system/gallery_cron.php');
@unlink(ENGINE_DIR.'/cache/system/gallery_banned.php');
clear_gallery_cache();
clear_gallery_vars();

msgbox("info","����������", "<form action=\"index.php\" method=\"GET\">���������� ���� ������ � ������ <b>4.0.5</b> �� ������ <b>4.0.7</b> ������� ���������.<br />������� ����� ��� ����������� �������a ���������� �������<br /><br /><input type=\"hidden\" name=\"next\" value=\"4.0.7\"><input class=\"edit\" type=\"submit\" value=\"����� ...\"></form>");

?>