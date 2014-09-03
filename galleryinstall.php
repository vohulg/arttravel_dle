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
 Данный код защищен авторскими правами
 This file may no be redistributed in whole or significant part.	
 Файл не может быть изменён или использован без прямого согласия автора
 Запрещается использование файла в люббых комменрческих целях
=====================================================
 Файл: galleryinstall.php
-----------------------------------------------------
 Назначение: Главная страница
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
galHeader("Установка скрипта автоматически заблокирована");

echo <<<HTML
<form method=POST action="">
<table width="100%">
    <tr>
        <td style="padding:2px;">Внимание, на сервере обнаружена уже установленная копия TWS Gallery. Если вы хотите еще раз произвести установку скрипта, то вам необходимо вручную удалить файл <b>/engine/data/gallery.config.php</b> (если он существует), используя FTP протокол, а так же удалить таблицу <b>_gallery_config</b> из базы данных (если она существует). При этом все существующие данные будут уничтожены.<br /><br /></td>
    </tr>
    <tr>
        <td style="padding:2px;"><input class=buttons type=submit value=" Обновить "></td>
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
// Приветствие
// ********************************************************************************
echoheader("", "");
galHeader("Мастер установки скрипта");

$last_news = @file_get_contents("http://inker.wonderfullife.ru/extras/updates.php?script=twsg&install=1&dle=".$config['version_id']."&version=".$new_version."&host=".$_SERVER['HTTP_HOST']);

echo <<<HTML
<form method=POST action="{$PHP_SELF}">
<table width="100%">
    <tr>
        <td style="padding:2px;">Добро пожаловать в мастер установки <b>TWS Gallery {$new_version}</b>. Данный мастер поможет вам установить скрипт всего за пару минут. Однако, не смотря на это, мы настоятельно рекомендуем Вам ознакомиться с документацией по его установке, которая поставляется вместе со скриптом.<br><br>
Прежде чем начать установку убедитесь, что все файлы дистрибутива загружены на сервер, а также выставлены необходимые права доступа для папок и файлов. Далее произведите правку файлов в соответствии с инструкцией или просто заменить свои файлы движка прилагаемыми в архиве.<br><br>
<font color="red">Внимание: если после установки галереи, она не открывается или работает не правильно - <b>не нужно снова и снова запускать файл galleryinstall.php</b>, т.к. данный файл предназначен только для создания таблиц галереи в базе данных! Как правило, неработоспособность галереи возникает вследствии других причин. Вы можете задать вопросы по установке скрипта на форуме поддержки inker.wonderfullife.ru!</font><br><br>
Приятной Вам работы,<br><br>
Al-x by TWS<br><br></td>
    </tr>
    <tr>
        <td style="padding:2px;"><input type=hidden name=action value="eula"><input class=buttons type=submit value="Начать установку"></td>
    </tr>
</table>
</form>
HTML;

galFooter();
echofooter();

} elseif($_REQUEST['action'] == "eula"){

echoheader("", "");

galHeader("Лицензионное соглашение");

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
		alert( 'Вы должны принять лицензионное соглашение, прежде чем продолжите установку.' );
		return false;
	}
}
document.getElementById( 'check-eula' ).onsubmit = check_eula;
HTML;
echo "</script>";

