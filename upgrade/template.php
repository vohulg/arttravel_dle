<?php

if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}


$skin_header = <<<HTML
<html>
<head>
<title>DataLife Engine</title>
<meta content="text/html; charset={$config['charset']}" http-equiv="content-type" />
<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="{$theme}/skins/calendar-blue.css" title="win2k-cold-1" />

<!-- main calendar program -->
<script type="text/javascript" src="{$theme}/skins/calendar.js"></script>
<script type="text/javascript" src="{$theme}/skins/default.js"></script>

<style type="text/css">
html,body{
height:100%;
margin:0px;
padding: 0px;
background: #F4F3EE;
}

form {
margin:0px;
padding: 0px;
}

table{
border:0px;
border-collapse:collapse;
}

table td{
padding:0px;
font-size: 11px;
font-family: verdana;
}
.quote {
	color:#545454;
	background-color:#ffffe0;
	border: 1px dotted #d8d8d8;
	text-align: justify;
	padding: 5px;
}
a:active,
a:visited,
a:link {
	color: #4b719e;
	text-decoration:none;
	}

a:hover {
	color: #4b719e;
	text-decoration: underline;
	}

.navigation {
	color: #999898;
	font-size: 11px;
	font-family: tahoma;
}

.option {
	color: #717171;
	font-size: 11px;
	font-family: tahoma;
}

.upload input {
	border:1px solid #9E9E9E;
	color: #000000;
	font-size: 11px;
	font-family: Verdana; BACKGROUND-COLOR: #ffffff 
}

.small {
	color: #999898;
}

.navigation a:active,
.navigation a:visited,
.navigation a:link {
	color: #999898;
	text-decoration:none;
	}

.navigation a:hover {
	color: #999898;
	text-decoration: underline;
	}

.list {
	font-size: 11px;
}

.list a:active,
.list a:visited,
.list a:link {
	color: #0B5E92;
	text-decoration:none;
	}

.list a:hover {
	color: #999898;
	text-decoration: underline;
	}

.quick {
	color: #999898;
	font-size: 11px;
	font-family: tahoma;
	padding: 5px;
}

.quick h3 {
	font-size: 18px;
	font-family: verdana;
	margin: 0px;
	padding-top: 5px;
}
.system {
	color: #999898;
	font-size: 11px;
	font-family: tahoma;
	padding-bottom: 10px;
	text-decoration:none;
}

.system h3 {
	font-size: 18px;
	font-family: verdana;
	margin: 0px;
	padding-top: 4px;
}
.system a:active,
.system a:visited,
.system a:link,
.system a:hover {
	color: #999898;
	text-decoration:none;
	}

.quick a:active,
.quick a:visited,
.quick a:link,
.quick a:hover {
	color: #999898;
	text-decoration:none;
	}

.unterline {
	background: url({$theme}/skins/images/line_bg.gif);
	width: 100%;
	height: 9px;
	font-size: 3px;
	font-family: tahoma;
	margin-bottom: 4px;
} 

.hr_line {
	background: url({$theme}/skins/images/line.gif);
	width: 100%;
	height: 7px;
	font-size: 3px;
	font-family: tahoma;
	margin-top: 4px;
	margin-bottom: 4px;
}

.edit {
	border:1px solid #9E9E9E;
	color: #000000;
	font-size: 11px;
	font-family: Verdana; BACKGROUND-COLOR: #ffffff 
}

.bbcodes {
	background: #FFF;
	border: 1px solid #9E9E9E;
	color: #666666;
	font-family: Verdana, Tahoma, helvetica, sans-serif;
	padding: 2px;
	vertical-align: middle;
	font-size: 10px; 
	margin:2px;
	height: 21px;
}

.buttons {
	background: #FFF;
	border: 1px solid #9E9E9E;
	color: #666666;
	font-family: Verdana, Tahoma, helvetica, sans-serif;
	padding: 0px;
	vertical-align: absmiddle;
	font-size: 11px; 
	height: 21px;
}

