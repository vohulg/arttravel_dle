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
 Файл: tagscloud.php
-----------------------------------------------------
 Назначение: Класс вывода облака тегов
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

class gallery_tags_cloud {

	var $count_tags = 0;
	var $limit = 'all';

	function tags_page(){
	global $tpl, $config;

		$tpl->result['content'] = get_gallery_cache("alltagscloud", $config['skin']);

		if ($tpl->result['content'] !== false) return;

		$tpl->load_template('gallery/tagscloud.tpl');

		if (preg_match("#\\{tags limit=['\"](.+?)['\"]\\}#i", $tpl->copy_template, $matches))
			$this->limit = $matches[1];

		if ($this->limit != 'all')
			$tpl->set($matches[0], $this->get_tags_list());
		else
			$tpl->set('{tags}', $this->get_tags_list());

		$tpl->compile('content');
		$tpl->clear();

		create_gallery_cache ("alltagscloud", $tpl->result['content'], $config['skin']);
	}

	function tags_tags($limit, $cache, $no_button){
	global $config;

		$cache_id = $config['skin'].$limit;

		if ($cache == "yes")
			return $this->get_tags_list(array("tag_tags", $cache_id), $no_button);

		return $this->get_tags_list(false, $no_button);
	}

	function get_tags_list ($cache = false, $no_button = false){
	global $db, $galConfig, $langGal, $config;

		if ($cache){
			$list_tags = get_gallery_cache ($cache[0], $cache[1]);
			if ($list_tags !== false) return $list_tags;
		}

		if ($this->limit != 'all'){

			$this->limit = intval($this->limit);
			if ($this->limit < 1) $this->limit = 40;

		}

		$min = $max = 1;
		$tags = array();

		$db->query("SELECT COUNT(m.mid) as count, t.tag_name FROM " . PREFIX . "_gallery_tags_match m INNER JOIN " . PREFIX . "_gallery_tags t ON t.id=m.tag_id GROUP BY t.id".($this->limit != 'all' ? " LIMIT 0,".$this->limit : ""));

		while($row = $db->get_row()){
			$tags[] = array(stripslashes($row['tag_name']), $row['count']);
			$min = min($min, $row['count']);
			$max = max($max, $row['count']);
		}

		$db->free();

		$range = $max-$min;
		if ($range <= 0) $range = 1;
		if ($min <= 0) $min = 1;

		$this->count_tags = count($tags);

		if (!$this->count_tags){
			if ($cache)	create_gallery_cache ($cache[0], "", $cache[1]);
			return "";
		}

		usort ($tags, array("gallery_tags_cloud", "compare_tags"));
		$sizes = array( "clouds_xsmall", "clouds_small", "clouds_medium", "clouds_large", "clouds_xlarge" );

		$list_tags = array();

		for ($i=0; $i < $this->count_tags; $i++){

			if ($config['allow_alt_url'] == "yes")
	        	$list_tags[] = "<a href=\"{$galConfig['mainhref']}all/tag-".urlencode($tags[$i][0])."/\" class=\"".$sizes[sprintf("%d", ($tags[$i][1]-$min)/$range*4)]."\" title=\"".$langGal['tags_count']." ".$tags[$i][1]."\">".$tags[$i][0]."</a>";
			else
				$list_tags[] = "<a href=\"{$galConfig['mainhref']}&act=15&p=tag-".urlencode($tags[$i][0])."\" class=\"".$sizes[sprintf("%d", ($tags[$i][1]-$min)/$range*4)]."\" title=\"".$langGal['tags_count']." ".$tags[$i][1]."\">".$tags[$i][0]."</a>";

		}

		unset($tags);

		$list_tags = implode(", ", $list_tags);

		if ($this->limit != 'all' && !$no_button){

			$count_tags = $db->super_query("SELECT COUNT(t.id) as count FROM " . PREFIX . "_gallery_tags_match m INNER JOIN " . PREFIX . "_gallery_tags t ON t.id=m.tag_id GROUP BY m.tag_id");

			if ($count_tags['count'] > $this->limit){

				if ($config['allow_alt_url'] == "yes")
					$list_tags .= "<br /><br /><a href=\"{$galConfig['mainhref']}tags/\">".$langGal['all_tags']."</a>";
				else
					$list_tags .= "<br /><br /><a href=\"{$galConfig['mainhref']}&act=22\">".$langGal['all_tags']."</a>";

			}

		}

		if ($cache)	
			create_gallery_cache ($cache[0], $list_tags, $cache[1]);

		return $list_tags;
	}

	function compare_tags($a, $b){

		if($a[0] == $b[0]) return 0;
		return strnatcasecmp($a[0] , $b[0]);
	}

}

?>