<div class="base dpad">
<form action="" name="searchform" method="post">
<input type="hidden" name="do" value="gallery" /><input type="hidden" name="act" value="15" /><input type="hidden" name="subaction" value="search" />
      <ul class="searchbar reset">
        <li class="lfield">
          <input id="gallery_story" name="story" value="����� � �������..." onblur="if(this.value=='') this.value='����� � �������...';" onfocus="if(this.value=='����� � �������...') this.value='';" type="text" />
        </li>
        <li class="lbtn"><input title="�����" alt="�����" type="image" src="{THEME}/images/spacer.gif" /></li>
      </ul>
  </form>
<div class="storenumber">&nbsp;</div>
</div>
<div class="base fullstory">
  <div class="dpad">
    <h3 class="btl">��������� �������</h3>
    <div class="maincont">
      [nocats]<div align="center"><b>��������� �� �������!</b></div>[/nocats]
      <table>{categories}</table>
    </div>
    <div class="storenumber">{pages}</div>
  </div>
  <div class="mlink">
    <span class="argmore">[create]<b>������� ���������</b>[/create]</span>
    <span class="argmore">[upload]<b>���������</b>[/upload]</span>
    <span class="argmore">[moderate]<b>������������</b>[/moderate]</span>
    <span class="argmore">[comments]<b>�����������</b>[/comments]</span>
    <span class="argmore">[foto]<b>���������</b>[/foto]</span>
  </div>
</div>
<div class="pheading"><h2>���������� �������:</h2></div>
<div class="basecont statistics">
  <ul class="lcol reset">
    <li><h5 class="blue">�����:</h5></li>
    <li>����� ���-�� ������: <b class="blue">{foto}</b></li>
    <li>�� ��� ��������� �������: <b class="blue">{day_foto}</b></li>
    <li>������� ���������: <b class="blue">{foto_moder}</b></li>
	<li>������� ��������������: <b class="blue">{downloads}</b></li>
  </ul>
  <ul class="lcol reset">
    <li><h5 class="blue">���������:</h5></li>
    <li>����� ���-�� ���������: <b class="blue">{countcats}</b></li>
    <li>�� ��� ��������� �� ������: <b class="blue">{week_categories}</b></li>
  </ul>
  <ul class="lcol reset">
    <li><h5 class="blue">�����������:</h5></li>
    <li>����� ���-�� ������������: <b class="blue">{comments}</b></li>
    <li>�� ��� ��������� �������: <b class="blue">{day_comments}</b></li>
  </ul>
</div>
<div class="basecont">
  <div class="pheading">
    <h3 class="heading">������ ������������</h3>
    {gallery_authors categories="0" subcats="0" marker=", " aviable="global" limit="10" cache="yes"}
  </div>
</div>
<div class="scrollblock">
  <div class="dpad">
    <h3 class="btl">����� �����</h3>
    <div class="maincont">
      <p>{galery_foto_tag action="date" categories="0" subcats="0" template="short_image" aviable="global" start="0" vertical="0" horizontal="0" cache="yes" search="0"}</p>
    </div>
  </div>
<div class="mlink"> &nbsp; </div>
</div>
<div class="base shortstory">
  <div class="dpad">
    <h3 class="btl">��������� �����</h3>
    <div class="maincont">
      <table width="100%">{galery_foto_tag action="random" categories="0" subcats="0" template="short_image" aviable="global" start="0" vertical="1" horizontal="4" cache="yes" search="5000"}</table>
    </div>
  </div>
<div class="mlink"> &nbsp; </div>
</div>
<div class="base shortstory">
  <div class="dpad">
    <h3 class="btl">�������� �����</h3>
    <div class="maincont">
      {galery_tags limit="40" cache="yes"}
    </div>
  </div>
<div class="mlink"> &nbsp; </div>
</div>
<div class="basecont">
  <div class="pheading">
    <h3 class="heading">������� ������</h3>
    {symbols}
  </div>
</div>

<!-- �� ������ ������� ������ ���� ������ ���� � ��� ����������� ����������� ��������. � ���� ������ �� ������ �������� ������� ��� ������, ���������� � �� ��������, �� �� ������ ����� � �������� ��� ������� � ����, ������������� ����������. � ������ ��������� �������� ����� ���� �������� ��� ����� ���������! ������ ����������� ����� ���� ����� -->
<div align="center" style="padding:0px;font-size:8px;color:#666666;" class="slink"><a href="http://inker.wonderfullife.ru/" target="_blank">Powered by TWS Gallery</a></div>