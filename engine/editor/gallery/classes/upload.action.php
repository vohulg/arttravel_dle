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
 Файл: upload.php
-----------------------------------------------------
 Назначение: Управление загрузкой файлов
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

class gallery_upload_action {

	var $page = 0;
	var $cat = 0;
	var $allow_remote = 0;
	var $upload_category = false;
	var $allow_create = 0;
	var $public_error = 0;
	var $onmoderation = false;
	var $buffer = "";
	var $lang = false;

	function gallery_upload_action($cat = 0){
	global $galConfig, $member_id, $is_logged, $config;

		if ($member_id['user_group'] == 1 && $is_logged){
			$galConfig['file_title_control'] = 0;
			$galConfig['tags_len'] = 0;
			$galConfig['tags_num'] = 0;
		}

		$this->cat = intval($cat);

		//$this->allow_create = !defined('ACP_ACTIVE') ? check_gallery_access ("addcat", "") : 2;$this->allow_create  = 0;
		$this->full_access = !defined('ACP_ACTIVE') ? check_gallery_access ("edit", "", "") : 2;
		$c_var = explode(',',$galConfig['remotelevel']);
		$this->allow_remote = (defined('ACP_ACTIVE') || $c_var[0] == '-1' || $c_var[0] == '' || $member_id['user_group'] == 1 && $is_logged || in_array($member_id['user_group'], $c_var));

		if (defined('ACP_ACTIVE')) $galConfig['max_once_upload'] = 200;
		else {

			if ($member_id['user_group'] == 1 && $is_logged) $galConfig['max_once_upload'] = 100;
			elseif ($this->full_access) $galConfig['max_once_upload'] = 50;
			elseif ($galConfig['rewrite_mode'] == '2') $galConfig['rewrite_mode'] = '1';

			if ($galConfig['max_once_upload'] < 1) $galConfig['max_once_upload'] = 50;
			if (date('H', TIME) < 12 && date('H', TIME) > 2) $galConfig['max_once_upload'] *= 2;

		}

		if ($config["lang_".$config['skin']]){
			include_once ROOT_DIR . '/language/' . $config["lang_" . $config['skin']] . '/gallery.upload.lng';
		} else {
			 include_once ROOT_DIR.'/language/'.$config['langs'].'/gallery.upload.lng';
		}

		if ($this->cat) $this->cat = $this->load_cat_by_id($this->cat);

	}

	function gallery_upload_init($act = 0){
	global $galConfig, $langGal;

		$this->set_metatags("<a href=\"{$galConfig['PHP_SELF']}&act=26\">".$langGal['menu_title11']."</a>", $langGal['menu_title11']);

		if ($galConfig['enable_banned'] && ($ban = check_banned()) != false){

			if (AJAX_ACTION) die("error");

			$this->msgbox ('all_err_1', $ban, array(1));
			return;

		}

		switch (intval($act)){
		case 0 :
			$this->page_prepare();
		break;
		case 1 :
			$this->page_upload();
		break;
		case 2 :

			$this->set_metatags($langGal['menu_title13']);

			if (!$this->show_wait_files())
				$this->msgbox ('all_info', $this->lang['no_wait_files'], array(0));

		break;
		case 3 :
			$this->upload_complete();
		break;
		case 4 :
			$this->remove_wait_files();
		break;
		}

	}

	function load_cat_by_id($id){
	global $db;

		if (isset($this->upload_category[$id])) return $id;

		$this->upload_category[$id] = $db->super_query("SELECT * FROM " . PREFIX . "_gallery_category WHERE id='{$id}'");

		if ($this->upload_category[$id]['id']){

			$this->upload_category[$id]['allow_upload'] = !defined('ACP_ACTIVE') ? check_gallery_access ("upload", $this->upload_category[$id]['upload_level'], $this->upload_category[$id]['moderators'], $this->upload_category[$id]['mod_level'], ($this->upload_category[$id]['locked'] || $this->upload_category[$id]['disable_upload']), $this->upload_category[$id]['user_name'], $this->upload_category[$id]['allow_user_admin']) : 2;
			$this->upload_category[$id]['allow_edit'] = !defined('ACP_ACTIVE') ? check_gallery_access ("edit", $this->upload_category[$id]['edit_level'], $this->upload_category[$id]['moderators']) : 2;

		}

		return intval($this->upload_category[$id]['id']);
	}

