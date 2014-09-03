// JavaScript Document
// TWS Gallery - by Al-x
// Version TWS Gallery 5.2
// Powered by http://wonderfullife.ru/
// Support by http://wonderfullife.ru/, http://inker.wonderfullife.ru/
//-----------------------------------------------------
// Copyright (c) 2007,2012 TWS
var twsg_status = new Array();
var fieldcheck = new Array();

function cas(text, show, delay){
	twsg_status = {'e':0,'k':0};
	var message = '';
	if (text.match(/\[HTML:Errors\](.*?)\[END:HTML:Errors\]/g)){
		twsg_status['e']++;
		message += text.replace(/\[HTML:Errors\](.*?)\[END:HTML:Errors\]/g, "$1");
		text = text.replace(/\[HTML:Errors\](.*?)\[END:HTML:Errors\]/g, "");
	} else if (text.match(/\[HTML:Ok\](.*?)\[END:HTML:Ok\]/g)){
		twsg_status['k']++;
		message += text.replace(/\[HTML:Ok\](.*?)\[END:HTML:Ok\]/g, "$1");
		text = text.replace(/\[HTML:Ok\](.*?)\[END:HTML:Ok\]/g, "");
	}
	if (message != '' && show) DLEalert(message, dle_info);
	if (delay) setTimeout(function() {$('#dlepopup').dialog("close");},(delay*1000));
	return text;
}

function GalUserMenu( url, name ){

var menu=new Array();

	menu[0]='<a onclick="ShowProfile(\'' + name + '\', \'' + url + '\', ' + gallery_admin_editusers + '); return false;" href="' + url +'">' + menu_profile + '</a>';
	menu[1]='<a href="' + dle_root + 'index.php?do=gallery&act=4&gal_user=' + name + '">' + gallery_lang_web[0] + '</a>';
	menu[2]='<a href="' + dle_root + (gallery_alt_url == 'yes' ? (gallery_web_root +'all/user-' + name + '/') : ('index.php?do=gallery&act=15&p=user-' + name))+'">' + gallery_lang_web[1] + '</a>';
	menu[3]='<a href="' + dle_root + (gallery_alt_url == 'yes' ? (gallery_web_root +'users/' + name + '/') : ('index.php?do=gallery&act=28&gal_user=' + name))+'">' + gallery_lang_web[2] + '</a>';

return menu;
};

function MenuGalComment( com_id ){

var menu=new Array();

menu[0]='<a onclick="twsg_ajax_comm_edit(\'' + com_id + '\'); return false;" href="#">' + menu_short + '</a>';
menu[1]='<a href="' + dle_root + 'index.php?do=gallery&act=3&dle_allow_hash=' + dle_login_hash + '&subaction=comm_edit&com_id=' + com_id + '">' + menu_full + '</a>';

return menu;
};

function doAddTWSGComments(){

	var form = document.getElementById('dle-comments-form');

	if (dle_wysiwyg == "yes" || dle_wysiwyg == "1" || dle_wysiwyg == "2") {
		if (gallery_dle_id > 95 && dle_wysiwyg != "2"){
			submit_all_data();
		} else {
			document.getElementById('comments').value = $('#comments').html();
		}
		var editor_mode = 'wysiwyg';
	} else var editor_mode = '';

	if (form.comments.value == '' || form.name.value == '')
	{
		DLEalert ( dle_req_field, dle_info );
		return false;
	}

	var	allow_subscribe = form.allow_subscribe ? form.allow_subscribe.value : '';
	var	question_answer = form.question_answer ? form.question_answer.value : '';
	var	sec_code = form.sec_code ? form.sec_code.value : '';
	var	recaptcha_response_field = form.recaptcha_response_field ? Recaptcha.get_response() : '';
	var	recaptcha_challenge_field = form.recaptcha_response_field ? Recaptcha.get_challenge() : '';

	ShowLoading('');

	$.post(dle_root + "engine/gallery/ajax/comments.php", { id: form.id.value, allow_subscribe:allow_subscribe, comments: form.comments.value, name: form.name.value, mail: form.mail.value, editor_mode: editor_mode, skin: dle_skin, sec_code: sec_code, question_answer: question_answer, recaptcha_response_field: recaptcha_response_field, recaptcha_challenge_field: recaptcha_challenge_field, action: 'add' }, function(data){

		if ( form.sec_code ) {
           form.sec_code.value = '';
           reload();
	    }

		HideLoading('');

		RunAjaxJS('dle-ajax-comments', data);

		if (data != 'error' && document.getElementById('blind-animation')) {

			$("html"+( ! $.browser.opera ? ",body" : "")).animate({scrollTop: $("#dle-ajax-comments").position().top - 70}, 1100);
	
			setTimeout(function() { $('#blind-animation').show('blind',{},1500)}, 1100);
		}

	});

};

