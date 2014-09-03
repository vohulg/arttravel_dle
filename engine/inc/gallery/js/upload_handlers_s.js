// JavaScript Document
// TWS Gallery - by Al-x
// Version TWS Gallery 5.2
// Powered by http://wonderfullife.ru/
// Support by http://wonderfullife.ru/, http://inker.wonderfullife.ru/
//-----------------------------------------------------
// Copyright (c) 2007,2012 TWS
var StopUpload = 0;
var numUploaded = 0;
var twsg_opened_rows= 2;

function uploadFlashReady(){
	var form = document.getElementById('entryform');
	if (!form || !form.cat || !form.cat.value || form.cat.value == '0' || form.cat.value == '')
		form.btnBrowse.value = gallery_lang_upload['js16'];
	else {	
		form.btnBrowse.disabled = false;
		form.btnBrowse.value = gallery_lang_upload['js18'];
	}
}
function fileQueued(a){
	StopUpload = 0;
	var c = new FileProgress(a, this.customSettings.progressTarget), e = '', t = a.type.replace('.',''), i, file, fn=false;
	var i = 0;	
	while (file = this.getFile(i)){ i++;
		if (file && file.filestatus === SWFUpload.FILE_STATUS.QUEUED && file.name == a.name){
			if (fn){
				e = gallery_lang_upload['js15'];
				break;
			}
			fn=true;
		}
	}
	t = t.toLowerCase();
	if (e=='' && limitsizetable && limitsizetable[t] && limitsizetable[t] < Math.ceil(a.size/1024)) e = gallery_lang_upload['5'];
	if (e!=''){
		c.setError();
		c.toggleCancel(false);
		c.setStatus(e);
		StopUpload = 1;
		this.cancelUpload(a.id);
	} else {
		c.setStatus(gallery_lang_upload['js1']);
		c.toggleCancel(true, this);
	}
}
function fileDialogComplete(a,b){
	if (a>0) setUploadButton(false, true);
	//else setUploadButton(true, true);
}
function fileQueueError(a,d,b){
	if (d === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED){
		DLEalert(gallery_lang_upload['js2']+(b===0?gallery_lang_upload['js3']:gallery_lang_upload['js4'].replace('{num}', b)),dle_info);
		return;
	}
	var c=new FileProgress(a,this.customSettings.progressTarget);
	c.setError();
	c.toggleCancel(false);
	switch(d){
	case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT: c.setStatus(gallery_lang_upload['5']); break;
	case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE: c.setStatus(gallery_lang_upload['24']); break;
	case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE: c.setStatus(gallery_lang_upload['3']); break;
	default: a!==null&&c.setStatus(gallery_lang_upload['js5']);
	}
}
function uploadError(a,d,b){
	if (StopUpload){ StopUpload = 0; return; }
	var c=new FileProgress(a,this.customSettings.progressTarget), e;
	c.setError();
	c.toggleCancel(false);
	switch(d){
	case SWFUpload.UPLOAD_ERROR.HTTP_ERROR: e=gallery_lang_upload['js8']+b; break;
	case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED: e=gallery_lang_upload['js9']; break;
	case SWFUpload.UPLOAD_ERROR.IO_ERROR: e=gallery_lang_upload['js10']; break;
	case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR: e=gallery_lang_upload['js11']; break;
	case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED: e=gallery_lang_upload['js3']; break;
	case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED: e=gallery_lang_upload['js12']; break;
	case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED: e=gallery_lang_upload['js13'];
		c.setCancelled();
		if (this.getStats().files_queued===0) setUploadButton(true, true);
	break;
	case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
		c.setStatus(gallery_lang_upload['js1']);
		c.toggleCancel(true, this);
	return;
	default: e=gallery_lang_upload['js5'] + d; break;
	}
	c.setStatus(e);
}
function uploadStart(a){
	var c = new FileProgress(a, this.customSettings.progressTarget);
	c.setStatus(gallery_lang_upload['js6']);
	c.toggleCancel(true, this);
	return true;
}
function uploadProgress(a, b, d){
	var p = Math.ceil((b / d) * 100), c = new FileProgress(a, this.customSettings.progressTarget);
	c.setProgress(p);
	if (p == 100) {
		c.setStatus(gallery_lang_upload['js14']);
		c.toggleCancel(false, this);
	} else {
		c.setStatus(gallery_lang_upload['js6']+' '+p+'%');
		c.toggleCancel(true, this);
	}
}
function uploadSuccess(a,d){
	var b=new FileProgress(a,this.customSettings.progressTarget);
	if (!cattitle) cattitle = {'id':0,'a':'','t':''};
	var error = d.match(/\[HTML:Errors\](.*?)\[END:HTML:Errors\]/g);
	if (error){
		this.cancelQueue();
		b.setCancelled();
		error = d.replace(/\[HTML:Errors\](.*?)\[END:HTML:Errors\]/g, "$1");
		DLEalert(gallery_lang_upload[error].replace('{cattitle}', cattitle['t']), dle_info);
	} else {
		d=parseInt(d);
		if (isNaN(d) || !isFinite(d) || d < 1){
			b.setStatus(gallery_lang_upload['js7']);
			b.setComplete();
			b.toggleCancel(false);
			numUploaded++;
		} else {
			if (gallery_lang_upload[d])
				b.setStatus(gallery_lang_upload[d]);
			else
				b.setStatus('Unknown Error: '+d+' Please, report site administrator!');
			b.setError();
			b.toggleCancel(false);
		}
	}
}
function uploadComplete(a){
	
}
function queueComplete(a){
	setUploadButton((this.getStats().files_queued===0 ? true : false), true);
	var form = document.getElementById('entryform');
	var mode = (gallery_advance_default != '2' && form.upload_mode) ? form.upload_mode.value : gallery_advance_default;
	if (numUploaded){
		if (mode == 0){
			setTimeout(function(){
				ShowLoading('');
				$.get(dle_root + "engine/gallery/ajax/upload.php", {skin: dle_skin, action: 'complete' }, function(data){
					HideLoading('');
					data = cas(data, 0, 0);
					if (data){
						$("#dle-content").html(data);
						setTimeout(function() {
						$("html:not(:animated)"+( ! $.browser.opera ? ",body:not(:animated)" : "")).animate({scrollTop: $("#dle-content").position().top - 70}, 700);
						}, 100);
					}
				});
			}, 2500);
		} else if (mode == 2){
			setTimeout(function() { location.replace( dle_root+dle_admin+'?mod=twsgallery&act=12&ap=1&cat='+form.cat.value+'&rndval='+new Date().getTime() ); }, 1500);
		} else {
			if (!cattitle) cattitle = {'m':0,'id':0,'a':'','t':''};
			$("#dlegallerypopup").remove();
			title_on_moderation = cattitle['m'] ? '<br /><br />'+gallery_lang_upload['js20'] : '';
			$("body").append("<div id='dlegallerypopup' title='" + dle_info + "' style='display:none'><br />"+gallery_lang_upload['js19'].replace('{num}', numUploaded)+title_on_moderation+"</div>");
			var bt = {};
			bt["Ok"] = function() { 
				$(this).dialog('close');	
				$("#dlegallerypopup").remove();	
			};
			bt[gallery_lang_upload['js21']] = function() { 
				$(this).dialog("close");
				$("#dlegallerypopup").remove();
				location.replace( dle_root+(gallery_alt_url == 'yes' ? gallery_web_root+cattitle['a']+'/' : 'index.php?do=gallery&act=1&cid='+cattitle['id']));
			};
			$('#dlegallerypopup').dialog({
				autoOpen: true,
				width: 470,
				dialogClass: "modalfixed",
				buttons: bt
			});
			$('.modalfixed.ui-dialog').css({position:"fixed"});
			$('#dlegallerypopup').dialog( "option", "position", ['0','0'] );
			//setTimeout(function() {$('#dlegallerypopup').dialog("close");}, 10000);
		}
		numUploaded = 0;
	}
}