	function category_rules($id){
	global $langGal, $galConfig, $config;

		if (!$id || !($id = $this->load_cat_by_id($id)))
			return array($this->lang['js_lang_17'], "", 0, "", "");

		if (!$this->upload_category[$id]['allow_upload'])
			return array("<br />".str_ireplace('{cattitle}',stripslashes($this->upload_category[$id]['cat_title']), $this->lang['add_foto_error_18']), "", 0, "", "var cattitle = {'m':0,'id':{$id},'a':'".$this->upload_category[$id]['cat_alt_name']."','t':'".stripslashes($this->upload_category[$id]['cat_title'])."'};");

		$allowed_ext = ($this->upload_category[$id]['allowed_extensions']) ? $this->upload_category[$id]['allowed_extensions'] : $galConfig['allowed_extensions'];
		$allowed_ext = explode(',',$allowed_ext);

		$max_file_size = 0;
		$allowed_extensions = array();
		$limitsizetable = array();
		$limitsizetext = array();

		foreach ($galConfig['extensions'] as $ext => $options){
			if (($allowed_ext[0] == 'all' || in_array($ext, $allowed_ext)) && !in_array($ext, array('youtube.com', 'rutube.ru', 'video.mail.ru', 'vimeo.com','smotri.com','gametrailers.com'))){
				$allowed_extensions[] = $ext;
				$allowed_extensions[] = strtoupper($ext);
				if (!$options['s'] || in_array($ext, array('jpg','jpeg','jpe','png','gif')) && $galConfig['allow_foto_resize'] && $this->upload_category[$id]['auto_resize']){
					$max_file_size = 1000 * 1024;
					$limitsizetext[] = $ext;
				} else {
					if ($this->upload_category[$id]['size_factor']) $options['s'] *= $this->upload_category[$id]['size_factor']/100;
					$limitsizetable[] = '"'.$ext.'":"'.$options['s'].'"';
					$max_file_size = max($max_file_size, $options['s']);
					$limitsizetext[] = $ext.' ('.$langGal['size_prefix'].' '.formatsize(1024*$options['s']).')';
				}
			}
		}

		$limitsizetable = "limitsizetable = {".implode(",", $limitsizetable)."};";
		$allowed_extensions = "*.".implode(";*.", $allowed_extensions);
		$limitsizetext = implode(", ", $limitsizetext);

		$buffer = "<br />".$langGal['upload_options']." <b>".stripslashes($this->upload_category[$id]['cat_title']).":</b><br /><br />";

		if ($this->upload_category[$id]['allow_upload'] == 1)
			$buffer .= "&raquo; ".$langGal['upload_withmod']."<br />";

		$width = ($this->upload_category[$id]['width_max']) ? $this->upload_category[$id]['width_max'] : $galConfig['global_max_foto_width'];
		$height = ($this->upload_category[$id]['height_max']) ? $this->upload_category[$id]['height_max'] : $galConfig['global_max_foto_height'];

		if ($galConfig['allow_foto_resize'] && $this->upload_category[$id]['auto_resize']){
			$buffer .= "&raquo; ".$langGal['upload_wid_res'].": {$width} px<br />";
			$buffer .= "&raquo; ".$langGal['upload_hei_res'].": {$height} px<br />";
		} else {
			$buffer .= "&raquo; ".$langGal['upload_wid_max'].": {$width} px<br />";
			$buffer .= "&raquo; ".$langGal['upload_hei_max'].": {$height} px<br />";
		}

		$buffer .= "&raquo; ".$langGal['upload_al_ext'].": ".$limitsizetext."<br />";

		$com_thumb_max = ($this->upload_category[$id]['com_thumb_max']) ? $this->upload_category[$id]['com_thumb_max'] : $galConfig['comms_foto_size'];
		$thumb_max = ($this->upload_category[$id]['thumb_max']) ? $this->upload_category[$id]['thumb_max'] : $galConfig['max_thumb_size'];

		if ($galConfig['comms_foto_size'] && $com_thumb_max)
			$buffer .= "&raquo; ".$langGal['upload_com_thumb'].": {$com_thumb_max} px<br />";

		$buffer .= "&raquo; ".$langGal['upload_thumb_max'].": {$thumb_max} px<br />";
		$buffer .= "&raquo; ".$langGal['upload_llrename']."<br />";

		if ($galConfig['allow_watermark'] && $this->upload_category[$id]['allow_watermark'])
			$buffer .= "&raquo; ".$langGal['upload_watermark']."<br />";
		if ($galConfig['allow_comments'] && $this->upload_category[$id]['allow_comments'])
			$buffer .= "&raquo; ".$langGal['upload_comments']."<br />";
		if ($galConfig['allow_rating'] && $this->upload_category[$id]['allow_rating'])
			$buffer .= "&raquo; ".$langGal['upload_rating']."<br />";

		$edit = ($galConfig['allow_edit_picture'] || $this->upload_category[$id]['allow_upload'] == 2) ? $langGal['upload_alledit'] : $langGal['upload_nalledit'];
		$delete = ($galConfig['allow_delete_picture'] || $this->upload_category[$id]['allow_upload'] == 2) ? $langGal['upload_alldel'] : $langGal['upload_nalldel'];

		$buffer .= "&raquo; ".$edit."<br />";
		$buffer .= "&raquo; ".$delete."<br />";

		return array($buffer, $allowed_extensions, $max_file_size, $limitsizetable, "var cattitle = {'c':'{$config['allow_alt_url']}','m':".($this->upload_category[$id]['allow_upload'] != 2 ? 1 : 0).",'id':{$id},'a':'".$this->upload_category[$id]['cat_alt_name']."','t':'".stripslashes($this->upload_category[$id]['cat_title'])."'};");
	}