$lic_text = <<<HTML
<!--sizestart:3--><span style="font-size:12pt;line-height:100%"><!--/sizestart-->Лицензионное пользовательское соглашение<!--sizeend--></span><!--/sizeend--><br /><br /><b>Предмет лицензионного соглашения</b><br /><br />Предметом настоящего лицензионного соглашения является право на использование одной лицензионной копии программного продукта <b>TWS Gallery</b> совместно с <b>DataLife Engine</b>, в порядке и на условиях, установленных настоящим соглашением. Если вы не согласны с условиями данного соглашения, вы не должны использовать данный продукт. Установка и использование продукта означает ваше полное согласие со всеми пунктами настоящего соглашения.<br /><br /><b>Статус лицензионного соглашения</b><br /><br />Лицензионное соглашение на скрипт <b>TWS Gallery</b> составлено на основе лицензионного соглашения на программный продукт <b>DataLife Engine</b>, расположенного на официальном сайте <b>DataLife Engine</b>. Лицензионное соглашение на <b>TWS Gallery</b> не входит в противоречие с лицензионным соглашением на <b>DataLife Engine</b> и никак не нарушает прав указанного соглашения <b>SoftNews Media Group</b>. Вместе с этим лицензионное соглашение на <b>TWS Gallery</b> является самостоятельным соглашением и никак не зависит от <b>SoftNews Media Group</b> и других соглашений. Публикация данного лицензионного соглашения является законным.<br /><br /><b>Содержание договора</b><br /><br />Срок обслуживания клиента с момента приобретения одной лицензионной копии программного продукта <b>TWS Gallery</b> совместно с <b>DataLife Engine</b> по базовой лицензии равен <b>одному году</b>. Если по истечении срока обслуживания, Вы решите не продлевать его действие, Ваш программный продукт будет функционировать в полном объеме, но без нашей технической поддержки и без предоставления новых версий скрипта, за исключением критических обновлений безопасности скрипта, а так же без гарантий совместимости с новыми версиями <b>DataLife Engine</b>.<br /><br />В случае приобретения и использования базовой лицензии на скрипт, клиент имеет право только на стандартный годовой пакет услуг службы технической поддержки TWS Gallery.  В стандартный пакет входит: предоставление дистрибутивов, новых версий скрипта, критических обновлений скрипта. Обязательное индивидуальное техническое обслуживание по базовым лицензиям не предусмотрено. Однако возможны бесплатные консультации по работе TWS Gallery на общем форуме поддержки. Так же базовая лицензия подразумевает обязательное наличие ссылки на сайт <b>wonderfullife.ru</b> на главной странице галереи. Для получения постоянной индивидуальной <a href="http://wonderfullife.ru/14-tehnicheskaya-podderzhka.html" >технической поддержки</a> по скрипту или удаления вышеупомянутой ссылки, пользователям необходимо приобрести расширенную лицензию, включающую в себя полный пакет услуг службы технической поддержки, либо стать подписчиком материалов службы технической поддержки.<br /><br />Мы оставляем за собой право публиковать списки избранных пользователей своих программных продуктов. Мы оставляем за собой право в любое время изменять условия данного договора, но данные действия не имеют обратной силы. Информация о любых изменениях настоящего договора будет отправлена пользователям по электронной почте на адреса, указанные при приобретении системы.<br /><br /><b>Ограниченное использование</b><br /><br />Приобретая лицензию на программный продукт <b>TWS Gallery</b>, вы должны знать, что приобретаете только <b>право на использование</b> программного продукта, но не авторские права на него. Авторское право на программный продукт принадлежит администратору и владельцу сетевого ресурса <a href="http://wonderfullife.ru" >wonderfullife.ru</a> и защищено законом. Лицензия на программный продукт <b>TWS Gallery</b> предполагает использование <b>TWS Gallery</b> на единственном веб-сайте (одном имени домена) и его поддоменах, принадлежащим Вам или Вашему клиенту. Для использования скрипта на другом сайте, вам необходимо приобретать лицензию повторно. Запрещается перепродажа скрипта третьим лицам, если же вы являетесь посредником и приобретаете продукт изначально не для себя, вы обязаны ознакомить Ваших клиентов с данным лицензионным соглашением. Мы не несём никаких обязательств по их технической поддержке и обслуживанию. Пакеты услуг службы поддержки предусмотрены только для тех, кто работает с нами напрямую, а не через третьих лиц.  <br /><br /><b>Права и обязанности сторон</b><br /><br /><b>Покупатель обязан:</b><br /><br /><!--dle_list--><ul><li>Ознакомиться с минимальными техническими требованиями, необходимыми для нормального функционирования скрипта, и удостовериться, что указанные требования выполнены.</li></ul><!--dle_list_end--><br /><b>Покупатель имеет право:</b><br /><br /><!--dle_list--><ul><li>Изменять дизайн и структуру программного кода в соответствии со своими желаниями и под нужды своего сайта.<br /></li><!--dle_li--><li>Создавать и распространять инструкции по Вашим модификациям наших шаблонов и языковых файлов, при условии, что в инструкциях будет присутствовать указание на оригинального разработчика TWS Gallery, в последствие модифицированной Вами. Все дополнения и изменения, внесённые Вами в наш продукт самостоятельно, не являются собственностью TWS, если не содержат программные коды непосредственно скрипта.<br /></li><!--dle_li--><li>Создавать модули, совместимые и взаимодействующие с нашими программными кодами, однако с отметками, что это Ваша разработка, оригинальный продукт и ответственность за его работу и техническая поддержка лежит на Вас.<br /></li><!--dle_li--><li>Переносить программный продукт на другой сайт после предварительного и обязательного уведомления нас о переносе, а также полного удаления скрипта с предыдущего сайта.</li></ul><!--dle_list_end--><br /><b>Покупатель не имеет право:</b><br /><br /><!--dle_list--><ul><li>Передавать права на использование программного продукта третьим лицам.<br /></li><!--dle_li--><li>Изменять структуру программных кодов, функции программы с целью создания или распространения родственных продуктов.<br /></li><!--dle_li--><li>Создавать отдельные самостоятельные продукты, базирующиеся на программном коде скрипта TWS Gallery. <br /></li><!--dle_li--><li>Использовать копии программного продукта TWS Gallery по одной лицензии на более чем одном сайте (одном имени домена и его поддоменах)<br /></li><!--dle_li--><li>Рекламировать, продавать или публиковать, распространять или содействовать распространению нелицензионных копий программного продукта TWS Gallery и DataLife Engine<br /></li><!--dle_li--><li>Удалять программные компоненты, осуществляющие проверку наличия на сайте оригинальной лицензии на использование скрипта.</li></ul><!--dle_list_end--><br /><b>Ограничение гарантийных обязательств</b><br /><br />Компоненты безопасности, установленные на <b>TWS Gallery</b>, имеют ряд ограничений. И несмотря на то, что мы прилагаем максимальные усилия по обеспечению безопасности скрипта, вы должны понимать, что абсолютной защиты от взлома вашего сайта не существует. Наши гарантии и техническая поддержка не распространяются также на модификации продукта и любые изменения <b>TWS Gallery</b>, произведенные владельцами лицензии или третьей стороной, включая изменения программного кода, стиля, языковых пакетов. Сами изменяли - сами обслуживайте. Не работает - используйте рабочую версию, приобретённую у нас вместе с пакетом услуг службы поддержки. Программный продукт <b>TWS Gallery</b> не подлежит возврату или обмену из-за отсутствия гарантий, защищающих программный продукт от копирования, а так же из-за несоответствий ваших серверов и ПО минимальным техническим требованиям, приведённых <a href="http://wonderfullife.ru/11-sistemnye-trebovaniya.html" >по данной ссылке</a> и необходимых для нормального функционирования скрипта.<br /><br /><b>Механизмы активации</b><br /><br />Программный продукт <b>TWS Gallery</b> имеет компонент проверки приобретения и подлинности лицензии. До момента активации лицензии функционирование скрипта ограниченно. Под активацией подразумевается ввод лицензионного ключа (выданного при приобретении лицензии) в соответствующее поле в административной панели. Компонент активации <b>TWS Gallery</b> может привязывается к личным данным администратора сайта или настройкам галереи, однако изменение этих данных <b>не</b> приведёт к потери права на использование продукта. <b>Для проведения активации обязательно наличие интернета на том сайте, где производится установка TWS Gallery.</b> В случае невозможности активации через интернет использование продукта возможно в случае согласия покупателя с дополнительными требованиями, которые оговариваются отдельно.<br /><br /><b>Права на интеллектуальную собственность</b><br /><br />Наименование <b>TWS Gallery</b>, а также входящие в данный продукт скрипты, являются собственностью <b>TWS</b>, за исключением случаев, когда для компонента системы применяется другой тип лицензии. Программный продукт защищен законом. Любые публикуемые оригинальные материалы, создаваемые на базе программного кода нашего скрипта, являются собственностью администратора и владельца сетевого ресурса <a href="http://wonderfullife.ru" >wonderfullife.ru</a> и защищены законом. <b>TWS</b> не несет никакой ответственности за содержание сайтов, создаваемых пользователем скрипта <b>TWS Gallery</b>.<br /><br /><b>Досрочное расторжение договорных обязательств</b><br /><br />Соглашение расторгается автоматически при нарушении условий данного договора или отказе от исполнения обязательств по заключённому договору. Лицензионное соглашение может быть расторгнуто нами в одностороннем порядке, в случае установления фактов нарушения условий данного лицензионного соглашения. В случае досрочного расторжения договора, Вы обязуетесь удалить все Ваши копии нашего программного продукта в течении 3 рабочих дней с момента получения соответствующего уведомления.
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
        <td style="padding:2px;">Пожалуйста, внимательно прочитайте и примите пользовательское соглашение по использованию TWS Gallery совместно с DataLife Engine.<br /><br /><div style="height: 300px; border: 1px solid #76774C; background-color: #FDFDD3; padding: 5px; overflow: auto;">{$lic_text_str}</div>
		<input type='checkbox' name='eula' id='eula'><label for="eula"><b>Я принимаю данное соглашение</b></label>
		<br />
