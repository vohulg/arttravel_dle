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
 Файл: thumbnailer.php
-----------------------------------------------------
 Назначение: Класс создания уменьшенных копий
=====================================================
 Версия thumbnailer.php 1,1
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

class gallery_thumbnailer {

	var $info;
	var $imagedata;
	var $quality = 90;
	var $current_image = '';
	var $wm_inserted = false;
	var $size = array();
	var $thumbnails = array();
	var $global_error = 0;
	var $sx = 0;
	var $sy = 0;
	var	$save = false;
	var	$save_first = false;
	var	$preview = '';

	function gallery_thumbnailer($file, $quality, $type, $resize_config, $save_first = false){

		$this->current_image 	= $file;
		$this->type				= $type;
		$this->quality			= $quality;
		$this->save_first		= $save_first;

		switch ($this->type){

			case "jpg" :
			case "jpeg" :
			case "jpe" :
			case "png" :
			case "gif" :

				if (!count($resize_config)) return true;

				$this->loadimage();

				if ($this->global_error == 1) return false;

				uasort ($resize_config, array("gallery_thumbnailer", "compare_res_par"));

				foreach ($resize_config as $index => $data){

					$this->thumbnails[$index] = $this->resize_image($data[0], $data[1], $data[2], $data[3], $data[4], $data[5]);       

				}

				$this->clear_image();

				$this->thumbnails = array_reverse($this->thumbnails, true);

			break;

		}
 	 return true;
	}

	function compare_res_par($a, $b){

		$subb = intval($b[0]) - intval($a[0]);

		if ($subb != 0) return $subb;

		$t_b = explode("x", $b[0]);
		$t_a = explode("x", $a[0]);

		return intval($t_b[1] > 0 ? $t_b[1] : $t_b[0]) - intval($t_a[1] > 0 ? $t_a[1] : $t_a[0]);

	}

	function loadimage(){

		$this->info = @getimagesize($this->current_image);

		switch ($this->info[2]){

			case 2 :
				if(!defined('DEBUG_MODE')){
					$this->imagedata = @imagecreatefromjpeg ($this->current_image);
				} else {
					$this->imagedata = imagecreatefromjpeg ($this->current_image);
				}
				$this->type = "jpeg";
				break;

			case 3 :
				if(!defined('DEBUG_MODE')){
					$this->imagedata = @imagecreatefrompng ($this->current_image);
				} else {
					$this->imagedata = imagecreatefrompng ($this->current_image);
				}
				$this->type = "png";
				break;

			case 1 :
				if(!defined('DEBUG_MODE')){
					$this->imagedata = @imagecreatefromgif ($this->current_image);
				} else {
					$this->imagedata = imagecreatefromgif ($this->current_image);
				}
				$this->type = "gif";
				break;

			default :
				$this->global_error = 1;
				return false;

		}

		if (!is_resource($this->imagedata)){ $this->global_error = 1; return false; }

		if(!defined('DEBUG_MODE')){
			$this->size['width'] = @imagesx($this->imagedata);
			$this->size["height"] = @imagesy($this->imagedata);
		} else {
			$this->size['width'] = imagesx($this->imagedata);
			$this->size["height"] = imagesy($this->imagedata);
		}

	  return true;
	}

	function resize_image($new_size, $allow_new_size, $insert_watermark, $resize_type, $new_pic_filname, $do_image_resize){
						//размер,    разрешить сжатие, наложить взнак,   тип сжатия,   путь сохранения,  сжать файл принудительно

		$this->save = 0;

		if ($new_size && $allow_new_size){

			$new_size = explode("x", $new_size);
			$allow_resize = $this->get_new_size(intval($new_size[0]), (count($new_size) == 2 ? intval($new_size[1]) : intval($new_size[0])), $resize_type);

			if ($allow_resize == 1 || $allow_resize == 0 && ($do_image_resize || $this->save_first))
				$this->do_image_resize();

		}

		if ($insert_watermark && ($this->save || @file_exists($new_pic_filname)))
			$this->insert_watermark($insert_watermark);

		return $this->save_image($new_pic_filname);
	}