function GalRate( rate, id ){
	ShowLoading('');
	$.get(dle_root + "engine/gallery/ajax/file.php", { go_rate: rate, id: id, skin: dle_skin, act: 2 }, function(data){
		HideLoading('');
		$("#ratig-layer-"+id).html(data);
	});
};

function subscribe_comments(id, user_id){

		var global_email = '', hash = '';

		var request_function = function(action){

			if (!user_id && $('#dle-promt-text').val().length < 5) {
				$('#dle-promt-text').addClass('ui-state-error');
				return false;
			} else if (!user_id){
				global_email = $('#dle-promt-text').val();
			}

			$(this).dialog('close');
			$('#dlepopup').remove();

			ShowLoading('');

			$.post(dle_root + "engine/gallery/ajax/comments.php", { email: global_email, user_id: user_id, hash: hash, action: action, skin: dle_skin, fid: id }, function(data){
				HideLoading('');
				if (data.status != 'found')
					DLEalert(data.txt1, dle_info);
				else {
					hash = data.hash;
					fields_function(data);
				}
			}, "json");
		};

		var fields_function = function(data){

			var bt = {}, txt = gallery_lang_web[4];

			bt[dle_act_lang[3]] = function(){
				$(this).dialog('close');
			};

			if (data && data.txt3){
				txt = data.txt1;
				bt[data.txt2] = function() {
					request_function('unsubscribe');
				};
				bt[data.txt3] = function() {
					id = 0;
					request_function('unsubscribe');
				};
			} else {
				bt['OK'] = function() {
					request_function('subscribe');
				};
			}

			$('#dlepopup').remove();
			if (!user_id) txt += "<br /><br /><input type='text' name='dle-promt-text' id='dle-promt-text' class='ui-widget-content ui-corner-all' style='width:80%; padding: .4em;' value='" + global_email + "'/>";
			$('body').append("<div id='dlepopup' title='"+gallery_lang_web[3]+"' style='display:none'><br />"+txt+"</div>");

			$('#dlepopup').dialog({
				autoOpen: true,
				width: 500,
				dialogClass: "modalfixed",
				buttons: bt
			});

			$('.modalfixed.ui-dialog').css({position:"fixed"});
			$('#dlepopup').dialog( "option", "position", ['0','0'] );

		};

		if (user_id)
			request_function('subscribe');
		else 
			fields_function(false);

};

function whois_view(id){
	ShowLoading('');
	$.get(dle_root + "engine/gallery/ajax/file.php", { id: id, skin: dle_skin, act: 3 }, function(data){
		HideLoading('');
		$("#dlepopup_galview").remove();
		
		if (!data.num || data.num < 1){
			var message = data.title_no;
			data.title = dle_info;
		} else {
			var message = [], user_url;
			for (var i=0;i<data.num;i++){
				user_url = data.alt == 'yes' ? (dle_root+'user/'+data.data[i][2]+'/') : (dle_root+'index.php?subaction=userinfo&amp;user='+data.data[i][2]);
				message[i] = '<a onclick="ShowProfile(\'' + data.data[i][1] + '\', \'' + user_url + '\', ' + gallery_admin_editusers + '); return false;" href="' + user_url +'">' + data.data[i][0] + '</a>';
			}
			message = message.join(', ');
			data.title += ' ('+data.num+'):';
		}

		$("body").append("<div id='dlepopup_galview' title='" + data.title + "' style='display:none'><br /><div id='dlepopup_galviewcontent' style='overflow:auto;'>"+ message +"</div></div>");
		$('#dlepopup_galview').dialog({
			autoOpen: true,
			show: 'fade',
			hide: 'fade',
			width: 450,
			height: 150,
			buttons: {
				"Ok": function() { 
					$(this).dialog("close");
					$("#dlepopup_galview").remove();							
				} 
			}
		});

		if ($('#dlepopup_galviewcontent').height() > 200 ) $('#dlepopup_galviewcontent').height(200);
		$('#dlepopup_galview').dialog( "option", "position", 'center' );

	}, "json");
};

