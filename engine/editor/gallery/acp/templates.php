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
=====================================================
 Файл: templates.php
-----------------------------------------------------
 Назначение: Шаблоны админа-панели
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

//$script_tag = "</script>"; // Исправляем глюк редактора, если данный тег поместить в блок HTML

include_once ENGINE_DIR . '/classes/templates.class.php';

$tpl = new dle_template ();
$tpl->allow_php_include = false;

switch ($template){

case 'file_edit' :
///********************************************************************************************************
//                                   Шаблон загрузки и редактирования файла
//*********************************************************************************************************

	$bb_panel_width = 435;

	$tpl->copy_template = $tpl->template = <<<HTML
	<tr valign="middle" align="center">
		<td>
		<table width="100%" border="0">
			<tr>
				<td align="left" style="padding:3px;">{$langGal['foto_list_tit']}:</td>
				<td align="left" style="padding:3px;"><input type="text" name="title[{id}]" style="width:351px;" class="edit bk" value="{title}" /></td>
			</tr>
			<tr>
				<td align="left" style="padding:3px;">{$langGal['gal_user']}:</td>
				<td align="left" style="padding:3px;">[admin-autor]<input type="text" name="autor[{id}]" style="width:351px;" class="finduser edit bk" value="[/admin-autor]{autor}[admin-autor]" />[/admin-autor]&nbsp;{user_edit}</td>
			</tr>
			<tr>
			  <td align="left" style="padding:3px;">{$langGal['foto_alt_name']}:</td>
				<td align="left" style="padding:3px;"><input type="text" name="alt_name[{id}]" style="width:351px;" class="edit bk" value="{alt-name}" /></td>
			</tr>
[tags]
			<tr>
			  <td align="left" style="padding:3px;">{$langGal['foto_list_vt']}:</td>
			  <td align="left" style="padding:3px;"><input type="text" name="tags[{id}]" style="width:351px;" value="{tags}" class="gallery_tags edit bk" /></td>
			</tr>
[/tags]
			<tr>
			  <td align="left" style="padding:3px;">{$langGal['foto_list_cat']}:</td>
			  <td align="left" style="padding:3px;">{category}</td>
		  </tr>
			<tr>
			  <td align="left" style="padding-top:3px;padding-left:3px;padding-right:3px;">&nbsp;</td>
				<td>{bbcode}</td>
			</tr>
			<tr>
			  <td align="left" style="padding-left:3px;padding-right:3px;">{$langGal['foto_description']}:</td>
			  <td align="left" style="padding-left:3px;padding-right:3px;"><textarea name="short_story[{id}]" id="short_story{id}" style="width: 430px; height: 110px;" onclick="setFieldName(this.id)">{short-story}</textarea></td>
			</tr>
			<tr>
				<td align="left" style="padding:3px;">{$langGal['foto_preview']}:<br /></td>
				<td align="left" style="padding:3px;" class="upload"><input type="file" name="preview[{id}]" class="f_input" /><br /><span class=small>{$langGal['edit_preview_allow']}</span></td>
			</tr>
			<tr>
			  <td align="left" style="padding:3px;">{$langGal['foto_alt_title']}:</td>
				<td align="left" style="padding:3px;"><input type="text" name="alt_title[{id}]" style="width:351px;" class="edit bk" value="{alt_title}" /></td>
			</tr>
			<tr>
			  <td align="left" style="padding:3px;">{$langGal['foto_symbol']}:</td>
			  <td align="left" style="padding:3px;"><input type="text" name="symbol[{id}]" style="width:90px;" class="edit bk" value="{symbol}" /></td>
			</tr>
[admin-reason]
			<tr>
			  <td align="left" style="padding:3px;">{$langGal['foto_list_edr']}:</td>
				<td align="left" style="padding:3px;"><input type="text" name="edit_reason[{id}]" style="width:351px;" class="edit bk" value="{edit_reason}" /></td>
			</tr>
[/admin-reason]
			<tr>
			  <td align="left" style="padding-top:3px;padding-left:3px;padding-right:3px;">&nbsp;</td>
			  <td align="left" style="padding-top:3px;padding-left:3px;padding-right:3px;">{admin-tags}</td>
			</tr>
		</table></td>
		<td height="280" style="padding-left:4px;">
		[fullimageurl]{thumb}[/fullimageurl]<br /><span class=small>ID: {id}, {$langGal['upl_s2_tit7']} {path}</span>
		</td>
	</tr>
	<tr>
		<td colspan="2"><div class="hr_line"></div></td>
	</tr>
HTML;

break;

case 'category_edit' :
///********************************************************************************************************
//                                   Шаблон редактирования категории
//*********************************************************************************************************

	$bb_panel_width = 435;

	$js_script = initTabs1(array($langGal['c_cat_title1'], $langGal['c_cat_title2'], $langGal['c_cat_title3'], $langGal['c_cat_title4'], $langGal['c_cat_title5']), 'dle_tabView1{id}');

	$tpl->copy_template = $tpl->template = <<<HTML
<div id="dle_tabView1{id}">
<div class="dle_aTab" style="display:none;">
<table width="100%">
	<tr>
		<td colspan="2"><div class="hr_line"></div></td>
	</tr>
    <tr>
        <td width="290" style="padding:4px;">{$lang['cat_name']}</td>
        <td><input class="edit" value="{cat_title}" type="text" name="cat_title[{id}]"></td>
    </tr>
	<tr>
		<td style="padding:4px;">{$langGal['gal_user']}:</td>
		<td>[admin-autor]<input type="text" name="autor[{id}]" class="finduser edit" value="[/admin-autor]{autor}[admin-autor]" />[/admin-autor]&nbsp;{user_edit}</td>
	</tr>
    <tr>
        <td style="padding:4px;">{$lang['cat_url']}</td>
        <td><input class="edit" value="{cat_alt_name}" style="width:345px;" type="text" name="cat_alt_name[{id}]"></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$lang['meta_title']}</td>
        <td><input type="text" name="metatitle[{id}]" style="width:345px;" value="{metatitle}" class="edit"> ({$lang['meta_descr_max']})</td>
    </tr>
	<tr>
		<td align="left" style="padding:4px;">&nbsp;</td>
		<td align="left">{bbcode}</td>
	</tr>
	<tr>
		<td style="padding:4px;">{$lang['meta_descr_cat']}</td>
		<td><textarea name="cat_short_desc[{id}]" id="cat_short_desc{id}" style="width:435px;height:100px;" onclick="setFieldName(this.id)">{cat_short_desc}</textarea></td>
	</tr>
	<tr>
		<td style="padding:4px;">{$langGal['meta_descr_cat']}<br /><span class=small>{$lang['meta_descr_max']}</span></td>
		<td><input type="text" name="meta_descr[{id}]" style="width:345px;" value="{meta_descr}" class="edit"></td>
	</tr>
	<tr>
		<td style="padding:4px;">{$lang['meta_keys']}<br /><span class=small>{$lang['meta_descr_max']}</span></td>
		<td><textarea name="keywords[{id}]" style="width:345px;height:50px;">{keywords}</textarea></td>
	</tr>
    <tr>
        <td style="padding:4px;">{$lang['cat_parent']}<br /><span class=small>{$langGal['cat_parent_d']}</span></td>
        <td>{category}</td>
    </tr>
	<tr>
		<td colspan="2"><div class="hr_line"></div></td>
	</tr>
    <tr>
        <td style="padding:4px;">{$langGal['cat_stat']}</td>
        <td>{categorystat}</td>
    </tr>
    <tr>
		<td colspan="2"><div class="hr_line"></div></td>
    </tr>
	<tr>
        <td style="padding:4px;">{$langGal['cat_disable_upload']}</td>
        <td>{disable_upload}</td>
    </tr>
	<tr>
		<td colspan="2"><div class="hr_line"></div></td>
	</tr>
	<tr>
        <td style="padding:4px;">{$lang['cat_addicon']}<br /><span class=small>{$langGal['edit_icon_allow']}</span></td>
        <td><input type="file" name="image[{id}]" style="width:304px; height:18px" class="edit" /></td>
    </tr>
