<?PHP
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
 Назначение: Класс загрузки файлов
=====================================================
 Версия class.upload.php 2,0
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

global $UPL;
$UPL = false;

function myUnzipPreExtractCallBack($p_event, &$p_header){
global $UPL;
static $extracted = 0;

		if ($UPL->config['max_once_upload'] && ($UPL->upload_stats['counter']+$extracted) >= $UPL->config['max_once_upload']) return 0;

		$info = pathinfo($p_header['filename']);

		if ($info['extension'] && array_key_exists(strtolower($info['extension']), $UPL->config['allowed_extensions'])){
	 		$p_header['stored_filename'] = $info['basename'];
			$extracted++;
	 		return 1;
		}

		return 0;
}

class gallery_upload_images {

	var $global_error		= false;
	var $upload_stats 		= array();
	var $upload_result 		= array();
	var $config 			= array();
	var $sub_dir			= '';
	var $main_dir			= '';
	var $current_moved		= false;
	var $current_image		= '';
	var $current_url		= '';
	var $current_name		= '';
	var $current_type		= '';
	var $current_check		= true; // проверять ли параметры файла (размер, разрешение, xxs, хэш)
	var $current_hosting	= false;
	var $current_foto		= array();
	var $pic_filname		= '';
	var $insert_data		= false;
	var	$remote_upload		= false;
	var	$zip_temp_dir		= '';
	var	$size_factor		= 1;
	var $temp_exceptions 	= array();

	function gallery_upload_images($galConfig = false, $sub_dir = '', $allowed = 'all', $post_config = 0){

		if ($galConfig == false) return;

		@set_time_limit ( 180 );
		@ini_set('max_execution_time', 180);
		@ini_set("output_buffering", "off");
		@ini_set('4096M');

		$this->sub_dir = $sub_dir;
		$this->main_dir = 'main/' . $sub_dir;
		$this->set_allowed_extensions($galConfig, $allowed);

		if ($post_config){

			$this->config['max_once_upload'] = 200;

		} else {

			$this->config['max_once_upload'] = $galConfig['max_once_upload'];
			if ($this->config['max_once_upload'] < 1) $this->config['max_once_upload'] = 50;
			if (date('H', TIME) < 12 && date('H', TIME) > 2)$this->config['max_once_upload'] *= 2;

		}

		$this->config['check_small_file_size'] = 0; // проверяеть ли размер файла, разрешение которого меньше установленного
		$this->config['zip_filesize'] = 100; // максимальный размер зип архива, в мегабайтах
		$this->config['convert_png_thumb'] = $galConfig['convert_png_thumb']; // можно ли конвертировать миниатюрные png изображения в jpeg
		$this->config['thumb_res_type'] = ($post_config) ? intval($_POST['thumb_res_type']) : $galConfig['thumb_res_type']; // тип сжатия тумбы
		$this->config['comm_res_type'] = ($post_config) ? intval($_POST['comm_res_type']) : $galConfig['comm_res_type']; // тип сжатия ком-тумбы
		$this->config['full_res_type'] = ($post_config) ? intval($_POST['full_res_type']) : $galConfig['full_res_type']; // тип сжатия полной фотографии
		$this->config['global_max_foto_width'] = ($post_config) ? intval($_POST['global_width_max']) : $galConfig['global_max_foto_width']; // ширина сжатия или ограничение
		$this->config['global_max_foto_height'] = ($post_config) ? intval($_POST['global_height_max']) : $galConfig['global_max_foto_height']; // высота сжатия или ограничение
		$this->config['comm_thumb'] = ($post_config) ? totranslit($_POST['comms_foto_size'], true, false) : $galConfig['comms_foto_size']; // размер ком-тумбы
		$this->config['thumb_size'] = ($post_config) ? totranslit($_POST['max_thumb_size'], true, false) : $galConfig['max_thumb_size']; // размер тумбы
		$this->config['icon_size'] = ($post_config && $galConfig['icon_type']) ? totranslit($_POST['max_icon_size'], true, false) : ($galConfig['icon_type'] ? $galConfig['max_icon_size'] : 0); // размер иконки категории
		$this->config['allow_resize'] = ($post_config) ? intval($_POST['allow_foto_resize']) : $galConfig['allow_foto_resize']; // можно ли сжимать
		$this->config['insert_watermark'] = $galConfig['allow_watermark'] ? (($post_config) ? intval($_POST['min_watermark']) : $galConfig['min_watermark']) : false;
		$this->config['no_main_watermark'] = $galConfig['no_main_watermark'];
		$this->config['rewrite'] = ($post_config) ? intval($_POST['rewrite_mode']) : $galConfig['rewrite_mode']; // тип перезаписи файла
		$this->config['quality'] = ($post_config) ? intval($_POST['resize_quality']) : $galConfig['resize_quality']; // качество сжатия
		$this->config['check_double'] = ($post_config) ? intval($_POST['allow_check_double']) : $galConfig['allow_check_double'];
		$this->config['delete'] = ($post_config) ? intval($_POST['delete']) : 1; // удалять ли ошибочные файлы ! 1 на 0 менять нельзя!
		$this->config['file_hash'] = 1; // вычислять ли md5 хэш файла
		$this->config['file_db_exists'] = 1; // проверять наличие файла в базе данных
		$this->config['random_name'] = ($galConfig['random_filename'] > 4 ? $galConfig['random_filename'] : 0); // количество символов при создании имени файла случайным образом (0 - отключение)
		$this->config['disable_thumbnailer'] = 0;  // полное отключение модуля сжатия файла

	}

	function set_allowed_extensions($galConfig, $allowed){

		$this->config['allowed_extensions'] = array();
		$allowed = explode(',', $allowed);

		foreach ($galConfig['extensions'] as $ext => $options)
			if ($allowed[0] == 'all' || in_array($ext, $allowed))
				$this->config['allowed_extensions'][$ext] = $options;

	}