function cat_moderators_show(id){
	ShowLoading('');
	$.get(dle_root + "engine/gallery/ajax/category.php", { id: id, skin: dle_skin, act: 1 }, function(data){
		HideLoading('');
		$("#dlepopup").remove();
		if (!data.num || data.num < 1) return;
		else {
			var message = [], user_url;
			for (var i=0;i<data.num;i++){
				user_url = data.alt == 'yes' ? (dle_root+'user/'+data.data[i][2]+'/') : (dle_root+'index.php?subaction=userinfo&amp;user='+data.data[i][2]);
				message[i] = '<a onclick="ShowProfile(\'' + data.data[i][1] + '\', \'' + user_url + '\', ' + gallery_admin_editusers + '); return false;" href="' + user_url +'">' + data.data[i][0] + '</a>';
			}
			message = message.join(', ');
			data.title += ' ('+data.num+'):';
		}

		$("body").append("<div id='dlepopup_galview' title='" + data.title + "' style='display:none'><br /><div id='dlepopup_galviewcontent' style='overflow:auto;'>"+ message +"</div></div>");
		$('#dlepopup_galview').dialog({
			autoOpen: true,
			show: 'fade',
			hide: 'fade',
			width: 450,
			height: 150,
			buttons: {
				"Ok": function() { 
					$(this).dialog("close");
					$("#dlepopup_galview").remove();							
				} 
			}
		});

		if ($('#dlepopup_galviewcontent').height() > 200 ) $('#dlepopup_galviewcontent').height(200);
		$('#dlepopup_galview').dialog( "option", "position", 'center' );

	}, "json");
};

function gallery_change_sort(sort, direction){
  var frm = document.getElementById('gallery_set_sort');
  frm.foto_sort.value=sort;
  frm.foto_msort.value=direction;
  frm.submit();
  return false;
};

function GalleryComPage(cstart, id){
	ShowLoading('');
	$.get(dle_root + "engine/gallery/ajax/comments.php", {  id: id, action: "list", skin: dle_skin, cstart: cstart }, function(data){
		HideLoading('');
		var data = data.split('{comments-delimiter}');
		$("#ajaxcommslist").html('<div id="blind-animation" style="display:none">'+data[0]+'</div>');
		$("html"+( ! $.browser.opera ? ",body" : "")).animate({scrollTop: $("#ajaxcommslist").position().top - 70}, 700);
		setTimeout(function() { $('#blind-animation').show('blind',{},1500)}, 500);
		if (data[1]) $("#ajaxcommsnav").html(data[1]);
	});
};

function ckeck_title(tpl, text){
var frm = document.getElementById('entryform');
for (var i=0;i<frm.elements.length;i++){
var elmnt = frm.elements[i];
if (elmnt.name && (tpl == 'title' && elmnt.name.replace(/^(.+?)\[(.+?)$/, "$1") == 'title' || tpl == 'cat_title' && elmnt.name.match(/cat_title/g)) && elmnt.value == ''){
DLEalert(text, dle_info); return false;
}
}
return true;
};

function ckeck_field(id, value){
	if (value && value.length > 2 && (!fieldcheck[id] || value != fieldcheck[id])){
		fieldcheck[id] = value;
		$.get(dle_root + 'engine/gallery/ajax/file.php', { field: id, data: value, skin: dle_skin, act: 5 }, function(data){
			if (data != 'error') $('#result-'+id).html(data);
		});
	}
};

function gallery_autocomplete(obj, path, num_vars, min_length){

	if (!min_length) var min_length = 3;
	if (!num_vars) var num_vars = 0;

	obj.autocomplete({
		source: function( request, response ) {
			var term = request.term.split( /,\s*/ );
			term = term.pop();
			$.getJSON(dle_root+path, {term: term, skin: dle_skin}, response);
		},
		search: function() {
			var term = this.value.split( /,\s*/ );
			if (num_vars && num_vars < term.length) return false;
			term = term.pop();
			if (min_length && term.length < min_length) return false;
		},
		focus: function() { return false; },
		select: function( event, ui ) {
			var terms = this.value.split( /,\s*/ );
			terms.pop();
			terms.push( ui.item.value );
			if (!num_vars || num_vars > terms.length) terms.push( '' );
			this.value = terms.join( ', ' );
			return false;
		}
	});

};

function old_dle_ins(name){
	if ( !document.getElementById('dle-comments-form') ) return false;

	var input=document.getElementById('dle-comments-form').comments;
	var finalhtml = "";

	if (dle_wysiwyg == "0") {
		if (dle_txt!= "") {
			input.value += dle_txt;
			input.focus();
		}
		else { 
			input.value += "[b]"+name+"[/b],"+"\n";
			input.focus();
		}
	} else {
		if (dle_txt!= "") {
			finalhtml = dle_txt;
		}
		else { 
			finalhtml = "<b>"+name+"</b>,";
		}

		if (dle_wysiwyg == "1") {
			oUtil.obj.focus();	
			oUtil.obj.insertHTML(finalhtml+"<br />");
		} else {
			tinyMCE.execInstanceCommand('comments', 'mceInsertContent', false, finalhtml, true) 
		}
	}

};