[ifdelete]	<tr>
        <td style="padding:4px;">&nbsp;</td>
        <td valign="top"><div class="checkbox">{icon} <input type="checkbox" name="delete_icon[{id}]" id="delete_icon{id}" value="1" /> <label for="delete_icon{id}">{$langGal['delete_icon']}</label></div></td>
    </tr>[/ifdelete]
    <tr>
        <td style="padding:4px;">{$langGal['c_icon_res']}<br /><span class=small>{$langGal['not_ness2']}</span></td>
        <td><input class="edit" value="{icon_max_size}" type="text" size="8" id="icon_max_size{id}" name="icon_max_size[{id}]"></td>
    </tr>
	<tr>
		<td colspan="2"><div class="hr_line"></div></td>
	</tr>
    <tr>
        <td style="padding:4px;">{$langGal['profile_use']}</td>
        <td>{profiles}</td>
    </tr>
</table>
</div>
<div class="dle_aTab" style="display:none;">
<table width="100%">
	<tr>
		<td colspan="2"><div class="hr_line"></div></td>
	</tr>
    <tr>
        <td style="padding:4px;">{$lang['cat_skin']}<br /><span class=small>{$langGal['not_ness']}</span></td>
        <td>{skinlist}</td>
    </tr>
	<tr>
		<td colspan="2"><div class="hr_line"></div></td>
	</tr>
    <tr>
        <td style="padding:4px;">{$langGal['subcats_td']}<br /><span class=small>{$langGal['not_ness2']}</span></td>
        <td><input class="edit" value="{subcats_td}" type="text" size="5" id="subcats_td{id}" name="subcats_td[{id}]"></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['subcats_tr']}<br /><span class=small>{$langGal['not_ness2']}</span></td>
        <td><input class="edit" value="{subcats_tr}" type="text" size="5" id="subcats_tr{id}" name="subcats_tr[{id}]"></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['foto_td']}<br /><span class=small>{$langGal['not_ness2']}</span></td>
        <td><input class="edit" value="{foto_td}" type="text" size="5" id="foto_td{id}" name="foto_td[{id}]"></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['foto_tr']}<br /><span class=small>{$langGal['not_ness2']}</span></td>
        <td><input class="edit" value="{foto_tr}" type="text" size="5" id="foto_tr{id}" name="foto_tr[{id}]"></td>
    </tr>
	<tr>
		<td colspan="2"><div class="hr_line"></div></td>
	</tr>
    <tr>
        <td style="padding:4px;">{$langGal['skin_maincat']} (category.tpl)<br /><span class=small>{$langGal['not_nesstpl']}, category.tpl</span></td>
        <td><input class="edit" type="text" name="maincatskin[{id}]" id="maincatskin{id}" value="{maincatskin}">.tpl</td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['skin_subcat']} (short_category.tpl)<br /><span class=small>{$langGal['not_nesstpl']}, short_category.tpl</span></td>
        <td><input class="edit" type="text" name="subcatskin[{id}]" id="subcatskin{id}" value="{subcatskin}">.tpl</td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['skin_smallfoto']} (short_image.tpl)<br /><span class=small>{$langGal['not_nesstpl']}, short_image.tpl</span></td>
        <td><input class="edit" type="text" name="smallfotoskin[{id}]" id="smallfotoskin{id}" value="{smallfotoskin}">.tpl</td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['skin_bigfoto']} (full_image.tpl)<br /><span class=small>{$langGal['not_nesstpl']}, full_image.tpl</span></td>
        <td><input class="edit" type="text" name="bigfotoskin[{id}]" id="bigfotoskin{id}" value="{bigfotoskin}">.tpl</td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['skin_upload']} (upload.tpl)<br /><span class=small>{$langGal['not_nesstpl']}, upload.tpl</span></td>
        <td><input class="edit" type="text" name="uploadskin[{id}]" id="uploadskin{id}" value="{uploadskin}">.tpl</td>
    </tr>
	<tr>
		<td colspan="2"><div class="hr_line"></div></td>
	</tr>
	<tr>
        <td style="padding:4px;">{$langGal['c_sort']}<br /><span class=small>{$langGal['not_ness2']}</span></td>
        <td>{c_sort}</td>
    </tr>
	<tr>
        <td style="padding:4px;">{$langGal['c_msort']}<br /><span class=small>{$langGal['not_ness2']}</span></td>
        <td>{c_msort}</td>
    </tr>