	function check_onmoderation(){
	global $galConfig, $db, $member_id;

		if ($this->onmoderation !== false || $galConfig['files_on_moderation'] < 1) return true;

		$count = $db->super_query("SELECT COUNT(picture_id) as count FROM " . PREFIX . "_gallery_picturies WHERE approve=0 AND date > '".date("Y-m-d", TIME)."' AND user_id='".intval($member_id['user_id'])."' AND date < '".date("Y-m-d", TIME)."' + INTERVAL 24 HOUR");
		$this->onmoderation = $count['count'];

		if ($this->onmoderation >= $galConfig['files_on_moderation']) $this->public_error = 1;

		return true;

	}

	function js_print($uploads_params){
	global $js_array, $galConfig, $config, $js_options;

		$js_options['mode'] = 2;

		$swfupload = version_compare($config['version_id'], "9.5", ">") ? "engine/classes/uploads/swfupload" : "engine/classes/swfupload";

		$js_array[] = "{$swfupload}/swfupload.js";
		$js_array[] = "{$swfupload}/swfupload.queue.js";
		$js_array[] = "{$swfupload}/fileprogress.js";
		$js_array[] = "engine/gallery/js/upload_handlers.js";
		$js_array[] = "engine/classes/js/bbcodes.js";

		if ($config['allow_add_tags'] || $this->upload_category[$this->cat]['allow_edit']) $js_array[] = "engine/skins/autocomplete.js";

		$ajax = "<script language=\"javascript\" type=\"text/javascript\">
<!--
var gallery_max_once_upload = '{$galConfig['max_once_upload']}';
var gallery_file_title_control = '".($this->full_access == 2 ? 0 : $galConfig['file_title_control'])."';
var gallery_advance_default = '{$galConfig['advance_default']}';\n";

		$ajax .= "var gallery_lang_upload = {";

		$i = 1;
		while (isset($this->lang['add_foto_error_'.$i])) $ajax .= "'{$i}':'".$this->lang['add_foto_error_'.$i++]."',";
		$i = 1;
		while (isset($this->lang['js_lang_'.$i])) $ajax .= "'js{$i}':'".$this->lang['js_lang_'.$i++]."',";

		$ajax = substr($ajax, 0, -1) . "};\n";

		if ($uploads_params[3]) 
			$ajax .= "var {$uploads_params[3]}\n{$uploads_params[4]}\n";
		else
			$ajax .= "var limitsizetable;\n{$uploads_params[4]}\n";

		$ajax .= "var swfu = false;
window.onload = function(){

	$('.fieldcheck').blur(function(){
		ckeck_field(this.id, this.value);
	});

	swfu = new SWFUpload({
		flash_url : dle_root+\"{$swfupload}/swfupload.swf\",
		upload_url: dle_root+\"engine/gallery/ajax/upload.php\",
		post_params: {\"PHPSESSID\" : \"".session_id()."\",\"cat\" : \"".$this->cat."\", \"action\" : \"upload\", \"skin\": dle_skin},
		file_size_limit : \"{$uploads_params[2]} KB\",
		file_types : \"{$uploads_params[1]}\",
		file_types_description : \"All Files\",
		file_upload_limit : gallery_max_once_upload,
		file_queue_limit : gallery_max_once_upload,
		custom_settings : {	progressTarget : \"fsUploadProgress\",cancelButtonId : \"btnCancel\",_StartButtonId : \"btnStart\" },
		debug: false,
		flash_container_id : \"flash_container\",
		file_queued_handler : fileQueued,
		file_queue_error_handler : fileQueueError,
		file_dialog_complete_handler : fileDialogComplete,
		upload_start_handler : uploadStart,
		upload_progress_handler : uploadProgress,
		upload_error_handler : uploadError,
		upload_success_handler : uploadSuccess,
		upload_complete_handler : uploadComplete,
		swfupload_loaded_handler: uploadFlashReady,	
		queue_complete_handler : queueComplete
	});

	gallery_autocomplete($( '#tags' ), 'engine/gallery/ajax/file.php?act=1', ".intval($galConfig['tags_num']).", 3);

};
//-->
</script>";

	return $ajax;
	}

	///********************************************************************************************************
	//                                   Начало загрузки, первая страница
	//*********************************************************************************************************
	function page_prepare(){
	global $db, $langGal, $galConfig, $is_logged, $member_id, $tpl, $ajax, $cat_selected_first, $config;

		$this->set_metatags($langGal['menu_title12']);

		$error = array();

		$this->check_onmoderation();

		$cat_selected_first = true;

		$upload_tpl = ($this->cat && $this->upload_category[$this->cat]['uploadskin']) ? $this->upload_category[$this->cat]['uploadskin'] : 'upload';

		$categories = CategoryGalSelection($this->cat, 0, ($this->public_error == 1));

		if (!$this->cat && $cat_selected_first !== true) $this->cat = $this->load_cat_by_id($cat_selected_first);

		if ($this->cat && !$this->upload_category[$this->cat]['allow_upload']) $error[] = str_ireplace('{cattitle}',stripslashes($this->upload_category[$this->cat]['cat_title']),$this->lang['add_foto_error_18']);
		elseif ($this->public_error == 1 && $this->upload_category[$this->cat]['allow_upload'] == 1) $error[] = str_ireplace('{cattitle}',stripslashes($this->upload_category[$this->cat]['cat_title']),$this->lang['add_foto_error_19']);
		elseif ($this->public_error == 1) $error[] = $this->lang['add_foto_error_20'];
		elseif ($cat_selected_first === true) $error[] = $this->lang['add_foto_error_22'];

		if ($this->full_access || !$galConfig['disable_select_upload'] || $galConfig['advance_default'] == 0){

			$count = $db->super_query("SELECT COUNT(picture_id) as count FROM " . PREFIX . "_gallery_picturies WHERE approve=3 AND ".($is_logged ? "user_id='".$member_id['user_id']."'" : "session_id='".session_id()."'")." AND date > '".date ("Y-m-d H:i:s", (TIME-3600*2))."'");

			if ($count['count']) $error[] = str_ireplace(array("{num}", "{link_continue}", "{link_delete}"), array($count['count'], $galConfig['PHP_SELF'].'&act=26&ap=2', $galConfig['PHP_SELF'].'&act=26&ap=4'), $this->lang['files_found_bd']);

		}

		if (count($error)) $this->msgbox ('all_info', implode("<br /><br />", $error), array(1));

		if ($cat_selected_first === true && !$this->allow_create) return;

		$tpl->load_template('gallery/'.$upload_tpl.'.tpl');

		$uploads_params = $this->category_rules($this->cat);

		if ($this->cat){
			$tpl->set('{rules}', "<div id=\"rules-layer\">".$uploads_params[0]."</div>");
			$tpl->set('{foto_title}', stripslashes($this->upload_category[$this->cat]['cat_title']));
		} else {
			$tpl->set('{rules}', "<div id=\"rules-layer\">".$uploads_params[0]."</div>");
			$tpl->set('{foto_title}', '');
		}

		if (!$this->full_access && $galConfig['disable_select_upload'])
			$tpl->set_block("'\\[selectmode\\](.*?)\\[/selectmode\\]'si","");
		else {
			$tpl->set('[selectmode]', '');
			$tpl->set('[/selectmode]', '');
			$tpl->set('{selectmode}',  makeDropDownGallery(array("1"=>$langGal['c_ad_v2'],"0"=>$langGal['c_ad_v1']), 'upload_mode', $galConfig['advance_default']));
		}

		if (!$this->allow_remote)
			$tpl->set_block("'\\[remote\\](.*?)\\[/remote\\]'si","");
		else {
			$tpl->set('[remote]', '');
			$tpl->set('[/remote]', '');
		}

		if (!$this->allow_create)
			$tpl->set_block("'\\[create\\](.*?)\\[/create\\]'si","");
		else {
			$tpl->set('[create]', '');
			$tpl->set('[/create]', '');
			//$tpl->set('{profiles}', "<select size=\"1\" name=\"profile\">\r\n".$profiles."</select>\r\n");
		}

		if ($config['allow_add_tags'] || $this->upload_category[$this->cat]['allow_edit']){
			$tpl->set('[tags]', '');
			$tpl->set('[/tags]', '');
		} else $tpl->set_block("'\\[tags\\](.*?)\\[/tags\\]'si","");

		$tpl->set('{tags_num}', ($galConfig['tags_num'] ? intval($galConfig['tags_num']) : '--'));
		$tpl->set('{tags_len}', ($galConfig['tags_len'] ? $galConfig['tags_len'] : '--'));

		$hidden = "";

		if (strpos($tpl->copy_template, "{category}") !== false)
			$tpl->set('{category}', "<select name=\"cat\" id=\"cat\" onChange=\"show_rules(this.value); return false;\">{$categories}</select>");
		else
			$hidden = "<input type=\"hidden\" name=\"cat\" id=\"cat\" value=\"".$this->cat."\">";

		if ($is_logged)
			$tpl->set_block("'\\[not-logged\\](.*?)\\[/not-logged\\]'si","");
		else {
			$tpl->set('[not-logged]', '');
			$tpl->set('[/not-logged]', '');
		}

		$tpl->copy_template = "<form method=post name=\"entryform\" id=\"entryform\" onsubmit=\"return upload_check(0)\" action=\"\" enctype=\"multipart/form-data\">".$tpl->copy_template."<input type=\"hidden\" name=\"do\" value=\"gallery\"><input type=\"hidden\" name=\"act\" value=\"26\"><input type=\"hidden\" name=\"ap\" value=\"1\">{$hidden}</form>";

		$ajax .= $this->js_print($uploads_params);

		$tpl->compile('content');
		$tpl->clear();

	}

	///********************************************************************************************************
	//                                   Продолжение загрузки, вторая страница
	//*********************************************************************************************************
	function page_upload(){
	global $db, $langGal, $galConfig, $config, $is_logged, $relates_word;

		$error = array();

		$this->check_onmoderation();

		if (!$this->cat) $error[] = ((!AJAX_ACTION) ? $this->lang['add_foto_error_4'] : 4);
		elseif (!$this->upload_category[$this->cat]['allow_upload']) $error[] = ((!AJAX_ACTION) ? str_ireplace('{cattitle}',stripslashes($this->upload_category[$this->cat]['cat_title']),$this->lang['add_foto_error_18']) : 18);
		elseif ($this->public_error == 1 && $this->upload_category[$this->cat]['allow_upload'] == 1) $error[] = ((!AJAX_ACTION) ? str_ireplace('{cattitle}',stripslashes($this->upload_category[$this->cat]['cat_title']),$this->lang['add_foto_error_19']) : 19);

		if ($this->full_access || !$galConfig['disable_select_upload']) $galConfig['advance_default'] = intval($_REQUEST['upload_mode']);

		if ($galConfig['advance_default'] == 0)
			$this->set_metatags($langGal['menu_title13']);
		else
			$this->set_metatags($langGal['menu_title14']);

		include_once ENGINE_DIR.'/classes/parse.class.php';

		$parse = new ParseFilter();
		$parse->safe_mode = true;

		if (AJAX_ACTION)
			foreach (array('foto_title','tags','name','email') as $item)
				$_POST[$item] = convert_unicode($_POST[$item], $config['charset']);

		if (!$is_logged){

			$not_allow_symbol = array("\x22", "\x60", "\t", '\n', '\r', "\n", "\r", '\\', ",", "/", "¬", "#", ";", ":", "~", "[", "]", "{", "}", ")", "(", "*", "^", "%", "$", "<", ">", "?", "!", '"', "'" );
			$email = $db->safesql(trim(str_replace($not_allow_symbol, '', strip_tags(stripslashes($_POST['email'])))));
			$name = $db->safesql($parse->process(trim($_POST['name'])));

			if (strlen($email) > 50)
				$error[] = ((!AJAX_ACTION) ? $this->lang['add_foto_error_34'] : 34);
			elseif ($email != "" && !preg_match("/^[\.A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $email))
				$error[] = ((!AJAX_ACTION) ? $this->lang['add_foto_error_35'] : 35);

			if(($strlen = dle_strlen($name, $config['charset'])) > 20)
				$error[] = ((!AJAX_ACTION) ? $this->lang['add_foto_error_33'] : 33);
			elseif ($name != "" && ($strlen < 2 || preg_match( "/[\||\'|\<|\>|\[|\]|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\{\+]/", $name)))
				$error[] = ((!AJAX_ACTION) ? $this->lang['add_foto_error_36'] : 36);

			if (!count($error) && ($name || $email)){

				$where = array();

				if ($name != "") $where[] = "LOWER(name) REGEXP '[[:<:]]".strtr(strtolower($name), $relates_word)."[[:>:]]' OR name='".strtolower($name)."'";
				if ($email != "") $where[] = "email='{$email}'";
	
				$user_exists = $db->super_query( "SELECT COUNT(user_id) as count FROM " . USERPREFIX . "_users WHERE " . implode(" OR ", $where));

				if ($user_exists['count'])
					$error[] = ((!AJAX_ACTION) ? $this->lang['add_foto_error_37'] : 37);

			}

		} else $email = $name = '';

		$parse->safe_mode = false;

		$foto_title = $db->safesql($parse->process(trim($_POST['foto_title'])));

		if ($foto_title == '' && $galConfig['file_title_control'] && $this->full_access != 2 && $galConfig['advance_default']) $error[] = ((!AJAX_ACTION) ? $this->lang['add_foto_error_21'] : 21);

		if (count($error)){

			$this->msgbox ((!AJAX_ACTION ? 'all_info' : 'all_err_1'), implode("<br />", $error), array(1));
			return;

		}

		global $UPL;

		$admin_upload = (isset($_REQUEST['mod']) && $_REQUEST['mod'] == 'twsgallery' && $this->full_access == 2);

		$UPL = new gallery_upload_images($galConfig, $this->cat, (($this->upload_category[$this->cat]['allowed_extensions']) ? $this->upload_category[$this->cat]['allowed_extensions'] : $galConfig['allowed_extensions']), $admin_upload);

		if (!$admin_upload){

			$UPL->config['allow_resize'] = ($galConfig['allow_foto_resize'] && $this->upload_category[$this->cat]['auto_resize']) ? 1 : 0; // можно ли сжимать
			if ($this->upload_category[$this->cat]['width_max']) $UPL->config['global_max_foto_width'] = $this->upload_category[$this->cat]['width_max'];
			if ($this->upload_category[$this->cat]['height_max']) $UPL->config['global_max_foto_height'] = $this->upload_category[$this->cat]['height_max'];
			if ($galConfig['comms_foto_size'] && $this->upload_category[$this->cat]['com_thumb_max']) $UPL->config['comm_thumb'] = $this->upload_category[$this->cat]['com_thumb_max'];
			if ($this->upload_category[$this->cat]['thumb_max']) $UPL->config['thumb_size'] = $this->upload_category[$this->cat]['thumb_max'];
			if ($this->upload_category[$this->cat]['icon_max_size']) $UPL->config['icon_size'] = $this->upload_category[$this->cat]['icon_max_size'];
			if (!$this->upload_category[$this->cat]['allow_watermark']) $UPL->config['insert_watermark'] = false;

		}

		$UPL->size_factor = $admin_upload ? 10 : ($this->upload_category[$this->cat]['size_factor']/100);
		$UPL->remote_upload = ($this->allow_remote && $_REQUEST['remote_upload'] == 1) ? true : false;
		$UPL->insert_data = array(
		'foto_title' => $foto_title,
		'all_time_images' => (intval($this->upload_category[$this->cat]['all_time_images']) + 1),
		'p_id' => $this->upload_category[$this->cat]['p_id'],
		'approve' => ($galConfig['advance_default'] == 0 ? 3 : ($this->upload_category[$this->cat]['allow_upload'] == 2 ? 1 : 0)),
		'tags' => (($config['allow_add_tags'] || $this->upload_category[$this->cat]['allow_edit']) ? $_POST['tags'] : false),
		'name' => $name,
		'email' => $email,
		);

		if (!AJAX_ACTION)
			$UPL->doupload();
		else
			$UPL->doupload(array(1), 'Filedata');

		if ($UPL->global_error){

			$this->msgbox ('all_err_1', ((!AJAX_ACTION) ? $this->lang['add_foto_error_'.$UPL->upload_result[0][1]] : $UPL->upload_result[0][1]), array(0, 1));
			return;

		}

		if ($UPL->upload_stats['insert'] && $UPL->insert_data['approve'] == 1){

			$db->query("UPDATE " . PREFIX . "_gallery_search SET actual=0 WHERE actual != 0");

			if ($galConfig['show_statistic']){

				$db->query("UPDATE " . PREFIX . "_gallery_config SET value='-1' WHERE name IN ('statistic_file_day','statistic_file')");
				@unlink(ENGINE_DIR.'/cache/system/gallery_config.php');

			}

			clear_gallery_vars();
			clear_gallery_cache();

		} elseif ($UPL->upload_stats['insert'] && $UPL->insert_data['approve'] == 0 && $galConfig['show_statistic']){

			$db->query("UPDATE " . PREFIX . "_gallery_config SET value='-1' WHERE name IN ('statistic_file_onmod')");
			@unlink(ENGINE_DIR.'/cache/system/gallery_config.php');

		}

		if (AJAX_ACTION){

			$this->msgbox ('all_message', $UPL->upload_result[0][1]);
			return;

		}

/* $UPL->upload_result[$i]: $foto[0] - image_name, $foto[1] - error, $foto[2] - upltype, $foto[3] - insert_id, $foto[4] - pic_filname, $foto[5] - media_type, $foto[6] - current_type, $foto[7] - update_id */

		$error_text = array();
		$ok_text = array();
		$r_key = count($UPL->upload_result);

		for ($i = 0; $i < $r_key; $i++){

			if ($UPL->upload_result[$i][1]){

				if ($UPL->upload_result[$i][0] !== false)
					$image = "<font color=red>".(($UPL->upload_result[$i][0] == "") ? $this->lang['foto_notitle'] : $UPL->upload_result[$i][0])."</font>  &raquo; ";
				else
					$image = "";

				$error_text[] = "<b>".$image.stripslashes($this->lang['add_foto_error_'.$UPL->upload_result[$i][1]] . $UPL->upload_result[$i][2])."</b>";

			} else $ok_text[] = "<b><font color=green>".$UPL->upload_result[$i][0]."</font>  &raquo; ".stripslashes($this->lang['add_foto_ok'])."</b>";

		}

		$count_find = ($galConfig['advance_default'] == 0) ? $this->show_wait_files() : 0;

		$text = "";

		if (count($ok_text)){

			$text .= $this->lang['add_foto_ok1']."<br /><br />".implode("<br />", $ok_text)."<br /><br />";

			if ($UPL->insert_data['approve'] == 0) $text .= $this->lang['js_lang_20']."<br /><br />";

		}

		if ($count_find) $text .= $this->lang['add_foto_ok3']."<br /><br />";

		if (count($error_text))
			$text .= $this->lang['add_foto_ok2']."<br /><br />".implode("<br />", $error_text)."<br /><br />";

		if ($text == "") $text = $this->lang['add_foto_error_16'];

		if ($config['allow_alt_url'] == "yes")
			$categoryurl = $this->upload_category[$this->cat]['cat_alt_name']."/";
		else
			$categoryurl = "&act=1&cid=".$this->cat;

		$back = ($galConfig['advance_default'] == 0 && $count_find) ? false : array(array('upload_more', $config['http_home_url']."index.php?do=gallery&act=26&cat=".$this->cat,''), array('edit_cat_go', $galConfig['mainhref'].$categoryurl));

		$this->msgbox ('all_info', $text, $back);

	}

	///********************************************************************************************************
	//                                   Возобновление загрузки, вторая страница
	//*********************************************************************************************************
	function show_wait_files(){
	global $tpl, $galConfig, $bb_code, $input, $dle_login_hash, $js_array, $langGal;

		$tpl->load_template('gallery/editpicture.tpl');

		$edit = new gallery_file_edit();
		$edit->edit_prepare(1);

		if ($edit->affected_files){

			$tpl->set('{fotolist}', $tpl->result['content']);
			$tpl->set('{actiontitle}', $langGal['menu_title13']);
			$tpl->result['content'] = "";

			$tpl->set('[button]', '');
			$tpl->set('[/button]', '');
			$tpl->set_block("'\\[foto\\](.*?)\\[/foto\\]'si","");
			$tpl->set_block("'\\[admin\\](.*?)\\[/admin\\]'si","");

			$tpl->compile('content');
			$tpl->clear();

			$check_title = ($galConfig['file_title_control'] && $this->full_access != 2) ? " onsubmit=\"if (!ckeck_title('title', gallery_lang_user[1])){ return false; }\"" : "";
			$tpl->result['content'] = "<form method=post name=\"entryform\" id=\"entryform\" action=\"\"{$check_title}>".$bb_code.$tpl->result['content']."{$input}<input type=\"hidden\" name=\"do\" value=\"gallery\"><input type=\"hidden\" name=\"act\" value=\"26\"><input type=\"hidden\" name=\"ap\" value=\"3\"><input type=\"hidden\" name=\"dle_allow_hash\" value=\"{$dle_login_hash}\"></form>";

			if (!AJAX_ACTION)
				$js_array[] = "engine/gallery/js/upload_handlers.js";

		}

		return $edit->affected_files;
	}

	///********************************************************************************************************
	//                                   Окончание загрузки, третья страница
	//*********************************************************************************************************
	function upload_complete(){
	global $galConfig, $langGal, $config;

		$this->set_metatags($langGal['menu_title14']);

		$edit = new gallery_file_edit();
		$edit->edit(1);

		if (!$edit->affected_files){

			$this->msgbox ('all_err_1', $this->lang['no_wait_files'], array(1));
			return;

		}

		$text = "";

		if ($edit->stat['ok']){

			$text .= str_ireplace('{num}', $edit->stat['ok'], $this->lang['js_lang_19'])."<br /><br />";

			if ($edit->stat['on_moderation'])
				$text .= $this->lang['js_lang_20']."<br /><br />";

		}

		if (count($edit->error_result))
			$text .= $this->lang['add_foto_ok2']."<br /><br />".implode("<br />", $edit->error_result)."<br /><br />";

		if ($text == "") $text = $this->lang['no_wait_files'];

		if ($edit->stat['category_id']){

			if ($config['allow_alt_url'] == "yes")
				$categoryurl = $edit->stat['cat_alt_name']."/";
			else
				$categoryurl = "&act=1&cid=".$edit->stat['category_id'];

		} else $categoryurl = "";

		$this->msgbox ('all_info', $text, array(array('upload_more', $config['http_home_url']."index.php?do=gallery&act=26&cat=".$edit->stat['category_id'],''), array(($edit->stat['category_id'] ? 'edit_cat_go' : 'all_main'), $galConfig['mainhref'].$categoryurl)));

	}

	function remove_wait_files(){
	global $langGal, $config, $is_logged, $member_id;

		$this->set_metatags($langGal['menu_title3']);

		$edit = new gallery_file_edit();
		$edit->remove(1, "approve=3 AND ".($is_logged ? "user_id='".$member_id['user_id']."'" : "session_id='".session_id()."'")." AND date > '".date ("Y-m-d H:i:s", (TIME-3600*2))."'");

		if (!$edit->affected_files){

			$this->msgbox ('all_err_1', $this->lang['no_wait_files'], array(1));
			return;

		}

		$edit->redirect($config['http_home_url']."index.php?do=gallery&act=26");

	}

	function msgbox($title, $text, $back = false){
	global $langGal, $galConfig;

		if (!AJAX_ACTION){

			if (is_array($back)){
				foreach ($back as $b_array){
					if (is_array($b_array))
						$text .= (isset($b_array[2]) ? $b_array[2] : '<br /><br />') . '<a href="'.$b_array[1].'">'.$langGal[$b_array[0]].'</a>';
					else
						switch ($b_array){
							case 0 : $text .= '<br /><br /><a href="javascript:history.go(-1)">'.$langGal['all_prev'].'</a>'; break;
							case 1 : $text .= '<br /><br /><a href="'.$galConfig['mainhref'].'">'.$langGal['all_main'].'</a>'; break;
						}
				}
			}

			msgbox($langGal[$title], $text);
			return;

		}

		switch ($title){
			case 'all_err_1' : $this->buffer .= '[HTML:Errors]'.$text.'[END:HTML:Errors]'; break;
			case 'all_info'  : $this->buffer .= '[HTML:Ok]'.$text.'[END:HTML:Ok]'; break;
			case 'all_message'  : $this->buffer .= $text; break;
		}

	}

	function set_metatags($navigation, $title = "", $meta_refresh = false){

		if (!AJAX_ACTION){

			global $s_navigation, $metatags;

			if ($navigation != "") $s_navigation .= " &raquo; ".$navigation;
			if ($title != "" && !$meta_refresh) $metatags['title'] = $title;
			elseif ($title != "") $metatags['title'] .= $title;

		}

	}

}

?>