select, option {
	color: #000000;
	font-size: 11px;
	font-family: Verdana; 
	background-color: #ffffff 
}

textarea {
	border: #9E9E9E 1px solid;
	color: #000000;
	font-size: 11px;
	font-family: Verdana; 
	background-color: #ffffff 
}

.xfields textarea {
width:550px; height:90px;border: #9E9E9E 1px solid; font-size: 11px;font-family: Verdana;
}
.xfields input {
width:350px; height:18px;border: #9E9E9E 1px solid; font-size: 11px;font-family: Verdana;
}
.xfields select {
height:18px; font-size: 11px;font-family: Verdana;
}

.xfields {
height:30px; font-size: 11px;font-family: Verdana;
}

#dropmenudiv{
border:1px solid white;
border-bottom-width: 0;
font:normal 10px Verdana;
background-color: #6497CA;
line-height:20px;
margin:2px;
filter: alpha(opacity=95, enabled=1) progid:DXImageTransform.Microsoft.Shadow(color=#CACACA,direction=135,strength=3);
}

#dropmenudiv a{
display: block;
text-indent: 3px;
border: 1px solid white;
padding: 1px 0;
MARGIN: 1px;
color: #FFF;
text-decoration: none;
font-weight: bold;
}

#dropmenudiv a:hover{ /*hover background color*/
background-color: #FDD08B;
color: #000;
}

#hintbox{ /*CSS for pop up hint box */
position:absolute;
top: 0;
background-color: lightyellow;
width: 150px; /*Default width of hint.*/ 
padding: 3px;
border:1px solid #787878;
font:normal 11px Verdana;
line-height:18px;
z-index:100;
border-right: 2px solid #787878;
border-bottom: 2px solid #787878;
visibility: hidden;
}

.hintanchor{ 
padding-left: 8px;
}

.editor_button {
	float:left;
	cursor:pointer;
	padding-left: 2px;
	padding-right: 2px;
}
.editor_buttoncl {
	float:left;
	cursor:pointer;
	padding-left: 1px;
	padding-right: 1px;
	border-left: 1px solid #BBB;
	border-right: 1px solid #BBB;
}
.editbclose {
	float:right;
	cursor:pointer;
}

