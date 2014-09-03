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
 Файл: configuration.php
-----------------------------------------------------
 Назначение: Настройка конфигурации
=====================================================
*/

if(!defined('DATALIFEENGINE') OR !defined( 'LOGGED_IN' ))
{
  die("Hacking attempt!");
}

if($member_id['user_group'] != 1){ msg("error", $lang['addnews_denied'], $lang['db_denied'], "?mod=twsgallery&act=0"); }

function return_config_options(){
global $langGal, $_extensions, $players;

	$_extensions = array(
	//тип/единица измерения/плеер/максимальный размер по умолчанию/плеер по умолчанию/управление миниатюрой
	"jpg" => array('Image','Kb', '1', 500, 1, 1),//
	"jpeg" => array('Image','Kb', '1', 500, 1, 1),//
	"jpe" => array('Image','Kb', '1', 500, 1, 1),//
	"png" => array('Image','Kb', '1', 500, 1, 1),//
	"gif" => array('Image','Kb', '1', 250, 1, 1),//
	"psd" => array('Image','Mb', '5', 10, 5, 0),//
	"mp3" => array('Music','Mb', '2,8', 5, 2, true),//
	"cue" => array('Music','Mb', '5', 2, 5, 0),//
	"m3u" => array('Music','Kb', '5', 1, 5, 0),//
	"mp4" => array('Video','Mb', '2,4,5', 10, 4, true),
	"swf" => array('Flash','Mb', '6,2,7', 10, 6, true),//
	"m4v" => array('Video','Mb', '4', 10, 4, true),
	"m4a" => array('Video','Mb', '4', 10, 4, true),
	"mov" => array('Video','Mb', '4', 10, 4, true),
	"3gp" => array('Video','Mb', '4', 10, 4, true),
	"f4v" => array('Video','Mb', '4', 10, 4, true),
	"mkv" => array('Video Divx','Mb', '3', 10, 3, true),
	"divx" => array('Video Divx','Mb', '3', 10, 3, true),
	"avi" => array('Video Divx','Mb', '3', 10, 3, true),//
	"wmv" => array('Video','Mb', '7', 10, 7, true),//
	"mpg" => array('Video','Mb', '7', 10, 7, true),//
	"youtube.com" => array('Video','--', '4,9', 0, 9, true),//
	"rutube.ru" => array('Video','--', '10', 0, 10, true),//
	"video.mail.ru" => array('Video','--', '13', 0, 13, true),//
	"vimeo.com" => array('Video','--', '12', 0, 12, true),//
	"smotri.com" => array('Video','--', '11', 0, 11, true),//
	"gametrailers.com" => array('Video','--', '14', 0, 14, true),//
	"flv" => array('Flash','Mb', '2,4,7,8', 10, 4, true),//
	"rar" => array('File','Mb', '5', 10, 5, 0),//
	"zip" => array('File','Mb', '5', 10, 5, 0),//
	"exe" => array('File','Mb', '5', 10, 5, 0),//
	"iso" => array('File','Mb', '5', 10, 5, 0),//
	"reg" => array('File','Kb', '5', 100, 5, 0),//
	"doc" => array('Document','Mb', '5', 2, 5, 0),//
	"pdf" => array('Document','Mb', '5', 10, 5, 0),//
	"txt" => array('Document','Mb', '5', 1, 5, 0),//
	);

	$players = array(
	"1"=>$langGal['player_1'],
	"2"=>$langGal['player_2'],
	"4"=>$langGal['player_4'],
	"8"=>$langGal['player_8'],
	"3"=>$langGal['player_3'],
	"6"=>$langGal['player_6'],
	"7"=>$langGal['player_7'],
	"5"=>$langGal['player_5'],
	"9"=>$langGal['player_9'],
	"10"=>$langGal['player_10'],
	"11"=>$langGal['player_11'],
	"12"=>$langGal['player_12'],
	"13"=>$langGal['player_13'],
	"14"=>$langGal['player_14'],
	);

}

return_config_options();

