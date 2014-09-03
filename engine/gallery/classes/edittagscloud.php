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
 Файл: edittagscloud.php
-----------------------------------------------------
 Назначение: Класс редактирования облака тегов
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

class gallery_tags_edit {

	var	$_tags = array();
	var	$current_tags = array();
	var	$flag_newtags = false;
	var	$changed = false;
	var	$action = 0;
	var	$changed_data = false;

	function filter_tags ($input){
	global $db, $galConfig, $config;

		$this->flag_newtags = true;
		$this->current_tags = array();

		if (@preg_match("/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $input)) return "";
		$input = @$db->safesql(htmlspecialchars(strip_tags(stripslashes($input)), ENT_QUOTES, $config['charset']));

		$max = 60;
		$min = 3;

		if ($galConfig['tags_len']){

			$filter = explode('-', $galConfig['tags_len']);
			$filter[0] = intval($filter[0]);

			if (count($filter) == 1){
				if ($filter[0] > 0 && $filter[0] < 61) $max = $filter[0];
			} else {
				$filter[1] = intval($filter[1]);
				if ($filter[0] < 60) $min = $filter[0];
				if ($filter[1] > 0 && $filter[1] < 61) $max = $filter[1];
			}

		}

		$c = 0;
		$length = 0;

		$input = explode(",", $input);

		for ($i = 0; $i < count($input); $i++){
			if ($length) $length += 2;
			$input[$i] = trim($input[$i]);
			$len = dle_strlen($input[$i], $config['charset']);

			if ($len <= $max && $len >= $min && (!$galConfig['tags_num'] || ++$c < $galConfig['tags_num']) && ($length+=$len) <= 255)
				$this->current_tags[] = $input[$i];
		}

		$this->current_tags = array_unique($this->current_tags);

	}

	function get_tags(){
		if (!$this->flag_newtags && defined('DEBUG_MODE')) die("Tag cloud error: [DEBUG: 1]");
		return implode(",", $this->current_tags);
	}

	function current_tags ($input, $approve = true, $was_approve = false){
	global $db;

		$this->changed_data = false;

		switch (true){
		case ($approve && !$was_approve) : $this->action = 1; break; // INSERT
		case (!$approve && $was_approve) : $this->action = 2; break; // DELETE
		default	: $this->action = 0;
		}

		if ($this->flag_newtags)
			$return = $this->get_tags();
		else
			$return = @$db->safesql($input);

		$this->changed = ($approve && $this->flag_newtags && $input != stripslashes($return));

		if (!$this->flag_newtags && $this->action == 1){

			$this->current_tags = array();
			$input = explode(",", $input);

			for ($i = 0; $i < count($input); $i++){
				$input[$i] = trim($input[$i]);
				if ($input[$i]) $this->current_tags[] = $input[$i];
			}

		}

		$this->flag_newtags = false;

		return $return;
	}

	function update_files_tags ($file_id){
	global $db;

		if (!$this->changed && !$this->action)
			return;

		if (!is_array($file_id)) $file_id = array($file_id);

		switch ($this->action){
		case 1 : // INSERT

			$this->insert_files_tags ($file_id);

		break;
		case 2 : // DELETE

			$this->remove_files_tags ($file_id);

		break;
		default	:

			$this->remove_files_tags ($file_id);
			$this->insert_files_tags ($file_id);

		}

	}

	function remove_files_tags ($file_id){
	global $db;

		if (!is_array($file_id) || !count($file_id))
			return 0;

		$db->query("DELETE FROM " . PREFIX . "_gallery_tags_match WHERE file_id IN (".implode(",", $file_id).")");

		$this->changed_data = true;
	}

	function insert_files_tags ($file_id){
	global $db;

		if (!is_array($file_id) || !count($file_id) || !count($this->current_tags))
			return 0;

		$db->query("INSERT IGNORE INTO " . PREFIX . "_gallery_tags (tag_name) VALUES ('".implode("'),('", $this->current_tags)."')");

		$_tags = array_diff($this->current_tags, $this->_tags);

		if (count($_tags)){

			$db->query("SELECT id, tag_name FROM " . PREFIX . "_gallery_tags WHERE tag_name IN ('".implode("','", $_tags)."')");

			while($row = $db->get_row())
				$this->_tags[$row['tag_name']] = $row['id'];

			$db->free();

		}

		$_tags = array();

		foreach ($this->current_tags as $tag)
			$_tags[] = "({$this->_tags[$tag]},".implode("),({$this->_tags[$tag]},", $file_id).")";

		$db->query("INSERT IGNORE INTO " . PREFIX . "_gallery_tags_match (tag_id, file_id) VALUES ".implode(",", $_tags));

		$this->changed_data = true;
	}

}

?>