function setUploadButton(start, cancel){
	if (start != 'null'){ ob = document.getElementById(swfu.customSettings._StartButtonId); if (ob) ob.disabled = start; }
	if (cancel != 'null'){ ob = document.getElementById(swfu.customSettings.cancelButtonId); if (ob) ob.disabled = cancel; }
}
function upload_check(mode){
	var e = [], form = document.getElementById('entryform'), v;
	if (!form || !form.cat || !form.cat.value || form.cat.value == '0' || form.cat.value == '') e[0] = gallery_lang_upload['js17'];
	if (gallery_file_title_control != '0' && form.foto_title.value == '') e[e.length] = gallery_lang_upload['21'];
	if (e.length < 1){
		if (mode == 1 || swfu.getStats().files_queued > 0){
			setUploadButton(true, false);
			e=form.elements;
			for(i=0;i<e.length;i++)
				if(n=e[i].name){
					v="";
					switch(e[i].type){
						case "select":v=e[i].options[e[i].selectedIndex].value;break;
						case "radio":case "checkbox":v=e[i].checked?1:0;break;
						default:v=e[i].value;break
					}
					swfu.addPostParam(n,v);
				}
			swfu.startUpload();
			return false;
		}
		return true;
	}
	DLEalert(e.join('<br />'), dle_info);
	return false;
}
function show_rules( id ){
	ShowLoading('');
	$.get(dle_root + "engine/gallery/ajax/upload.php", { id: id, skin: dle_skin, action: 'rules' }, function(data){
		HideLoading('');
		RunAjaxJS('rules-layer', data);
		document.getElementById('btnBrowse').disabled = false;
		document.getElementById('btnBrowse').value = gallery_lang_upload['js18'];
		el = document.getElementById('foto_title');
		if (!cattitle) cattitle = {'id':0,'a':'','t':''};
		if (el) el.value = cattitle['t'];
	});
}
function AddImages(id, id_name, id_type, id_size) {
	var tbl = document.getElementById(id);
	if (!gallery_max_once_upload) gallery_max_once_upload = 50;
	if (twsg_opened_rows < gallery_max_once_upload){
		twsg_opened_rows++;
		var row = tbl.insertRow(tbl.rows.length);
		var cellRight = row.insertCell(0);
		var el = document.createElement('input');
		el.setAttribute('type', id_type);
		el.setAttribute('name', id_name);
		el.setAttribute('size', id_size);
		cellRight.appendChild(el);
	}
}
function RemoveImages(id) {
	var tbl = document.getElementById(id);
	if (tbl.rows.length > 1){
	  tbl.deleteRow(tbl.rows.length - 1);
	  twsg_opened_rows--;
	}
};