	function doupload($type_upload = array(1,2,3,4), $_input_param = 'image', $insert_file = true){

		$this->upload_result = array();
		$this->upload_stats['counter'] = 0;

		if (!count($this->config['allowed_extensions'])){
			$this->upload_result[] = array(false, 4);
			$this->global_error = true;
			return false;
		}

		if ($this->main_dir && !$this->create_dirs(array($this->main_dir)) || $this->sub_dir && !$this->create_dirs(array('comthumb/' . $this->sub_dir, 'thumb/' . $this->sub_dir, 'caticons/' . $this->sub_dir))){
			$this->upload_result[] = array(false, 15);
			$this->global_error = true;
			return false;
		}

		if (isset($_FILES[$_input_param]) && !is_array($_FILES[$_input_param]['name']) && $_FILES[$_input_param]["name"] != ''){
			$_FILES[$_input_param]['name'] = array($_FILES[$_input_param]['name']);
			$_FILES[$_input_param]['tmp_name'] = array($_FILES[$_input_param]['tmp_name']);
			$_FILES[$_input_param]['error'] = array($_FILES[$_input_param]['error']);
		}

		if (!$this->size_factor || $this->size_factor < 0)
			$this->size_factor = 1;

		if (in_array(1, $type_upload) && count($_FILES[$_input_param]['name']))
			foreach ($_FILES[$_input_param]['name'] as $i => $d){

				if ($this->global_error) break;
				if ($d == '') continue;

				if (!($this->get_file($_input_param, $i, 1) && $this->check_extensions() && $this->check_existing() && $this->move_file(1) && $this->check_file_params() && $this->image_operations())){
					$this->move_error_file();
				} else {
					$insert_id = ($insert_file && is_array($this->insert_data)) ? $this->insert_file() : 0;
					$this->remove_temp_row();
					$this->upload_stats['counter']++;
					$this->upload_result[] = array($this->current_foto[6], 0, 1, $insert_id, $this->pic_filname, $this->current_foto [5], $this->current_type, $this->current_foto[3]);
				}

			}

		if ((!$this->remote_upload && in_array(2, $type_upload) || $this->remote_upload && in_array(3, $type_upload)) && count($_POST['url'])){
			foreach ($_POST['url'] as $i => $url){

				if ($this->global_error) break;

				if (trim($url) == '') continue;

				if (!($this->get_file($url, $i, 2) && $this->check_extensions() && $this->check_existing() && $this->move_file(2) && $this->check_file_params() && $this->image_operations())){
					$this->move_error_file();
				} else {
					$insert_id = ($insert_file && is_array($this->insert_data)) ? $this->insert_file() : 0;
					$this->remove_temp_row();
					$this->upload_stats['counter']++;
					$this->upload_result[] = array($this->current_foto[6], 0, (!$this->remote_upload ? 2 :3), $insert_id, $this->pic_filname, $this->current_foto [5], $this->current_type, $this->current_foto[3]);
				}

			}
		}

		$_input_param = 'zip_archive';

		if (in_array(4, $type_upload) && is_uploaded_file($_FILES[$_input_param]['tmp_name'])){

			$this->zip_temp_dir = (string) time().'unzip'.mt_rand(10,99);

			if (($result_files = $this->Unzip($_input_param)) != false){

				$found_files = false;

				foreach ($result_files as $file){

					if ($this->global_error) break;
					if ($file['folder'] == 1) continue;

					$found_files = true;

					if (!($this->get_file($file['filename'], 0, 3) && $this->check_extensions() && $this->check_existing() && $this->check_file_params($this->current_image) && $this->move_file(3) && $this->image_operations())){
						$this->move_error_file();
					} else {
						$insert_id = ($insert_file && is_array($this->insert_data)) ? $this->insert_file() : 0;
						$this->remove_temp_row();
						$this->upload_stats['counter']++;
						$this->upload_result[] = array($this->current_foto[6], 0, 4, $insert_id, $this->pic_filname, $this->current_foto [5], $this->current_type, $this->current_foto[3]);
					}

				}

				if (!$found_files) $this->upload_result[] = array($this->current_foto[6], 13);

			}

			$this->delete_folder(FOTO_DIR . '/temp/' . $this->zip_temp_dir);

		}

		if (in_array(5, $type_upload) && isset($_POST['folder']) && trim($_POST['folder']) != ''){

			$folder  = str_replace(array("\\", "..", "."), array("/", "", ""), trim(htmlspecialchars(strip_tags($_POST['folder']), ENT_QUOTES)));
			$folder = FOTO_DIR . '/' . $folder;

			if (!is_dir($folder) || !is_writable($folder) || !($handle_folder = @opendir($folder)))
				$this->upload_result[] = array(false, 14);
			else {

				while (false !== ($file = @readdir($handle_folder))){

					if (in_array($file, array('.','..','.htaccess','_notes')) || (FOTO_DIR . '/temp' == $folder) && in_array($file, $this->temp_exceptions) || is_dir($handle . '/' . $file)) continue;

					if ($this->global_error) break;

					if (!($this->get_file($folder.'/'.$file, 0, 3) && $this->check_extensions() && $this->check_existing() && $this->check_file_params($this->current_image) && $this->move_file(3) && $this->image_operations())){
						$this->move_error_file();
					} else {
						$insert_id = ($insert_file && is_array($this->insert_data)) ? $this->insert_file() : 0;
						$this->remove_temp_row();
						$this->upload_stats['counter']++;
						$this->upload_result[] = array($this->current_foto[6], 0, 5, $insert_id, $this->pic_filname, $this->current_foto [5], $this->current_type, $this->current_foto[3]);
					}

				}

				@closedir($handle_folder);

			}

		}

		if (!count($this->upload_result)){

			$this->upload_result[] = array(false, 16);
			$this->global_error = 1;
			return false;

		}

		return true;
	}