	function get_new_size($new_width, $new_height, $resize_type){

		if (!$new_width || !$new_height) return -1;

		$this->size['new_width'] = min($new_width, $this->size['width']);
		$this->size["new_height"] = min($new_height, $this->size['height']);

		if ($new_width >= $this->size['width'] && $new_height >= $this->size['height']) return 0;

		switch ($resize_type){

			case "5" :

				$size_ratio = max($this->size['new_width'] / $this->size['width'], $this->size["new_height"] / $this->size['height']);

				$src_w = ceil($this->size['new_width'] / $size_ratio);
				$src_h = ceil($this->size["new_height"] / $size_ratio);

				$prop_const = 0.6;

				if ((($this->size['width'] - $src_w)/2) > (($this->size['watermark_width']+$this->size['watermark_margin'])*$prop_const) || 
				(($this->size['height'] - $src_h)/2) > (($this->size['watermark_height']+$this->size['watermark_margin'])*$prop_const))
					$this->wm_inserted = false;

				$this->sx = floor(($this->size['width'] - $src_w)/2);
				$this->sy = floor(($this->size['height'] - $src_h)/2);

				$this->size['width'] = $src_w;
				$this->size['height'] = $src_h;

				break;

			case "1" :

				if ($this->size['width'] <= $new_width){
					return 0;
				} else {
					$this->size['new_width'] = $new_width;
					$this->size['new_height'] = ($new_width/$this->size['width'])*$this->size['height'];
				}

				break;

			case "2" :

				if ($this->size['height'] <= $new_height){
					return 0;
				} else {
					$this->size['new_width'] = ($new_height/$this->size['height'])*$this->size['width'];
					$this->size['new_height'] = $new_height;
				}

				break;

			case "3" :

				$this->size['new_width'] = $new_width;
				$this->size['new_height'] = $new_height;

				break;

			case "4" :

				if ($this->size['height'] >= $this->size['width']){

                    $this->size['new_height'] = $new_height;
                    $this->size['new_width'] = ($new_height/$this->size['height'])*$this->size['width'];

                    if ($this->size['new_width'] <= $new_width){
                        return 0;
                    }

					$this->size['new_width'] = $new_width;
					$this->size['new_height'] = ($new_width/$this->size['width'])*$this->size['height'];

                } else {

                    $this->size['new_width'] = $new_width;
                    $this->size['new_height'] = ($new_width/$this->size['width'])*$this->size['height'];

                    if ($this->size['new_height'] <= $new_height){
                        return 0;
                    }

					$this->size['new_height'] = $new_height;
					$this->size['new_width'] = ($new_height/$this->size['height'])*$this->size['width'];

                }

			break;

			default:

				if ($this->size['width'] >= $this->size['height']){
					$this->size['new_width'] = $new_width;
					$this->size['new_height'] = ($new_width/$this->size['width'])*$this->size['height'];
				} else {
					$this->size['new_height'] = $new_height;
					$this->size['new_width'] = ($new_height/$this->size['height'])*$this->size['width'];
				}

		}

	return 1;
	}


	function do_image_resize(){

		$this->save = 1;
		$this->save_first = false;

		$temp = imagecreatetruecolor($this->size['new_width'], $this->size['new_height']);

		if ($this->type == "png"){
			imagealphablending($temp, false);
			imagesavealpha($temp, true);
		}

		imagecopyresampled($temp, $this->imagedata, 0, 0, $this->sx, $this->sy, $this->size['new_width'], $this->size['new_height'], $this->size['width'], $this->size['height']);

		$this->clear_image();
		$this->imagedata = $temp;

		if(!defined('DEBUG_MODE')){
			$this->size['width'] = @imagesx($this->imagedata);
			$this->size['height'] = @imagesy($this->imagedata);
		} else {
			$this->size['width'] = imagesx($this->imagedata);
			$this->size['height'] = imagesy($this->imagedata);
		}

	}


