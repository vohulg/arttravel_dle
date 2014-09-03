<style type="text/css">
fieldset { 
	border:1px solid #CDCDCD;
	padding: 20px 10px;
	margin:0px;
}
fieldset.flash {
	margin: 0px;
	border-color: #CDCDCD;
}
.progressWrapper {
	width: 99%;
	overflow: hidden;
}
.progressContainer {
	margin: 5px;
	padding: 4px;
	border: solid 1px #E8E8E8;
	background-color: #F7F7F7;
	overflow: hidden;
}
/* Message */
.message {
	margin: 1em 0;
	padding: 10px 20px;
	border: solid 1px #FFDD99;
	background-color: #FFFFCC;
	overflow: hidden;
}
/* Error */
.red {
	border: solid 1px #B50000;
	background-color: #FFEBEB;
}
/* Current */
.green {
	border: solid 1px #DDF0DD;
	background-color: #EBFFEB;
}
/* Complete */
.blue {
	border: solid 1px #CEE2F2;
	background-color: #F0F5FF;
}
.progressName {
	font-size: 8pt;
	font-weight: 700;
	color: #555;
	width: 323px;
	height: 14px;
	text-align: left;
	white-space: nowrap;
	overflow: hidden;
}
.progressBarInProgress,
.progressBarComplete,
.progressBarError {
	font-size: 0;
	width: 0%;
	height: 2px;
	background-color: blue;
	margin-top: 2px;
}
.progressBarComplete {
	width: 100%;
	background-color: green;
	visibility: hidden;
}
.progressBarError {
	width: 100%;
	background-color: red;
	visibility: hidden;
}
.progressBarStatus {
	margin-top: 2px;
	width: 99%;
	font-size: 7pt;
	font-family: Arial;
	text-align: left;
	white-space: nowrap;
}
a.progressCancel {
	font-size: 0;
	display: block;
	height: 14px;
	width: 14px;
	background-image: url(/engine/classes/swfupload/cancelbutton.gif);
	background-repeat: no-repeat;
	background-position: -14px 0px;
	float: right;
}
a.progressCancel:hover {
	background-position: 0px 0px;
}
</style>


<div class="base fullstory">
	<div class="dpad">
		<h3 class="btl">�������� ������</h3>
	</div>
	<div class="maincont">&nbsp;</div>
</div>
<div class="baseform">
	<table class="tableform">
        <tr>
          <td class="label">���������: <span class="impot">*</span></td>
          <td>{category}</td>
        </tr>
        <tr>
          <td class="label">H������� ������:</td>
          <td><input type="text" name="foto_title" id="foto_title" value="{foto_title}" class="f_input" /></td>
        </tr>
[not-logged]
		<tr>
			<td class="label">���� ���:</td><!-- ��������! ����� fieldcheck � input-��� ������ ��� ������ ������ �������� ������������ �������� ������. �� �������� ���, ���� ������, ����� ��� � email ����������� ��� ���������� -->
			<td><input type="text" name="name" id="name" class="f_input fieldcheck" /><div id="result-name" class="small impot"></div></td>
		</tr>
		<tr>
			<td class="label">��� E-Mail:</td>
			<td><input type="text" name="email" id="email" class="f_input fieldcheck" /><div id="result-email" class="small impot"></div></td>
		</tr>
[/not-logged]
[selectmode]
       <tr>
          <td class="label">����� ��������:</td>
          <td>{selectmode}</td>
        </tr>
[/selectmode]
[tags]
       <tr>
          <td class="label">�������� �����:</td>
          <td><input type="text" name="tags" id="tags" class="f_input" /></td>
        </tr>