.btn {
  display: inline-block;
  *display: inline;
  /* IE7 inline-block hack */

  *zoom: 1;
  padding: 2px 7px 2px;
  margin-bottom: 0;
  font-size: 12px;
  line-height: 18px;
  color: #333333;
  text-align: center;
  text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
  background-color: #f5f5f5;
  background-image: -moz-linear-gradient(top, #ffffff, #e6e6e6);
  background-image: -ms-linear-gradient(top, #ffffff, #e6e6e6);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6));
  background-image: -webkit-linear-gradient(top, #ffffff, #e6e6e6);
  background-image: -o-linear-gradient(top, #ffffff, #e6e6e6);
  background-image: linear-gradient(top, #ffffff, #e6e6e6);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#e6e6e6', GradientType=0);
  border-color: #e6e6e6 #e6e6e6 #bfbfbf;
  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
  filter: progid:dximagetransform.microsoft.gradient(enabled=false);
  border: 1px solid #cccccc;
  border-bottom-color: #b3b3b3;
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
  -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
  -moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
  cursor: pointer;
  *margin-left: .3em;
}
.btn:hover,
.btn:active,
.btn.active,
.btn.disabled,
.btn[disabled] {
  background-color: #e6e6e6;
}
.btn:active,
.btn.active {
  background-color: #cccccc \9;
}
.btn:first-child {
  *margin-left: 0;
}
.btn:hover {
  color: #333333;
  text-decoration: none;
  background-color: #e6e6e6;
  background-position: 0 -15px;
  -webkit-transition: background-position 0.1s linear;
  -moz-transition: background-position 0.1s linear;
  -ms-transition: background-position 0.1s linear;
  -o-transition: background-position 0.1s linear;
  transition: background-position 0.1s linear;
}
.btn:focus {
  outline: thin dotted #333;
  outline: 5px auto -webkit-focus-ring-color;
  outline-offset: -2px;
}
.btn.active,
.btn:active {
  background-image: none;
  -webkit-box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
  -moz-box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
  background-color: #e6e6e6;
  background-color: #d9d9d9 \9;
  outline: 0;
}
.btn.disabled,
.btn[disabled] {
  cursor: default;
  background-image: none;
  background-color: #e6e6e6;
  opacity: 0.65;
  filter: alpha(opacity=65);
  -webkit-box-shadow: none;
  -moz-box-shadow: none;
  box-shadow: none;
}
.btn-large {
  padding: 9px 14px;
  font-size: 15px;
  line-height: normal;
  -webkit-border-radius: 5px;
  -moz-border-radius: 5px;
  border-radius: 5px;
}
.btn-large [class^="icon-"] {
  margin-top: 1px;
}
.btn-small {
  padding: 5px 9px;
  font-size: 11px;
  line-height: 16px;
}
.btn-small [class^="icon-"] {
  margin-top: -1px;
}
.btn-mini {
  padding: 2px 6px;
  font-size: 11px;
  line-height: 14px;
}
.btn-primary,
.btn-primary:hover,
.btn-warning,
.btn-warning:hover,
.btn-danger,
.btn-danger:hover,
.btn-success,
.btn-success:hover,
.btn-info,
.btn-info:hover,
.btn-inverse,
.btn-inverse:hover {
  text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
  color: #ffffff;
}
.btn-primary.active,
.btn-warning.active,
.btn-danger.active,
.btn-success.active,
.btn-info.active,
.btn-inverse.active {
  color: rgba(255, 255, 255, 0.75);
}
.btn-primary {
  background-color: #0074cc;
  background-image: -moz-linear-gradient(top, #0088cc, #0055cc);
  background-image: -ms-linear-gradient(top, #0088cc, #0055cc);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#0088cc), to(#0055cc));
  background-image: -webkit-linear-gradient(top, #0088cc, #0055cc);
  background-image: -o-linear-gradient(top, #0088cc, #0055cc);
  background-image: linear-gradient(top, #0088cc, #0055cc);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#0088cc', endColorstr='#0055cc', GradientType=0);
  border-color: #0055cc #0055cc #003580;
  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
  filter: progid:dximagetransform.microsoft.gradient(enabled=false);
}
.btn-primary:hover,
.btn-primary:active,
.btn-primary.active,
.btn-primary.disabled,
.btn-primary[disabled] {
  background-color: #0055cc;
}
.btn-primary:active,
.btn-primary.active {
  background-color: #004099 \9;
}
.btn-warning {
  background-color: #faa732;
  background-image: -moz-linear-gradient(top, #fbb450, #f89406);
  background-image: -ms-linear-gradient(top, #fbb450, #f89406);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#fbb450), to(#f89406));
  background-image: -webkit-linear-gradient(top, #fbb450, #f89406);
  background-image: -o-linear-gradient(top, #fbb450, #f89406);
  background-image: linear-gradient(top, #fbb450, #f89406);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fbb450', endColorstr='#f89406', GradientType=0);
  border-color: #f89406 #f89406 #ad6704;
  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
  filter: progid:dximagetransform.microsoft.gradient(enabled=false);
}
.btn-warning:hover,
.btn-warning:active,
.btn-warning.active,
.btn-warning.disabled,
.btn-warning[disabled] {
  background-color: #f89406;
}
.btn-warning:active,
.btn-warning.active {
  background-color: #c67605 \9;
}
.btn-danger {
  background-color: #da4f49;
  background-image: -moz-linear-gradient(top, #ee5f5b, #bd362f);
  background-image: -ms-linear-gradient(top, #ee5f5b, #bd362f);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ee5f5b), to(#bd362f));
  background-image: -webkit-linear-gradient(top, #ee5f5b, #bd362f);
  background-image: -o-linear-gradient(top, #ee5f5b, #bd362f);
  background-image: linear-gradient(top, #ee5f5b, #bd362f);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ee5f5b', endColorstr='#bd362f', GradientType=0);
  border-color: #bd362f #bd362f #802420;
  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
  filter: progid:dximagetransform.microsoft.gradient(enabled=false);
}
.btn-danger:hover,
.btn-danger:active,
.btn-danger.active,
.btn-danger.disabled,
.btn-danger[disabled] {
  background-color: #bd362f;
}
.btn-danger:active,
.btn-danger.active {
  background-color: #942a25 \9;
}
.btn-success {
  background-color: #5bb75b;
  background-image: -moz-linear-gradient(top, #62c462, #51a351);
  background-image: -ms-linear-gradient(top, #62c462, #51a351);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#62c462), to(#51a351));
  background-image: -webkit-linear-gradient(top, #62c462, #51a351);
  background-image: -o-linear-gradient(top, #62c462, #51a351);
  background-image: linear-gradient(top, #62c462, #51a351);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#62c462', endColorstr='#51a351', GradientType=0);
  border-color: #51a351 #51a351 #387038;
  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
  filter: progid:dximagetransform.microsoft.gradient(enabled=false);
}
.btn-success:hover,
.btn-success:active,
.btn-success.active,
.btn-success.disabled,
.btn-success[disabled] {
  background-color: #51a351;
}
.btn-success:active,
.btn-success.active {
  background-color: #408140 \9;
}
.btn-info {
  background-color: #49afcd;
  background-image: -moz-linear-gradient(top, #5bc0de, #2f96b4);
  background-image: -ms-linear-gradient(top, #5bc0de, #2f96b4);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#5bc0de), to(#2f96b4));
  background-image: -webkit-linear-gradient(top, #5bc0de, #2f96b4);
  background-image: -o-linear-gradient(top, #5bc0de, #2f96b4);
  background-image: linear-gradient(top, #5bc0de, #2f96b4);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#5bc0de', endColorstr='#2f96b4', GradientType=0);
  border-color: #2f96b4 #2f96b4 #1f6377;
  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
  filter: progid:dximagetransform.microsoft.gradient(enabled=false);
}
.btn-info:hover,
.btn-info:active,
.btn-info.active,
.btn-info.disabled,
.btn-info[disabled] {
  background-color: #2f96b4;
}
.btn-info:active,
.btn-info.active {
  background-color: #24748c \9;
}
.btn-inverse {
  background-color: #414141;
  background-image: -moz-linear-gradient(top, #555555, #222222);
  background-image: -ms-linear-gradient(top, #555555, #222222);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#555555), to(#222222));
  background-image: -webkit-linear-gradient(top, #555555, #222222);
  background-image: -o-linear-gradient(top, #555555, #222222);
  background-image: linear-gradient(top, #555555, #222222);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#555555', endColorstr='#222222', GradientType=0);
  border-color: #222222 #222222 #000000;
  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
  filter: progid:dximagetransform.microsoft.gradient(enabled=false);
}
.btn-inverse:hover,
.btn-inverse:active,
.btn-inverse.active,
.btn-inverse.disabled,
.btn-inverse[disabled] {
  background-color: #222222;
}
.btn-inverse:active,
.btn-inverse.active {
  background-color: #080808 \9;
}
button.btn,
input[type="submit"].btn {
  *padding-top: 2px;
  *padding-bottom: 2px;
}
button.btn::-moz-focus-inner,
input[type="submit"].btn::-moz-focus-inner {
  padding: 0;
  border: 0;
}
button.btn.btn-large,
input[type="submit"].btn.btn-large {
  *padding-top: 7px;
  *padding-bottom: 7px;
}
button.btn.btn-small,
input[type="submit"].btn.btn-small {
  *padding-top: 3px;
  *padding-bottom: 3px;
}
button.btn.btn-mini,
input[type="submit"].btn.btn-mini {
  *padding-top: 1px;
  *padding-bottom: 1px;
}
</style>
</head>
<body>
<table align="center" id="main_body" style="width:94%;">
    <tr>
        <td width="4" height="16"><img src="{$theme}/skins/images/tb_left.gif" width="4" height="16" border="0" /></td>
		<td background="{$theme}/skins/images/tb_top.gif"><img src="{$theme}/skins/images/tb_top.gif" width="1" height="16" border="0" /></td>
		<td width="4"><img src="{$theme}/skins/images/tb_right.gif" width="3" height="16" border="0" /></td>
    </tr>
	<tr>
        <td width="4" background="{$theme}/skins/images/tb_lt.gif"><img src="{$theme}/skins/images/tb_lt.gif" width="4" height="1" border="0" /></td>
		<td valign="top" style="padding-top:12px; padding-left:13px; padding-right:13px;" bgcolor="#FAFAFA">
		
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29"><div class="navigation"> </td>
        <td bgcolor="#EFEFEF" height="29" align="right" style="padding-right:10px;"><div class="navigation"> </td>
    </tr>
</table>

<div style="padding-top:5px;">
<table width="100%">
    <tr>
        <td width="4"><img src="{$theme}/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
        <td background="{$theme}/skins/images/tl_oo.gif"><img src="{$theme}/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
        <td width="6"><img src="{$theme}/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
    </tr>
    <tr>
        <td background="{$theme}/skins/images/tl_lb.gif"><img src="{$theme}/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
        <td style="padding:5px;" bgcolor="#FFFFFF">
<table width="100%">
    <tr>
        <td  width="267"><img src="{$theme}/skins/images/nav.jpg" width="311" height="99" border="0"></td>
        <td background="{$theme}/skins/images/logo_bg.gif">&nbsp;</td>
        <td width="490"><img src="{$theme}/skins/images/logos.jpg" width="490" height="99" border="0"></td>
    </tr>
</table>
</td>
        <td background="{$theme}/skins/images/tl_rb.gif"><img src="{$theme}/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
    </tr>
    <tr>
        <td><img src="{$theme}/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
        <td background="{$theme}/skins/images/tl_ub.gif"><img src="{$theme}/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
        <td><img src="{$theme}/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
    </tr>
</table>
</div>
<!--MAIN area-->
HTML;

// ********************************************************************************
// Skin FOOTER
// ********************************************************************************
$skin_footer = <<<HTML
	 <!--MAIN area-->
<div style="padding-top:5px; padding-bottom:10px;">
<table width="100%">
    <tr>
<td bgcolor="#EFEFEF" height="40" align="center" style="padding-right:10px;"><div class="navigation"><a href="http://dle-news.ru/" target="_blank">DataLife Engine</a><br />Copyright 2013 &copy; <a href="http://dle-news.ru/" target="_blank">SoftNews Media Group</a>. All rights reserved.</div></td>
    </tr>
</table></div>		
		</td>
		<td width="4" background="{$theme}/skins/images/tb_rt.gif"><img src="{$theme}/skins/images/tb_rt.gif" width="4" height="1" border="0" /></td>
    </tr>
	<tr>
        <td height="16" background="{$theme}/skins/images/tb_lb.gif"></td>
		<td background="{$theme}/skins/images/tb_tb.gif"></td>
		<td background="{$theme}/skins/images/tb_rb.gif"></td>
    </tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function getClientWidth()
{
  return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth;
}
var main_body_size = getClientWidth();

if (main_body_size > 1300) document.getElementById('main_body').style.width = "1200px";

//-->
</script>
</body>
</html>
HTML;

function msgbox($type, $title, $text, $back=FALSE){
global $lang, $theme;

  if($back){
        $back = "<br /><br> <a class=main href=\"$back\">$lang[func_msg]</a>";
  }

  echoheader($type, $title);

echo <<<HTML
<div style="padding-top:5px;padding-bottom:2px;">
<table width="100%">
    <tr>
        <td width="4"><img src="{$theme}/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
        <td background="{$theme}/skins/images/tl_oo.gif"><img src="{$theme}/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
        <td width="6"><img src="{$theme}/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
    </tr>
    <tr>
        <td background="{$theme}/skins/images/tl_lb.gif"><img src="{$theme}/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
        <td style="padding:5px;" bgcolor="#FFFFFF">
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$title}</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td height="100" align="center">{$text} {$back}</td>
    </tr>
</table>
</td>
        <td background="{$theme}/skins/images/tl_rb.gif"><img src="{$theme}/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
    </tr>
    <tr>
        <td><img src="{$theme}/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
        <td background="{$theme}/skins/images/tl_ub.gif"><img src="{$theme}/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
        <td><img src="{$theme}/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
    </tr>
</table>
</div>
HTML;
  echofooter();
exit();
}

$login_panel = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>DataLife Engine</title>
<meta content="text/html; charset={$config['charset']}" http-equiv="content-type" />
<style type="text/css">
html,body{
	width:100%;
	margin:0px;
	padding: 0px;
	background: #F4F3EE;
	font-size: 11px;
	font-family: verdana;
}

#login-box {
	width:447px;
	height:310px;
	margin:10% auto 0 auto;
	background:#FFFFFF;
}

form {
	margin:0px;
	padding: 0px;
}

input,
button {
	color: #000000;
	outline:none;
}

input[type="text"] {
	width:340px;
	background-color: #FFFFFF;
	color: #000000;
	font-size: 18px;
	font-family: verdana;
	font-weight: bold;
	border: none;
	margin-top: 20px;
	margin-left: 60px;
}

input[type="password"] {
	width:340px;
	background-color: #FFFFFF;
	color: #000000;
	font-size: 18px;
	font-family: verdana;
	font-weight: bold;
	border: none;
	margin-top: 20px;
	margin-left: 60px;
}

.error {
	padding-top: 75px;
	padding-left: 27px;
}
.info {
	padding-top: 20px;
}
</style>
</head>
<body>
<form  name="login" action="" method="post"><input type="hidden" name="action" value="dologin">
<div id="login-box">
	<div style="width:447px;height:95px;background: url({$theme}/skins/images/loginheader2.png);"><div class="error">{result}</div></div>
	<div style="width:447px;height:66px;background: url({$theme}/skins/images/loginbox1.png);"><input type="text" name="username"></div>
	<div style="width:447px;height:67px;background: url({$theme}/skins/images/loginbox3.png);"><input type="password" name="password"></div>
	<div style="width:37px;height:82px;float:left;background: url({$theme}/skins/images/loginbox6.png);"></div>
	<div style="width:283px;height:82px;float:left;background: url({$theme}/skins/images/loginbox7.png);"><div class="info">Для обновления скрипта, вам необходимо ввести логин и пароль.</div></div>
	<div style="width:102px;height:82px;float:left;"><input type="image" src="{$theme}/skins/images/loginbox8.png"></div>
	<div style="width:25px;height:82px;float:right;background: url({$theme}/skins/images/loginbox5.png);"></div>
</div></form>
</body>
</html>
HTML;

$is_logged = false;
$result="";

if ($_SESSION['member_name'] != "") {

	$member_name = $db->safesql($_SESSION['member_name']);
	$password = $db->safesql($_SESSION['member_password']);

	if (version_compare($version_id, '4.2', ">")) $password = md5($_SESSION['member_password']);

	if (!defined('USERPREFIX')) {
		define('USERPREFIX', PREFIX);
	}

	$db->query("SELECT * FROM " . USERPREFIX . "_users WHERE name='$member_name' AND password='$password' AND user_group = '1'");

	if ($db->num_rows() > 0){
		$member_id = $db->get_row();
		$is_logged = TRUE;
	}

	$db->free();
}

if ($_POST['action'] == "dologin")
{

	$login_name = $db->safesql($_POST['username']);
	
	$login_password = md5($_POST['password']);

	if (version_compare($version_id, '4.2', ">")) $pass = md5($login_password); else $pass = $login_password;

	if (!defined('USERPREFIX')) {
		define('USERPREFIX', PREFIX);
	}

	$db->query("SELECT * FROM " . USERPREFIX . "_users where name='$login_name' and password='$pass' and user_group = '1'");

	if ($db->num_rows() > 0){
	
			$member_id = $db->get_row();
	
	        $_SESSION['member_name']        = $member_id['name'];
	        $_SESSION['member_password']    = $login_password;
	
	        $is_logged = TRUE;
	} else $result="<font color=red>Неверно введены даннные для входа!</font>";

	$db->free();
}

if(!$is_logged) {
	$login_panel = str_replace("{result}", $result, $login_panel);
	echo $login_panel;
	exit();
}


if(!is_writable(ENGINE_DIR.'/data/config.php')){
	msgbox("info","Информация", "Установите права для записи на файл 'engine/data/config.php' CHMOD 666");
}

if(!is_writable(ENGINE_DIR.'/data/dbconfig.php')){
	msgbox("info","Информация", "Установите права для записи на файл 'engine/data/dbconfig.php' CHMOD 666");
}

if(!is_writable(ENGINE_DIR.'/data/xfields.txt')){
	msgbox("info","Информация", "Установите права для записи на файл 'engine/data/xfields.txt' CHMOD 666");
}

if( !$_SESSION['dle_update'] ) {

	echoheader( "", "" );
echo <<<HTML
<form action="index.php" method="GET">
<div style="padding-top:5px;">
<table width="100%">
    <tr>
        <td width="4"><img src="{$theme}/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
        <td background="{$theme}/skins/images/tl_oo.gif"><img src="{$theme}/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
        <td width="6"><img src="{$theme}/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
    </tr>
    <tr>
        <td background="{$theme}/skins/images/tl_lb.gif"><img src="{$theme}/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
        <td style="padding:5px;" bgcolor="#FFFFFF">
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">Информация</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td style="padding:2px;"><font color="red"><b>Внимание:</b></font> Прежде чем приступить к процедуре обновления скрипта и базы данных, убедитесь что вы создали и сохранили у себя полные бекапы файлов скрипта и базы данных. Процедура обновления вносит необратимые изменения в структуру базы данных, отмена которых в будущем будет невозможна, вернуть в предыдущее состояние базу данных, можно будет только путем восстановления бекапов базы данных. Также во время процедуры обновления скрипт выполняет тяжелые запросы к базе данных, выполнение которых может потребовать продолжительное время, поэтому обновление рекомендуется проводить во время минимальной нагрузки на сервер. Для больших сайтов, имеющие большое количество публикаций, рекомендуется предварительно проводить обновление на локальном компьютере.
		<br /><br />
		Текущая версия скрипта: <b>{$version_id}</b>, обновление будет пошагово произведено до версии: <b>{$dle_version}</b>
		<br /><br />
</td>
    </tr>
    <tr>
        <td style="padding:2px;"><input class="btn btn-success" type=submit value=" Продолжить >> "></td>
    </tr>
</table>
</td>
        <td background="{$theme}/skins/images/tl_rb.gif"><img src="{$theme}/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
    </tr>
    <tr>
        <td><img src="{$theme}/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
        <td background="{$theme}/skins/images/tl_ub.gif"><img src="{$theme}/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
        <td><img src="{$theme}/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
    </tr>
</table>
</div><input type="hidden" name="next" value="start"></form>
HTML;
	echofooter();
	$_SESSION['dle_update'] =1;
	exit();
}
?>