	function random_name($max = 10){

			$current_name = "";
			$max = $max ? min($max, 40) : 10;

			if(function_exists('openssl_random_pseudo_bytes') && (version_compare(PHP_VERSION, '5.3.4') >= 0 || strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN'))
				$salt = str_shuffle(md5(openssl_random_pseudo_bytes(15)));
			else
				$salt = str_shuffle(md5(uniqid(mt_rand(), TRUE)));

			for($i=0;$i < $max; $i++)
				$current_name .= $salt{mt_rand(0,31)};

		return $current_name;
	}

	function get_file($file, $current_id = 0, $type_upload){
	global $db, $config;

		$this->pic_filname		 = '';
		$this->current_moved	= false;
		$this->current_hosting	= false;
		$this->current_check	= true;
		$this->current_image	= '';
		$this->current_url		= '';
		$this->current_name		= '';
		$this->current_type		= '';
		$this->current_foto		= array();

		if ($this->config['max_once_upload'] != 0 && $this->upload_stats['counter'] >= $this->config['max_once_upload'])
		{
			$this->upload_result[] = array(false, 1);
			return false;
		}

		$error_code = 'no_test';

		switch ($type_upload){
			case 1 :

				$error_code 		 = $_FILES[$file]['error'][$current_id];
				$this->current_name	 = $_FILES[$file]['name'][$current_id];
				if (!is_uploaded_file($_FILES[$file]['tmp_name'][$current_id])) break;
				$this->current_image = $_FILES[$file]['tmp_name'][$current_id];

		break;
			case 2 :

				$this->current_image = strip_tags(trim(stripslashes($_REQUEST['url'][$current_id])));
				$this->current_image = str_replace('\\', '/', $this->current_image);
				$this->current_image = str_replace('..', '.', $this->current_image);
				$this->current_image = str_replace('\"', '"', $this->current_image);
				$this->current_image = str_replace("'", "", $this->current_image);
				$this->current_image = str_replace('"', "", $this->current_image);
				$this->current_image = htmlspecialchars($this->current_image, ENT_QUOTES, $config['charset']);
				$this->current_image = str_ireplace("document.cookie", "d&#111;cument.cookie", $this->current_image);
				$this->current_image = str_replace(" ", "%20", $this->current_image);
				$this->current_image = str_replace("<", "&#60;", $this->current_image);
				$this->current_image = str_replace(">", "&#62;", $this->current_image);
				$this->current_image = preg_replace("/javascript:/i", "j&#097;vascript:", $this->current_image);
				$this->current_image = preg_replace("/data:/i", "d&#097;ta:", $this->current_image);

				$source = @parse_url ($this->current_image);
				$source['host'] = str_replace("www.", "", strtolower($source['host']));

				$this->current_name = explode ("/", $this->current_image);
				$this->current_name = end($this->current_name);
				if ($this->remote_upload) $this->current_url = $this->current_image;

				$registered_speshial_hostings = array(
					'youtube.com', 'rutube.ru', 'video.mail.ru', 'vimeo.com','smotri.com','gametrailers.com'
				);

				if (in_array($source['host'], $registered_speshial_hostings)){

					$this->current_hosting	= true;
					$this->current_check = false;
					$this->current_type = $source['host'];
					$this->current_image = "";

					switch ($this->current_type){
					case 'youtube.com' :
					case 'rutube.ru' :

						$query = explode('&', $source['query']);

						foreach ($query as $a){
							$b = explode('=', $a);
							if ($b[0] == "v"){
								$this->current_url = $this->current_image = totranslit($b[1], false);
								break 2;
							}
						}

					break;
					case 'video.mail.ru' :

						$this->current_url = $this->current_image = str_replace( ".html", "", substr($source['path'], 1));

					break;
					case 'vimeo.com' :

						$this->current_url = $this->current_image = intval(substr($source['path'], 1));

					break;
					case 'smotri.com' :

						$query = explode('&', $source['query']);

						foreach ($query as $a){
							$b = explode('=', $a);
							if ($b[0] == "id"){
								$this->current_url = $this->current_image = totranslit($b[1], false);
								break 2;
							}
						}

					break;
					case 'gametrailers.com' :

						if (substr ( $source['path'], - 1, 1 ) == '/') $source['path'] = substr ( $source['path'], 0, - 1 );
						$source['path'] = explode( "/", $source['path'] );

						$this->current_url = $this->current_image = intval(end($source['path']));

					break;
					}

					if ($this->current_image == ""){
						$this->upload_result[] = array("", 2);
						return false;
					}

					$this->current_name = md5($this->current_url);
					$this->current_foto[6] = $this->current_type . ' URL ' . ($current_id+1);

					return true;
				}

		break;
			case 3 :

                $this->current_image  = str_replace(array("\\", ".."), array("/", ""), $file);
				$this->current_name = explode ("/", $this->current_image);
                $this->current_name = end($this->current_name);

		break;
		}

		$img_arr = explode(".", $this->current_name);

		$this->current_type = end($img_arr);
		$this->current_type = totranslit($this->current_type, true, false);

		unset($img_arr[key($img_arr)]);

		$this->current_name = trim(implode("", $img_arr));
		if (AJAX_ACTION) $this->current_name = convert_unicode($this->current_name, $config['charset']);

		$this->current_foto[6] = $db->safesql(htmlspecialchars(strip_tags($this->current_name), ENT_QUOTES, $config['charset']));
		$this->current_name = totranslit($this->current_name, true, false);

		if (strlen($this->current_foto[6]) < 3) $this->current_foto[6] = $this->current_name;
		$this->current_foto[6] .= '.' . $this->current_type;

		if ($error_code != 'no_test' && $error_code !== UPLOAD_ERR_OK){

		    switch ($error_code) { 
		        case UPLOAD_ERR_INI_SIZE: 
		            $error_code = 26; break;
		        case UPLOAD_ERR_FORM_SIZE: 
		            $error_code = 27; break;
		        case UPLOAD_ERR_PARTIAL: 
		            $error_code = 28; break;
		        case UPLOAD_ERR_NO_FILE: 
		            $error_code = 29; break;
		        case UPLOAD_ERR_NO_TMP_DIR: 
		            $error_code = 30; break;
		        case UPLOAD_ERR_CANT_WRITE: 
		            $error_code = 31; break;
		        case UPLOAD_ERR_EXTENSION: 
		            $error_code = 32; break;
		        default: 
		            $error_code = 3;  break;
		    }

			$this->upload_result[] = array($this->current_foto[6], $error_code);
			return false;

		}

		$name_len = ($this->config['random_name'] ? 0 : strlen($this->current_name));

		if ($name_len > 40){

			$this->current_name = substr($this->current_name, 0, 40);
			if (($temp_max = strrpos($this->current_name, '-')))  $this->current_name = substr ($this->current_name, 0, $temp_max);

		} elseif ($name_len < 3)
			$this->current_name = $this->random_name($this->config['random_name']);

		if ($this->current_image == "" || $this->current_name == "" || strpos($this->current_type, "php") !== false || strpos($this->current_type, "phtml") !== false || strpos($this->current_type, "htaccess") !== false || strpos($this->current_type, "shtm") !== false || strpos($this->current_name, "php") !== false || strpos($this->current_name, "phtml") !== false || strpos($this->current_name, "htaccess") !== false || strpos($this->current_name, "shtm") !== false){
			$this->upload_result[] = array("", 2);
			return false;
		}

	return true;
	}

	function create_dirs($dirs){

		$error = false;

		foreach ($dirs as $sub_dir){

			if (!is_dir(FOTO_DIR.'/' . $sub_dir)){
				if(!defined('DEBUG_MODE')){
					@mkdir(FOTO_DIR.'/' . $sub_dir, 0777) or $error = true;
				} else {
					mkdir(FOTO_DIR.'/' . $sub_dir, 0777) or $error = true;
				}
				if ($error) return false;
				if(!defined('DEBUG_MODE'))
					@chmod(FOTO_DIR.'/' . $sub_dir, 0777);
				else
					chmod(FOTO_DIR.'/' . $sub_dir, 0777);
			} elseif (!is_writable(FOTO_DIR.'/' . $sub_dir)) {
				if(!defined('DEBUG_MODE'))
					@chmod(FOTO_DIR.'/' . $sub_dir, 0777);
				else
					chmod(FOTO_DIR.'/' . $sub_dir, 0777);
			}

		}

	return true;
	}

	function check_extensions(){

		$this->current_foto[5] = 0;

		if (array_key_exists($this->current_type, $this->config['allowed_extensions'])){
			switch ($this->current_type){
				case "jpg" : $this->current_foto[5] = 0; break;
				case "jpeg" : $this->current_foto[5] = 0; break;
				case "jpe" : $this->current_foto[5] = 0; break;
				case "png" : $this->current_foto[5] = 0; break;
				case "gif" : $this->current_foto[5] = 0; break;
				case "mp3" : $this->current_foto[5] = 1; break;
				case "mp4" : $this->current_foto[5] = 2; break;
				case "avi" : $this->current_foto[5] = 3; break;
				case "divx" : $this->current_foto[5] = 7; break;
				case "mkv" : $this->current_foto[5] = 8; break;
				case "wmv" : $this->current_foto[5] = 4; break;
				case "flv" : $this->current_foto[5] = 5; break;
				case "mpg" : $this->current_foto[5] = 6; break;
				case "swf" : $this->current_foto[5] = 9; break;
				case "m4v" : $this->current_foto[5] = 11; break;
				case "m4a" : $this->current_foto[5] = 12; break;
				case "mov" : $this->current_foto[5] = 13; break;
				case "3gp" : $this->current_foto[5] = 14; break;
				case "f4v" : $this->current_foto[5] = 15; break;
				case "youtube.com" : $this->current_foto[5] = 51; break;
				case "rutube.ru" : $this->current_foto[5] = 52; break;
				case "video.mail.ru" : $this->current_foto[5] = 53; break;
				case "vimeo.com" : $this->current_foto[5] = 54; break;
				case "smotri.com" : $this->current_foto[5] = 55; break;
				case "gametrailers.com" : $this->current_foto[5] = 56; break;
				default : $this->current_foto[5] = 10; break;
			}
			return true;
		}

		$this->upload_result[] = array($this->current_foto[6], (!$this->current_hosting ? 3 : 25));

	return false;
	}

	function get_filesize ($check_file){

		if(!defined('DEBUG_MODE'))
			$this->current_foto [0] = @filesize($check_file);
		else
			$this->current_foto [0] = filesize($check_file);
	}

	function check_filesize($check_image){

		$this->get_filesize($check_image);

		if ($this->config['allowed_extensions'][$this->current_type]['s'] && $this->current_foto[0] > ($this->config['allowed_extensions'][$this->current_type]['s'] * 1024 * $this->size_factor)){

			$this->upload_result[] = array($this->current_foto[6], 5);
			return false;

		}
		
		if ($this->current_foto[0] < 200){

			$this->upload_result[] = array($this->current_foto[6], 24);
			return false;

		}

	return true;
	}

	function check_file_params ($file = false, $full_test = true){

		if (!$this->current_check) return true;
		if ($this->current_foto[5] && $this->current_url != '') return true;

		if (!$file)
			$file = FOTO_DIR . '/' . $this->main_dir . '/' . $this->pic_filname;

		while(1){

			if ($this->current_foto[5]){

				$this->current_foto[1] = $this->current_foto[2] = 0;

				$result = $this->check_filesize($file);
				break;

			}

			if(!defined('DEBUG_MODE'))
				$img_info = @getimagesize($file);
			else
				$img_info = getimagesize($file);

			if (!$img_info || $img_info == 'null'){
				$this->upload_result[] = array($this->current_foto[6], 3);
				return false;
			}

			$this->current_foto[1] = $img_info[0];
			$this->current_foto[2] = $img_info[1];

			if (!$this->config['allow_resize']){
				if ($this->config['global_max_foto_width'] < $img_info[0] || $this->config['global_max_foto_height'] < $img_info[1]){
					$this->upload_result[] = array($this->current_foto[6], 6);
					return false;
				}
				$result = $this->check_filesize($file);
				break;
			}

			if ($this->config['check_small_file_size'] && $this->config['global_max_foto_width'] > $img_info[0] && $this->config['global_max_foto_height'] > $img_info[1]){
				$result = $this->check_filesize($file);
				break;
			}

			$this->get_filesize($file);

			$result = true;
			break;

		}

		if (!$full_test) return $result;

		if ($result && $this->check_xss($file) && $this->get_file_hash($file)) return true;

		return false;
	}

	function check_xss($file){

		$fh = @fopen($file, 'rb' );
		$file_check = @fread( $fh, 512 );
		@fclose( $fh );

		# Thanks to Nicolas Grekas from comments at www.splitbrain.org for helping to identify all vulnerable HTML tags
		if($file_check && !preg_match( "#<script|<html|<head|<title|<body|<pre|<table|<a\s+href|<img|<plaintext|<cross\-domain\-policy#si", $file_check ) )
		{
			return true;
		}

		$this->upload_result[] = array($this->current_foto[6], 9);
		return false;
	}

	function get_file_hash($file){
	global $db;

		if (!$this->config['file_hash']) return true;

		$this->current_foto[4] = @md5_file($file);

		if ($this->config['check_double']){

			$control = $db->super_query("SELECT COUNT(picture_id) as count FROM " . PREFIX . "_gallery_picturies WHERE md5_hash = '".$this->current_foto[4]."'");
			if ($control['count']){

				$this->upload_result[] = array($this->current_foto[6], 23);
				return false;

			}

		}

	 return true;
	}

	function get_new_filename ($i = ''){

		$this->pic_filname = $this->current_name . $i . '.' . $this->current_type;

	}

	function check_existing($root_set = false){
	global $db;

		if (!$root_set)
			$root = FOTO_DIR . '/' . $this->main_dir .'/';
		else
			$root = $root_set;

		$this->get_new_filename ();

		$this->current_foto[3] = 0;
		$r_temp = $this->pic_filname;

		$exists = ($this->current_url != '') ? false : file_exists($root . $this->pic_filname);

		if ($this->config['file_db_exists'] && !$exists){

			$control = $db->super_query("SELECT picture_id, preview_filname FROM " . PREFIX . "_gallery_picturies WHERE category_id = '".$this->sub_dir."' AND picture_filname = '".$this->pic_filname."'");
			if ($control['picture_id']) $this->current_foto[3] = $control['picture_id'];

		}

		if (!$exists && !$this->current_foto[3]) return true;

		switch ($this->config['rewrite']){
		default : 
			$this->upload_result[] = array($this->current_foto[6], 7);
			return false;
		break;
		case '2' : 

			if ($this->config['file_db_exists'] && !$this->current_foto[3] && $exists){

				$control = $db->super_query("SELECT picture_id, preview_filname FROM " . PREFIX . "_gallery_picturies WHERE category_id = '".$this->sub_dir."' AND picture_filname = '".$this->pic_filname."'");
				if ($control['picture_id']) $this->current_foto[3] = $control['picture_id'];

			}

			@unlink($root . $this->pic_filname);

			if (!$this->current_foto[3]) break;
			//if ($this->config['file_db_exists']) break;

			if ($control['preview_filname'] == '') $control['preview_filname'] = $this->pic_filname;

			@unlink(FOTO_DIR . '/comthumb/'.$this->sub_dir.'/'.$control['preview_filname']);
			@unlink(FOTO_DIR . '/thumb/'.$this->sub_dir.'/'.$control['preview_filname']);
			@unlink(FOTO_DIR . '/caticons/'.$this->sub_dir.'/'.$control['preview_filname']);

		break;
		case '1' : 

			$this->current_foto[3] = 0;
			$in = 2;
			$stop = false;

			while (!$stop){

				$this->get_new_filename ('_'.$in);
				$in++;

				$is = ($this->current_url != '') ? false : file_exists($root . $this->pic_filname);

				if (!$is && !$this->config['file_db_exists']) $stop = true;
				elseif (!$is)
				{
					$control = $db->super_query("SELECT picture_id FROM " . PREFIX . "_gallery_picturies WHERE category_id = '".$this->sub_dir."' AND picture_filname = '".$this->pic_filname."'");
					if (!$control['picture_id']) $stop = true;
				}

			}

			$this->current_name .= '_' . ($in - 1);

		}

	return true;
	}

	function move_file($upload_mode, $root_set = false){
	global $db, $member_id;

		if ($this->current_foto[5] && $this->current_url != '') return true;

		$error = $register_file = false;

		if (!$root_set)
			$root = FOTO_DIR . '/' . $this->main_dir .'/';
		else
			$root = $root_set;

		switch ($upload_mode){
			case 1 :

				if (!$root_set) $register_file = '/' . $this->main_dir .'/' . $this->pic_filname;

				if(!defined('DEBUG_MODE')){
					@move_uploaded_file($this->current_image, $root . $this->pic_filname) or $error = true;
				} else {
					move_uploaded_file($this->current_image, $root . $this->pic_filname) or $error = true;
				}

				$this->current_moved = true;

			break;
			case 2 :

				if (!$root_set) $register_file = '/' . $this->main_dir .'/' . $this->pic_filname;

				if(!defined('DEBUG_MODE')){
					@copy($this->current_image, $root . $this->pic_filname) or $error = true;
				} else {
					copy($this->current_image, $root . $this->pic_filname) or $error = true;
				}

				$this->current_moved = true;

			break;
			case 3 :

				if (!$root_set) $register_file = '/' . $this->main_dir .'/' . $this->pic_filname;

				if(!defined('DEBUG_MODE')){
					@rename($this->current_image, $root . $this->pic_filname) or $error = true;
				} else {
					rename($this->current_image, $root . $this->pic_filname) or $error = true;
				}

				$this->current_moved = true;

			break;
		}

		if ($error){

			$this->upload_result[] = array($this->current_foto[6], 8);
			return false;

		} elseif ($register_file) {

			$db->query("INSERT " . PREFIX . "_gallery_temp_files (path, date, user_id) VALUES ('".$db->safesql($register_file)."', '".DATETIME."', '".$member_id['user_id']."')");

		}

		if(!defined('DEBUG_MODE'))
			@chmod ($root . $this->pic_filname, 0666);
		else
			chmod ($root . $this->pic_filname, 0666);

		return true;

	}

	function image_operations (){

		if ($this->config['disable_thumbnailer'] || !$this->config['allow_resize'] && !$this->config['insert_watermark'] && !$this->config['comm_thumb'] && !$this->config['thumb_size'] && !$this->config['icon_size'])
			return true;

		$resize_config = array();
		$root = FOTO_DIR . '/' . $this->main_dir . '/';

		if ($this->current_url == '')
			$resize_config['m'] = array ($this->config['global_max_foto_width'].'x'.$this->config['global_max_foto_height'], $this->config['allow_resize'], (!$this->config['no_main_watermark'] ? $this->config['insert_watermark'] : 0), $this->config['full_res_type'], $root . $this->pic_filname, !$this->config['check_small_file_size']);

		$preview = $this->pic_filname;
		$preview_ext = ($this->config['convert_png_thumb'] && in_array($this->current_type, array('png'))) ? 'jpg' : $this->current_type;
		$in = 1;
		$img_arr = explode('.', $preview);
		end($img_arr);
		unset($img_arr[(key($img_arr))]);
		$img_arr = ($this->config['random_name']) ? $this->random_name($this->config['random_name']) : implode('.', $img_arr);

		while ($in == 1 || file_exists(FOTO_DIR . '/thumb/'.$this->sub_dir.'/'.$preview) || file_exists(FOTO_DIR . '/comthumb/'.$this->sub_dir.'/'.$preview) || file_exists(FOTO_DIR . '/caticons/'.$this->sub_dir.'/'.$preview)){

			$preview = $img_arr.($in == 1 ? '' : '_'.$in).'.'.$preview_ext;
			$in++;

		}

		if ($this->config['icon_size']){

			switch (true){
			case (intval($this->config['icon_size']) <= intval($this->config['thumb_size'])) : $icon_type = $this->config['thumb_res_type']; break;
			case ($this->config['comm_thumb'] && intval($this->config['icon_size']) <= intval($this->config['comm_thumb'])) : $icon_type = $this->config['comm_res_type']; break;
			default : $icon_type = $this->config['full_res_type'];
			}

			$resize_config['i'] = array ($this->config['icon_size'], true, $this->config['insert_watermark'], $icon_type, FOTO_DIR . '/caticons/' . $this->sub_dir .'/' . $preview, false);

		}

		if ($this->config['comm_thumb']) $resize_config['c'] = array ($this->config['comm_thumb'], true, $this->config['insert_watermark'], $this->config['comm_res_type'], FOTO_DIR . '/comthumb/' . $this->sub_dir .'/' . $preview, false);
		$resize_config['t'] = array ($this->config['thumb_size'], true, $this->config['insert_watermark'], $this->config['thumb_res_type'], FOTO_DIR . '/thumb/' . $this->sub_dir .'/' . $preview, false);

		$thumb = new gallery_thumbnailer($root . $this->pic_filname, $this->config['quality'], $this->current_type, $resize_config, (bool)($this->current_url));

		if ($thumb->error()){
			$this->upload_result[] = array($this->current_foto[6], 17);
			return false;
		}

		$this->current_foto[8] = ($preview == $this->pic_filname || ($thumb->thumbnails['i'] != 1 && $thumb->thumbnails['c'] != 1 && $thumb->thumbnails['t'] != 1)) ? '' : $preview;
		$this->current_foto[7] = $thumb->get_thumbnails();

		if ($this->current_url == ''){

			clearstatcache();
			list($this->current_foto[1], $this->current_foto[2]) = @getimagesize($root . $this->pic_filname);
			$this->current_foto[0] = intval(@filesize($root . $this->pic_filname));
			$this->current_foto[1] = intval($this->current_foto[1]);
			$this->current_foto[2] = intval($this->current_foto[2]);

		}

	return true;
	}

	function remove_temp_row(){
	global $db;

		if ($this->pic_filname && $this->current_moved){

			$register_file = '/' . $this->main_dir .'/' . $this->pic_filname;

			if ($this->current_url != '') @unlink(FOTO_DIR . $register_file);

			$db->query("DELETE FROM " . PREFIX . "_gallery_temp_files WHERE path='".$db->safesql($register_file)."'");

		}

	}

	function insert_file(){
	global $db, $is_logged, $member_id, $galConfig, $catedit, $config;
	static $tagscloud = false;

		if (!isset($this->insert_data['ip'])){

			$this->insert_data['ip'] = version_compare($config['version_id'], "9.6", ">") ? get_ip() : $this->safesql($_SERVER['REMOTE_ADDR']);
			$this->insert_data['user_id'] = 0;
			$this->insert_data['category'] = $this->sub_dir;
			$this->insert_data['session_id'] = $this->insert_data['approve'] == 3 ? session_id() : '';

			if ($is_logged){
				$this->insert_data['name'] = $db->safesql($member_id['name']);
				$this->insert_data['email'] = '';
				$this->insert_data['user_id'] = intval($member_id['user_id']);
			}

		}

		if ($this->insert_data['tags'] && !is_object($tagscloud)){

			include_once TWSGAL_DIR.'/classes/edittagscloud.php';

			$tagscloud = new gallery_tags_edit();
			$tagscloud->filter_tags($this->insert_data['tags']);
			$this->insert_data['tags'] = $tagscloud->get_tags();

		}

		if ($this->current_url == ''){

			$type_upload = 0;
			$full_link = '';

		} else {

			$type_upload = 1;
			$full_link = $db->safesql($this->current_url);

		}

		$foto_title = (isset($this->insert_data['foto_title']) && $this->insert_data['foto_title'] != '') ? $this->insert_data['foto_title'] . " " . $this->insert_data['all_time_images'] : "";
		$foto_alt_name = totranslit($foto_title, true, false);
		$foto_symbol = ($config['create_catalog'] && $foto_title) ? $foto_title{0} : "";

		if (isset($this->insert_data['all_time_images'])) $this->insert_data['all_time_images']++;

		if (!$this->current_foto[3]){

			if (!isset($this->insert_data['maxposi'])){

				$ps = $db->super_query("SELECT MAX(posi) AS total FROM " . PREFIX . "_gallery_picturies");
				$this->insert_data['maxposi'] = $ps['total'];

			}

			$this->insert_data['maxposi']++;

			$db->query("INSERT INTO " . PREFIX . "_gallery_picturies (picture_title, picture_alt_name, image_alt_title, text, tags, posi, picture_filname, preview_filname, media_type, md5_hash, full_link, type_upload, size, width, height, picture_user_name, user_id, email, ip, date, lastdate, category_id, file_views, allow_comms, allow_rate, comments, rating, vote_num, approve, symbol, logs, session_id, thumbnails)
			VALUES ('{$foto_title}', '{$foto_alt_name}', '', '', '".$this->insert_data['tags']."', '".$this->insert_data['maxposi']."', '".$this->pic_filname."', '".$this->current_foto[8]."', '".$this->current_foto[5]."', '".$this->current_foto[4]."', '{$full_link}', '{$type_upload}', '".$this->current_foto[0]."', '".$this->current_foto[1]."', '".$this->current_foto[2]."', '".$this->insert_data['name']."', '".$this->insert_data['user_id']."', '".$this->insert_data['email']."', '".$this->insert_data['ip']."', '".DATETIME."', '".DATETIME."', '".$this->insert_data['category']."', '0', '1', '1', '0', '0', '0', '".$this->insert_data['approve']."', '{$foto_symbol}', '', '".$this->insert_data['session_id']."', '".$this->current_foto[7]."')");

			$this->upload_stats['insert']++;

			$id = $db->insert_id();

			if ($this->insert_data['approve'] == 1){

				if (!is_object($catedit))
					$catedit = new gallery_category_edit();

				$category_update_id = $catedit->get_parents_id($this->insert_data['category'], $this->insert_data['p_id']);

				if ($galConfig['icon_type']){ // Сохраняем иконку самого нового файла из вновь публикуемых
					if ($this->current_foto[5] && !$this->current_foto[8])
						$file = "{THEME}/gallimages/extensions/".get_extension_icon ($this->current_type, $this->current_foto[5]);
					else {
						$thumb_path = thumb_path($this->current_foto[7], 'i');
						if ($type_upload && $thumb_path == 'main')
							$file = $this->current_url;
						elseif ($thumb_path == 'main' || !$this->current_foto[8])
							$file = '{FOTO_URL}/'.$thumb_path.'/'.$this->insert_data['category'].'/'.$this->pic_filname;
						else
							$file = '{FOTO_URL}/'.$thumb_path.'/'.$this->insert_data['category'].'/'.$this->current_foto[8];
					}
				}

				$db->query("UPDATE " . PREFIX . "_gallery_category SET ".($galConfig['icon_type'] ? "icon=IF((icon='' OR icon_picture_id),'{$file}',icon), icon_picture_id=IF((icon!='' AND icon='{$file}'),'{$id}',icon_picture_id), " : "")."images=IF(id=".$this->insert_data['category'].",images+1,images), all_time_images=IF(id=".$this->insert_data['category'].",all_time_images+1,all_time_images), last_date=IF(id=".$this->insert_data['category'].",'".DATETIME."',last_date), last_cat_date='".DATETIME."', cat_images=cat_images+1 WHERE id IN (".implode(",",$category_update_id).")"); 

				if ($this->insert_data['tags']) $tagscloud->insert_files_tags(array($id));

			} else
				$db->query("UPDATE " . PREFIX . "_gallery_category SET all_time_images=all_time_images+1 WHERE id='".$this->insert_data['category']."'");

			return $id;

		} else {

			$db->query("UPDATE " . PREFIX . "_gallery_picturies SET picture_title='{$foto_title}', picture_alt_name='{$foto_alt_name}', image_alt_title='', symbol='{$foto_symbol}', picture_filname='".$this->pic_filname."', preview_filname='".$this->current_foto[8]."', media_type='".$this->current_foto[5]."', md5_hash='".$this->current_foto[4]."', full_link='{$full_link}', type_upload='{$type_upload}', size='".$this->current_foto[0]."', width='".$this->current_foto[1]."', height='".$this->current_foto[2]."', picture_user_name='".$this->insert_data['name']."', user_id='".$this->insert_data['user_id']."', email='".$this->insert_data['email']."', ip='".$this->insert_data['ip']."', lastdate='".DATETIME."', file_views='0', thumbnails='".$this->current_foto[7]."' WHERE picture_id='".$this->current_foto[3]."'");

			$db->query("UPDATE " . PREFIX . "_gallery_category SET all_time_images=all_time_images+1 WHERE id='".$this->insert_data['category']."'");

			$this->upload_stats['update']++;

			return 0;

		}

	}

	function move_error_file(){

		if ($this->pic_filname && $this->current_moved){

			if ($this->config['delete'] || $this->current_url != ''){

				@unlink(FOTO_DIR . '/' . $this->main_dir .'/' . $this->pic_filname);

			} else {

				$this->move_to_dir(FOTO_DIR . '/' . $this->main_dir .'/' . $this->pic_filname, FOTO_DIR . '/temp/' . $this->current_name . '.' . $this->current_type);
				$this->temp_exceptions[] = $this->current_name . '.' . $this->current_type;

			}

			$this->remove_temp_row();

		} elseif ($this->current_url == '') {

			if ($this->config['delete']){

				@unlink($this->current_image);

			} else {

				$this->move_to_dir($this->current_image, FOTO_DIR . '/temp/' . $this->current_name . '.' . $this->current_type);
				$this->temp_exceptions[] = $this->current_name . '.' . $this->current_type;

			}

		}

	}

	function move_to_dir($file, $newfile){

		$error = false;

		$new_file = $newfile;
		$in = 1;

		while (file_exists($new_file))
		{
			$_name = explode(".", $newfile);
			$_ext = end($_name);
			unset($_name[key($_name)]);
			$_newfile = implode("", $_name);
			$in++;
			$new_file = $_newfile . '_' . $in . '.' . $_ext;
		}

		if(!defined('DEBUG_MODE')){
			@rename($file, $new_file) or $error = true;
		} else {
			rename($file, $new_file) or $error = true;
		}

		if (!$error){
			if(!defined('DEBUG_MODE'))
				@chmod ($new_file, 0666);
			else
				chmod ($new_file, 0666);
		}

	 return $error;
	}

	function Unzip($_input_param){

		$_FILES[$_input_param]['name'] = array($_FILES[$_input_param]['name']);
		$_FILES[$_input_param]['tmp_name'] = array($_FILES[$_input_param]['tmp_name']);
		$_FILES[$_input_param]['error'] = array($_FILES[$_input_param]['error']);
		$filesize = $_FILES[$_input_param]['size'];

		if (!$this->get_file($_input_param, 0, 1)) return false;

		if ($this->current_type != 'zip'){

			$this->upload_result[] = array($this->current_foto[6], 10);
			return false;

		}

		if (!$this->config['zip_filesize'] || $filesize <= ($this->config['zip_filesize'] * 1024 * 1024)){

			if ($this->create_dirs(array('temp/' . $this->zip_temp_dir)) == false){

				$this->upload_result[] = array(false, 20);
				return false;

			}

			$this->get_new_filename($this->zip_temp_dir);

			if (!$this->move_file(1, FOTO_DIR . '/temp/' . $this->zip_temp_dir . '/')) return false;

			if ($this->config['zip_filesize']) $filesize = @filesize(FOTO_DIR . '/temp/' . $this->zip_temp_dir . '/' . $this->pic_filname);

		}

		if ($this->config['zip_filesize'] && $filesize > ($this->config['zip_filesize'] * 1024 * 1024)){

			$this->upload_result[] = array($this->current_foto[6], 11);
			return false;

		}

		if ($filesize < 1000){

			$this->upload_result[] = array($this->current_foto[6], 24);
			return false;

		}

		include_once(TWSGAL_DIR.'/classes/pclzip.lib.php');

		$archive = new PclZip(FOTO_DIR . '/temp/' . $this->zip_temp_dir . '/' . $this->pic_filname);

		if ($archive->properties() == 0){

			$this->upload_result[] = array($this->current_foto[6], 12, "<br />" . $archive->errorInfo(true));
			return false;

		}

		$result = $archive->extract(PCLZIP_OPT_PATH, FOTO_DIR .'/temp/'. $this->zip_temp_dir, PCLZIP_CB_PRE_EXTRACT, 'myUnzipPreExtractCallBack');

		@unlink(FOTO_DIR . '/temp/' . $this->zip_temp_dir .'/' . $this->pic_filname);

		if (!$result || !is_array($result)){
			$this->upload_result[] = array($this->current_foto[6], 12, "<br />" . $archive->errorInfo(true));
			return false;
		}

		return $result;

	}

	function delete_folder($handle, $delete_dirs = true){

		if (!is_dir($handle)) return 0;

		if(!defined('DEBUG_MODE'))
			$handle_folder = @opendir($handle);
		else
			$handle_folder = opendir($handle);

		if (!$handle_folder) return 0;

		while (false !== ($file = @readdir($handle_folder))){

			if ($file != "." && $file != ".htaccess" && $file != ".."){
				if (!is_dir($handle . '/' . $file))
					@unlink($handle . '/' . $file);
				elseif ($delete_dirs)
					$this->delete_folder($handle . '/' . $file);
			}

		}

		@closedir($handle_folder);

		if ($delete_dirs){

			if(!defined('DEBUG_MODE'))
				@rmdir($handle);
			else
				rmdir($handle);

		}

	}

}


?>