[/tags]
	</table>

	<div id="upload1_layer" style="display:block;">
	<table class="tableform">
        <tr>
          <td>
		<br />
		<div style="position: relative">
			<input id="btnBrowse" type="button" value=" - ���������� Flash - " style="width:160px;" class="edit" disabled="disabled" />&nbsp;&nbsp;<input id="btnStart" type="button" value="  ������ ��������  " onclick="upload_check(1);" style="width:160px;" class="edit" disabled="disabled" />&nbsp;&nbsp;<input id="btnCancel" type="button" value="  ���������� ��� ��������  " onclick="swfu.cancelQueue();" disabled="disabled" style="width:160px;" class="edit" />
			<div style="padding-left:3px;padding-top:3px;font-size:11px;">* ���� flash ��������� �� ��������, �������������� <a href="javascript:void(0);" onclick="ShowOrHide('upload1_layer');ShowOrHide('upload2_layer')"><b>�������</b></a> ���������</div>
			<div id="flash_container" style="width:130px; height: 20px;position:absolute;top:0;left:0px;"></div>
		</div>
		<br />
		<fieldset class="flash" id="fsUploadProgress">
			<legend>������ ����������� ������</legend>
		</fieldset>
		 </td>
        </tr>
	</table>
	</div>

	<div id="upload2_layer" style="display:none;">
	<table class="tableform">
    	<tr>
		   <td class="label">����� � �������� �����:
		   <br /><br /><div align="center">
		<input type=button class=buttons value=' - ' style="width:30px;" title='������� ��������� ���� ��� �������� ��������' onClick="RemoveImages('tblSample');return false;">
	    <input type=button class=buttons value=' + ' style="width:30px;" title='�������� ��� ���� ���� ��� �������� ��������' onClick="AddImages('tblSample', 'image[]', 'file', '81');return false;">
		   </div></td>
          <td>
		  <table id="tblSample">
        <tr id="row">
          <td style="border:0;"><input type="file" size="81" name="image[]"></td>
        </tr>
      </table>
		  </td>
		 </tr>
	</table>
	</div>

	<div id="upload3_layer" style="display:none;">
	<table class="tableform">
    	<tr>
		   <td class="label">�� url ������:
		   <br /><br /><div align="center">
      <input type=button class=buttons value=' - ' style="width:30px;" title='������� ��������� ���� ��� �������� ��������' onClick="RemoveImages('tblSample2');return false;">
      <input type=button class=buttons value=' + ' style="width:30px;" title='�������� ��� ���� ���� ��� �������� ��������' onClick="AddImages('tblSample2', 'url[]', 'text', '81');return false;">
		   </div></td>
          <td>
		  <table id="tblSample2">
        <tr id="row">
          <td style="border:0;"><input type="text" size="81" name="url[]"></td>
        </tr>
      </table>
		  </td>
		 </tr>
[remote]
       <tr>
          <td class="label">&nbsp;</td>
          <td>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="remote_upload" id="remote_upload" value="1" /> <label for="remote_upload">�� ��������� ����� �� ������</label></td>
        </tr>
[/remote]
	</table>
	</div>

	<div id="upload4_layer" style="display:none;">
	<table class="tableform">
        <tr>
          <td class="label">Zip-�����:</td>
          <td>
		  <table>
        <tr>
          <td style="border:0;"><input type="file" size="81" name="zip_archive"></td>
        </tr>
      </table>
		 </td>
        </tr>
	</table>
	</div>
</div>
<div class="base fullstory">
<div class="dpad small" id="rules_layer" style="display:none;">

��� ����, ����� ��������� ����� � �������, ��� ���������� ������� �� �����, ������� �� ������ ��������, ����� �� ��������� ��������:<br /><br />
<li>������ ����� �� ���� ���������</li>
<li>������ ������ �� ����, ������� ��������� �� ������ �������</li>
<li>������ ������ �� �������� � ������ �� ����� ��������, ��� youtube.com, rutube.ru, video.mail.ru, vimeo.com, smotri.com, gametrailers.com</li>
[remote]<li>������ ������ �� ����, ������� ��������� �� ������ �������� �����������. ��� ���� �� ����� ����� ����� ������� ������ ����������� �����, � �������� ����� ����������� � ��������� ���������</li>[/remote]
<li>��������� ��� ����� � zip ����� � ������ ��� ����</li>

<br />��� �������� ������ ����� ������� ����, ����� �� ������ ������.
<br />� ������� ������ + � - ����� �������� ��� ������ ���� ��� ��������.
[remote]<br />����� �� ��������� �� ���� ��������� ����������� ��� �������� �� ������, ������� ����� "�� ��������� ����� �� ������".[/remote]
<br />����� ������������ ��������� ����� �������� ������������. ��� ���� ����� ���������� ����������� ������ ����� ������������� � ������������ � ����������� ����������.
<br />� ������ ��������� ���������� ���� ���������� � ����� ������, �� �������, ����������� ������� ����������. ��� ����� ���������� �������� ���� ���������� �������� ��������� ��� ��������.
[tags]<br /><br />��� ���������� ������ ����� ������� �������� �����, �� ������� ������������ ����� ������� "������ �����". �������� ����� ����������� ����� �������, �� �� ����� {tags_num}. ������ �������� ����� ����� �������� �� ���������� ����, ���������� ��������� ��� ������ ������� ���������. ����� ����� ������ ��������� ����� ������ ���� {tags_len} ��������.[/tags]

<br /><br />{rules}
<div class="maincont">&nbsp;</div>
</div>
	<div class="mlink">
		<span class="argmore"><a href="javascript:ShowOrHide('rules_layer')"><b>�������</b></a></span>
		<span class="argmore"><a href="javascript:ShowOrHide('upload4_layer')"><b>� ZIP ������</b></a></span>
		<span class="argmore"><a href="javascript:ShowOrHide('upload3_layer')"><b>�� ������</b></a></span>
		<span class="argmore"><a href="javascript:ShowOrHide('upload2_layer')"><b>�������</b></a></span>
		<span class="argmore"><a href="javascript:ShowOrHide('upload1_layer')"><b>��������</b></a></span>
	</div>
</div>
<div class="fieldsubmit">
	<input class="fbutton" type="submit" name="submit" value="���������" />
	<input name="submit" type="hidden" id="submit" value="submit" />
</div>