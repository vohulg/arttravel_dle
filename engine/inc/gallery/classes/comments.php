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
 Файл: comments.php
-----------------------------------------------------
 Назначение: Класс комментариев
=====================================================
 Версия class.comments.php 1,2
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

class UnComments extends db {

	var $this_id		= "";
	var $data_querry	= "";
	var $count_querry	= "";
	var $comments_num	= "";
	var $sort_order 	= "asc";
	var $comm_nummers 	= 20;
	var $template 		= 'comments';
	var $cstart			= 0;
	var $comm_table		= '';
	var $allow_cmod		= 0;
	var $allow_addc		= 0;
	var $add_sript		= '';
	var $id				= 0;
	var $url			= '';
	var $flood_table	= false;
	var $update_user_sql = false;
	var $update_news_sql = false;
	var $user_mod_id 	= false;
	var $item_url 		= '';
	var $doact 			= '';
	var $allow_ajax		= false;
	var $allow_subscribe = true;

	function UnComments(){
	global $config, $member_id, $is_logged, $galConfig;

		if (in_array($config['comm_msort'], array('DESC', 'ASC', 'asc', 'desc'))) $this -> sort_order = strtolower($config['comm_msort']);

		$this->comm_table 	= PREFIX . '_gallery_comments';
		$this->flood_table = PREFIX . '_gallery_flood';

		if (!$galConfig['comsubslevel']) $this->allow_subscribe = false;

		if ($is_logged && $member_id['gallery_cs_flag']){

			$this->query("UPDATE " . $this->comm_table . "_subscribe SET flag=1, date='".date('Y-m-d', TIME)."' WHERE user_id={$member_id['user_id']} AND flag=0");
			$this->query("UPDATE " . USERPREFIX . "_users SET gallery_cs_flag=0 WHERE user_id={$member_id['user_id']}");

		} elseif ($this->allow_subscribe && $_GET['subscribe'] == 'update' && $_GET['fid']){

			$_GET['fid'] = isset($_GET['fid']) ? intval($_GET['fid']) : 0;
			$_GET['user_id'] = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
			$_GET['email'] = isset($_GET['email']) ? $this->safesql(base64_decode(@rawurldecode(trim($_GET['email'])))) : '';

			if ($_GET['fid'] && ($_GET['email'] || $_GET['user_id']))
				$this->query("UPDATE " . $this->comm_table . "_subscribe SET flag=1, date='".date('Y-m-d', TIME)."' WHERE file_id={$_GET['fid']} AND ".($_GET['user_id'] ? "user_id=".$_GET['user_id'] : "gast_email='".$_GET['email']."'"));
		}

	}

	function ajax_added(){
	global $member_id, $user_group;

		$this->cstart 		= 1;
		if ($this -> allow_cmod) $approve = " AND c.approve=1"; else $approve = "";
		$this->data_querry = "SELECT c.id, c.post_id, c.date, c.autor as gast_name, c.email as gast_email, c.text, c.ip, u.user_id, u.name, u.email, u.news_num, u.comm_num, u.user_group, u.reg_date, u.lastdate, u.signature, u.foto, u.fullname, u.land, u.icq, u.xfields FROM " . $this -> comm_table . " c LEFT JOIN " . USERPREFIX . "_users u ON c.user_id=u.user_id WHERE c.post_id = '". $this -> id ."'".$approve." ORDER BY c.id DESC";
		$this->comm_nummers = 1;
		$this->comments_num = 1;
		$this->allow_addc	= true;
		$this->ShowCommentslist(1);

	}

