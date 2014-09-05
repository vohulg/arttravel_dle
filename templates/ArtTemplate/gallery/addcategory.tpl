<div class="pheading"><h2>
{title}
</h2></div>
<div class="baseform">
	<table class="tableform">
		<tr>
			<td class="label">
				Имя: <span class="impot">*</span>
			</td>
			<td>
				<input class="f_input" type="text" value="{cat_title}" name="cat_title">
			</td>
		</tr>
[profiles]
		<tr>
			<td class="label">
				Профиль категории[create]<br />Сменить режим[/create]
			</td>
			<td>{profiles}</td>
		</tr>
[/profiles]
[profile_type]
		<tr>
			<td class="label">
				Профиль категории[create]<br />Сменить режим[/create]
			</td>
			<td>{profile_type}</td>
		</tr>
[/profile_type]
		<tr>
			<td class="label">Описание категории:</b></td>
			<td>
					<div>{bbcode}</div>
					<textarea name="cat_short_desc" id="cat_short_desc" onclick=setFieldName(this.name) style="width:465px;" rows="10" class="f_textarea" >{cat_short_desc}</textarea>
			</td>
		</tr>
		<tr>
			<td class="label">Иконка:<br /></td>
			<td>
			<input type="file" name="image" class="f_input" /><br /><span class=small>Допускаются к загрузке "jpeg", "png", "gif" файлы размером до 500Кб</span><br />
			[ifdelete]
			<div class="checkbox">{icon} <input type="checkbox" name="delete_icon" id="delete_icon" value="1" /> <label for="delete_icon">Удалить старую иконку</label></div>
			[/ifdelete]
			</td>
		</tr>
	</table>
	<div class="fieldsubmit">
		<input class="fbutton" type="submit" name="submit" value="Отправить" />
		<input name="submit" type="hidden" id="submit" value="submit" />
	</div>
</div>