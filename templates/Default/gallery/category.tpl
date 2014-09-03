[images]<div class="sortn dpad"><div class="sortn">{sort}</div></div>[/images]
<div class="base fullstory">
	<div class="dpad">
		<h3 class="btl">{cattitle}</h3>
		[in_category]<div class="bhinfo">
		[not-group=5]
			<ul class="isicons reset">
				<li>[edit]<img src="{THEME}/dleimages/editstore.png" title="Редактировать" alt="Редактировать" />[/edit]</li>
			</ul>
		[/not-group]
			<span class="baseinfo radial">
				Автор: [author-link]{author}[/author-link] от {created} (обновлена: {updated}), файлов: {images}, подкатегорий: {subcats} [locked]<b>Категория закрыта</b>[/locked] [disupload]<b>Загрузка в категорию временно закрыта</b>[/disupload]
			</span>
		</div><div><div style="float:right;">{moderators}</div>
[description]
		<div class="maincont">
		{description}
		</div>
[/description]</div>[/in_category]
[categories]
		<h4 class="btl">Подкатегории</h4>
		<div class="maincont">
			<table>{categories}</table>
		</div>
		<div class="storenumber">{category_pages}</div>
[/categories]
[images]
		[categories]<h4 class="btl">Файлы категории</h4>[/categories]
		<div class="maincont">
			[massactions]
			<table width="100%">{imageslist}</table>
			{massactions}[/massactions]
		</div>
		<div class="storenumber">{pages}</div>
[/images]
[nothing]
<div class="maincont">
	<div align="center"><b>В данной категории файлов не найдено!</b></div>
</div>
[/nothing]
	</div>
	<div class="mlink">
		<span class="argmore">[create]<b>Создать категорию</b>[/create]</span>
		<span class="argmore">[upload]<b>Загрузить</b>[/upload]</span>
		<span class="argmore">[comments]<b>Комментарии</b>[/comments]</span>
		<span class="argmore">[foto]<b>Последнее</b>[/foto]</span>
	</div>
</div>