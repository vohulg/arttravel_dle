<div class="base fullstory">
	<div class="dpad">
		<h3 class="btl">{title}</h3>
		<div class="bhinfo">
		[not-group=5]
			<ul class="isicons reset">
				<li>[edit]<img src="{THEME}/dleimages/editstore.png" title="Редактировать" alt="Редактировать" />[/edit]</li>
			</ul>
		[/not-group]
			<span class="baseinfo radial">
				Автор: [author-link]<b>{author}</b>[/author-link] от {created} [author-mail]Написать автору[/author-mail] {moderation}
			</span>
			<div class="ratebox"><div class="rate">{rating}</div></div>
		</div>
		<div class="maincont">
		<h3 class="btl" style="float:left;">[prev-link]Назад[/prev-link]</h3>
		<h3 class="btl" style="float:right;">[next-link]Вперёд[/next-link]</h3>
		<div align="center">[fullfotourl]{comm-thumb}[/fullfotourl]</div>
		<div class="clr"></div>
		<div class="small" align="center">[download]Скачать (Уже {download-num} загрузок)[isfoto] ({width}x{height}, {size})[/isfoto][/download] | [whois_view]Смотрели файл[/whois_view] | [isfoto]<a href="#" onclick="$('#link').toggle();return false;">Ссылки на файл</a>[/isfoto]</div>
[description]
		<div class="clr"></div>
		<div>{description}</div>
[/description]
		<div class="clr"></div>
		[edit-reason]<p class="editdate"><br /><i>Файл отредактировал: <b>{editor}</b> - {updated}
		<br />Причина: {edit-reason}</i></p>[/edit-reason]
   	</div>
[isfoto]<div class="dpad" id="link" style="display:none;">
		<h3 class="btl">Постоянные ссылки на изображение</h3>
		<div class="bhinfo">
		Ссылки на эту страницу с изображением и комментариями (уменьшенная превью):
		</div>
		<div class="maincont">
		HTML ссылка для вставки на страницу вашего сайта:<br /><br />
{html_thumb_code}<br /><br />
BBcode ссылка для вставки картинки в форуме:<br /><br />
{bb_thumb_code}
	   	</div>
		<div class="bhinfo">
		Ссылки на эту страницу с изображением и комментариями (увеличенная превью):
		</div>
		<div class="maincont">
HTML ссылка для вставки на страницу вашего сайта:<br /><br />
{html_comm_code}<br /><br />
BBcode ссылка для вставки картинки в форуме:<br /><br />
{bb_comm_code}
	   	</div>
		<div class="bhinfo">
		Ссылки на ориганал изображения:
		</div>
		<div class="maincont">
HTML ссылка для вставки на страницу вашего сайта:<br /><br />
{html_original_code}<br /><br />
BBcode ссылка для вставки картинки в форуме:<br /><br />
{bb_original_code}
	   	</div>
</div>[/isfoto]
	</div>
	<div class="mlink">
		<span class="argback"><a href="javascript:history.go(-1)"><b>Вернуться</b></a></span>
		<span class="argviews"><span title="Просмотров: {views}"><b>{views}</b></span></span>
		<span class="argcoms">
		[com-link]<span title="Комментариев: {comments-num}"><b>{comments-num}</b></span>[/com-link]</span>
		<div class="mlarrow">&nbsp;</div>
		<p class="lcol argcat">Категория: {link-category}</p>
	</div>
[search]
<div class="dpad"><p><br /><i>Вы искали: <b>{search}</b></i><br /></p></div>
[/search]
[carousel]
<style type="text/css" media="all">
	.list_carousel {
		background:  #D5D5D5 url("{THEME}/gallimages/bg_silver.jpg") 50% 0% no-repeat;
		margin:30px auto;
		width: 681px;
		height:220px;
		border: 1px solid #ccc;
		position:relative;
	}
	.list_carousel ul {
		margin:0 0 0 31px;
		padding: 0;
		list-style: none;
		display: block;
	}
	.list_carousel li {
		position: relative;
		color: #fff;
		text-align: center;
		background-color: #EAEAEA;
		border:7px solid #fff;
		width: 150px;
		min-height: 140px;
		max-height:155px;
		padding: 0;
		margin:26px 21px;
		display: block;
		float: left;
		box-shadow: 0px 0px 5px #637682;
	}
	.list_carousel ul li div{
		position:relative;
		float:left;
		width:150px;
		height:100%;
		max-height:30px;;
		overflow:hidden;
		background: url("{THEME}/gallimages/highlight.png") 0px 1px repeat-x #EAEAEA;
		color:#2b2b2b;
		text-shadow: #FFFFFF 0px 1px 0px;
		box-shadow: 0px 0px 5px #637682;
		font: 12px/16px "Arial", "Helvetica", sans-serif;
		line-height: 13px;
		padding:2px 0;
	}
	.list_carousel ul li img{
		opacity: 1;
	}
	.list_carousel ul li a:hover img {
		box-shadow: 0px 0px 5px #637682;
		opacity: 0.5;
	}
	.loading {
		display:none;
	}
	.loading_show {
		width:60px;
		height:60px;
		display: block;
		background:  url("{THEME}/gallimages/load_bg.png") no-repeat;
		position: absolute;
		top:70px;
		left:310px;
		opacity: 0.8;
	}
	.loading_show b {
		width:32px;
		height:32px;
		display: block;
		position:absolute;
		top:13px;
		left:13px;
	}
	.list_carousel.responsive {
		width: auto;
		margin-left: 0;
	}
	.clearfix {
		float: none;
		clear: both;
	}
	.list_carousel .prev, .list_carousel .next {
		margin-left: 10px;
		width:15px;
		height:21px;			
		display:block;				
		text-indent:-999em;
		background: transparent url('{THEME}/gallimages/carousel_control.png') no-repeat 0 0;
		position:absolute;
		top:90px;				
	}
	.list_carousel .prev {
		background-position:0 0;
		left:5px;
	}
	.list_carousel .prev:hover {
		left:4px;
	}			
	.list_carousel .next {
		background-position: -18px 0;
		right:15px;
	}
	.list_carousel .next:hover {
		right:14px;
	}			
	.timer {
		background-color: #999;
		height: 6px;
		width: 0px;
	}
</style>
  <div class="list_carousel">
  	<ul id="{carousel-id}"></ul>
	<div class="clearfix"></div>
	<a id="{carousel-id}prev" class="prev" href="#">&lt;</a>
    <span id="{carousel-id}loading" class="loading"> <b><img src="{THEME}/gallimages/loading2.gif"  /></b></span>
	<a id="{carousel-id}next" class="next" href="#">&gt;</a>
  </div>
[/carousel]
	[group=5]
	<div class="clr berrors" style="margin: 0;">
		Уважаемый посетитель, Вы зашли на сайт как незарегистрированный пользователь.<br />
		Мы рекомендуем Вам <a href="/index.php?do=register">зарегистрироваться</a> либо войти на сайт под своим именем.
	</div>
	[/group]
</div>
<div class="pheading">
	<h2 class="lcol">[subscribe]<img src="{THEME}/gallimages/subscribe.png" title="Подписка на комментарии" alt="Подписка на комментарии" />[/subscribe] Комментарии:</h2>
</div>
{comments}
<br />
<div class="pheading">
	<a class="addcombtn" href="#" onclick="$('#addcform').toggle();return false;"><b>Оставить комментарий</b></a>
	<div class="clr"></div>
</div>
{addcomments}
{navigation}