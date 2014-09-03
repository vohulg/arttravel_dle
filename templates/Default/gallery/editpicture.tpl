[button]
<div class="pheading"><h2>
{actiontitle}
</h2></div>
<div class="baseform">
	<table class="tableform">
	{fotolist}
	</table>
	
	<div class="fieldsubmit">
		<input class="fbutton" type="submit" name="submit" value="Отправить" />
		<input name="submit" type="hidden" id="submit" value="submit" />
		[admin]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input name="send_notice" id="send_notice" value="1" type="checkbox" /> <label for="send_notice">Отправить пользователю уведомление о редактировании</label>[/admin]
	</div>
</div>
[/button]
[foto]
  		<tr>
			<td width="170">
				<div class="maincont">[fullimageurl]{thumb}[/fullimageurl]</div>
				<span class="baseinfo radial">Файл: {name}</span>
				<span class="baseinfo radial">Разрешение: {width}x{height}</span>
				<span class="baseinfo radial">Размер: {size}</span>
				<span class="baseinfo radial">Разместил: {user_name}</span>
				<span class="baseinfo radial">Просмотров: {views}</span>
			</td>
			<td>
	<table class="tableform">
		<tr>
		  <td class="label">Название:</td>
		  <td><input type="text" name="title[{id}]" id="title[{id}]" value="{title}" class="f_input" /></td>
		</tr>
[admin-autor]
      <tr>
        <td class="label">Автор:</td>
        <td><input type="text" name="autor[{id}]" id="autor[{id}]" value="{autor}" class="f_input" /></td>
      </tr>
[/admin-autor]
[admin]
      <tr>
        <td class="label">Alt-атрибут изображения:</td>
        <td><input type="text" name="alt_title[{id}]" id="alt_title[{id}]" value="{alt_title}" class="f_input" /></td>
      </tr>
        <tr>
          <td class="label">Alt-имя:</td>
          <td><input type="text" name="alt_name[{id}]" id="alt_name[{id}]" value="{alt-name}" maxlength="40" class="f_input" /></td>
        </tr>
        <tr>
          <td class="label">Символьный код:</td>
			  <td><input type="text" name="symbol[{id}]" id="symbol[{id}]" value="{symbol}" maxlength="10" class="f_input" /></td>
        </tr>
[/admin]
        <tr>
          <td class="label">Категория: <span class="impot">*</span></td>
          <td>{category}</td>
        </tr>
		<tr>
			<td class="label">Описание:</td>
			<td>
				<div>{bbcode}</div>
				<textarea name="short_story[{id}]" id="short_story{id}" onclick=setFieldName(this.id) style="width:365px;" rows="10" class="f_textarea" >{short-story}</textarea>
			</td>
		</tr>
[tags]
        <tr>
          <td class="label">Теги:</td>
          <td><input type="text" name="tags[{id}]" value="{tags}" class="gallery_tags f_input" /></td><!-- ВНИМАНИЕ! Класс gallery_tags в input-тах указан для работы модуля автозаполнения. Не удаляйте его, если хотите, чтобы модуль работал -->
        </tr>
[/tags]
		<tr>
			<td class="label">Предпросмотр:<br /></td>
			<td>
			<input type="file" name="preview[{id}]" class="f_input" /><br /><span class=small>Допускаются к загрузке "jpeg", "jpg", "png", "gif" файлы</span>
			</td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td>{admin-tags}</td>
		</tr>
[admin-reason]
        <tr>
          <td class="label">Причина редактирования:</td>
          <td><input type="text" name="edit_reason[{id}]" value="{edit_reason}" class="f_input" /></td>
        </tr>
[/admin-reason]
	</table>
			</td>
		 </tr>
[/foto]