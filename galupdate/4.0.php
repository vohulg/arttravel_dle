<?php

$galConfig['rand_foto_cats'] = "1";
unset($galConfig['show_active_users']);
$galConfig['version_gallery'] = "4.0.5";

$handler = @fopen(ENGINE_DIR.'/data/gallery.config.php', "w") or die("���������� �������� ������ � ���� engine/data/gallery.config.php. ���������� �� ���� ����� 666 � ��������� �������");

fwrite($handler, "<?PHP \n\n//System Configurations\n\n\$galConfig = array (\n\n");
foreach($galConfig as $name => $value)
{
	fwrite($handler, "'{$name}' => \"{$value}\",\n\n");
}
fwrite($handler, ");\n\n?>");
fclose($handler);

$tableSchema = array();

$tableSchema[] = "DROP TABLE IF EXISTS " . USERPREFIX . "_gallery_banned";

$tableSchema[] = "CREATE TABLE " . USERPREFIX . "_gallery_banned (
  `id` smallint(5) NOT NULL auto_increment,
  `users_id` mediumint(8) NOT NULL default '0',
  `descr` text NOT NULL,
  `date` varchar(20) NOT NULL default '',
  `ip` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`users_id`),
  KEY `ip` (`ip`)
  ) TYPE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";

foreach($tableSchema as $table){
	$db->query ($table);
}

@unlink(ENGINE_DIR.'/cache/system/gallery_category.php');
@unlink(ENGINE_DIR.'/cache/system/gallery_cron.php');
@unlink(ENGINE_DIR.'/cache/system/gallery_banned.php');
clear_gallery_cache();
clear_gallery_vars();

msgbox("info","����������", "<form action=\"index.php\" method=\"GET\">���������� ���� ������ � ������ <b>4.0.0</b> �� ������ <b>4.0.5</b> ������� ���������.<br />������� ����� ��� ����������� �������a ���������� �������<br /><br /><input type=\"hidden\" name=\"next\" value=\"4.0.5\"><input class=\"edit\" type=\"submit\" value=\"����� ...\"></form>");

?>