	function insert_watermark($min_image){
	global $config, $galConfig;

		if ($this->wm_inserted) return;

		$this->wm_inserted = true;

		if ($galConfig['watermark_light'] != "")
			$watermark_light = ROOT_DIR.'/templates/'.$config['skin'].'/'.$galConfig['watermark_light'];
		else
			$watermark_light =  ROOT_DIR.'/templates/'.$config['skin'].'/dleimages/watermark_light.png';

		if ($galConfig['watermark_dark'] != "")
			$watermark_dark =  ROOT_DIR.'/templates/'.$config['skin'].'/'.$galConfig['watermark_dark'];
		else
			$watermark_dark =  ROOT_DIR.'/templates/'.$config['skin'].'/dleimages/watermark_dark.png';

		if (!isset($this->size['watermark_width'])){
			list($this->size['watermark_width'], $this->size['watermark_height']) = getimagesize($watermark_light);
			$this->size['watermark_margin'] = 7;
		}

		$watermark_x = $this->size['width'] - $this->size['watermark_margin'] - $this->size['watermark_width'];
		$watermark_y = $this->size["height"] - $this->size['watermark_margin'] - $this->size['watermark_height'];

		$watermark_x2 = $watermark_x + $this->size['watermark_width'];
		$watermark_y2 = $watermark_y + $this->size['watermark_height'];

		if ($watermark_x < 0 OR $watermark_y < 0 OR	$watermark_x2 > $this->size['width'] OR $watermark_y2 > $this->size["height"] OR $this->size['width'] < $min_image OR $this->size["height"] < $min_image)
		{
		return;
		}

		$this->save = 1;

		$test = imagecreatetruecolor(1, 1);
		imagecopyresampled($test, $this->imagedata, 0, 0, $watermark_x, $watermark_y, 1, 1, $this->size['watermark_width'], $this->size['watermark_height']);
		$rgb = imagecolorat($test, 0, 0);
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;

		$max = min($r, $g, $b);
		$min = max($r, $g, $b);
		$lightness = (double)(($max + $min) / 510.0);
		imagedestroy($test);

		$watermark_image = ($lightness < 0.5) ? $watermark_light : $watermark_dark;

		$watermark = imagecreatefrompng($watermark_image);
		imagealphablending($this->imagedata, TRUE);
		imagealphablending($watermark, TRUE);

		if (in_array($this->type, array("png", "gif"))){

			$temp_img = imagecreatetruecolor($this->size['width'], $this->size["height"]);
			imagealphablending ( $temp_img , false );
			imagesavealpha ( $temp_img , true );
			imagecopy($temp_img, $this->imagedata, 0, 0, 0, 0, $this->size['width'], $this->size["height"]);
			imagecopy($temp_img, $watermark, $watermark_x, $watermark_y, 0, 0, $this->size['watermark_width'], $this->size['watermark_height']);
			imagecopy($this->imagedata, $temp_img, 0, 0, 0, 0, $this->size['width'], $this->size["height"]);
			imagedestroy($temp_img);

		} else {

			imagecopy($this->imagedata, $watermark, $watermark_x, $watermark_y, 0, 0, $this->size['watermark_width'], $this->size['watermark_height'] );

		}

		imagedestroy($watermark);

	}


	function save_image($file){

		if (!$this->save) return intval(@file_exists($file));

		$img_arr = explode('.',$file);
		$type = end($img_arr);

		switch ($type){

			case "jpeg" :
			case "jpg" :
			case "jpeg" :
			case "jpe" :
				imagejpeg($this->imagedata, "$file", $this->quality);
				break;

			case "png" :
				imagepng($this->imagedata, "$file");
				break;

			case "gif" :
				imagegif($this->imagedata, "$file");
				break;

			default :

				$this->global_error = 1;
				return 0;

		}

		if (!file_exists($file)){
			$this->global_error = 1;
			return 0;
		}

		@chmod ($file, 0666);
		return 1;

	}

	function get_thumbnails(){

		foreach ($this->thumbnails as $thumbnail)
			if ($thumbnail){
				return implode('|', array_keys($this->thumbnails)) . '||' . implode('|', $this->thumbnails);
				break;
			}

		return "";
	}

	function clear_image(){
		imagedestroy($this->imagedata);
	}

	function error(){
		return $this->global_error;
	}

}

?>