</td>
    </tr>
    <tr>
        <td style="padding:2px;"><input type=hidden name=action value="version_check"><input class=buttons type=submit value=" Продолжить >> "></td>
    </tr>
</table>
</form>
HTML;

} elseif ($_REQUEST['action'] == "version_check"){

// ********************************************************************************
// Проверка версии движка
// ********************************************************************************

	if (!in_array($config['version_id'], array("9.2", "9.3", "9.4", "9.5", "9.6", "9.7", "9.8"))){

		echoheader("", "");
		galHeader("Проверка версии CMS");

		echo <<<HTML
<form method=POST action="{$PHP_SELF}">
<table width="100%">
	<tr>
		<td style="padding:2px;"><br><br><font color="red"><b>Внимание: версия CMS DLE не соответсвует требуемой для нормальной работы галереи! Вы можете продолжить установку на свой страх и риск, однако работоспособность галереи не гарантируется! Так же разработчики скрипта галереи не занимаются выпуском патчей для адаптации устаревших версий на новые версии cms. В случае, если разработчики обещали, что устанавливаемая версия галереи будет работать на вашей версии движка, пожалуйста, сообщите об этой ошибке разработчикам!</b></font><br><br>
Приятной Вам работы,<br><br>
Al-x by TWS</td>
	</tr>
	<tr>
		<td style="padding:2px;"><input type=hidden name=action value="chmod_check"><input class=buttons type=submit value="Продолжить установку"></td>
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
galHeader("Проверка на запись у важных файлов системы");

echo <<<HTML
<form method=POST action="$PHP_SELF">
<table width="100%">
HTML;

echo"<tr>
<td height=\"25\">&nbsp;Папка/Файл
<td width=\"100\" height=\"25\">&nbsp;CHMOD
<td width=\"100\" height=\"25\">&nbsp;Статус</tr><tr><td colspan=3><div class=\"hr_line\"></div></td></tr>";
 
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
            $file_status = "<font color=red>не найден!</font>";
            $not_found_errors ++;
        }
        elseif(is_writable($file)){
            $file_status = "<font color=green>разрешено</font>";
        }
        else{
            @chmod($file, 0777);
            if(is_writable($file)){
                $file_status = "<font color=green>разрешено</font>";
            }else{
                @chmod("$file", 0755);
                if(is_writable($file)){
                    $file_status = "<font color=green>разрешено</font>";
                }else{
                    $file_status = "<font color=red>запрещено</font>";
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
$status_report = 'Проверка успешно завершена! Можете продолжить установку!';
}else{
if($chmod_errors > 0){
$status_report = "<font color=red>Внимание!!!</font><br /><br />Во время проверки обнаружены ошибки: <b>$chmod_errors</b>. Запрещена запись в файл.<br />Вы должны выставить для папок CHMOD 777, для файлов CHMOD 666, используя ФТП-клиент.<br /><br /><font color=red><b>Настоятельно не рекомендуется</b></font> продолжать установку, пока не будут произведены изменения.<br />";
}
if($not_found_errors > 0){
$status_report .= "<font color=red>Внимание!!!</font><br />Во время проверки обнаружены ошибки: <b>$not_found_errors</b>. Файлы не найдены!<br /><br /><font color=red><b>Не рекомендуется</b></font> продолжать установку, пока не будут произведены изменения.<br />";
}
}

echo"<tr><td colspan=3><div class=\"hr_line\"></div></td></tr><tr><td height=\"25\" colspan=3>&nbsp;&nbsp;Состояние проверки</td></tr><tr><td style=\"padding: 5px\" colspan=3>$status_report</td></tr><tr><td colspan=3><div class=\"hr_line\"></div></td></tr>";    

echo <<<HTML
     <tr>
     <td height="40" colspan=3 align="right">&nbsp;&nbsp;
     <input class=buttons type=submit value="Продолжить >>">&nbsp;&nbsp;<input type=hidden name="action" value="function_check">
     </tr>
</table>
</form>
HTML;

galFooter();
echofooter();

} elseif ($_REQUEST['action'] == "function_check"){

echoheader("", "");
galHeader("Проверка настроек сервера");

	echo <<<HTML
<form method=POST action="$PHP_SELF">
<table width="100%">
HTML;

	echo"<tr>
<td height=\"25\" width=\"250\">&nbsp;Минимальные требования скрипта
<td height=\"25\" colspan=2>&nbsp;Текущее значение
<tr><td colspan=3><div class=\"hr_line\"></div></td></tr>";

	$status = ini_get('file_uploads') ? '<font color=green><b>Включено</b></font>' : '<font color=red><b>Выключено</b></font>';

	echo"<tr>
      <td height=\"22\" class=\"tableborder main\">&nbsp;Загрузка файлов</td>
      <td>&nbsp;$status</td>
      </tr><tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=2></td></tr>";

	$status = (ini_get('safe_mode') == 1 || ini_get('safe_mode') == 'on') ? '<font color=red><b>Включено</b></font>' : '<font color=green><b>Выключено</b></font>';

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

		$status = (@file_exists($handle.'/boxsmall.jpg') && !(@ini_get('safe_mode') == 1 || @ini_get('safe_mode') == 'on')) ? '<font color=green><b>Успешно</b></font>' : '<font color=red><b>Ошибка!</b> Не удалось создать папку и скопировать файл!</font>';

		@unlink($handle.'/boxsmall.jpg');
		@rmdir($handle);

	} else $status = '<font color=red><b>Ошибка!</b> Не найден файл /uploads/gallery/boxsmall.jpg</font>';

	echo"<tr>
      <td height=\"22\" class=\"tableborder main\">&nbsp;Проверка загрузки</td>
      <td>&nbsp;$status</td>
      </tr><tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=2></td></tr>";

	$status = !function_exists('gzopen') ? '<font color=red><b>Выключено</b></font>' : '<font color=green><b>Включено</b></font>';;

	echo"<tr>
      <td height=\"22\" class=\"tableborder main\">&nbsp;Gzopen</td>
      <td>&nbsp;$status</td>
      </tr><tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=2></td></tr>";

	$maxupload = str_replace(array('M','m'), '', @ini_get('upload_max_filesize'));
	$maxupload = intval($maxupload);

	$status = ($maxupload && $maxupload < 2) ? '<font color=red><b>Ограничение занижено (менее 2 Мегабайт)</b></font>' : '<font color=green><b>В норме (более 2 Мегабайт)</b></font>';;

	echo"<tr>
      <td height=\"22\" class=\"tableborder main\">&nbsp;upload_max_filesize</td>
      <td>&nbsp;$status</td>
      </tr><tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=2></td></tr>";

	echo"<tr>
         <td colspan=2 class=\"navigation\"><br />Если любой из этих пунктов выделен красным, то пожалуйста выполните действия для исправления положения. В случае несоблюдения минимальных требований скрипта возможна его некорректная работа в системе.<br /><br /></td>
         </tr>";

	echo <<<HTML
     <tr>
     <td height="40" colspan=3 align="right">&nbsp;&nbsp;
     <input class=buttons type=submit value="Продолжить >>">&nbsp;&nbsp;<input type=hidden name="action" value="set_config">
     </tr>
	</table>
	</form>
HTML;

galFooter();
echofooter();

} elseif ($_REQUEST['action'] == "set_config"){

echoheader("", "");
galHeader("Настройка конфигурации системы");

echo <<<HTML
<form method=POST action="$PHP_SELF">
Ниже вы можете указать некоторые настройки, которые будут автоматически применены при установке скрипта. Все настройки ниже могут быть впоследствии изменены в настройках системы, т.е. при необходимости, вы сможете удалить не нужные вам категории, создать новые, или отредактировать созданные. Все параметры ниже даны только для упрощения процесса настройки, поэтому для того, чтобы настроить галереию иначе (чем будет отмечено ниже) <b>не нужно</b> запускать данный инсталятор снова.<br /><br />

<div class="unterline"></div>
<table width="100%">
HTML;

echo'<tr>
<td style="padding: 5px;">Создать базовые категории:
<td style="padding: 5px;">
<select class=rating name="create_cats"><option value="1">Да</option><option value="0">Нет</option></select>&nbsp;&nbsp;<span class="navigation">Eсли вы хотите, чтобы скрипт создал несколько категорий в качестве образца, можете включить данную опцию. Будут созданы широкораспространённые по тематике категории с разными параметрами (в качестве примера для ваших будущих категорий).</span>
</tr>
<tr>
<td style="padding: 5px;">Включить поддержку видеофайлов и флэш:
<td style="padding: 5px;">
<select class=rating name="allow_video"><option value="1">Да</option><option value="0">Нет</option></select>&nbsp;&nbsp;<span class="navigation">Eсли вы хотите, чтобы скрипт разрешил загрузку media файлов, можете включить данную опцию. Если разрешено создание базовых категорий, то будет создана группа категорий для загрузки media файлов.</span>
</tr>
<tr>
<td style="padding: 5px;">Включить поддержку аудиофайлов:
<td style="padding: 5px;">
<select class=rating name="allow_audio"><option value="1">Да</option><option value="0">Нет</option></select>&nbsp;&nbsp;<span class="navigation">Eсли вы хотите, чтобы скрипт разрешил загрузку audio файлов, можете включить данную опцию. Если разрешено создание базовых категорий, то будет создана группа категорий для загрузки audio файлов.</span>
</tr>
<tr>
<td style="padding: 5px;">Включить поддержку rar и zip архивов:
<td style="padding: 5px;">
<select class=rating name="allow_rarzip"><option value="1">Да</option><option value="0">Нет</option></select>&nbsp;&nbsp;<span class="navigation">Eсли вы хотите, чтобы скрипт разрешил загрузку архивных файлов, можете включить данную опцию. Если разрешено создание базовых категорий, то будет создана группа категорий для загрузки архивных файлов.</span>
</tr>
<tr>
<td style="padding: 5px;">Включить загрузку с сервисов youtube.com, rutube.ru, video.mail.ru, vimeo.com, smotri.com, gametrailers.com:
<td style="padding: 5px;">
<select class=rating name="allow_youtube"><option value="1">Да</option><option value="0">Нет</option></select>&nbsp;&nbsp;<span class="navigation">Eсли вы хотите, чтобы скрипт разрешил загрузку файлов с указанных сервисов, можете включить данную опцию. Если разрешено создание базовых категорий, то будет создана группа категорий для файлов с данных сервисов.</span>
</tr>
<tr>
<td style="padding: 5px;">Разрешить пользователям создавать подкатегорию в публичных категориях:
<td style="padding: 5px;">
<select class=rating name="allow_user_create"><option value="1">Да</option><option value="0">Нет</option></select>&nbsp;&nbsp;<span class="navigation">Eсли вы хотите, чтобы пользователи могли создавать публичные категории и подкатегории по профилям, можете включить данную опцию. В этом случае будет создано несколько профилей категорий для соответвующих типов файлов.</span>
</tr>
<tr>
<td style="padding: 5px;">Разрешить пользователям загружать файлы в публичные категории:
<td style="padding: 5px;">
<select class=rating name="allow_user_upload"><option value="1">Да</option><option value="0">Нет</option></select>&nbsp;&nbsp;<span class="navigation">Eсли вы хотите, чтобы пользователи могли загружать файлы в публичные категории, можете включить данную опцию.</span>
</tr>
<tr>
<td style="padding: 5px;">Использовать предмодерацию для файлов, которые загружают пользователи в публичные категории:
<td style="padding: 5px;">
<select class=rating name="moderate"><option value="1">Да</option><option value="0">Нет</option></select>&nbsp;&nbsp;<span class="navigation">Eсли вы хотите, чтобы загружаемые пользователем файлы попадали на модерацию администраторам, можете включить данную опцию.</span>
</tr>
<tr>
<td style="padding: 5px;">Разрешить пользователям создать несколько личных подкатегорий:
<td style="padding: 5px;">
<select class=rating name="allow_user_own"><option value="1">Да</option><option value="0">Нет</option></select>&nbsp;&nbsp;<span class="navigation">Eсли вы хотите, чтобы пользователь мог создать несколько личных категорий, имел возможность ими управлять, а так же загружать в них файлы без предмодерации, можете включить данную опцию. В этом случае будет создан соответвующий профиль категории. Включение данной опции <b>не</b> предоставит возможности пользователям управлять файлами в публичных категориях, даже если пользователи будут являться авторами таких категорий.</span>
</tr>
<tr><td style="padding: 5px;">Максиммальное количество личных категорий для одного пользователя:<td style="padding: 5px;"><input class="edit" type=text size="10" value="1" name="max_users_cats"></tr>
<tr>
<td style="padding: 5px;">Разрешить комментирование и рейтинг файлов:
<td style="padding: 5px;">
<select class=rating name="allow_comrate"><option value="1">Да</option><option value="0">Нет</option></select>&nbsp;&nbsp;<span class="navigation">Eсли вы хотите, чтобы пользователи могли комментировать файлы и выставлять рейтинг, можете включить данную опцию.</span>
</tr>
';

echo <<<HTML
     <tr>
     <td height="40" colspan=3 align="right">&nbsp;&nbsp;
     <input class=buttons type=submit value="Продолжить >>">&nbsp;&nbsp;<input type=hidden name="action" value="doinstall">
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
'description' => 'Моя tws галерея картинок',
'keywords' => 'Личные галереи, картинки, обои, tws',
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
'empty_title_template' => 'Файл {%i%}',
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

 $tableSchema[] = "INSERT IGNORE INTO `" . PREFIX . "_tws_email` (`prefix`, `name`, `title`, `description`, `template`) VALUES (1, 'gallery_newcomment', 'E-Mail сообщение, которое отсылается при добавлении нового комментария к файлу', 'При написании шаблона для данного сообщения вы можете использовать следующие теги:\r\n<b>{%site%}</b> - название сайта, с которого отправлено уведомление\r\n<b>{%username_to%}</b> - получатель сообщения\r\n<b>{%username%}</b> - автор комментария\r\n<b>{%date%}</b> - дата написания\r\n<b>{%link%}</b> - ссылка на файл, к которому был оставлен комментарий с параметром продолжения подписки\r\n<b>{%ip%}</b> - IP адрес автора\r\n<b>{%text%}</b> - текст комментария\r\n<b>{%unsubscribe%}</b> - ссылка на отмену подписки на комментарии к данной новости', 'Уважаемый посетитель,\r\n\r\nуведомляем вас о том, что на сайт {%site%} был добавлен комментарий.\r\n\r\n------------------------------------------------\r\nКраткая информация о комментарии\r\n------------------------------------------------\r\n\r\nАвтор: {%username%}\r\nДата добавления: {%date%}\r\nСсылка на фотографию: {%link%}\r\nIP адрес: {%ip%}\r\n------------------------------------------------\r\nТекст комментария\r\n------------------------------------------------\r\n\r\n{%text%}\r\n\r\n------------------------------------------------\r\n\r\nДля продолжения подписки на комментарии к данному файлу необходимо прочитать уже оставленные пользователями комментарии, перейдя по ссылке {%link%}\r\n\r\nЕсли вы не хотите больше получать уведомлений о новых комментариях к данной новости, то проследуйте по данной ссылке: {%unsubscribe%}\r\n\r\nС уважением,\r\nАдминистрация {%site%}')";
 $tableSchema[] = "INSERT IGNORE INTO `" . PREFIX . "_tws_email` (`prefix`, `name`, `title`, `description`, `template`) VALUES (1, 'gallery_newfoto', 'E-Mail сообщение, которое отсылается при загрузке новой фотографии пользователями, требующей модерации', 'При написании шаблона для данного сообщения вы можете использовать следующие теги:\r\n<b>{%name%}</b> - имя пользователя, которому отправлено уведомление\r\n<b>{%site%}</b> - название сайта, с которого отправлено уведомление\r\n<b>{%username%}</b> - имя загрузившего\r\n<b>{%ip%}</b> - IP адрес загрузившего (доступен только администраторам)\r\n<b>{%date%}</b> - дата загрузки\r\n<b>{%category%}</b> - имя категории, в которую были загружены файлы\r\n<b>{%images%}</b> - количество новых файлов\r\n<b>{%link%}</b> - ссылка для модерирования файлов', 'Уважаемый {%name%}, \r\n\r\nуведомляем вас о том, что в галерею на сайте {%site%} были добавлены новые фотографии, требующие модерации.\r\n\r\n------------------------------------------------\r\nКраткая информация о файлах\r\n------------------------------------------------\r\n\r\nАвтор: {%username%}{%ip%}\r\nДата добавления: {%date%}\r\nКатегория: {%category%}\r\nКоличество файлов: {%images%}\r\n------------------------------------------------\r\n\r\nПровести администрирование фотографий вы можете, перейдя по ссылке ниже\r\n{%link%}\r\n\r\nС уважением,\r\nАдминистрация {%site%}')";
 $tableSchema[] = "INSERT IGNORE INTO `" . PREFIX . "_tws_email` (`prefix`, `name`, `title`, `description`, `template`) VALUES (1, 'gallery_editfoto', 'PM сообщение, которое отсылается при отсылке уведомления пользователю', 'При написании шаблона для данного сообщения вы можете использовать следующие теги:\r\n<b>{%name%}</b> - имя пользователя, которому отправлено уведомление\r\n<b>{%site%}</b> - название сайта, с которого отправлено уведомление\r\n<b>{%username%}</b> - имя администратора\r\n<b>{%usergroup%}</b> - группа администратора\r\n<b>{%date%}</b> - дата редактирования\r\n<b>{%fileslist%}</b> - список отредактированных файлов и ссылки на них\r\n<b>{%action%}</b> - действие, которое выполнил администратор\r\n<b>{%notice%}</b> - текст сообщения, которое указал адмиинистратор', 'Уважаемый {%name%},\r\n\r\nуведомляем вас о том, что следующие добавленные вами файлы были отредактированы в галерее:\r\n\r\n{%fileslist%}\r\n\r\n[action]Действие: {%action%}[/action]\r\n[notice]Указанная причина: {%notice%}[/notice]\r\n\r\nАдминистратор: {%username%}\r\nГруппа: {%usergroup%}\r\nДата: {%date%}')";
 $tableSchema[] = "INSERT IGNORE INTO `" . PREFIX . "_tws_email` (`prefix`, `name`, `title`, `description`, `template`) VALUES (1, 'gallery_subscribe', 'E-Mail сообщение, которое отсылается для подтверждения подписки', 'При написании шаблона для данного сообщения вы можете использовать следующие теги:\r\n<b>{%site%}</b> - название сайта, с которого отправлено уведомление\r\n<b>{%subscribe%}</b> - ссылка на подтверждение подписки на комментарии', 'Уважаемый посетитель,\r\n\r\nвы подписывались на рассылку новых комментариев в галерее на сайте {%site%}.\r\n\r\nДля подтверждения подписки пройдите по следующей ссылке: {%subscribe%}\r\n\r\nЕсли вы никогда не бывали на нашем сайте и не подписывались на комментарии - не обращайте внимание на данное сообщение - возможно кто-то просто ошибся при вводе e-mail адреса. Если сообщения будут приходить постоянно - пожалуйста, обратитесь к администратору сайта - мы постараемся решить вашу проблему.')";

$tableSchema[] = "INSERT IGNORE INTO " . PREFIX . "_admin_sections (name, title, descr, icon, allow_groups) VALUES ('twsgallery', 'Фотогалерея', 'Общие настройки, категории и изображения галереи', 'iPhoto.png', 'all')";

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
		 VALUES (0, 'Виды нашего города', 'Фотографиями зданий, улиц нашего города вы можете поделиться, разместив изображения в данном разделе', '', '', '', '{$posi}', 'city', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '1', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, '', 0, 0, 0, 1, '')");

	$posi++;
	$p_id_array[0] = $db->insert_id();

	$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
		 VALUES ({$p_id_array[0]}, 'Здания', 'Здания нашего города', '', '', '', '{$posi}', 'city/buildings', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, 'jpg,jpeg,jpe,png,gif', 0, 0, 0, 1, '')");

	$posi++;

	$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
		 VALUES ({$p_id_array[0]}, 'Улицы', 'Улицы нашего города', '', '', '', '{$posi}', 'city/streets', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, 'jpg,jpeg,jpe,png,gif', 0, 0, 0, 1, '')");

	$posi++;

	if ($set_allow_user_create){

		$db->query("INSERT INTO " . PREFIX . "_gallery_profiles (p_id, allow_user, profile_name, skin, moderators, upload_level, allowed_extensions, allow_user_admin, allow_watermark, allow_rating, allow_comments, auto_resize, alt_name_tpl) 
			VALUES ({$p_id_array[0]}, 1, 'Виды нашего города', '', '', '', 'jpg,jpeg,jpe,png,gif', 0, 1, 1, 1, 1, '{%category%}/')");

	}

	if ($set_allow_video){

		$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
			 VALUES (0, 'Видео галерея нашего города', 'Видеороликами ночных улиц, видеоэкскурсими по нашему городу вы можете поделиться в этом разделе', '', '', '', '{$posi}', 'videcity', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '1', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, '', 0, 0, 0, 1, '')");

		$posi++;
		$p_id_array[1] = $db->insert_id();

		$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
			 VALUES ({$p_id_array[1]}, 'Дневные прогулки', 'Видеоэкскурсии по городу', '', '', '', '{$posi}', 'videcity/videostreets', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, 'avi,wmv,flv,mp4,swf,mov,mkv,divx,mpg', 0, 0, 0, 1, '')");

		$posi++;

		$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
			 VALUES ({$p_id_array[1]}, 'Ночные улицы', 'Улицы нашего города, запечатлённые на видео в тёмное время', '', '', '', '{$posi}', 'videcity/videonightstreets', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, 'avi,wmv,flv,mp4,swf,mov,mkv,divx,mpg', 0, 0, 0, 1, '')");

		$posi++;

		if ($set_allow_user_create){

			$db->query("INSERT INTO " . PREFIX . "_gallery_profiles (p_id, allow_user, profile_name, skin, moderators, upload_level, allowed_extensions, allow_user_admin, allow_watermark, allow_rating, allow_comments, auto_resize, alt_name_tpl) 
				VALUES ({$p_id_array[1]}, 1, 'Видео нашего города', '', '', '', 'avi,wmv,flv,mp4,swf,mov,mkv,divx,mpg', 0, 1, 1, 1, 1, '{%category%}/')");

		}

	}

	if ($set_allow_audio){

		$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
			 VALUES (0, 'Музыка', 'Музыкальный раздел нашего сайта', '', '', '', '{$posi}', 'music', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '1', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, '', 0, 0, 0, 1, '')");

		$posi++;
		$p_id_array[2] = $db->insert_id();

		$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
			 VALUES ({$p_id_array[2]}, 'Танцевальная музыка', '', '', '', '', '{$posi}', 'music/dance', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, 'mp3,3gp', 0, 0, 0, 1, '')");

		$posi++;

		$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
			 VALUES ({$p_id_array[2]}, 'Расслабляющая музыка', '', '', '', '', '{$posi}', 'music/relax', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, 'mp3,3gp', 0, 0, 0, 1, '')");

		$posi++;

		if ($set_allow_user_create){

			$db->query("INSERT INTO " . PREFIX . "_gallery_profiles (p_id, allow_user, profile_name, skin, moderators, upload_level, allowed_extensions, allow_user_admin, allow_watermark, allow_rating, allow_comments, auto_resize, alt_name_tpl) 
				VALUES ({$p_id_array[2]}, 1, 'Музыкальная подкатегория', '', '', '', 'mp3,3gp', 0, 1, 1, 1, 1, '{%category%}/')");

		}

	}

	if ($set_allow_rarzip){

		$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
			 VALUES (0, 'Файловый архив', 'Обмен файлами', '', '', '', '{$posi}', 'files', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '1,2,3', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, 'rar,zip', 0, 0, 0, 1, '')");

		$posi++;

	}

	if ($set_allow_youtube == 1){

		$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
			 VALUES (0, 'Видео с youtube.com', 'В эту карегорию можно загружать видео со следующих сайтов: youtube.com, rutube.ru, video.mail.ru, vimeo.com, smotri.com, gametrailers.com.', '', '', '', '{$posi}', 'youtube', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, 'youtube.com,rutube.ru,video.mail.ru,vimeo.com,smotri.com,gametrailers.com', 0, 0, 0, 1, '')");

		$posi++;

	}

}

if ($set_allow_user_own){

	$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, metatitle, meta_descr, keywords, position, cat_alt_name, user_name, locked, disable_upload, reg_date, last_date, last_cat_date, images, cat_images, all_time_images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin, sub_cats, allow_carousel, uploadskin)
		 VALUES (0, 'Личные албомы', 'В данном разделе каждый пользователь может создать свой личный альбом', '', '', '', '{$posi}', 'users', '{$member_name}', '0', '0', '{$last_date}', '{$last_date}', '0000-00-00 00:00:00', '0', '0', '0', '', '1', '', '', '', '', '', '', '1', '1', '1', '', 0, 0, 0, 0, 0, 1, '', '', '', '', '', 0, 0, 0, 0, 0, '', 0, 0, 0, 1, '')");

	$posi++;
	$p_id_array[3] = $db->insert_id();

	$db->query("INSERT INTO " . PREFIX . "_gallery_profiles (p_id, allow_user, profile_name, skin, moderators, upload_level, allowed_extensions, allow_user_admin, allow_watermark, allow_rating, allow_comments, auto_resize, alt_name_tpl) 
		 VALUES ({$p_id_array[3]}, 1, 'Личный альбом', '', '', '1', 'jpg,jpeg,jpe,png,gif', 1, 1, 1, 1, 1, '{%user%}/')");

}

$sql = $db->query("SELECT COUNT(*) as count, p_id FROM " . PREFIX . "_gallery_category WHERE p_id > 0 GROUP BY p_id");

while ($row = $db->get_row($sql))
	$db->query( "UPDATE " . PREFIX . "_gallery_category SET sub_cats={$row['count']} WHERE id={$row['p_id']}");

$db->free($sql);


$l=@file_get_contents("http://inker.wonderfullife.ru/extras/updates.php?script=twsg&install=2&dle=".$config['version_id']."&version=".$new_version."&host=".$_SERVER['HTTP_HOST']); // данная строчка передаёт статистику о том, что установка успешно завершена и взбоев в инсталяторе не обнаружено
unset($l);

echoheader("", "");
galHeader("Установка успешно завершена");

echo <<<HTML
<table width="100%">
    <tr>
        <td style="padding:2px;"><br>Поздравляем Вас, TWS Gallery {$new_version} была успешно установлена на Ваш сервер. Вы можете теперь просмотреть главную <a href="/index.php?do=gallery">страницу вашей галереи</a> и изучить возможности скрипта. Либо Вы можете <a href="/{$config['admin_path']}?mod=twsgallery&act=1">зайти</a> в панель управления и изменить другие настройки скрипта. 
<br><br><font color="red">Внимание: при установки скрипта создается структура базы данных, а также прописываются основные настройки системы, поэтому после успешной установки удалите файл <b>galleryinstall.php</b> во избежание повторной установки скрипта!</font><br><br>
Приятной Вам работы<br><br>
Al-x by TWS<br><br></td>
    </tr>
</table>
HTML;

galFooter();
echofooter();

}


?>