</table>
</div>
<div class="dle_aTab" style="display:none;">
<table width="100%">
	<tr>
		<td colspan="2"><div class="hr_line"></div></td>
	</tr>
	<tr>
        <td style="padding:4px;">{$langGal['cat_uviewlevel']}</td>
        <td>{view_level}</td>
    </tr>
	<tr>
        <td style="padding:4px;">{$langGal['cat_ucomlevel']}</td>
        <td>{comment_level}</td>
    </tr>
	<tr>
        <td style="padding:4px;">{$langGal['cat_uuploadlevel']}</td>
        <td>{upload_level}</td>
    </tr>
	<tr>
        <td style="padding:4px;">{$langGal['cat_umodlevel']}<br /><span class=small>{$langGal['umodlevel_d']}</span></td>
        <td>{mod_level}</td>
    </tr>
	<tr>
        <td style="padding:4px;">{$langGal['cat_ueditlevel']}</td>
        <td>{edit_level}</td>
    </tr>
    <tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr>
	<tr>
		<td style="padding:4px;">{$langGal['profile_allow_user_admin']}</td>
		<td>{allow_user_admin}</td>
	</tr>
    <tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr>
	<tr>
        <td width="240" style="padding:4px;">{$langGal['moderators']}<br /><span class=small>{$langGal['moderators_d']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input class="findusermod edit" type="text" size="40" name="moderators[{id}]" id="moderators{id}" value="{moderators}"></td>
    </tr>
</table>
</div>
<div class="dle_aTab" style="display:none;">
<table width="100%">
    <tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['c_global_width_max']}<br /><span class=small>{$langGal['not_ness2']} {$langGal['c_global_max_s_d']}</span></td>
        <td><input class="edit" value="{width_max}" type="text" id="width_max{id}" size="5" name="width_max[{id}]"></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['c_global_height_max']}<br /><span class=small>{$langGal['not_ness2']} {$langGal['c_global_max_s_d']}</span></td>
        <td><input class="edit" value="{height_max}" type="text" id="height_max{id}" size="5" name="height_max[{id}]"></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['c_fotoop']}<br /><span class=small>{$langGal['not_ness2']}</span></td>
        <td><input class="edit" value="{com_thumb_max}" type="text" id="com_thumb_max{id}" size="8" name="com_thumb_max[{id}]"></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['c_iubw']}<br /><span class=small>{$langGal['not_ness2']}</span></td>
        <td><input class="edit" value="{thumb_max}" type="text" id="thumb_max{id}" size="8" name="thumb_max[{id}]"></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['c_allowed_ext']}<br /><span class=small>{$langGal['not_ness2']}<br />{$langGal['ctrl_help']}</span></td>
        <td><select name="extensions[{id}][]" id="extensions{id}" class="cat_select" multiple="multiple">{allowed_extensions}</select></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['c_size_factor']}<br /><span class=small>{$langGal['c_size_factor_d']}</span></td>
        <td><input class="edit" value="{size_factor}" type="text" id="size_factor{id}" size="8" name="size_factor[{id}]">%</td>
    </tr>
</table>
</div>
<div class="dle_aTab" style="display:none;">
<table width="100%">
    <tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr>
	<tr>
        <td>{admin-tags}</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr>
	<tr>
        <td style="padding:4px;">{$langGal['c_auto_prune']}<br /><span class=small>{$langGal['not_ness2']} {$langGal['c_auto_prune_d']}</span></td>
        <td width="30%"><input class="edit" value="{exprise_delete}" type="text" id="exprise_delete{id}" size="5" name="exprise_delete[{id}]"></td>
    </tr>
</table>
</div>
</div>
{$js_script}
HTML;

break;

}

?>