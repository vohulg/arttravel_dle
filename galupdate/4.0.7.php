<?php

$galConfig['version_gallery'] = "4.1";
$galConfig['extensions'] = "a:12:{s:3:\\\"jpg\\\";a:2:{s:1:\\\"s\\\";i:250;s:1:\\\"p\\\";i:1;}s:4:\\\"jpeg\\\";a:2:{s:1:\\\"s\\\";i:250;s:1:\\\"p\\\";i:1;}s:3:\\\"jpe\\\";a:2:{s:1:\\\"s\\\";i:250;s:1:\\\"p\\\";i:1;}s:3:\\\"png\\\";a:2:{s:1:\\\"s\\\";i:250;s:1:\\\"p\\\";i:1;}s:3:\\\"gif\\\";a:2:{s:1:\\\"s\\\";i:200;s:1:\\\"p\\\";i:1;}s:3:\\\"mp3\\\";a:2:{s:1:\\\"s\\\";i:6144;s:1:\\\"p\\\";i:2;}s:3:\\\"avi\\\";a:2:{s:1:\\\"s\\\";i:10240;s:1:\\\"p\\\";i:3;}s:3:\\\"wmv\\\";a:2:{s:1:\\\"s\\\";i:10240;s:1:\\\"p\\\";i:4;}s:3:\\\"flv\\\";a:2:{s:1:\\\"s\\\";i:10240;s:1:\\\"p\\\";i:2;}s:3:\\\"rar\\\";a:2:{s:1:\\\"s\\\";i:10240;s:1:\\\"p\\\";i:5;}s:3:\\\"zip\\\";a:2:{s:1:\\\"s\\\";i:10240;s:1:\\\"p\\\";i:5;}s:3:\\\"doc\\\";a:2:{s:1:\\\"s\\\";i:2048;s:1:\\\"p\\\";i:5;}}";

$handler = @fopen(ENGINE_DIR.'/data/gallery.config.php', "w") or die("Невозможно записать данные в файл engine/data/gallery.config.php. Установите на файл права 666 и повторите попытку");

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

 $tableSchema[] = "INSERT INTO `" . PREFIX . "_tws_email` (`prefix`, `name`, `title`, `description`, `template`) VALUES (1, 'gallery_newfoto', 'E-Mail сообщение, которое отсылается при загрузке новой фотографии пользователями, требующей модерации', 'При написании шаблона для данного сообщения вы можете использовать следующие теги:\r\n<b>{%name%}</b> - имя пользователя, которому отправлено уведомление\r\n<b>{%site%}</b> - название сайта, с которого отправлено уведомление\r\n<b>{%username%}</b> - имя загрузившего\r\n<b>{%ip%}</b> - IP адрес загрузившего (доступен только администраторам)\r\n<b>{%date%}</b> - дата загрузки\r\n<b>{%category%}</b> - имя категории, в которую были загружены файлы\r\n<b>{%images%}</b> - количество новых файлов\r\n<b>{%link%}</b> - ссылка для модерирования файлов', 'Уважаемый {%name%}, \r\n\r\nуведомляем вас о том, что в галерею на сайте {%site%} были добавлены новые фотографии, требующие модерации.\r\n\r\n------------------------------------------------\r\nКраткая информация о файлах\r\n------------------------------------------------\r\n\r\nАвтор: {%username%}{%ip%}\r\nДата добавления: {%date%}\r\nКатегория: {%category%}\r\nКоличество файлов: {%images%}\r\n------------------------------------------------\r\n\r\nПровести администрирование фотографий вы можете, перейдя по ссылке ниже\r\n{%link%}\r\n\r\nС уважением,\r\nАдминистрация {%site%}')";
 
 $tableSchema[] = "INSERT INTO `" . PREFIX . "_tws_email` (`prefix`, `name`, `title`, `description`, `template`) VALUES (1, 'gallery_newcomment', 'E-Mail сообщение, которое отсылается при добавлении нового комментария к файлу', 'При написании шаблона для данного сообщения вы можете использовать следующие теги:\r\n<b>{%site%}</b> - название сайта, с которого отправлено уведомление\r\n<b>{%username%}</b> - автор комментария\r\n<b>{%date%}</b> - дата написания\r\n<b>{%link%}</b> - ссылка на файл, к которому был оставлен комментарий\r\n<b>{%ip%}</b> - IP адрес автора\r\n<b>{%text%}</b> - текст комментария', 'Уважаемый администратор,\r\n\r\nуведомляем вас о том, что на сайт {%site%} был добавлен комментарий.\r\n\r\n------------------------------------------------\r\nКраткая информация о комментарии\r\n------------------------------------------------\r\n\r\nАвтор: {%username%}\r\nДата добавления: {%date%}\r\nСсылка на фотографию: {%link%}\r\nIP адрес: {%ip%}\r\n------------------------------------------------\r\nТекст комментария\r\n------------------------------------------------\r\n\r\n{%text%}\r\n\r\nС уважением,\r\nАдминистрация {%site%}')";


foreach($tableSchema as $table){
	$db->query ($table);
}

@unlink(ENGINE_DIR.'/cache/system/gallery_category.php');
@unlink(ENGINE_DIR.'/cache/system/gallery_cron.php');
@unlink(ENGINE_DIR.'/cache/system/gallery_banned.php');
clear_gallery_cache();
clear_gallery_vars();

msgbox("info","Информация", "<form action=\"index.php\" method=\"GET\">Обновление базы данных с версии <b>4.0.7</b> до версии <b>4.1</b> успешно завершено.<br />Нажмите далее для продолжения процессa обновления скрипта<br /><br /><input type=\"hidden\" name=\"next\" value=\"4.1\"><input class=\"edit\" type=\"submit\" value=\"Далее ...\"></form>");

?>