if ($act == "4"){

function showConfRow($title, $field, $description = ""){

echo "<tr>
		<td style=\"padding:4px;\" class=\"option\"><b>{$title}</b><br /><span class=small>{$description}</span></td>
		<td style=\"padding:4px;\" width=\"394\" align=middle>{$field}</td>
	  </tr>
	  <tr>
		<td background=\"engine/skins/images/mline.gif\" height=1 colspan=2></td>
	  </tr>";

}

function makeInputForm($name, $value, $size = 30){

	return "<input class=edit type=\"edit bk\" style=\"text-align: center;\" name=\"{$name}\" value=\"{$value}\" size={$size}>";

}

//$js_array[] = "engine/skins/tabs.js";
$js_array[] = "engine/skins/tabset.js";

echoheader("", $langGal['menu_main']);
galnavigation();
galHeader($langGal['menu_conf']);

//<a id="g" class="tab  {content:'cont_8'}" >{$langGal['c_menu_title8']}</a>
echo <<<HTML
<form action="{$PHP_SELF}?mod=twsgallery&act=5" method="post">
<input type="hidden" name="dle_allow_hash" value="{$dle_login_hash}">
<div id="dle_tabView1">
	<div class="tabset" id="tabset">
		<a id="a" class="tab  {content:'cont_1'}">{$langGal['c_menu_title1']}</a><a id="b" class="tab  {content:'cont_2'}">{$langGal['c_menu_title2']}</a><a id="c" class="tab  {content:'cont_3'}" >{$langGal['c_menu_title3']}</a><a id="c" class="tab  {content:'cont_4'}" >{$langGal['c_menu_title4']}</a><a id="d" class="tab  {content:'cont_5'}" >{$langGal['c_menu_title5']}</a><a id="e" class="tab  {content:'cont_6'}" >{$langGal['c_menu_title6']}</a><a id="f" class="tab  {content:'cont_7'}" >{$langGal['c_menu_title7']}</a>
	</div>
HTML;

echo <<<HTML
<div style="display:none;" id="cont_1">
<table width="100%">
HTML;
//общее

	showConfRow($langGal['c_off'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[off]", "{$galConfig['off']}"));
	showConfRow($langGal['c_allow_cache'], makeDropDownGallery(array("0"=>$langGal['no'], "1"=>1,"2"=>2,"3"=>3), "save_con[allow_cache]", "{$galConfig['allow_cache']}"), $langGal['c_allow_cache_d']);
	showConfRow($langGal['c_skin'], $twsg->SelectGallSkin($galConfig['skin_name']), $langGal['c_skin_d']);
	showConfRow($langGal['c_prefix'], makeInputForm("save_con[work_postfix]", $galConfig['work_postfix'], 40), $langGal['c_prefix_d']);
	showConfRow($langGal['opt_sys_descr'], makeInputForm("save_con[description]", $galConfig['description'], 40), $langGal['opt_sys_descrd']);
	showConfRow($langGal['opt_sys_key'], "<textarea class=edit style=\"width:250px;height:50px;\" name='save_con[keywords]'>{$galConfig['keywords']}</textarea>", $langGal['opt_sys_keyd']);
	showConfRow($langGal['c_allow_check_update'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[allow_check_update]", "{$galConfig['allow_check_update']}"), $langGal['c_allow_check_update_d']);

echo <<<HTML
</table>
</div>
<div id="cont_2" style="display:none;">
<table width="100%">
HTML;
//внешний вид

	showConfRow($langGal['c_perpage'], makeInputForm("save_con[main_cat_td]", $galConfig['main_cat_td'], 5), $langGal['c_cat_columns_d']);
	showConfRow($langGal['c_cat_columns'], makeInputForm("save_con[main_cat_tr]", $galConfig['main_cat_tr'], 5), $langGal['c_cat_columns_d']);
	showConfRow($langGal['c_columns_i'], makeInputForm("save_con[foto_td]", $galConfig['foto_td'], 5), $langGal['c_cat_columns_d']);
	showConfRow($langGal['c_rows'], makeInputForm("save_con[foto_tr]", $galConfig['foto_tr'], 5), $langGal['c_cat_columns_d']);
	showConfRow($langGal['c_g_sort'], makeDropDownGallery(array("position"=>$langGal['opt_sys_sort_o'],"last_date"=>$langGal['opt_sys_date'],"cat_title"=>$langGal['opt_sys_salph'],"images"=>$langGal['opt_sys_img_n']), "save_con[category_sort]", $galConfig['category_sort']));
	showConfRow($langGal['c_g_msort'], makeDropDownGallery(array("desc"=>$langGal['opt_sys_mminus'],"asc"=>$langGal['opt_sys_mplus']), "save_con[category_msort]", $galConfig['category_msort']));
	showConfRow($langGal['c_sort'], makeDropDownGallery(array("posi"=>$langGal['opt_sys_sort_o'],"date"=>$langGal['opt_sys_sdate'],"rating"=>$langGal['opt_sys_srate'],"file_views"=>$langGal['opt_sys_sview'],"comments"=>$langGal['opt_sys_img_com'],"picture_title"=>$langGal['opt_sys_salph']), "save_con[foto_sort]", $galConfig['foto_sort']));
	showConfRow($langGal['c_msort'], makeDropDownGallery(array("desc"=>$langGal['opt_sys_mminus'],"asc"=>$langGal['opt_sys_mplus']), "save_con[foto_msort]", $galConfig['foto_msort']));
	showConfRow($langGal['c_max_title_len'], makeInputForm("save_con[max_title_lenght]", $galConfig['max_title_lenght'], 5), $langGal['c_max_title_len_d']);
	showConfRow($langGal['c_autowraptext'], makeInputForm("save_con[autowrap_foto]", $galConfig['autowrap_foto'], 5), $langGal['c_autowraptext_d']);
	showConfRow($langGal['c_show_in_fullimage'], makeInputForm("save_con[show_in_fullimage]", $galConfig['show_in_fullimage'], 5), $langGal['c_show_in_fullimage_d']);
	showConfRow($langGal['c_thumbs_mousewheel'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[thumbs_mousewheel]", "{$galConfig['thumbs_mousewheel']}"), $langGal['c_thumbs_mousewheel_d']);
	showConfRow($langGal['c_buffer_in_fullimage'], makeInputForm("save_con[buffer_in_fullimage]", $galConfig['buffer_in_fullimage'], 5), $langGal['c_buffer_in_fullimage_d']);
	showConfRow($langGal['c_thumbs_offset'], makeInputForm("save_con[thumbs_offset]", $galConfig['thumbs_offset'], 5), $langGal['c_thumbs_offset_d']);
	showConfRow($langGal['c_thumbs_template_d'], "<textarea class=edit style=\"width:250px;height:50px;\" name='save_con[thumbs_template]'>".str_replace(array ("<", ">"), array ("&lt;", "&gt;"),htmlspecialchars($galConfig['thumbs_template'], ENT_QUOTES, $config['charset']))."</textarea>", $langGal['c_thumbs_template']);
	showConfRow($langGal['c_thumbs_fx'], makeDropDownGallery(array("none"=>$langGal['no'],"scroll"=>"scroll","directscroll"=>"directscroll","fade"=>"fade","crossfade"=>"crossfade","cover"=>"cover","uncover"=>"uncover"), "save_con[thumbs_fx]", "{$galConfig['thumbs_fx']}"), $langGal['c_thumbs_fx_d']);
	showConfRow($langGal['c_empty_title'], makeInputForm("save_con[empty_title_template]", $galConfig['empty_title_template'], 40), $langGal['c_empty_title_d']);
	showConfRow($langGal['c_timestamp_active'], makeInputForm("save_con[timestamp_active]", $galConfig['timestamp_active'], 40), "<a onclick=\"javascript:Help('date')\" class=main href=\"javascript:void(0);\">$lang[opt_sys_and]</a>");
	showConfRow($langGal['c_file_title_control'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[file_title_control]", "{$galConfig['file_title_control']}"), $langGal['c_file_title_control_d']);

echo <<<HTML
</table>
</div>
<div id="cont_3" style="display:none;">
<table width="100%">
HTML;
//Параметры загрузки

	$allowed_extensions = "";
	$galConfig['allowed_extensions'] = explode(',',$galConfig['allowed_extensions']);

	if (!is_array($galConfig['extensions'])) $galConfig['extensions'] = array("{$langGal['extact_no']}"=>'');

	foreach($galConfig['extensions'] as $value=>$description){
		$allowed_extensions .= "<option value=\"{$value}\"";
		if(in_array($value, $galConfig['allowed_extensions'])){ $allowed_extensions .= " selected "; }
		$allowed_extensions .= ">{$value}</option>\n";
	}

	showConfRow($langGal['c_allowed_ext'], "<select name=\"extensions[]\" class=\"cat_select\" multiple=\"multiple\">{$allowed_extensions}</select>", $langGal['c_allowed_ext_d']);
	showConfRow($langGal['c_advance_default'], makeDropDownGallery(array("1"=>$langGal['c_ad_v2'],"0"=>$langGal['c_ad_v1']), "save_con[advance_default]", "{$galConfig['advance_default']}"), $langGal['c_advance_default_d']);
	showConfRow($langGal['c_advance_dis_user'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[disable_select_upload]", "{$galConfig['disable_select_upload']}"), $langGal['c_advance_dis_user_d']);
	showConfRow($langGal['c_resize'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[allow_foto_resize]", "{$galConfig['allow_foto_resize']}"), $langGal['c_resize_d']);
	showConfRow($langGal['c_global_width_max'], makeInputForm("save_con[global_max_foto_width]", $galConfig['global_max_foto_width'], 5), $langGal['c_global_max_d']);
	showConfRow($langGal['c_global_height_max'], makeInputForm("save_con[global_max_foto_height]", $galConfig['global_max_foto_height'], 5), $langGal['c_global_max_d']);
	showConfRow($langGal['c_res_title3'], makeDropDownGallery(array("4"=>$langGal['c_res_type5'],"3"=>$langGal['c_res_type4'],"1"=>$langGal['c_res_type3'],"2"=>$langGal['c_res_type2'],"0"=>$langGal['c_res_type1'],"5"=>$langGal['c_res_type6']), "save_con[full_res_type]", "{$galConfig['full_res_type']}"), $langGal['c_res_title3_d']);
	showConfRow($langGal['c_fotoop'], makeInputForm("save_con[comms_foto_size]", $galConfig['comms_foto_size'], 8), $langGal['c_fotoop_d']);
	showConfRow($langGal['c_res_title2'], makeDropDownGallery(array("4"=>$langGal['c_res_type5'],"3"=>$langGal['c_res_type4'],"1"=>$langGal['c_res_type3'],"2"=>$langGal['c_res_type2'],"0"=>$langGal['c_res_type1'],"5"=>$langGal['c_res_type6']), "save_con[comm_res_type]", "{$galConfig['comm_res_type']}"), $langGal['c_res_title3_d']);
	showConfRow($langGal['c_iubw'], makeInputForm("save_con[max_thumb_size]", $galConfig['max_thumb_size'], 8), $langGal['c_iubw_d']);
	showConfRow($langGal['c_res_title1'], makeDropDownGallery(array("4"=>$langGal['c_res_type5'],"3"=>$langGal['c_res_type4'],"1"=>$langGal['c_res_type3'],"2"=>$langGal['c_res_type2'],"0"=>$langGal['c_res_type1'],"5"=>$langGal['c_res_type6']), "save_con[thumb_res_type]", "{$galConfig['thumb_res_type']}"), $langGal['c_res_title3_d']);
	showConfRow($langGal['c_icon_res'], makeInputForm("save_con[max_icon_size]", $galConfig['max_icon_size'], 8), $langGal['c_iubw_d']);
	showConfRow($langGal['c_icon_type'], makeDropDownGallery(array("1"=>$langGal['c_icon_type_1'],"0"=>$langGal['c_icon_type_0']), "save_con[icon_type]", "{$galConfig['icon_type']}"), $langGal['c_icon_type_d']);
	showConfRow($langGal['c_rewrite'], makeDropDownGallery(array("2"=>$langGal['upl_s1_c6_1'],"1"=>$langGal['upl_s1_c6_2'],"0"=>$langGal['upl_s1_c6_3']), "save_con[rewrite_mode]", "{$galConfig['rewrite_mode']}"), $langGal['c_rewrite_d']);
	showConfRow($langGal['c_check_double'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[allow_check_double]", "{$galConfig['allow_check_double']}"), $langGal['c_check_double_d']);
	showConfRow($langGal['c_allow_wat'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[allow_watermark]", "{$galConfig['allow_watermark']}"));
	showConfRow($langGal['c_no_main_watermark'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[no_main_watermark]", "{$galConfig['no_main_watermark']}"), $langGal['c_no_main_watermark_d']);
	showConfRow($langGal['c_wid_wat'], makeInputForm("save_con[min_watermark]", $galConfig['min_watermark'], 5));
	showConfRow($langGal['c_wm_light'], makeInputForm("save_con[watermark_light]", $galConfig['watermark_light'], 40), $langGal['c_wm_dark_d']);
	showConfRow($langGal['c_wm_dark'], makeInputForm("save_con[watermark_dark]", $galConfig['watermark_dark'], 40), $langGal['c_wm_dark_d']);
	showConfRow($langGal['c_quality'], makeInputForm("save_con[resize_quality]", $galConfig['resize_quality'], 5));
	showConfRow($langGal['c_max_once_upload'], makeInputForm("save_con[max_once_upload]", $galConfig['max_once_upload'], 5), $langGal['c_max_once_upload_d']);
	showConfRow($langGal['c_random_filename'], makeInputForm("save_con[random_filename]", $galConfig['random_filename'], 5), $langGal['c_random_filename_d']);
	showConfRow($langGal['c_convert_png_thumb'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[convert_png_thumb]", "{$galConfig['convert_png_thumb']}"), $langGal['c_convert_png_thumb_d']);

echo <<<HTML
</table>
</div>
<div id="cont_4" style="display:none;">
<table width="100%">
HTML;
//Права доступа

	$uviewlevel = get_gal_groups(explode(',', $galConfig['viewlevel']));
	$ucomlevel = get_gal_groups(explode(',', $galConfig['comlevel']));
	$uratelevel = get_gal_groups(explode(',', $galConfig['ratelevel']));
	$uuploadlevel = get_gal_groups(explode(',', $galConfig['uploadlevel']));
	$umodlevel = get_gal_groups(explode(',', $galConfig['modlevel']), 0, 1, array(1,2));
	$uaddlevel = get_gal_groups(explode(',', $galConfig['addlevel']), 0, 1, array(5));
	$ueditlevel = get_gal_groups(explode(',', $galConfig['editlevel']), 0, 0, array(4,5));
	$uremotelevel = get_gal_groups(explode(',', $galConfig['remotelevel']));
	$ucomsubslevel = get_gal_groups(explode(',', $galConfig['comsubslevel']));
	$uadminaccess = get_gal_groups(explode(',', $galConfig['adminaccess']), 0, 0, array(4,5));

	showConfRow($langGal['uviewlevel'], "<select name=\"viewlevel[]\" class=\"cat_select\" multiple=\"multiple\">{$uviewlevel}</select>");
	showConfRow($langGal['ucomlevel'], "<select name=\"comlevel[]\" class=\"cat_select\" multiple=\"multiple\">{$ucomlevel}</select>");
	showConfRow($langGal['ucomsubslevel'], "<select name=\"comsubslevel[]\" class=\"cat_select\" multiple=\"multiple\">{$ucomsubslevel}</select>", $langGal['ucomsubslevel_d']);
	showConfRow($langGal['uratelevel'], "<select name=\"ratelevel[]\" class=\"cat_select\" multiple=\"multiple\">{$uratelevel}</select>");
	showConfRow($langGal['uuploadlevel'], "<select name=\"uploadlevel[]\" class=\"cat_select\" multiple=\"multiple\">{$uuploadlevel}</select>");
	showConfRow($langGal['umodlevel'], "<select name=\"modlevel[]\" class=\"cat_select\" multiple=\"multiple\">{$umodlevel}</select>", $langGal['umodlevel_d']);
	showConfRow($langGal['uaddlevel'], "<select name=\"addlevel[]\" class=\"cat_select\" multiple=\"multiple\">{$uaddlevel}</select>", $langGal['uaddlevel_d']);
	showConfRow($langGal['uremotelevel'], "<select name=\"remotelevel[]\" class=\"cat_select\" multiple=\"multiple\">{$uremotelevel}</select>", $langGal['uremotelevel_d']);
	showConfRow($langGal['ueditlevel'], "<select name=\"editlevel[]\" class=\"cat_select\" multiple=\"multiple\">{$ueditlevel}</select>");

	showConfRow($langGal['c_allow_user_admin'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[allow_user_admin]", "{$galConfig['allow_user_admin']}"), $langGal['с_allow_user_admin_d']);
	showConfRow($langGal['c_max_user_categories'], makeInputForm("save_con[max_user_categories]", $galConfig['max_user_categories'], 5), $langGal['c_max_user_categoriesd']);
	showConfRow($langGal['c_allow_edit_picture'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[allow_edit_picture]", "{$galConfig['allow_edit_picture']}"), $langGal['с_allow_own_edit_d']);
	showConfRow($langGal['c_allow_delete_picture'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[allow_delete_picture]", "{$galConfig['allow_delete_picture']}"));
	showConfRow($langGal['c_allow_delete_com_onmod'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[allow_delete_omcomments]", "{$galConfig['allow_delete_omcomments']}"));
	showConfRow($langGal['c_admin_user_upload'], "<select name=\"adminaccess[]\" class=\"cat_select\" multiple=\"multiple\">{$uadminaccess}</select>", $langGal['c_admin_user_upload_d']);
	showConfRow($langGal['c_admin_user_access'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[admin_user_access]", "{$galConfig['admin_user_access']}"), $langGal['c_admin_user_access_d']);

echo <<<HTML
</table>
</div>
<div id="cont_5" style="display:none;">
<table width="100%">
HTML;
//Дополнительно

	showConfRow($langGal['c_allow_download'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[allow_download]", "{$galConfig['allow_download']}"), $langGal['c_allow_download_d']);
	showConfRow($langGal['c_dinamic_symbols'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[dinamic_symbols]", "{$galConfig['dinamic_symbols']}"), $langGal['c_dinamic_symbols_d']);
	showConfRow($langGal['c_allow_comm'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[allow_comments]", "{$galConfig['allow_comments']}"));
	showConfRow($langGal['c_allow_rating'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[allow_rating]", "{$galConfig['allow_rating']}"));
	showConfRow($langGal['c_allow_stat'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[show_statistic]", "{$galConfig['show_statistic']}"));
	showConfRow($langGal['opt_sys_cmod'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[comments_mod]", "{$galConfig['comments_mod']}"), $langGal['opt_sys_cmodd']);
	showConfRow($langGal['c_new_com_not'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[mail_comments]", "{$galConfig['mail_comments']}"), $langGal['c_new_com_not_d']);
	showConfRow($langGal['c_new_foto_not'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[mail_foto]", "{$galConfig['mail_foto']}"), $langGal['c_new_foto_not_d']);
	showConfRow($langGal['c_opt_sys_mcommd'], makeInputForm("save_con[max_comments_days]", $galConfig['max_comments_days'], 5), $langGal['c_opt_sys_mcommdd']);
	showConfRow($langGal['c_allow_ajax_comments'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[allow_ajax_comments]", "{$galConfig['allow_ajax_comments']}"), $langGal['c_allow_ajax_comments_d']);
	showConfRow($langGal['c_recycle'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[allow_recycle]", "{$galConfig['allow_recycle']}"), $langGal['c_recycle_d']);
	showConfRow($langGal['c_recycle_own'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[allow_recycle_own]", "{$galConfig['allow_recycle_own']}"), $langGal['c_recycle_d']);
	showConfRow($langGal['c_sys_max_mod'], makeInputForm("save_con[files_on_moderation]", $galConfig['files_on_moderation'], 5), $langGal['c_sys_max_modd']);
	showConfRow($langGal['c_sys_tags_len'], makeInputForm("save_con[tags_len]", $galConfig['tags_len'], 8), $langGal['c_sys_tags_lend']);
	showConfRow($langGal['c_sys_tags_num'], makeInputForm("save_con[tags_num]", $galConfig['tags_num'], 5), $langGal['c_sys_tags_numd']);
	showConfRow($langGal['c_allow_file_views'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[file_views]", "{$galConfig['file_views']}"), $langGal['c_allow_file_views_d']);
	showConfRow($langGal['c_allow_unique_views'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[whois_view_file]", "{$galConfig['whois_view_file']}"), $langGal['c_allow_unique_views_d']);
	showConfRow($langGal['c_allow_unique_day'], makeInputForm("save_con[whois_view_file_day]", $galConfig['whois_view_file_day'], 5), $langGal['c_allow_unique_day_d']);

echo <<<HTML
</table>
</div>
<div id="cont_6" style="display:none;">
<table width="100%">
	<tr>
		<td style="padding:5px;" width="20%"><b>{$langGal['extact_ext']}</b></td>
		<td style="padding:5px;" width="25%" align="center"><b>{$langGal['extact_all']}</b></td>
		<td style="padding:5px;" width="25%" align="center"><b>{$langGal['extact_max']}</b></td>
		<td style="padding:5px;" width="30%" align="center"><b>{$langGal['extact_pl_mini']}</b></td>
		<td style="padding:5px;" width="30%" align="center"><b>{$langGal['extact_pl']}</b></td>
	</tr>
	<tr>
		<td background="engine/skins/images/mline.gif" height=1 colspan=5></td>
	</tr>
HTML;
//Расширения

foreach ($_extensions as $extension => $options){

	$allow = isset($galConfig['extensions'][$extension]) ? 1 : 0;
	$extsize = ($allow) ? $galConfig['extensions'][$extension]['s'] : $options[3];
	$extpl = ($allow) ? $galConfig['extensions'][$extension]['p'] : $options[4];
	if ($allow && $options[1] == "Mb") $extsize = $extsize / 1024;
	$extplayer = array();
	$options_2 = explode(',',$options[2]);
	
	foreach ($players as $key => $name){
		if (in_array($key, $options_2)) $extplayer[$key] = $name;
	}

	$mdd = (count($extplayer) > 1) ? makeDropDownGallery($extplayer, "player[{$extension}]", "{$extpl}") : ($extplayer[$options[2]]);
	if ($options[5] === true) $short = radiomenu(array("1"=>$langGal['yes'],"0"=>$langGal['no']) ,"short[{$extension}]", intval($galConfig['extensions'][$extension]['m']));
	else $short = ($options[5] === 1) ? $langGal['yes'] : $langGal['no'];

echo "<tr>
		<td style=\"padding:4px;\" class=\"option\"><b>{$extension}</b><br /><span class=small>{$options[0]}</span></td>
		<td width=\"394\" align=middle>".radiomenu(array("1"=>$langGal['yes'],"0"=>$langGal['no']) ,"allow[{$extension}]", $allow)."</td>
		<td width=\"394\" align=middle><input class=edit type=text style=\"text-align: center;\" name=\"size[{$extension}]\" value=\"{$extsize}\" size={$size}> {$options[1]}</td>
		<td width=\"394\" align=middle>".$short."</td>
		<td width=\"394\" align=middle>".$mdd."</td>
	  </tr>
	  <tr>
		<td background=\"engine/skins/images/mline.gif\" height=1 colspan=5></td>
	  </tr>";

}

$maxupload = str_ireplace('m', '', @strtolower(@ini_get('upload_max_filesize')));
$maxupload = formatsize($maxupload*1024*1024);

echo <<<HTML
	<tr>
		<td style="padding:5px;" colspan="5">{$langGal['extact_desc']}<br />
		<b><font color="red">{$langGal['extact_desc1']} {$maxupload}</font></b><br />
		{$langGal['extact_desc2']}
		</td>
	</tr>
	<tr>
		<td background="engine/skins/images/mline.gif" height=1 colspan=5></td>
	</tr>
</table>
</div>

<div id="cont_7" style="display:none;">
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$langGal['c_vc_title1']}</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
HTML;
//Проигрыватели

	showConfRow($langGal['c_vc_jw_flv_mp_full_width'], makeInputForm("save_con[jw_flv_mp_full_width]", $galConfig['jw_flv_mp_full_width'], 5));
	showConfRow($langGal['c_vc_jw_flv_mp_full_height'], makeInputForm("save_con[jw_flv_mp_full_height]", $galConfig['jw_flv_mp_full_height'], 5));
	showConfRow($langGal['c_vc_jw_flv_mp_width'], makeInputForm("save_con[jw_flv_mp_width]", $galConfig['jw_flv_mp_width'], 5));
	showConfRow($langGal['c_vc_jw_flv_mp_height'], makeInputForm("save_con[jw_flv_mp_height]", $galConfig['jw_flv_mp_height'], 5));
	showConfRow($langGal['c_vc_jw_flv_mp_mp3_full_width'], makeInputForm("save_con[jw_flv_mp_mp3_full_width]", $galConfig['jw_flv_mp_mp3_full_width'], 5));
	showConfRow($langGal['c_vc_jw_flv_mp_mp3_full_height'], makeInputForm("save_con[jw_flv_mp_mp3_full_height]", $galConfig['jw_flv_mp_mp3_full_height'], 5));
	showConfRow($langGal['c_vc_jw_flv_mp_mp3_width'], makeInputForm("save_con[jw_flv_mp_mp3_width]", $galConfig['jw_flv_mp_mp3_width'], 5));
	showConfRow($langGal['c_vc_jw_flv_mp_mp3_height'], makeInputForm("save_con[jw_flv_mp_mp3_height]", $galConfig['jw_flv_mp_mp3_height'], 5));
	showConfRow($langGal['c_vc_divx_wp_full_width'], makeInputForm("save_con[divx_wp_full_width]", $galConfig['divx_wp_full_width'], 5));
	showConfRow($langGal['c_vc_divx_wp_full_height'], makeInputForm("save_con[divx_wp_full_height]", $galConfig['divx_wp_full_height'], 5));
	showConfRow($langGal['c_vc_divx_wp_width'], makeInputForm("save_con[divx_wp_width]", $galConfig['divx_wp_width'], 5));
	showConfRow($langGal['c_vc_divx_wp_height'], makeInputForm("save_con[divx_wp_height]", $galConfig['divx_wp_height'], 5));
	showConfRow($langGal['c_vc_cms_fp_full_width'], makeInputForm("save_con[cms_fp_full_width]", $galConfig['cms_fp_full_width'], 5));
	showConfRow($langGal['c_vc_cms_fp_full_height'], makeInputForm("save_con[cms_fp_full_height]", $galConfig['cms_fp_full_height'], 5));
	showConfRow($langGal['c_vc_cms_fp_width'], makeInputForm("save_con[cms_fp_width]", $galConfig['cms_fp_width'], 5));
	showConfRow($langGal['c_vc_cms_fp_height'], makeInputForm("save_con[cms_fp_height]", $galConfig['cms_fp_height'], 5));
	showConfRow($langGal['c_vc_cms_fp_mp3_full_width'], makeInputForm("save_con[cms_fp_mp3_full_width]", $galConfig['cms_fp_mp3_full_width'], 5));
	showConfRow($langGal['c_vc_cms_fp_mp3_full_height'], makeInputForm("save_con[cms_fp_mp3_full_height]", $galConfig['cms_fp_mp3_full_height'], 5));
	showConfRow($langGal['c_vc_cms_fp_mp3_width'], makeInputForm("save_con[cms_fp_mp3_width]", $galConfig['cms_fp_mp3_width'], 5));
	showConfRow($langGal['c_vc_cms_fp_mp3_height'], makeInputForm("save_con[cms_fp_mp3_height]", $galConfig['cms_fp_mp3_height'], 5));
	showConfRow($langGal['c_vc_cms_flp_full_width'], makeInputForm("save_con[cms_flp_full_width]", $galConfig['cms_flp_full_width'], 5));
	showConfRow($langGal['c_vc_cms_flp_full_height'], makeInputForm("save_con[cms_flp_full_height]", $galConfig['cms_flp_full_height'], 5));
	showConfRow($langGal['c_vc_cms_flp_width'], makeInputForm("save_con[cms_flp_width]", $galConfig['cms_flp_width'], 5));
	showConfRow($langGal['c_vc_cms_flp_height'], makeInputForm("save_con[cms_flp_height]", $galConfig['cms_flp_height'], 5));
	showConfRow($langGal['c_vc_cms_ftp_full_width'], makeInputForm("save_con[cms_ftp_full_width]", $galConfig['cms_ftp_full_width'], 5));
	showConfRow($langGal['c_vc_cms_ftp_full_height'], makeInputForm("save_con[cms_ftp_full_height]", $galConfig['cms_ftp_full_height'], 5));
	showConfRow($langGal['c_vc_cms_ftp_width'], makeInputForm("save_con[cms_ftp_width]", $galConfig['cms_ftp_width'], 5));
	showConfRow($langGal['c_vc_cms_ftp_height'], makeInputForm("save_con[cms_ftp_height]", $galConfig['cms_ftp_height'], 5));
	showConfRow($langGal['c_vc_yrt_full_width'], makeInputForm("save_con[yrt_full_width]", $galConfig['yrt_full_width'], 5));
	showConfRow($langGal['c_vc_yrt_full_height'], makeInputForm("save_con[yrt_full_height]", $galConfig['yrt_full_height'], 5));
	showConfRow($langGal['c_vc_yrt_width'], makeInputForm("save_con[yrt_width]", $galConfig['yrt_width'], 5));
	showConfRow($langGal['c_vc_yrt_height'], makeInputForm("save_con[yrt_height]", $galConfig['yrt_height'], 5));

echo <<<HTML
</table>
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$langGal['c_vc_title2']}</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
HTML;

	showConfRow($langGal['c_vc_play'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[play]", "{$galConfig['play']}"), $langGal['c_vc_play_d']);
	showConfRow($langGal['c_vc_flv_watermark'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[flv_watermark]", "{$galConfig['flv_watermark']}"), $langGal['c_vc_flv_watermark_d']);
	showConfRow($langGal['c_vconf_flvpos'], makeDropDownGallery(array("left" => $lang['opt_sys_left'], "center" => $lang['opt_sys_center'], "right" => $lang['opt_sys_right'] ), "save_con[flv_watermark_pos]", "{$galConfig['flv_watermark_pos']}" ), $langGal['c_vconf_flvposd']);
	showConfRow($langGal['c_vconf_flval'], makeInputForm("save_con[flv_watermark_al]", $galConfig['flv_watermark_al'], 10), $langGal['c_vconf_flvald']);
	showConfRow($langGal['c_vconf_youtube_q'], makeDropDownGallery( array ("small" => $langGal['c_vconf_youtube_s'], "medium" => $langGal['c_vconf_youtube_m'], "large" => $langGal['c_vconf_youtube_l'], "hd720" => "HD 720p" ), "save_con[youtube_q]", "{$galConfig['youtube_q']}" ), $langGal['c_vconf_youtube_qd'] );
	showConfRow($langGal['c_vconf_startframe'], makeDropDownGallery( array ("1" => $lang['opt_sys_yes'], "0" => $lang['opt_sys_no'] ), "save_con[startframe]", "{$galConfig['startframe']}" ), $langGal['c_vconf_startframed'] );
	showConfRow($langGal['c_vconf_preview'], makeDropDownGallery( array ("1" => $lang['opt_sys_yes'], "0" => $lang['opt_sys_no'] ), "save_con[preview]", "{$galConfig['preview']}" ), $langGal['c_vconf_previewd'] );
	showConfRow($langGal['c_vconf_autohide'], makeDropDownGallery( array ("1" => $lang['opt_sys_yes'], "0" => $lang['opt_sys_no'] ), "save_con[autohide]", "{$galConfig['autohide']}" ), $langGal['c_vconf_autohided'] );
	showConfRow($langGal['c_vconf_buffer'], makeInputForm("save_con[buffer]", $galConfig['buffer'], 10), $langGal['c_vconf_bufferd'] );
	showConfRow($langGal['opt_sys_fsv'], makeDropDownGallery(array("1"=>$langGal['opt_sys_fsv_1'],"2"=>$langGal['opt_sys_fsv_2'],"3"=>$langGal['opt_sys_fsv_3']), "save_con[fullsizeview]", "{$galConfig['fullsizeview']}"), $langGal['opt_sys_fsvd']);
	showConfRow($langGal['opt_sys_turel'], makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "save_con[yrt_tube_related]", "{$galConfig['yrt_tube_related']}"), $langGal['opt_sys_tureld']);
	showConfRow($langGal['c_vc_progressBarColor'], makeInputForm("save_con[progressBarColor]", $galConfig['progressBarColor'], 10), $langGal['c_vc_progressBarColor_d']);

echo <<<HTML
    <tr>
        <td style="padding-top:10px; padding-bottom:10px;padding-right:10px;" colspan="2"><span class="small">{$langGal['vconf_info']}</span></td>
    </tr>
</table>
</div>
HTML;

//echo <<<HTML
//<div style="display:none;" id="cont_8">
//<table width="100%">
//HTML;
//админка

	//showConfRow($langGal['c_admin_files'], makeInputForm("save_con[admin_num_files]", $galConfig['admin_num_files'], 5));

//echo <<<HTML
//</table>
//</div>
//HTML;

echo <<<HTML
</div>
	<table width="100%">
		<tr>
			<td colspan="2" align="center" style="padding:10px;"><input type="image" border=0 align="absmiddle" src="engine/skins/images/send.png"></td>
		</tr>
	</table>
</form>
<script type="text/javascript">
		$(function(){
			$("#tabset").buildMbTabset({
				sortable:false,
				position:"left"
			});
		});
</script>
HTML;

galFooter();
$twsg->galsupport50();
echofooter();

} elseif ($act == "5"){

	$exts = array();

	$save_con = $_POST['save_con'];
	$allowed = $_POST['allow'];
	$size = $_POST['size'];
	$player = $_POST['player'];
	$short = $_POST['short'];

	foreach ($_extensions as $extension => $options){

		if (intval($allowed[$extension])){
			if ($options[1] == "Mb") $size[$extension] = $size[$extension] * 1024;
			$play_count = explode(',', $options[2]);
			if (count($play_count) < 2 && $options[4] != 5) $player[$extension] = $options[4];
			$exts[$extension] = array(
			"s"=> intval($size[$extension]),
			"p"=> intval($player[$extension]),
			"m"=> intval($short[$extension]),
			);
		}

	}

	$save_con['extensions'] = $db->safesql(serialize($exts));

	$option_parametr = array("viewlevel", "comlevel", "uploadlevel", "modlevel", "editlevel", "ratelevel", "addlevel", "remotelevel", "comsubslevel", "adminaccess");

	foreach ($option_parametr as $parametr){

		$save_con[$parametr] = save_group_info($_POST[$parametr]);
	
	}

	$get_post_ext = (isset($_POST['extensions'])) ? $_POST['extensions'] : array();
	$save_con['allowed_extensions'] = $db->safesql(implode(',',$get_post_ext));
	$save_con['skin_name'] = $db->safesql($_POST['skin_name']);

	$twsg->save_clean_gal_config(array(), $save_con);

	@unlink(ENGINE_DIR.'/cache/system/gallery_config.php');
	clear_gallery_vars();
	clear_gallery_cache();

	galExit($PHP_SELF."?mod=twsgallery&act=4&dle_allow_hash={$dle_login_hash}");


}


?>