<div class="pheading"><h2>
{title}
</h2></div>
<div class="baseform">
	<table class="tableform">
		<tr>
			<td class="label">
				���: <span class="impot">*</span>
			</td>
			<td>
				<input class="f_input" type="text" value="{cat_title}" name="cat_title">
			</td>
		</tr>
[profiles]
		<tr>
			<td class="label">
				������� ���������[create]<br />������� �����[/create]
			</td>
			<td>{profiles}</td>
		</tr>
[/profiles]
[profile_type]
		<tr>
			<td class="label">
				������� ���������[create]<br />������� �����[/create]
			</td>
			<td>{profile_type}</td>
		</tr>
[/profile_type]
		<tr>
			<td class="label">�������� ���������:</b></td>
			<td>
					<div>{bbcode}</div>
					<textarea name="cat_short_desc" id="cat_short_desc" onclick=setFieldName(this.name) style="width:465px;" rows="10" class="f_textarea" >{cat_short_desc}</textarea>
			</td>
		</tr>
		<tr>
			<td class="label">������:<br /></td>
			<td>
			<input type="file" name="image" class="f_input" /><br /><span class=small>����������� � �������� "jpeg", "png", "gif" ����� �������� �� 500��</span><br />
			[ifdelete]
			<div class="checkbox">{icon} <input type="checkbox" name="delete_icon" id="delete_icon" value="1" />�<label for="delete_icon">������� ������ ������</label></div>
			[/ifdelete]
			</td>
		</tr>
	</table>
	<div class="fieldsubmit">
		<input class="fbutton" type="submit" name="submit" value="���������" />
		<input name="submit" type="hidden" id="submit" value="submit" />
	</div>
</div>