	function ShowCommentslist($mode = 0, $cache_comments = false){
	global $tpl, $config, $is_logged, $member_id, $user_group, $lang, $allow_comments_ajax, $where_approve, $dle_login_hash, $langGal, $smartphone_detected;

		if ($this->count_querry == "" && !$this->comments_num) return;

		$this->comm_nummers = intval($this->comm_nummers);

		if ($this->cstart < 1) $this->cstart = 1;

		$approve = $this->allow_cmod ? " AND c.approve=1" : "";

		if ($this->data_querry == "")
			$this->data_querry = "SELECT c.id, c.post_id, c.date, c.autor as gast_name, c.email as gast_email, c.text, c.ip, u.user_id, u.name, u.email, u.news_num, u.comm_num, u.user_group, u.lastdate, u.reg_date, u.signature, u.foto, u.fullname, u.land, u.icq, u.xfields FROM " . $this -> comm_table . " c LEFT JOIN " . USERPREFIX . "_users u ON c.user_id=u.user_id WHERE c.post_id = '". $this -> id ."'".$approve." ORDER BY date " . $this -> sort_order . " LIMIT ".(($this -> cstart-1)*$this -> comm_nummers)."," . $this -> comm_nummers;
		else
			$this->data_querry .= " LIMIT ".(($this -> cstart-1)*$this -> comm_nummers)."," . $this -> comm_nummers;

		$cache_data = false;

		if ($cache_comments && !$is_logged && $this->cstart < 6){

			$cache_comments = 'all_comments_'.md5($this->data_querry);
			$cache_data = get_gallery_cache ($cache_comments);

			if ($cache_data !== false){

				$cache_data = unserialize($cache_data);
				$tpl->result['comments'] .= $cache_data[0];
				$tpl->result['fastnav'] = $cache_data[1];
				$this->comments_num = $cache_data[2];
				unset($cache_data);

				if (!$this->comments_num) return;

				$cache_data = true;

			}

		} else $cache_comments = '';

		if (!$cache_data){

			if ($this->count_querry != ""){

				$sql_count = $this->super_query("SELECT COUNT(c.id) as count FROM " . $this -> count_querry);

				if (!$sql_count['count']){

					if ($cache_comments)
						create_gallery_cache ($cache_comments, serialize(array("", "", 0)));

					return;
				}

				$this->comments_num = $sql_count['count'];

			}

			$tpl->load_template($this -> template . '.tpl');

			$xfound = (strpos($tpl->copy_template, "[xfvalue_") !== false) ? true : false;

			if ($xfound) $xfields = xfieldsload(true);

			$tpl->result['comments'] = "";

			if ($mode == 0){

				if ($this -> sort_order == "desc")	$tpl->result['comments'] .= "\n<div id=\"dle-ajax-comments\"></div>\n";

				$tpl->result['comments'] .= "<a name=\"comment\"></a>";

				if ($is_logged && (($user_group[$member_id['user_group']]['allow_editc'] || $user_group[$member_id['user_group']]['edit_allc'])))
					$tpl->result['comments'] .= "<form method=\"post\" action=\"\" name=\"dlemasscomments\" id=\"dlemasscomments\">\n";

				$tpl->result['comments'] .= "<div id=\"ajaxcommslist\">\n";

			}

			$sql_result = $this->query($this->data_querry);

			$i = 0;
			$gallery_referrer = false;

			while($row = $this->get_row($sql_result)){ $i++;

				$row['date'] = strtotime($row['date']);
				$row['gast_name'] = stripslashes($row['gast_name']);
				$row['gast_email'] = stripslashes($row['gast_email']);
				$row['name'] = stripslashes($row['name']);

				if (!$row['user_id'] OR $row['name'] == '') {

					if ($row['gast_email'] != "")
						$tpl->set( '{author}', "<a href=\"mailto:".htmlspecialchars($row['gast_email'], ENT_QUOTES, $config['charset'])."\">" . $row['gast_name'] . "</a>" );
					else
						$tpl->set( '{author}', $row['gast_name'] );

					$tpl->set( '{login}', $row['gast_name'] );
					$tpl->set( '[profile]', "" );
					$tpl->set( '[/profile]', "" );
					$tpl->set( '{profile_author}', "" );

				} else {

					$encoded = urlencode($row['name']);

					if ($config['allow_alt_url'] == "yes")
						$url_user =  $config['http_home_url']."user/".$encoded."/";
					else
						$url_user =  $config['http_home_url']."index.php?subaction=userinfo&amp;user=".$encoded;

					$tpl->set( '[profile]', "<a href=\"" . $url_user . "/\">" );
					$tpl->set( '{login}', $row['name']);
					$tpl->set( '[/profile]', "</a>" );

					$tpl->set('{author}', "<a onclick=\"return dropdownmenu(this, event, GalUserMenu('".htmlspecialchars($url_user, ENT_QUOTES, $config['charset'])."', '".$encoded."'), '220px')\" onMouseout=\"delayhidemenu()\" href=\"".$url_user."\">".$row['name']."</a>");
					$tpl->set('{profile_author}', "<a onclick=\"ShowProfile('{$encoded}', '".htmlspecialchars($url_user, ENT_QUOTES, $config['charset'])."', gallery_admin_editusers); return false;\" href=\"".$url_user."\">".$row['name']."</a>");

				}

				if ($is_logged AND $member_id['user_group'] == '1')
					$tpl->set('{ip}', "IP: <a onclick=\"return dropdownmenu(this, event, IPMenu('".$row['ip']."', '".$lang['ip_info']."', '".$lang['ip_tools']."', '".$lang['ip_ban']."'), '190px')\" onMouseout=\"delayhidemenu()\" href=\"http://www.nic.ru/whois/?ip={$row['ip']}\" target=\"_blank\">{$row['ip']}</a>");
				else
					$tpl->set('{ip}', '');

				$edit_limit = false;
				if (!$user_group[$member_id['user_group']]['edit_limit'] || ($row['date'] + ($user_group[$member_id['user_group']]['edit_limit'] * 60)) > TIME) $edit_limit = true;
				if (isset($member_id['restricted']) and in_array($member_id['restricted'], array(2,3))) $edit_limit = false;

				if ($is_logged AND $edit_limit AND (($member_id['user_id'] == $row['user_id'] AND $user_group[$member_id['user_group']]['allow_editc']) OR $member_id['user_group'] == '1' OR $user_group[$member_id['user_group']]['edit_allc'])){
					$tpl->set('[com-edit]',"<a onclick=\"twsg_ajax_comm_edit('".$row['id']."');return false;\" href=\"".$config['http_home_url']."index.php?".$this -> doact."subaction=comm_edit&com_id=".$row['id']."\">");
					$tpl->set('[/com-edit]',"</a>");
					$allow_comments_ajax = true;
					$gallery_referrer = true;
				} else $tpl->set_block("'\\[com-edit\\](.*?)\\[/com-edit\\]'si","");

				if ($is_logged AND $edit_limit AND (($member_id['user_id'] == $row['user_id']  AND $user_group[$member_id['user_group']]['allow_delc']) OR ($this -> user_mod_id AND $user_group[$member_id['user_group']]['allow_delc'] AND $row['user_group'] > 2) OR $member_id['user_group'] == '1' OR $user_group[$member_id['user_group']]['del_allc'])){
					$tpl->set('[com-del]',"<a onclick=\"DeleteComment('".$row['id']."');return false;\" href=\"".$config['http_home_url']."index.php?".$this -> doact."subaction=comm_del&com_id=".$row['id']."\">");
					$tpl->set('[/com-del]',"</a>");
					$gallery_referrer = true;
				} else $tpl->set_block("'\\[com-del\\](.*?)\\[/com-del\\]'si","");

				if ($this -> allow_addc){
					if (!$row['user_id'] OR $row['name'] == '') $row['name'] = $row['gast_name'];
					$tpl->set('[fast]',"<a onmouseover=\"dle_copy_quote('".str_replace( array(" ", "&#039;"), array("&nbsp;", "&amp;#039;"), $row['name'] )."');\" href=\"#\" onclick=\"old_dle_ins('".str_replace( array(" ", "&#039;"), array("&nbsp;", "&amp;#039;"), $row['name'] )."'); return false;\"\">");
					$tpl->set('[/fast]',"</a>");
				} else $tpl->set_block("'\\[fast\\](.*?)\\[/fast\\]'si","");

				if (($member_id['user_group'] == '1' OR $user_group[$member_id['user_group']]['del_allc']) AND !$user_group[$member_id['user_group']]['edit_limit'])
					$tpl->set( '{mass-action}', "<input name=\"com_id[]\" value=\"{$row['id']}\" type=\"checkbox\" />" );
				else
					$tpl->set( '{mass-action}', "" );

				$tpl->set('{mail}', $row['email']);

				if (date('Ymd', $row['date']) == date('Ymd', TIME))
					$tpl->set('{date}', $lang['time_heute'].langdate(", H:i", $row['date']));
				elseif (date('Ymd', $row['date']) == date('Ymd', (TIME - 86400)))
					$tpl->set('{date}', $lang['time_gestern'].langdate(", H:i", $row['date']));
				else			
					$tpl->set( '{date}', langdate($config['timestamp_comment'], $row['date']));

				$tpl->copy_template = preg_replace ( "#\{date=(.+?)\}#ie", "langdate('\\1', '{$row['date']}')", $tpl->copy_template );

////////////////////////////блок последних комментариев

				if ($this -> item_url != ''){

					if (!$row['type_upload'])
						$row['full_link'] = FOTO_URL.'/main/'.$row['category_id'].'/'.$row['picture_filname'];

					if ($config['allow_alt_url'] == "yes" && $row['picture_alt_name'])
						$fotourl = $row['cat_alt_name']."/".$row['picture_id']."-".$row['picture_alt_name'].".html";
					elseif ($config['allow_alt_url'] == "yes")
						$fotourl = $row['cat_alt_name']."/".$row['picture_id'].".html";
					else
						$fotourl = "&act=2&cid=".$row['category_id']."&fid=".$row['picture_id'];

					$title = stripslashes($row['picture_title']);
					$row['image_alt_title'] = stripslashes($row['image_alt_title']);

					if ($row['image_alt_title'] == '') $row['image_alt_title'] = $title;

					$tpl->set('{alt_title}', $row['image_alt_title']);
					$tpl->set('{news_title}', "<a href=\"".$this -> item_url . $fotourl . "\">".($title != '' ? $title : $langGal['view_no_title'])."</a>");

					$tpl->set('[lastcomments]', '');
					$tpl->set('[/lastcomments]', '');

					if (!$row['media_type']){

						if ($row['picture_text'] != ''){

							if ($user_group[$member_id['user_group']]['allow_hide'])
								$row['picture_text'] = str_ireplace(array("[hide]", "[/hide]"), "", stripslashes($row['picture_text']));
							else
								$row['picture_text'] = preg_replace("#\[hide\](.+?)\[/hide\]#is", "<div class=\"quote\">" . $lang['news_regus'] . "</div>", stripslashes($row['picture_text']));

						}

						if (!$smartphone_detected && ($row['picture_text'] != '' || $title != '')){
							$caption = '<span class="highslide-caption">'.$title;
							if ($row['picture_text'] != '') $caption .= '<div class"gallery_foto_descr">' . $row['picture_text'] . "</div>";
							$caption .= '</span>';
						} else $caption = '';

						$thumb_path = thumb_path($row['thumbnails'], 't');

						if ($thumb_path != 'main')
							$thumb_path = FOTO_URL.'/'.$thumb_path.'/'.$row['category_id'].'/'.$row[($row['preview_filname'] ? 'preview_filname' : 'picture_filname')];
						else
							$thumb_path = $row['full_link'];

						$alt_title = $row['image_alt_title'] ? ' alt="'.$row['image_alt_title'].'" title="'.$row['image_alt_title'].'"' : '';

						$tpl->set('{thumb}', '<img src="'.$thumb_path.'"'.$alt_title.' />');
						$tpl->set('[fullimageurl]', '<a href="'.$fotourl.'" onclick="return hs.expand(this, { src: \''.$row['full_link'].'\' } )">');
						$tpl->set('[/fullimageurl]', '</a>'.$caption);
						$tpl->set('[isfoto]', '');
						$tpl->set('[/isfoto]', '');
						$tpl->set_block("'\\[ismedia\\](.*?)\\[/ismedia\\]'si","");

					} else {

						if ($row['preview_filname']){
							$thumb_path = thumb_path($row['thumbnails'], 't');
							$thumb_path = FOTO_URL.'/'.$thumb_path.'/'.$row['category_id'].'/'.$row['preview_filname'];
						} else $thumb_path = "";

						$tpl->set('[ismedia]', '');
						$tpl->set('[/ismedia]', '');
						$tpl->set('[fullimageurl]', '');
						$tpl->set('[/fullimageurl]', '');
						$tpl->set_block("'\\[isfoto\\](.*?)\\[/isfoto\\]'si","");

						$tpl->set('{thumb}', players($row['full_link'], $row['media_type'], false, $thumb_path, $row['image_alt_title']));

					}

				} else {

					$tpl->set('{news_title}', '');
					$tpl->set_block("'\\[lastcomments\\](.*?)\\[/lastcomments\\]'si","");

				}

///////////////////////////////////////////////////

			  if ($xfound) {
				$xfieldsdata = xfieldsdataload ($row['xfields']);

				foreach ($xfields as $value) {
				  $preg_safe_name = preg_quote($value[0], "'");

				  if ($value[5] != 1 OR $member_id['user_group'] == 1 OR ($is_logged AND $member_id['user_id'] == $row['user_id'])) {
					if( empty( $xfieldsdata[$value[0]] ) ) {
						$tpl->copy_template = preg_replace( "'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );
						$tpl->copy_template = str_replace( "[xfnotgiven_{$preg_safe_name}]", "", $tpl->copy_template );
						$tpl->copy_template = str_replace( "[/xfnotgiven_{$preg_safe_name}]", "", $tpl->copy_template );
					} else {
						$tpl->copy_template = preg_replace( "'\\[xfnotgiven_{$preg_safe_name}\\](.*?)\\[/xfnotgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );
						$tpl->copy_template = str_replace( "[xfgiven_{$preg_safe_name}]", "", $tpl->copy_template );
						$tpl->copy_template = str_replace( "[/xfgiven_{$preg_safe_name}]", "", $tpl->copy_template );
					}
					$tpl->copy_template = preg_replace("'\\[xfvalue_{$preg_safe_name}\\]'i", stripslashes($xfieldsdata[$value[0]]), $tpl->copy_template);
				  } else {
					  $tpl->copy_template = preg_replace("'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template);
					  $tpl->copy_template = preg_replace("'\\[xfvalue_{$preg_safe_name}\\]'i", "", $tpl->copy_template);
					  $tpl->copy_template = preg_replace( "'\\[xfnotgiven_{$preg_safe_name}\\](.*?)\\[/xfnotgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );
				  }
				}
			}

				if ($this -> sort_order == "asc")
					$tpl->set('{comment-id}', $i+($this -> comm_nummers*($this -> cstart-1)));
				else
					$tpl->set('{comment-id}', $this->comments_num - $i - $this -> comm_nummers*($this -> cstart-1) + 1);

				if ($row['foto']){

					if (version_compare($config['version_id'], "9.7", ">") && count(explode("@", $row['foto'])) == 2)
						$tpl->set( '{foto}', 'http://www.gravatar.com/avatar/' . md5(trim($row['foto'])) . '?s=' . intval($user_group[$row['user_group']]['max_foto']) );	
					else
						$tpl->set('{foto}', $config['http_home_url']."uploads/fotos/".$row['foto']);

				} else $tpl->set('{foto}', version_compare($config['version_id'], "9.6", ">") ? "{THEME}/dleimages/noavatar.png" : "{THEME}/images/noavatar.png");

				if( $row['user_id'] AND $row['fullname'] ) {
					$tpl->set( '[fullname]', "" );
					$tpl->set( '[/fullname]', "" );
					$tpl->set( '{fullname}', stripslashes( $row['fullname'] ) );
					$tpl->set_block( "'\\[not-fullname\\](.*?)\\[/not-fullname\\]'si", "" );
				} else {
					$tpl->set_block( "'\\[fullname\\](.*?)\\[/fullname\\]'si", "" );
					$tpl->set( '{fullname}', "" );
					$tpl->set( '[not-fullname]', "" );
					$tpl->set( '[/not-fullname]', "" );
				}

				if( $row['user_id'] AND $row['icq'] ) {
					$tpl->set( '[icq]', "" );
					$tpl->set( '[/icq]', "" );
					$tpl->set( '{icq}', stripslashes( $row['icq'] ) );
					$tpl->set_block( "'\\[not-icq\\](.*?)\\[/not-icq\\]'si", "" );
				} else {
					$tpl->set_block( "'\\[icq\\](.*?)\\[/icq\\]'si", "" );
					$tpl->set( '{icq}', "" );
					$tpl->set( '[not-icq]', "" );
					$tpl->set( '[/not-icq]', "" );
				}

				if( $row['user_id'] AND $row['land'] ) {
					$tpl->set( '[land]', "" );
					$tpl->set( '[/land]', "" );
					$tpl->set( '{land}', stripslashes( $row['land'] ) );
					$tpl->set_block( "'\\[not-land\\](.*?)\\[/not-land\\]'si", "" );
				} else {
					$tpl->set_block( "'\\[land\\](.*?)\\[/land\\]'si", "" );
					$tpl->set( '{land}', "" );
					$tpl->set( '[not-land]', "" );
					$tpl->set( '[/not-land]', "" );
				}			

				if( $row['comm_num'] ) {
					$tpl->set( '[comm-num]', "" );
					$tpl->set( '[/comm-num]', "" );
					$tpl->set( '{comm-num}', intval($row['comm_num']) );
					$tpl->set_block( "'\\[not-comm-num\\](.*?)\\[/not-comm-num\\]'si", "" );
				} else {
					$tpl->set( '{comm-num}', 0 );
					$tpl->set( '[not-comm-num]', "" );
					$tpl->set( '[/not-comm-num]', "" );
					$tpl->set_block( "'\\[comm-num\\](.*?)\\[/comm-num\\]'si", "" );
				}

				if( $row['news_num'] ) {
					$tpl->set( '[news-num]', "" );
					$tpl->set( '[/news-num]', "" );
					$tpl->set( '{news-num}', intval($row['news_num']) );
					$tpl->set_block( "'\\[not-news-num\\](.*?)\\[/not-news-num\\]'si", "" );
				} else {
					$tpl->set( '{news-num}', 0 );
					$tpl->set( '[not-news-num]', "" );
					$tpl->set( '[/not-news-num]', "" );
					$tpl->set_block( "'\\[news-num\\](.*?)\\[/news-num\\]'si", "" );
				}

				if ($row['user_id']) $tpl->set('{registration}', langdate("j.m.Y", $row['reg_date']));
				else $tpl->set('{registration}', '--');

				if ($row['user_id'] and $row['signature'] and $user_group[$row['user_group']]['allow_signature']) {
					$tpl->set('[signature]', '');
					$tpl->set('[/signature]', '');
					$tpl->set('{signature}', stripslashes($row['signature']));
				} else $tpl->set_block("'\\[signature\\](.*?)\\[/signature\\]'si","");

				if (!$row['user_group']) $row['user_group'] = 5;

				if ($user_group[$row['user_group']]['icon'])
					$tpl->set('{group-icon}', "<img src=\"".$user_group[$row['user_group']]['icon']."\" />");
				else
					$tpl->set('{group-icon}', "");

				$tpl->set('{group-name}', $user_group[$row['user_group']]['group_name']);

				if (version_compare($config['version_id'], "9.5", ">") && $row['user_id'] AND $row['lastdate'] ) {

					$tpl->set( '{lastdate}', langdate( "j.m.Y", $row['lastdate'] ) );

					if ( ($row['lastdate'] + 1200) > TIME OR ($row['user_id'] AND $row['user_id'] == $member_id['user_id'])) {
						$tpl->set( '[online]', "" );
						$tpl->set( '[/online]', "" );
						$tpl->set_block( "'\\[offline\\](.*?)\\[/offline\\]'si", "" );
					} else {
						$tpl->set( '[offline]', "" );
						$tpl->set( '[/offline]', "" );
						$tpl->set_block( "'\\[online\\](.*?)\\[/online\\]'si", "" );
					}

				} else { 

					$tpl->set( '{lastdate}', '--' );
					$tpl->set_block( "'\\[offline\\](.*?)\\[/offline\\]'si", "" );
					$tpl->set_block( "'\\[online\\](.*?)\\[/online\\]'si", "" );

				}

				if( $user_group[$member_id['user_group']]['allow_hide'] ) $row['text'] = str_ireplace(array("[hide]", "[/hide]"), "", $row['text']);
				else $row['text'] = preg_replace ( "#\[hide\](.+?)\[/hide\]#is", "<div class=\"quote\">" . $lang['news_regus'] . "</div>", $row['text'] );

				$tpl->set('{comment}', "<div id='comm-id-".$row['id']."'>".stripslashes($row['text'])."</div>");

				$tpl->copy_template = "\n<div id='comment-".$row['id']."'>".$tpl->copy_template."</div>\n";

				$tpl->compile('comments');

			}

			$tpl->clear();

			$this->free($sql_result);

			if ($mode == 0){

				$tpl->result['comments'] .= "</div>\n";

				if ($this -> sort_order == "asc" )		
					$tpl->result['comments'] .= "\n<div id=\"dle-ajax-comments\"></div>\n";

				if (($member_id['user_group'] == '1' OR $user_group[$member_id['user_group']]['del_allc']) AND !$user_group[$member_id['user_group']]['edit_limit']){

					$tpl->result['comments'] .= "\n<div class=\"mass_comments_action\">{$lang['mass_comments']}&nbsp;<select name=\"subaction\"><option value=\"\">{$lang['edit_selact']}</option><option value=\"comm_combine\">{$lang['edit_selcomb']}</option><option value=\"comm_del\">{$lang['edit_seldel']}</option></select>&nbsp;&nbsp;<input type=\"submit\" class=\"bbcodes\" value=\"{$lang['b_start']}\" /></div>\n<input type=\"hidden\" name=\"do\" value=\"gallery\" /><input type=\"hidden\" name=\"dle_allow_hash\" value=\"{$dle_login_hash}\" /><input type=\"hidden\" name=\"act\" value=\"3\" />";

					$gallery_referrer = true;

				}

				$tpl->result['comments'] .= "</form>\n";

				if ($gallery_referrer && !isset($_SESSION['gallery_referrer']) || $_SESSION['gallery_referrer'] != $_SERVER['REQUEST_URI'])
					$_SESSION['gallery_referrer'] = $_SERVER['REQUEST_URI'];

			}

			$tpl->result['fastnav'] = "";

			if ($this->comments_num > $this->comm_nummers){

				fastpages ($this -> comments_num, $this -> comm_nummers, $this->cstart, $this->url[0], $this->url[1], ($this -> allow_ajax ? "GalleryComPage('{INS}','".$this -> id."');return false;" : false));
				if ($mode == 0) $tpl->result['fastnav'] = "<div id=\"ajaxcommsnav\">\n" . $tpl->result['fastnav'] . "</div>\n";

			}

			if ($cache_comments)
				create_gallery_cache ($cache_comments, serialize(array($tpl->result['comments'], $tpl->result['fastnav'], $this->comments_num)));

		}

		if (strpos($tpl->result['content'], "<!--dlecomments-->") !== false)
			$tpl->result['content'] = str_ireplace("<!--dlecomments-->", $tpl->result['comments'], $tpl->result['content']);
		else
			$tpl->result['content'] .= $tpl->result['comments'];

		unset($tpl->result['comments']);

		if ($this->comments_num > $this->comm_nummers){

			if (strpos($tpl->result['content'], "<!--dlenavigationcomments-->") !== false)
				$tpl->result['content'] = str_ireplace ( "<!--dlenavigationcomments-->", $tpl->result['fastnav'], $tpl->result['content']);
			else
				$tpl->result['content'] .= $tpl->result['fastnav'];

			unset($tpl->result['fastnav']);

		}

	}

	function AddComment(){
	global $config, $is_logged, $member_id, $user_group, $lang, $galConfig;

		include_once ENGINE_DIR . '/classes/parse.class.php';

		$parse = new ParseFilter();
		$parse->safe_mode = true;
		$parse->allow_url = $user_group[$member_id['user_group']]['allow_url'];
		$parse->allow_image = $user_group[$member_id['user_group']]['allow_image'];
		$stop = array ();
		$name = '';
		$mail = '';

		if (!$is_logged){

			$not_allow_symbol = array ("\x22", "\x60", "\t", '\n', '\r', "\n", "\r", '\\', ",", "/", "¬", "#", ";", ":", "~", "[", "]", "{", "}", ")", "(", "*", "^", "%", "$", "<", ">", "?", "!", '"', "'" );
			$mail = $this->safesql(trim( str_replace( $not_allow_symbol, '', strip_tags( stripslashes( $_POST['mail'] ) ) ) ) );
			$name = $this->safesql($parse->process(trim($_POST['name'])));

			if (empty($name)) $stop[] = $lang['news_err_9'];
			elseif(dle_strlen($name, $config['charset']) > 40) $stop[] = $lang['news_err_1'];
			if( preg_match( "/[\||\'|\<|\>|\[|\]|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\{\+]/", $name ) ) $stop[] = $lang['reg_err_4'];
			if (strlen($mail) > 50) $stop[] = $lang['news_err_2'];
			if ($mail != "" AND !preg_match( "/^[\.A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $mail ) ) $stop[] = $lang['news_err_10'];

			if (!count($stop)){
				$this->query("SELECT name from " . USERPREFIX . "_users where LOWER(name) = '".strtolower($name)."'");
				if ($this->num_rows() > 0) $stop[] = $lang['news_err_7'];
				$this->free();
			}

		}

		$_IP = version_compare($config['version_id'], "9.6", ">") ? get_ip() : $this->safesql($_SERVER['REMOTE_ADDR']);

		if ($is_logged AND $config['comments_restricted'] AND ((TIME - $member_id['reg_date']) < ($config['comments_restricted'] * 86400)) )
			$stop[] = str_ireplace( '{days}', intval($config['comments_restricted']), $lang['news_info_8'] );

		if ($config['allow_comments_wysiwyg'] == "no" || $config['allow_comments_wysiwyg'] == "0")
			$comments = $parse->BB_Parse($parse->process($_POST['comments']), false);
		else{
			$parse->wysiwyg = true;
			$parse->ParseFilter( Array ('div', 'span', 'p', 'br', 'strong', 'em', 'ul', 'li', 'ol', 'b', 'u', 'i', 's' ), Array(), 0, 1);
			if ($user_group[$member_id['user_group']]['allow_url'] ) $parse->tagsArray[] = 'a';
			if ($user_group[$member_id['user_group']]['allow_image'] ) $parse->tagsArray[] = 'img';
			$comments = $parse->BB_Parse($parse->process($_POST['comments']));
		}

		if (intval($config['comments_minlen']) AND dle_strlen(str_replace(" ", "", strip_tags($comments)), $config['charset']) < $config['comments_minlen'])
			$stop[] = $lang['news_err_40'];

		$enable_captcha = !($is_logged AND isset($user_group[$member_id['user_group']]['disable_comments_captcha']) AND $member_id['comm_num'] >= $user_group[$member_id['user_group']]['disable_comments_captcha']);

		if ($enable_captcha && $user_group[$member_id['user_group']]['captcha']){

			if ($config['allow_recaptcha']){

				include_once ENGINE_DIR . '/classes/recaptcha.php';

				$_REQUEST['sec_code'] = 1;
				$_SESSION['sec_code_session'] = false;

				if ($_POST['recaptcha_response_field'] AND $_POST['recaptcha_challenge_field']){

					$resp = recaptcha_check_answer ($config['recaptcha_private_key'], $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);

					if ($resp->is_valid)
						$_REQUEST['sec_code'] = $_SESSION['sec_code_session'] = 1;

				}

			}

		} else $_REQUEST['sec_code'] = $_SESSION['sec_code_session'] = 1;

		if ($enable_captcha && isset($user_group[$member_id['user_group']]['comments_question']) && $user_group[$member_id['user_group']]['comments_question']){

			$pass_answer = false;

			$question = intval($_SESSION['question']);
			$question_answer = trim((function_exists('mb_strtolower')) ? mb_strtolower($_POST['question_answer'], $config['charset']) : strtolower($_POST['question_answer']));

			if ($question && $question_answer){

				$answers = $this->super_query("SELECT answer FROM " . PREFIX . "_question WHERE id={$question}");

				if ($answers['answer']){

					$answers = explode("\n", $answers['answer']);

					foreach ($answers as $answer)
						if (trim((function_exists('mb_strtolower')) ? mb_strtolower($answer, $config['charset']) : strtolower($answer)) == $question_answer){
							$pass_answer	= true;
							break;
						}

				}

			}

			if (!$pass_answer) $stop[] = $lang['reg_err_24'];

		}

		if ($is_logged and isset($member_id['restricted']) and ($member_id['restricted'] == 2 or $member_id['restricted'] == 3)) $stop[] = $lang['news_info_3'];

		if(!$this -> id) $stop[] = $lang['news_err_id'];
		if($config['comments_maxlen'] && dle_strlen($comments, $config['charset']) > $config['comments_maxlen']) $stop[] = $lang['news_err_3'];
		if ($_REQUEST['sec_code'] != $_SESSION['sec_code_session'] OR !$_SESSION['sec_code_session']) $stop[] = $lang['news_err_30'];
		if ($comments =='') $stop[] = $lang['news_err_11'];
		if ($parse->not_allowed_tags) $stop[] = $lang['news_err_33'];
		if ($parse->not_allowed_text) $stop[] = $lang['news_err_37'];

		$flood = -1;

		if (($member_id['user_group'] > 2 || defined('DEBUG_MODE')) AND !count($stop) AND $this -> flood_table AND ($config['flood_time'] OR $user_group[$member_id['user_group']]['max_comment_day'])){

			if (!$is_logged){

				$check_user = 0;

				if (count(explode(".", $_IP)) == 4 && strlen($_IP) < 16)
					$check_user = intval(str_replace(".", "", $_IP));

			} else $check_user = $member_id['user_id'];

			$flood = $this->super_query("SELECT COUNT(id) as count, MAX(date) FROM " . $this -> flood_table . " WHERE member_key='{$check_user}' AND date > '".date("Y-m-d H:i:s", (TIME-86400))."'");

			if ($config['flood_time'] && $flood['date'] && (strtotime($flood['date']) + $config['flood_time']) > TIME)
				$stop[] = $lang['news_err_4']." ".$lang['news_err_5']." {$config['flood_time']} ".$lang['news_err_6'];

			if ($user_group[$member_id['user_group']]['max_comment_day'] && $flood['count'] && $flood['count'] >=  $user_group[$member_id['user_group']]['max_comment_day'])
				$stop[] = str_replace('{max}', $user_group[$member_id['user_group']]['max_comment_day'], $lang['news_err_45']);

			//$flood_hash = md5($comments);

			//if (true && $flood['hash'] && $flood['hash'] == $flood_hash && (strtotime($flood['date']) + 43200) > TIME)
			//	$stop[] = $lang['news_err_4'];

		}

		$where_approve = 1;
		$_SESSION['sec_code_session'] = 0;
		$_SESSION['question'] = false;
	
		if (count($stop)) return $stop;

		$update_comments = false;

		if ($config['allow_combine']){

			$row = $this->super_query("SELECT id, post_id, user_id, date, text, ip, approve FROM " . $this -> comm_table . " WHERE post_id = '". $this -> id ."' ORDER BY id DESC LIMIT 0,1");

			if ($row['id']){

				if ($row['user_id'] == $member_id['user_id'] AND $row['user_id'] || $row['ip'] == $_IP AND !$row['user_id'] AND !$is_logged) $update_comments = true;

				$row['date'] = strtotime($row['date']);

				if (date("Y-m-d", $row['date']) != date("Y-m-d", TIME)) $update_comments = false;

				if ($user_group[$member_id['user_group']]['edit_limit'] AND (($row['date'] + ($user_group[$member_id['user_group']]['edit_limit'] * 60)) < TIME)) $update_comments = false;

				if (((dle_strlen($row['text'], $config['charset']) + dle_strlen($comments, $config['charset'])) > $config['comments_maxlen']) AND $update_comments){
					$update_comments = false;
					$stop[] = $lang['news_err_3'];
					msgbox ($lang['all_err_1'], implode("<br />", $stop)."<br /><br /><a href=\"javascript:history.go(-1)\">".$lang['all_prev']."</a>");
				}

			}

		}

		if (count($stop)) return $stop;

		if ($this -> allow_cmod AND $user_group[$member_id['user_group']]['allow_modc']) {

			if ($row['approve']) $update_comments = false;

			$where_approve = 0;
			$stop[] = $lang['news_err_31'];

		}

		$comments = $this->auto_wrap_text($config['auto_wrap'], $comments);
	
		 if ($update_comments) {

			$comments = $this->safesql($row['text'])."<br /><br />".$this->safesql($comments);
			$this->query("UPDATE " . $this -> comm_table . " set text='{$comments}', approve='{$where_approve}' WHERE id='{$row['id']}'");

		 } else {

			$comments =	$this->safesql($comments);

			 if ($is_logged)
				 $this->query("INSERT INTO " . $this -> comm_table . " (post_id, user_id, date, autor, email, text, ip, approve) values ('". $this -> id ."', '$member_id[user_id]', '".DATETIME."', '', '', '$comments', '$_IP', '$where_approve')");
			 else
				 $this->query("INSERT INTO " . $this -> comm_table . " (post_id, date, autor, email, text, ip, approve) values ('". $this -> id ."', '".DATETIME."', '$name', '$mail', '$comments', '$_IP', '$where_approve')");

			 // обновление количества комментариев в новостях 
			 if ($where_approve && $this -> update_news_sql)
			 	$this->query("UPDATE " . $this -> update_news_sql . " = '". $this -> id ."'");

			 if ($where_approve) $this -> comments_num++;

			 // обновление количества комментариев у юзера 
			 if ($is_logged && $this -> update_user_sql)
				 $this->query("UPDATE " . USERPREFIX . "_users set " . $this -> update_user_sql  . " where user_id ='$member_id[user_id]'");

		}

		 // Защита от флуда
		if ($flood != -1)
			$this->query("INSERT INTO " . $this -> flood_table . " (member_key, date) values ('{$check_user}', '".DATETIME."')");

		$found_subscribe = $this -> inform_new_comment($this -> id, $name, $comments, $_IP, $mail);

		if ($_POST['allow_subscribe'] && !$found_subscribe)
			$this->insert_subscribe($this->id, $mail, false, false);

		if ($where_approve){
			if ($galConfig['show_statistic']){
				$this->query("UPDATE " . PREFIX . "_gallery_config SET value=value+1 WHERE name IN ('statistic_com','statistic_com_day')");
				@unlink(ENGINE_DIR.'/cache/system/gallery_config.php');
			}
			clear_gallery_cache();
		}

		if (count($stop)) return $stop;
		if (AJAX_ACTION) return true;

		$this->close ();
		@header("Location: ".$this->url);
		die();

	}

	function edit_comment($bbcode_file='/modules/bbcode.php', $wysiwyg_file='/editor/comments.php',$action=''){
	global $is_logged, $member_id, $tpl, $config, $lang, $allow_comments_ajax, $user_group, $js_array;

		$id = intval($_REQUEST['com_id']);

		if ($id < 1) die();

		$row = $this->super_query("SELECT user_id, date, text FROM " . $this -> comm_table . " where id = '$id'");

		$have_perm = 0;
		if ($is_logged and (($member_id['user_id'] == $row['user_id'] and $user_group[$member_id['user_group']]['allow_editc']) or $member_id['user_group'] == '1' or $user_group[$member_id['user_group']]['edit_allc'])) $have_perm = 1;
		if ($user_group[$member_id['user_group']]['edit_limit'] AND ((strtotime($row['date']) + ($user_group[$member_id['user_group']]['edit_limit'] * 60)) < TIME)) $have_perm = 0;

		if ($have_perm){

			include_once ENGINE_DIR.'/classes/parse.class.php';

			$parse = new ParseFilter( );
			$parse->safe_mode = true;
			$parse->allow_url = $user_group[$member_id['user_group']]['allow_url'];
			$parse->allow_image = $user_group[$member_id['user_group']]['allow_image'];

			$tpl->load_template($this -> template . '.tpl');

			if ($config['allow_comments_wysiwyg'] == "no" || $config['allow_comments_wysiwyg'] == "0"){
				$text = $parse->decodeBBCodes($row['text'], false);
				include_once ENGINE_DIR . $bbcode_file;
				$tpl->set( '{editor}', $bb_code );
			} else {
				$text = $parse->decodeBBCodes($row['text'], TRUE, $config['allow_comments_wysiwyg']);
				include_once ENGINE_DIR . $wysiwyg_file;
				$allow_comments_ajax = true;
				$tpl->set('{editor}', $wysiwyg);
			}

			$tpl->set( '{text}', $text );
			$tpl->set( '{title}', $lang['comm_title'] );
			$tpl->set_block( "'\\[sec_code\\](.*?)\\[/sec_code\\]'si", "" );
			$tpl->set( '{sec_code}', "" );
			$tpl->set_block( "'\\[recaptcha\\](.*?)\\[/recaptcha\\]'si", "" );
			$tpl->set( '{recaptcha}', "" );
			$tpl->set_block( "'\\[question\\](.*?)\\[/question\\]'si", "" );
			$tpl->set( '{question}', "" );
			$tpl->set_block( "'\\[not-logged\\].*?\\[/not-logged\\]'si", "" );
			$tpl->set_block( "'\\[add-comment\\](.*?)\\[/add-comment\\]'si", "" );
			$tpl->set( '[edit-comment]', "" );
			$tpl->set( '[/edit-comment]', "" );

			$tpl->copy_template = "<form  method=\"post\" name=\"dle-comments-form\" id=\"dle-comments-form\" action=\"{$action}\">".$tpl->copy_template."
<input type=\"hidden\" name=\"subaction\" value=\"do_comm_edit\" />
<input type=\"hidden\" name=\"com_id\" value=\"{$id}\" /></form>";

			$tpl->compile('content');
			$tpl->clear();

		} else msgbox($lang['comm_err_2'], $lang['comm_err_3']);

	}

	function do_edit_comment(){
	global $is_logged, $member_id, $config, $user_group, $lang;

		$id = intval($_REQUEST['com_id']);

		if (!$id) die();

		include_once ENGINE_DIR.'/classes/parse.class.php';

		$parse = new ParseFilter( );
		$parse->safe_mode = true;
		$parse->allow_url = $user_group[$member_id['user_group']]['allow_url'];
		$parse->allow_image = $user_group[$member_id['user_group']]['allow_image'];

		$row = $this->super_query("SELECT post_id, user_id, date, approve FROM " . $this -> comm_table . " where id = '$id'");

		$error = $lang['comm_err_3'];

		$have_perm = 0;
		if ($is_logged and $member_id['user_id'] == $row['user_id'] and $user_group[$member_id['user_group']]['allow_editc']) $have_perm = 1;
		if ($is_logged and ($member_id['user_group'] == '1' or $user_group[$member_id['user_group']]['edit_allc'] OR $user_group[$member_id['user_group']]['admin_comments'])) $have_perm = 2;
		if ($user_group[$member_id['user_group']]['edit_limit'] AND ((strtotime($row['date']) + ($user_group[$member_id['user_group']]['edit_limit'] * 60)) < TIME)) $have_perm = 0;
		if (isset($member_id['restricted']) and in_array($member_id['restricted'], array(2,3))) $have_perm = 0;

		if ($have_perm){

			if ($config['allow_comments_wysiwyg'] == "no" || $config['allow_comments_wysiwyg'] == "0") $comments = $this->safesql( $parse->BB_Parse( $parse->process( $_POST['comments'] ), false ) );
			else {
				$parse->wysiwyg = true;
				$parse->ParseFilter( Array ('div', 'span', 'p', 'br', 'strong', 'em', 'ul', 'li', 'ol', 'b', 'u', 'i', 's' ), Array (), 0, 1 );
				if( $user_group[$member_id['user_group']]['allow_url'] ) $parse->tagsArray[] = 'a';
				if( $user_group[$member_id['user_group']]['allow_image'] ) $parse->tagsArray[] = 'img';
				$comments = $this->safesql( $parse->BB_Parse( $parse->process( $_POST['comments'] ) ) );
			}

			$comments = $this->auto_wrap_text($config['auto_wrap'], $comments);

			$error = false;

			if ($config['comments_maxlen'] && dle_strlen($comments, $config['charset']) > $config['comments_maxlen'])
				$error = $lang['news_err_3'];
			elseif (intval($config['comments_minlen']) AND dle_strlen(str_replace(" ", "", strip_tags($comments)), $config['charset']) < $config['comments_minlen'])
				$error = $lang['news_err_40'];
			elseif ($parse->not_allowed_tags)
				$error = $lang['news_err_33'];
			elseif ($parse->not_allowed_text)
				$error = $lang['news_err_37'];
			else {

				if ($have_perm == 2)
					$this->query("UPDATE " . $this -> comm_table . " set text='$comments', approve='1' where id = '$id'");
				else
					$this->query("UPDATE " . $this -> comm_table . " set text='$comments' where id = '$id'");

				if ($have_perm == 2 && !$row['approve']){

					if ($this -> update_news_sql)
						$this->query("UPDATE " . $this -> update_news_sql . " ='{$row['post_id']}'");

					clear_gallery_cache();

				}

			}

		}

		if ($error)
			msgbox( $lang['comm_err_2'], $error . " <a href=\"javascript:history.go(-1)\">$lang[all_prev]</a>" );
		else {

			$this->close ();

			if (isset($_SESSION['gallery_referrer']))
				@header("Location: {$_SESSION['gallery_referrer']}");
			else
				@header("Location: ".$config['http_home_url']."index.php?do=gallery");

			die();

		}

	}

	function ajax_edit_comment(){
	global $is_logged, $member_id, $config, $lang, $user_group;

		$id = intval($_REQUEST['com_id']);

		if (!$id) die();
	
		$row = $this->super_query("SELECT user_id, date, text FROM " . $this -> comm_table . " where id = '$id'");

		$have_perm = 0;
		if ($is_logged and (($member_id['user_id'] == $row['user_id'] and $row['user_id'] and $user_group[$member_id['user_group']]['allow_editc']) or $member_id['user_group'] == '1' or $user_group[$member_id['user_group']]['edit_allc'])) $have_perm = 1;
		if ($user_group[$member_id['user_group']]['edit_limit'] AND ((strtotime($row['date']) + ($user_group[$member_id['user_group']]['edit_limit'] * 60)) < TIME)) $have_perm = 0;

		if ($have_perm){

		include_once ENGINE_DIR.'/classes/parse.class.php';

		$parse = new ParseFilter();
		$parse->safe_mode = true;

		if ($config['allow_comments_wysiwyg'] == "no" || $config['allow_comments_wysiwyg'] == "0"){

			include_once ENGINE_DIR . '/ajax/bbcode.php';
			$comm_txt = $parse->decodeBBCodes( $row['text'], false );

		} else {

			$comm_txt = $parse->decodeBBCodes( $row['text'], true, "yes" );

			if (version_compare($config['version_id'], "9.5", ">") && $config['allow_comments_wysiwyg'] != "2"){

				if( $user_group[$member_id['user_group']]['allow_url'] ) $link_icon = "\"LinkDialog\", \"DLELeech\","; else $link_icon = "";
				if( $user_group[$member_id['user_group']]['allow_image'] ) $link_icon .= "\"ImageDialog\",";

				$bb_code = <<<HTML
<script type="text/javascript">
function show_editor( root ) {
	var use_br = false;
	var use_div = true;
	if ($.browser.mozilla || $.browser.webkit) { use_br = true; use_div = false; }
	oUtil.initializeEditor("ajaxwysiwygeditor",  {
		width: "100%", 
		height: "250", 
		css: root + "engine/editor/scripts/style/default.css",
		useBR: use_br,
		useDIV: use_br,
		groups:[
			["grpEdit1", "", ["Bold", "Italic", "Underline", "Strikethrough", "ForeColor"]],
			["grpEdit2", "", ["JustifyLeft", "JustifyCenter", "JustifyRight", "JustifyFull", "Bullets", "Numbering"]],
			["grpEdit3", "", [{$link_icon}"DLESmiles", "DLEQuote", "DLEHide"]]
	    ],
		arrCustomButtons:[
			["DLESmiles", "modalDialog('"+ root +"engine/editor/emotions.php',250,160)", "{$lang['bb_t_emo']}", "btnEmoticons.gif"],
			["DLEQuote", "DLEcustomTag('[quote]', '[/quote]')", "{$lang['bb_t_quote']}", "dle_quote.gif"],
			["DLEHide", "DLEcustomTag('[hide]', '[/hide]')", "{$lang['bb_t_hide']}", "dle_hide.gif"],
			["DLELeech", "DLEcustomTag('[leech=http://]', '[/leech]')", "{$lang['bb_t_leech']}", "dle_leech.gif"]
		]
		}
	);	
};

show_editor(dle_root);
</script>
HTML;

			} else {

				if ($user_group[$member_id['user_group']]['allow_url'] ) $link_icon = "link,dle_leech,separator,"; else $link_icon = "";
				if ($user_group[$member_id['user_group']]['allow_image'] ) $link_icon .= "image,";

				$tiny_mce_file = (version_compare($config['version_id'], "9.5", "<")) ? "tiny_mce.js" : "tiny_mce_gzip.php";

				$bb_code = <<<HTML
<script type="text/javascript">
setTimeout(function() {
	$('textarea.ajaxwysiwygeditor').tinymce({
		script_url : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/{$tiny_mce_file}',
		theme : "advanced",
		skin : "cirkuit",
		language : "{$lang['wysiwyg_language']}",
		width : "99%",
		height : "220",
		plugins : "safari,emotions,inlinepopups",
		convert_urls : false,
		force_p_newlines : false,
		force_br_newlines : true,
		dialog_type : 'window',
		extended_valid_elements : "div[align|class|style|id|title]",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,bullist,numlist,separator,{$link_icon}emotions,dle_quote,dle_hide",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",

		// Example content CSS (should be your site CSS)
		content_css : "{$config['http_home_url']}engine/editor/css/content.css",

		setup : function(ed) {
		        // Add a custom button
			ed.addButton('dle_quote', {
			title : '{$lang['bb_t_quote']}',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_quote.gif',
			onclick : function() {
				// Add you own code to execute something on click
				ed.execCommand('mceReplaceContent',false,'[quote]{\$selection}[/quote]');
			}
	           });

			ed.addButton('dle_hide', {
			title : '{$lang['bb_t_hide']}',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_hide.gif',
			onclick : function() {
				// Add you own code to execute something on click
				ed.execCommand('mceReplaceContent',false,'[hide]{\$selection}[/hide]');
			}
	           });

			ed.addButton('dle_leech', {
			title : '{$lang['bb_t_leech']}',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_leech.gif',
			onclick : function() {
				ed.execCommand('mceReplaceContent',false,"[leech=http://]{\$selection}[/leech]");
			}
	           });
   		 }
	});
}, 100);
</script>
HTML;

			}

		}

return <<<HTML
<div class="editor">
{$bb_code}
<textarea name="dleeditcomments{$id}" id="dleeditcomments{$id}" onfocus="setNewField(this.name, document.getElementById( 'dlemasscomments' ) )" class="ajaxwysiwygeditor" style="width:99%; height:150px; border:1px solid #E0E0E0; margin: 0px 1px 0px 0px;padding: 0px;">{$comm_txt}</textarea><br>
<div align="right" style="width:99%;padding-top:5px;"><input class=bbcodes title="$lang[bb_t_apply]" type=button onclick="uncom_ajax_save_comm_edit('{$id}'); return false;" value="$lang[bb_b_apply]">
<input class=bbcodes title="$lang[bb_t_cancel]" type=button onclick="uncom_ajax_cancel_comm_edit('{$id}'); return false;" value="$lang[bb_b_cancel]">
</div></div>
HTML;
		
		} else die( "error" );
	
	}

	function ajax_do_edit_comment(){
	global $is_logged, $member_id, $config, $user_group, $lang;

	$id = intval($_REQUEST['com_id']);

		if (!$id) die();
	
		$row = $this->super_query("SELECT post_id, user_id, date, approve FROM " . $this -> comm_table . " where id = '$id'");

		$have_perm = 0;
		if ($is_logged and $member_id['user_id'] == $row['user_id'] and $user_group[$member_id['user_group']]['allow_editc']) $have_perm = 1;
		if ($is_logged and ($member_id['user_group'] == '1' or $user_group[$member_id['user_group']]['edit_allc'] OR $user_group[$member_id['user_group']]['admin_comments'])) $have_perm = 2;
		if ($user_group[$member_id['user_group']]['edit_limit'] AND ((strtotime($row['date']) + ($user_group[$member_id['user_group']]['edit_limit'] * 60)) < TIME)) $have_perm = 0;
		if (isset($member_id['restricted']) and in_array($member_id['restricted'], array(2,3))) $have_perm = 0;

		if ($have_perm){

			include_once ENGINE_DIR.'/classes/parse.class.php';

			$parse = new ParseFilter( );
			$parse->safe_mode = true;
			$parse->allow_url = $user_group[$member_id['user_group']]['allow_url'];
			$parse->allow_image = $user_group[$member_id['user_group']]['allow_image'];

			if ($config['allow_comments_wysiwyg'] != "no" && $config['allow_comments_wysiwyg'] != "0"){

				$parse->wysiwyg = true;
				$use_html = true;
				$parse->ParseFilter( Array ('div','span','p','br','strong','em','ul','li','ol', 'b', 'u', 'i', 's'), Array (), 0, 1 );
		
				if ($user_group[$member_id['user_group']]['allow_url']) $parse->tagsArray[] = 'a';
				if ($user_group[$member_id['user_group']]['allow_image']) $parse->tagsArray[] = 'img';

			} else $use_html = false;

			$comm_txt = trim( $parse->BB_Parse( $parse->process( convert_unicode( $_POST['comm_txt'], $config['charset'] ) ), $use_html ) );
			$comm_txt = $this->auto_wrap_text($config['auto_wrap'], $comm_txt);

			if ($parse->not_allowed_tags) die( "error" );
			if ($parse->not_allowed_text ) die( "error" );
			if ($config['comments_maxlen'] && dle_strlen($comm_txt, $config['charset']) > $config['comments_maxlen']) die( "error" );
			if ($comm_txt == "") die( "error" );
			if (intval($config['comments_minlen']) AND dle_strlen(str_replace(" ", "", strip_tags($comm_txt)), $config['charset']) < $config['comments_minlen']) die( "error" );

			$comm_update = $this->safesql($comm_txt);

			if ($have_perm == 2)
				$this->query("UPDATE " . $this -> comm_table . " set text='$comm_update', approve='1' where id = '$id'");
			else
				$this->query("UPDATE " . $this -> comm_table . " set text='$comm_update' where id = '$id'");

			if ($have_perm == 2 && !$row['approve']){

				if ($this -> update_news_sql)
					$this->query("UPDATE " . $this -> update_news_sql . " ='{$row['post_id']}'");

				clear_gallery_cache();

			}

			$comm_txt = str_ireplace(array("[hide]", "[/hide]"), "", $comm_txt);

			return stripslashes($comm_txt);

		} else die( "error" );

	}

	function inform_new_comment($id, $name, $comments, $_IP, $mail){
	global $config, $langGal, $lang, $member_id, $user_group, $is_logged;

		if ($this->allow_subscribe)
			$watch_info = $this->query("SELECT s.id, s.user_id, s.gast_email, u.name, u.email, u.user_group FROM " . $this->comm_table . "_subscribe s LEFT JOIN " . USERPREFIX . "_users u ON s.user_id=u.user_id WHERE s.file_id={$id} AND s.flag=1");

		if ((!$this->allow_subscribe || !$this->num_rows($watch_info)) && (!$galConfig['mail_comments'] || ($member_id['email'] == $config['admin_mail'] && !defined('DEBUG_MODE')))) return 0;

		include_once TWSGAL_DIR.'/classes/mail.php';

		$INFORM = new Mailer();

		$INFORM->template = 'gallery_newcomment';

		$INFORM->subject = $langGal['subj_new_com'];
		$INFORM->Mailer_set();
		$INFORM->set('{%username%}', ($is_logged ? $member_id['name'] : $name));
		$INFORM->set('{%date%}', langdate("j F Y H:i", TIME));
		$INFORM->set('{%site%}', $config['http_home_url']);
		$INFORM->set('{%text%}', strip_tags (str_replace ("<br />", "\n", stripslashes(stripslashes(str_replace (array('\n', '\r'), "", $comments))))));
		$INFORM->compile(1);

		if ($galConfig['mail_comments'] && $member_id['email'] != $config['admin_mail']){

			$INFORM->set('{%ip%}', $_IP);
			$INFORM->set('{%username_to%}', $lang['admin']);
			$INFORM->set('{%unsubscribe%}', "--");
			$INFORM->set('{%subscribelist%}', "--");
			$INFORM->set('{%link%}', $config['http_home_url']."index.php?do=gallery&act=2&fid=".$id);
			$INFORM->do_send_message ($config['admin_mail']);

		}

		if ($this->allow_subscribe){

			$update_user_id = array();
			$found_subscribe = false;
			$i = 0;

			while($info_user = $this->get_row($watch_info)){

				if (!$info_user['email']) $info_user['email'] = stripslashes($info_user['gast_email']);

				if ($is_logged && $info_user['user_id'] == $member_id['user_id'] || !$is_logged && $info_user['email'] == $mail){
					$found_subscribe = true;
					if (!defined('DEBUG_MODE')) continue;
				}

				if ($info_user['user_id'])
					$update_user_id[] = $info_user['user_id'];
				else
					$info_user['user_id'] = 0;

				$INFORM->set('{%username_to%}', stripslashes($info_user['name']));

				if ($info_user['user_group'] == '1')
					$INFORM->set('{%ip%}', ' ( IP: '.$_IP.' ) ');
				else
					$INFORM->set('{%ip%}', '');

				$key = "&fid=".$id.($info_user['user_id'] ? "&user_id=".$info_user['user_id'] : "&email=".rawurlencode(base64_encode($info_user['email'])))."&hash=".md5(sha1( ($info_user['user_id'] ? "" : $info_user['email']).$info_user['user_id'].DBHOST.DBNAME.$config['key']));

				$INFORM->set('{%link%}', $config['http_home_url']."index.php?do=gallery&act=2&subscribe=update".$key);
				$INFORM->set('{%unsubscribe%}', $config['http_home_url']."index.php?do=gallery&act=23&action=unsubscribe".$key);
				$INFORM->set('{%subscribelist%}', $config['http_home_url']."index.php?do=gallery&act=28");

				$INFORM->do_send_message ($info_user['email']);
				$i = 1;

			}

			$this->free($watch_info);

			if ($i)
				$this->query("UPDATE " . $this->comm_table . "_subscribe SET flag=0, date='".date('Y-m-d', TIME)."' WHERE file_id={$id} AND flag=1".(($is_logged && !defined('DEBUG_MODE')) ? " AND user_id!=".intval($member_id['user_id']) : ""));

			if (count($update_user_id))
				$this->query("UPDATE " . USERPREFIX . "_users SET gallery_cs_flag=1 WHERE user_id IN (".implode(",", $update_user_id).") AND gallery_cs_flag=0");

		}

		$INFORM->clear();

		return $found_subscribe;
	}

	function insert_subscribe($id, $mail, $hash, $clean){
	global $config, $langGal, $member_id, $is_logged, $lang, $galConfig;

		$allow = explode(',',$galConfig['comsubslevel']);
		if (!$galConfig['comsubslevel'] || (!in_array($member_id['user_group'], $allow) && $allow[0] != '-1'))return -1;

		if (!$is_logged && $clean){

			$id = intval($id);

			if (!AJAX_ACTION)
				$mail = base64_decode(@rawurldecode($mail));
			else
				$mail = convert_unicode($mail, $config['charset']);

			$not_allow_symbol = array ("\x22", "\x60", "\t", '\n', '\r', "\n", "\r", '\\', ",", "/", "¬", "#", ";", ":", "~", "[", "]", "{", "}", ")", "(", "*", "^", "%", "$", "<", ">", "?", "!", '"', "'" );
			$mail = $this->safesql(trim( str_replace( $not_allow_symbol, '', strip_tags( stripslashes( $mail ) ) ) ) );

			if (strlen($mail) > 50) return array($lang['news_err_2']);
			if ($mail != "" AND !preg_match( "/^[\.A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $mail ) ) return array($lang['news_err_10']);

		}

		if (!$id || !$is_logged && $mail == "") return -1;

		$this->url = md5(sha1($mail.intval($member_id['user_id']).DBHOST.DBNAME.$config['key']));

		$watch_info = $this->super_query("SELECT id, flag FROM " . $this->comm_table . "_subscribe WHERE file_id={$id} AND ".($is_logged ? "user_id=".$member_id['user_id'] : "gast_email='".$mail."'"));

		if ($watch_info['id']){

			if (!$watch_info['flag'])
				$this->query("UPDATE " . $this->comm_table . "_subscribe SET flag=1, date='".date('Y-m-d', TIME)."' WHERE id={$watch_info['id']}");

			return 1;
		}

		if ($is_logged){

			$this->query("INSERT INTO " . $this->comm_table . "_subscribe (file_id, user_id, gast_email, flag, date) VALUES ({$id}, {$member_id['user_id']}, '', 1, '".date('Y-m-d', TIME)."')");
			return 3;

		}

		if ($hash && $hash == $this->url || ($watch_info = $this->super_query("SELECT COUNT(id) as count FROM " . $this->comm_table . "_subscribe WHERE gast_email='".$mail."'")) && $watch_info['count']){

			$this->query("INSERT INTO " . $this->comm_table . "_subscribe (file_id, user_id, gast_email, flag, date) VALUES ({$id}, 0, '".$mail."', 1, '".date('Y-m-d', TIME)."')");
			return 3;

		} elseif ($hash) return -1;

		$watch_info = $this->super_query("SELECT COUNT(user_id) as count FROM " . USERPREFIX . "_users WHERE email='".$mail."'");
		if (!defined('DEBUG_MODE') && $watch_info['count']) return array($lang['news_err_7']);

		include_once TWSGAL_DIR.'/classes/mail.php';

		$INFORM = new Mailer();
		$INFORM->template = 'gallery_subscribe';
		$INFORM->subject = $langGal['subj_subscribe'];
		$INFORM->Mailer_set();
		$INFORM->set('{%site%}', $config['http_home_url']);
		$INFORM->set('{%subscribe%}', $config['http_home_url']."index.php?do=gallery&act=23&action=subscribe&fid=".$id."&email=".rawurlencode(base64_encode($mail))."&hash=".$this->url);

		if ($INFORM->do_send_message ($mail)) return array($INFORM->mail->smtp_msg);

		$INFORM->clear();

		return 2;
	}

	function remove_subscribe($id, $user_id, $mail, $hash){
	global $config;

		if (!AJAX_ACTION)
			$mail = base64_decode(@rawurldecode($mail));
		else
			$mail = convert_unicode($mail, $config['charset']);

		$id = intval($id);
		$user_id = intval($user_id);
		$mail = $this->safesql(trim($mail));

		if ((!$mail && !$user_id) || md5(sha1($mail.$user_id.DBHOST.DBNAME.$config['key'])) != $hash) return -1;

		$this->query("DELETE FROM " . $this->comm_table . "_subscribe WHERE ".($id ? "file_id={$id} AND " : "").($user_id ? "user_id=".$user_id : "gast_email='".$mail."'"));
		
		return 4;
	}

	function ShowAddform($bbcode_file='/modules/bbcode.php', $wysiwyg_file='/editor/comments.php'){
	global $tpl, $config, $is_logged, $member_id, $user_group, $lang, $allow_comments_ajax, $js_array, $galConfig;

		if ($this -> comments_num < 1){

			if(strpos($tpl->result['content'], "<!--dlecomments-->" ) !== false)	
				$tpl->result['content'] = str_ireplace ( "<!--dlecomments-->", "\n<div id=\"dle-ajax-comments\"></div>\n", $tpl->result['content'] );
			else
				$tpl->result['content'] .= "\n<div id=\"dle-ajax-comments\"></div>\n";

		}

		$tpl->load_template($this -> template . '.tpl');

		$allow_subscribe = true;

		$allow = explode(',',$galConfig['comsubslevel']);
		if (!$galConfig['comsubslevel'] || (!in_array($member_id['user_group'], $allow) && $allow[0] != '-1')) $allow_subscribe = false;

		if($config['allow_comments_wysiwyg'] != "no" && $config['allow_comments_wysiwyg'] != "0") {
			include_once ENGINE_DIR.$wysiwyg_file;
			$allow_comments_ajax = true;
			$tpl->set( '{editor}', $wysiwyg );
		} else {
			include_once ENGINE_DIR.$bbcode_file;
			$tpl->set( '{editor}', $bb_code );
		}

		$enable_captcha = !($is_logged AND isset($user_group[$member_id['user_group']]['disable_comments_captcha']) AND $member_id['comm_num'] >= $user_group[$member_id['user_group']]['disable_comments_captcha']);

		if ($enable_captcha && isset($user_group[$member_id['user_group']]['comments_question']) && $user_group[$member_id['user_group']]['comments_question'] ) {

			$tpl->set( '[question]', "" );
			$tpl->set( '[/question]', "" );

			$question = $this->super_query("SELECT id, question FROM " . PREFIX . "_question ORDER BY RAND() LIMIT 1");
			$tpl->set( '{question}', "<span id=\"dle-question\">".htmlspecialchars( stripslashes( $question['question'] ), ENT_QUOTES, $config['charset'] )."</span>" );

			$_SESSION['question'] = $question['id'];

		} else {

			$tpl->set_block( "'\\[question\\](.*?)\\[/question\\]'si", "" );
			$tpl->set( '{question}', "" );

		}

		if ($enable_captcha && $user_group[$member_id['user_group']]['captcha'] ) {

			if ( $config['allow_recaptcha'] ) {

				$tpl->set( '[recaptcha]', "" );
				$tpl->set( '[/recaptcha]', "" );
				$tpl->set( '{recaptcha}', '<div id="dle_recaptcha"></div>' );
				$tpl->set_block( "'\\[sec_code\\](.*?)\\[/sec_code\\]'si", "" );
				$tpl->set( '{sec_code}', "" );

			} else {

				$tpl->set( '[sec_code]', "" );
				$tpl->set( '[/sec_code]', "" );
				$path = parse_url( $config['http_home_url'] );
				$tpl->set( '{sec_code}', "<span id=\"dle-captcha\"><img src=\"" . $path['path'] . "engine/modules/antibot.php\" alt=\"${lang['sec_image']}\" /><br /><a onclick=\"reload(); return false;\" href=\"#\">{$lang['reload_code']}</a></span>" );
				$tpl->set_block( "'\\[recaptcha\\](.*?)\\[/recaptcha\\]'si", "" );
				$tpl->set( '{recaptcha}', "" );

			}

		} else {
			$tpl->set( '{sec_code}', "" );
			$tpl->set( '{recaptcha}', "" );
			$tpl->set_block( "'\\[recaptcha\\](.*?)\\[/recaptcha\\]'si", "" );
			$tpl->set_block( "'\\[sec_code\\](.*?)\\[/sec_code\\]'si", "" );
		}

		$tpl->set_block( "'\\[edit-comment\\](.*?)\\[/edit-comment\\]'si", "" );
		$tpl->set( '[add-comment]', "" );
		$tpl->set( '[/add-comment]', "" );

		$tpl->set('{text}', '');
		$tpl->set('{title}',$lang['news_addcom']);

		if ($allow_subscribe){
			$tpl->set('[subscribe]','<a href="javascript:void(0)" onclick="subscribe_comments('. $this -> id .','.intval($member_id['user_id']).')">');
			$tpl->set('[/subscribe]','</a>');
		} else $tpl->set_block("'\\[subscribe\\](.*?)\\[/subscribe\\]'si","");

		if (!$is_logged) {
			$tpl->set('[not-logged]','');
			$tpl->set('[/not-logged]','');
		} else $tpl->set_block("'\\[not-logged\\](.*?)\\[/not-logged\\]'si","");

		if ($is_logged) $hidden = "<input type=\"hidden\" name=\"name\" id=\"name\" value=\"1\" /><input type=\"hidden\" name=\"mail\" id=\"mail\" value=\"\" />"; else $hidden = "";

		$tpl->copy_template = "<form  method=\"post\" name=\"dle-comments-form\" id=\"dle-comments-form\" action=\"{$_SERVER['REQUEST_URI']}\">".$tpl->copy_template."
<input type=\"hidden\" name=\"doaction\" value=\"addcomment\" />{$hidden}
<input type=\"hidden\" name=\"id\" id=\"id\" value=\"". $this -> id ."\" /></form>";

		if (!isset($path['path'])) $path['path'] = "/";
		$scr = $this -> add_sript;

		$tpl->copy_template .= <<<HTML
<script language="javascript" type="text/javascript">
<!--
$(function(){

	$('#dle-comments-form').submit(function() {
	  {$scr}
	  return false;
	});

});

function reload () {

	var rndval = new Date().getTime(); 

	document.getElementById('dle-captcha').innerHTML = '<img src="{$path['path']}engine/modules/antibot.php?rndval=' + rndval + '" width="120" height="50" alt="" /><br /><a onclick="reload(); return false;" href="#">{$lang['reload_code']}</a>';

};
//-->
</script>
HTML;

		if ( $config['allow_recaptcha'] ) {

			$tpl->copy_template .= <<<HTML
<script type="text/javascript" src="http://www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>
<script language="javascript" type="text/javascript">
<!--
$(function(){
	Recaptcha.create("{$config['recaptcha_public_key']}",
     "dle_recaptcha",
     {
       theme: "{$config['recaptcha_theme']}",
       lang:  "{$lang['wysiwyg_language']}"
     }
   );
});
//-->
</script>
HTML;

		}

		if (strpos($tpl->result['content'], "<!--dleaddcomments-->") === false)
			$tpl->compile('content');
		else {
			$tpl->compile('addcomments');
			$tpl->result['content'] = str_ireplace ( "<!--dleaddcomments-->", $tpl->result['addcomments'], $tpl->result['content']);
			unset ($tpl->result['addcomments']);
		}

		$tpl->clear();

	}

	function comm_combine(){
	global $is_logged, $member_id, $user_group, $config, $lang;

		if (!$is_logged || ($member_id['user_group'] != 1 && !$user_group[$member_id['user_group']]['del_allc']) || $user_group[$member_id['user_group']]['edit_limit']) return $lang['comm_err_4'];
		if (isset($member_id['restricted']) and in_array($member_id['restricted'], array(2,3))) return $lang['comm_err_4'];

		$comments_array = array();

		if (isset($_POST['com_id']) && is_array($_POST['com_id'])){
			foreach ($_POST['com_id'] as $id){
				$id = intval($id);
				if ($id > 0) $comments_array[] = $id;
			}
		}

		if (count($comments_array) < 2) return $lang['comm_err_5'];

		$ids_array = array();
		$comments = array();
		$flag = false;

		$sql = $this->query("SELECT id, post_id, approve, user_id, text FROM " . $this -> comm_table . " where id IN (" . implode(",", $comments_array) . ") ORDER BY date " . $this -> sort_order);

		while ($row = $this->get_row($sql)){
			$ids_array[] = $row['id'];
			$comments[] = stripslashes($row['text']);
			if ($flag){

				// обновление количества комментариев у юзера 
				if ($row['user_id'] && $this -> update_user_sql)
					$this->query("UPDATE " . USERPREFIX . "_users set " . $this -> update_user_sql  . " where user_id ='{$row['user_id']}'");

				// обновление количества комментариев в новостях
				if ($row['approve'] && $this -> update_news_sql)
					$this->query("UPDATE " . $this -> update_news_sql . " = '{$row['post_id']}'");

			}
			$flag = true;
		}

		$this->free($sql);

		$comment = $this->safesql(implode("<br /><br />", $comments));

		if ($ids_array[0] > 0)
			$this->query( "UPDATE " . $this -> comm_table . " SET text='{$comment}' WHERE id='{$ids_array[0]}'" );

		unset($ids_array[0]);

		if (count($ids_array))
			$this->query("DELETE FROM " . $this -> comm_table . " where id IN (".implode(",", $ids_array).")");

		clear_gallery_cache();

		if (!AJAX_ACTION){

			$this->close ();

			if (isset($_SESSION['gallery_referrer']))
				@header("Location: {$_SESSION['gallery_referrer']}");
			else
				@header("Location: ".$config['http_home_url']."index.php?do=gallery");

			die();
		}

		return true;

	}


	function delete_comment(){
	global $is_logged, $member_id, $user_group, $config, $lang;

		if (!$is_logged) return $lang['comm_err_4'];
		if (isset($member_id['restricted']) and in_array($member_id['restricted'], array(2,3))) return $lang['comm_err_4'];;

		$comments_array = array();

		if (isset($_POST['com_id']) && is_array($_POST['com_id'])){
			foreach ($_POST['com_id'] as $id){
				$id = intval($id);
				if ($id > 0) $comments_array[] = $id;
			}
		} else {
			$id = isset($_REQUEST['com_id']) ? intval($_REQUEST['com_id']) : 0;
			if ($id > 0) $comments_array[] = $id;
		}

		if (!count($comments_array)) return $lang['comm_err_5'];

		$comments = implode(",", $comments_array);

		$ids_array = array();
		$post_ids = array();
		$user_ids = array();

		$sql = $this->query("SELECT id, user_id, approve, post_id, date FROM " . $this -> comm_table . " where id IN (" . $comments . ")");

		while ($row = $this->get_row($sql)){

			$allow = false;

			if ($user_group[$member_id['user_group']]['edit_limit'] AND ((strtotime($row['date']) + ($user_group[$member_id['user_group']]['edit_limit'] * 60)) < TIME)) $allow = false;
			elseif ((($member_id['user_id'] == $row['user_id'] AND $row['user_id']  AND $user_group[$member_id['user_group']]['allow_delc']) OR $member_id['user_group'] == '1' OR $user_group[$member_id['user_group']]['del_allc'] OR $user_group[$member_id['user_group']]['admin_comments'])) $allow = true;
			elseif ($this -> user_mod_id && $user_group[$member_id['user_group']]['allow_delc']){
				if (!isset($post_ids[$row['post_id']])){
					$post = $this->super_query("SELECT " . $this -> user_mod_id . " = '{$row['post_id']}'");
					$post_ids[$row['post_id']] = $post['sp_id'];
				}
				if ($post_ids[$row['post_id']] AND $member_id['user_id'] == $post_ids[$row['post_id']]) $allow = true;
			}

			if ($allow){

				$ids_array[] = $row['id'];

				// обновление количества комментариев у юзера 
				if ($row['user_id'] && $this -> update_user_sql)
					$this->query("UPDATE " . USERPREFIX . "_users set " . $this -> update_user_sql  . " where user_id ='{$row['user_id']}'");
		
				// обновление количества комментариев в новостях
				if ($row['approve'] && $this -> update_news_sql)
					$this->query("UPDATE " . $this -> update_news_sql . " = '{$row['post_id']}'");

			}

		}

		$this->free($sql);

		if (count($ids_array))
			$this->query("DELETE FROM " . $this -> comm_table . " where id IN (".implode(",", $ids_array).")");

		clear_gallery_cache();

		if (!AJAX_ACTION){
			$this->close ();
			if (isset($_SESSION['gallery_referrer']))
				@header("Location: {$_SESSION['gallery_referrer']}");
			else
				@header("Location: ".$config['http_home_url']."index.php?do=gallery");
			die();
		}

		return true;

	}

	function auto_wrap_text($simbols, $text){ //функции две - ещё одна в функциях администрирования
	global $config;

		$simbols = intval($simbols);

		if (!$simbols) return $text;

		if ($config['charset'] == "utf-8") $utf_pref = "u"; else $utf_pref = "";

		$text = preg_split('((>)|(<))', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
		$n = count($text);
		for ($i = 0; $i < $n; $i++) {
			if ($text[$i] == "<"){
				$i++; continue;
			}
			$text[$i] = preg_replace(
			"#([^\s\n\r]{".$simbols."})#{$utf_pref}i", 
				"\\1<br />", $text[$i]);
		}

		$text = join("", $text);

	  return $text;
	}

}

?>