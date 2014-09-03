// JavaScript Document
// TWS Gallery - by Al-x
// Version TWS Gallery 5.2
// Powered by http://wonderfullife.ru/
// Support by http://wonderfullife.ru/, http://inker.wonderfullife.ru/
//-----------------------------------------------------
// Copyright (c) 2007,2012 TWS
var twsg_p_cache	= new Array();

function MenuGalCat( ID, adm ){
var menu=new Array();
if (adm && adm != '0'){
	menu[0]='<a href="' + dle_root + 'index.php?do=gallery&act=21&subact=1&dle_allow_hash=' + dle_login_hash + '&si=' + ID + '">' + gallery_lang_admin[1] + '</a>';
	menu[1]='<a href="' + dle_root + 'index.php?do=gallery&act=21&subact=2&dle_allow_hash=' + dle_login_hash + '&si=' + ID + '">' + gallery_lang_admin[7] + '</a>';
	menu[2]='<a href="' + dle_root + 'index.php?do=gallery&act=21&subact=3&dle_allow_hash=' + dle_login_hash + '&si=' + ID + '">' + gallery_lang_admin[5] + '</a>';
	menu[3]='<a href="' + dle_root + 'index.php?do=gallery&act=21&subact=4&dle_allow_hash=' + dle_login_hash + '&si=' + ID + '">' + gallery_lang_admin[6] + '</a>';
	menu[4]='<a href="' + dle_root + 'index.php?do=gallery&act=19&dle_allow_hash=' + dle_login_hash + '&si=' + ID + '">' + menu_short + '</a>';
	if (adm == '2')
		menu[5]='<a href="' + dle_root + dle_admin + '?mod=twsgallery&act=2&si=' + ID + '" target="_blank">' + menu_full + '</a>';
} else menu[0]='<a href="' + dle_root + 'index.php?do=gallery&act=24&dle_allow_hash=' + dle_login_hash + '&si=' + ID + '">' + menu_short + '</a>';
if (!adm || adm == '0' || adm == '2')
	menu[menu.length]='<a onclick="DLEconfirm(\'' + gallery_lang_user[2] + '\', \'' + dle_confirm + '\', function (){ document.location=\'' + dle_root + 'index.php?do=gallery&act=27&dle_allow_hash=' + dle_login_hash + '&si=' + ID + '\'; } ); return false;" href="#">' + gallery_lang_user[0] + '</a>';
return menu;
};

function ShortGalFoto( ID, adm, ed, dl, own ){

var menu=new Array();

if (ed == '1')
	menu[0]='<a href="' + dle_root + 'index.php?do=gallery&act=17&dle_allow_hash=' + dle_login_hash + '&si=' + ID + '">' + menu_short + '</a>';
if (adm != '0'){
	if (adm == '2')
		menu[menu.length]='<a href="' + dle_root + dle_admin + '?mod=twsgallery&act=24&si=' + ID + '" target="_blank">' + menu_full + '</a>';
	menu[menu.length]='<a onclick="tws_file_actions(1, ' + ID + ', ' + own + '); return false;" href="#">' + gallery_lang_admin[2] + '</a>';
	menu[menu.length]='<a onclick="tws_file_actions(2, ' + ID + ', ' + own + '); return false;" href="#">' + gallery_lang_admin[5] + '</a>';
	menu[menu.length]='<a onclick="tws_file_actions(3, ' + ID + ', ' + own + '); return false;" href="#">' + gallery_lang_admin[6] + '</a>';
	if (own == '0')
		menu[menu.length]='<a onclick="tws_file_actions(4, ' + ID + ', ' + own + '); return false;" href="#">' + gallery_lang_admin[4] + '</a>';
	menu[menu.length]='<a href="' + dle_root + 'index.php?do=gallery&act=7&dle_allow_hash=' + dle_login_hash + '&si=' + ID + '">' + gallery_lang_admin[3] + '</a>';
}
if (dl == '1')
	menu[menu.length]='<a onclick="tws_file_actions(5, ' + ID + ', ' + own + '); return false;" href="#">' + gallery_lang_user[0] + '</a>';

return menu;
};

function twsg_ajax_comm_edit( com_id ){

	for (var i = 0, length = c_cache.length; i < length; i++) {
	    if (i in c_cache) {
			if ( c_cache[ i ] != '' )
			{
				ajax_cancel_comm_edit( i );
			}
	    }
	}

	if ( ! twsg_p_cache[ com_id ] || twsg_p_cache[ com_id ] == '' )
	{
		twsg_p_cache[ com_id ] = $('#comm-id-'+com_id).html();
	}

	ShowLoading('');

	$.get(dle_root + "engine/gallery/ajax/comments.php", { com_id: com_id, dle_allow_hash: dle_login_hash, action: "edit", skin: dle_skin }, function(data){

		HideLoading('');

		RunAjaxJS('comm-id-'+com_id, data);

		setTimeout(function() {
           $("html:not(:animated)"+( ! $.browser.opera ? ",body:not(:animated)" : "")).animate({scrollTop: $("#comm-id-" + com_id).position().top - 70}, 700);
       }, 100);

	});
	return false;

};

