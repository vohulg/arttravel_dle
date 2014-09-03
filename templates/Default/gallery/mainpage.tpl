<div class="base dpad">
<form action="" name="searchform" method="post">
<input type="hidden" name="do" value="gallery" /><input type="hidden" name="act" value="15" /><input type="hidden" name="subaction" value="search" />
      <ul class="searchbar reset">
        <li class="lfield">
          <input id="gallery_story" name="story" value="Поиск в галерее..." onblur="if(this.value=='') this.value='Поиск в галерее...';" onfocus="if(this.value=='Поиск в галерее...') this.value='';" type="text" />
        </li>
        <li class="lbtn"><input title="Найти" alt="Найти" type="image" src="{THEME}/images/spacer.gif" /></li>
      </ul>
  </form>
<div class="storenumber">&nbsp;</div>
</div>
<div class="base fullstory">
  <div class="dpad">
    <h3 class="btl">Категории галереи</h3>
    <div class="maincont">
      [nocats]<div align="center"><b>Категорий не найдено!</b></div>[/nocats]
      <table>{categories}</table>
    </div>
    <div class="storenumber">{pages}</div>
  </div>
  <div class="mlink">
    <span class="argmore">[create]<b>Создать категорию</b>[/create]</span>
    <span class="argmore">[upload]<b>Загрузить</b>[/upload]</span>
    <span class="argmore">[moderate]<b>Модерировать</b>[/moderate]</span>
    <span class="argmore">[comments]<b>Комментарии</b>[/comments]</span>
    <span class="argmore">[foto]<b>Последнее</b>[/foto]</span>
  </div>
</div>
<div class="pheading"><h2>Статистика галереи:</h2></div>
<div class="basecont statistics">
  <ul class="lcol reset">
    <li><h5 class="blue">Файлы:</h5></li>
    <li>Общее кол-во файлов: <b class="blue">{foto}</b></li>
    <li>Из них добавлено сегодня: <b class="blue">{day_foto}</b></li>
    <li>Ожидает модерации: <b class="blue">{foto_moder}</b></li>
	<li>Скачано пользователями: <b class="blue">{downloads}</b></li>
  </ul>
  <ul class="lcol reset">
    <li><h5 class="blue">Категории:</h5></li>
    <li>Общее кол-во категорий: <b class="blue">{countcats}</b></li>
    <li>Из них добавлено на неделе: <b class="blue">{week_categories}</b></li>
  </ul>
  <ul class="lcol reset">
    <li><h5 class="blue">Комментарии:</h5></li>
    <li>Общее кол-во комментариев: <b class="blue">{comments}</b></li>
    <li>Из них добавлено сегодня: <b class="blue">{day_comments}</b></li>
  </ul>
</div>
<div class="basecont">
  <div class="pheading">
    <h3 class="heading">Лучшие пользователи</h3>
    {gallery_authors categories="0" subcats="0" marker=", " aviable="global" limit="10" cache="yes"}
  </div>
</div>
<div class="scrollblock">
  <div class="dpad">
    <h3 class="btl">Новые файлы</h3>
    <div class="maincont">
      <p>{galery_foto_tag action="date" categories="0" subcats="0" template="short_image" aviable="global" start="0" vertical="0" horizontal="0" cache="yes" search="0"}</p>
    </div>
  </div>
<div class="mlink"> &nbsp; </div>
</div>
<div class="base shortstory">
  <div class="dpad">
    <h3 class="btl">Случайные файлы</h3>
    <div class="maincont">
      <table width="100%">{galery_foto_tag action="random" categories="0" subcats="0" template="short_image" aviable="global" start="0" vertical="1" horizontal="4" cache="yes" search="5000"}</table>
    </div>
  </div>
<div class="mlink"> &nbsp; </div>
</div>
<div class="base shortstory">
  <div class="dpad">
    <h3 class="btl">Ключевые слова</h3>
    <div class="maincont">
      {galery_tags limit="40" cache="yes"}
    </div>
  </div>
<div class="mlink"> &nbsp; </div>
</div>
<div class="basecont">
  <div class="pheading">
    <h3 class="heading">Каталог файлов</h3>
    {symbols}
  </div>
</div>

<!-- Вы можете удалить ссылку ниже только если у вас приобретена расширенная лицензия. В ином случае вы можете изменить внешний вид ссылки, перемещать её по странице, но не имеете права её удаления или скрытия в теги, препятсвующие индексации. В случае нарушения лицензия может быть отозвана без права продления! Данный комментарий может быть удалён -->
<div align="center" style="padding:0px;font-size:8px;color:#666666;" class="slink"><a href="http://inker.wonderfullife.ru/" target="_blank">Powered by TWS Gallery</a></div>