function uncom_ajax_cancel_comm_edit( com_id )
{
	if (twsg_p_cache[ com_id ] != "" ){
		$("#comm-id-"+com_id).html(twsg_p_cache[ com_id ]);
		twsg_p_cache[ com_id ] = "";
	}
	return false;
};


function uncom_ajax_save_comm_edit( com_id )
{
	if (gallery_dle_id > 95 && (dle_wysiwyg == "yes" || dle_wysiwyg == "1"))
		submit_all_data();

	if (gallery_dle_id > 95 || dle_wysiwyg != "yes")
		var comm_txt = $('#dleeditcomments'+com_id).val();
	else
		var comm_txt = $('#dleeditcomments'+com_id).html();

	ShowLoading('');

	$.post(dle_root + "engine/gallery/ajax/comments.php", { com_id: com_id, comm_txt: comm_txt, dle_allow_hash: dle_login_hash, action: "do_edit", skin: dle_skin }, function(data){

		HideLoading('');
		twsg_p_cache[ com_id ] = '';
		$("#comm-id-"+com_id).html(data);

	});
	return false;

};

function DeleteComment( id ) {

    DLEconfirm( dle_del_agree, dle_confirm, function () {

		ShowLoading('');
	
		$.get(dle_root + "engine/gallery/ajax/comments.php", { com_id: id, dle_allow_hash: dle_login_hash, action: "delete", skin: dle_skin }, function(data){

			HideLoading('');//$("#comment-" + id).html(data);
			$("html"+( ! $.browser.opera ? ",body" : "")).animate({scrollTop: $("#comment-" + id).position().top - 70}, 700);
			setTimeout(function() { $("#comment-" + id).hide('blind',{},1400)}, 700);

		});

	});

};

function tws_file_actions( act, id, own ){

		var response = '', send_notice = 0, request_function = function(){
			ShowLoading('');
			$.post(dle_root + "engine/gallery/ajax/editfile.php", { si: id, send_notice_text: response, re: gallery_mode, send_notice : send_notice, skin: dle_skin, act: act, dle_allow_hash: dle_login_hash }, function(data){
				HideLoading('');
				data = cas(data, 1, 3);
				if (data && act == 5) document.location = data; 
			});
		};

		if (own != '0'){

			if (act == 5)
				DLEconfirm(gallery_lang_user[2], dle_confirm, request_function );
			 else
			 	request_function ();

			return;
		}

		var full_function = function(){

			var bt = {};

			bt[dle_act_lang[3]] = function(){
				$(this).dialog('close');
			};

			bt[gallery_lang_admin[11]] = function(){
				if ((act == 4 || act == 5) && $('#dle-promt-text').val().length < 1) {
					$('#dle-promt-text').addClass('ui-state-error');
				} else {
					response = $('#dle-promt-text').val();
					send_notice = 1;
					request_function ();
					$(this).dialog('close');
					$('#dlepopup').remove();
				}
			};

			$('#dlepopup').remove();

			var def_text = act == 4 ? '' : gallery_lang_admin[8];

			$('body').append("<div id='dlepopup' title='"+gallery_lang_admin[4]+"' style='display:none'><br />"+gallery_lang_admin[10]+"<br /><br /><textarea name='dle-promt-text' id='dle-promt-text' class='ui-widget-content ui-corner-all' style='width:97%;height:100px; padding: .4em;'>"+def_text+"</textarea></div>");

			$('#dlepopup').dialog({
				autoOpen: true,
				width: 500,
				dialogClass: "modalfixed",
				buttons: bt
			});

			$('.modalfixed.ui-dialog').css({position:"fixed"});
			$('#dlepopup').dialog( "option", "position", ['0','0'] );

		};

		if (act == 4){
			full_function ();
			return;
		}

		var b = {};

		b[dle_act_lang[1]] = function(){
			$(this).dialog("close");
		};

		if (own == '0')
			b[gallery_lang_admin[4]] = function(){
				$(this).dialog("close");
				full_function ();
			};

		b[dle_act_lang[0]] = request_function;

		$("#dlepopup").remove();

		$("body").append("<div id='dlepopup' title='"+dle_confirm+"' style='display:none'><br /><div id='dlepopupmessage'>"+gallery_lang_user[2]+(own == '0'  ? ' ' + gallery_lang_admin[9] : '')+"</div></div>");

		$('#dlepopup').dialog({
			autoOpen: true,
			width: 500,
			dialogClass: "modalfixed",
			buttons: b
		});

		$('.modalfixed.ui-dialog').css({position:"fixed"});
		$('#dlepopup').dialog( "option", "position", ['0','0'] );

};