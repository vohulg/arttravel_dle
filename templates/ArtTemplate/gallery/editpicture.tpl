[button]
<div class="pheading"><h2>
{actiontitle}
</h2></div>
<div class="baseform">
	<table class="tableform">
	{fotolist}
	</table>
	
	<div class="fieldsubmit">
		<input class="fbutton" type="submit" name="submit" value="���������" />
		<input name="submit" type="hidden" id="submit" value="submit" />
		[admin]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input name="send_notice" id="send_notice" value="1" type="checkbox" /> <label for="send_notice">��������� ������������ ����������� � ��������������</label>[/admin]
	</div>
</div>
[/button]
[foto]
  		<tr>
			<td width="170">
				<div class="maincont">[fullimageurl]{thumb}[/fullimageurl]</div>
				<span class="baseinfo radial">����: {name}</span>
				<span class="baseinfo radial">����������: {width}x{height}</span>
				<span class="baseinfo radial">������: {size}</span>
				<span class="baseinfo radial">���������: {user_name}</span>
				<span class="baseinfo radial">����������: {views}</span>
			</td>
			<td>
	<table class="tableform">
		<tr>
		  <td class="label">��������:</td>
		  <td><input type="text" name="title[{id}]" id="title[{id}]" value="{title}" class="f_input" /></td>
		</tr>
[admin-autor]
      <tr>
        <td class="label">�����:</td>
        <td><input type="text" name="autor[{id}]" id="autor[{id}]" value="{autor}" class="f_input" /></td>
      </tr>
[/admin-autor]
[admin]
      <tr>
        <td class="label">Alt-������� �����������:</td>
        <td><input type="text" name="alt_title[{id}]" id="alt_title[{id}]" value="{alt_title}" class="f_input" /></td>
      </tr>
        <tr>
          <td class="label">Alt-���:</td>
          <td><input type="text" name="alt_name[{id}]" id="alt_name[{id}]" value="{alt-name}" maxlength="40" class="f_input" /></td>
        </tr>
        <tr>
          <td class="label">���������� ���:</td>
			  <td><input type="text" name="symbol[{id}]" id="symbol[{id}]" value="{symbol}" maxlength="10" class="f_input" /></td>
        </tr>
[/admin]
        <tr>
          <td class="label">���������: <span class="impot">*</span></td>
          <td>{category}</td>
        </tr>
		<tr>
			<td class="label">��������:</td>
			<td>
				<div>{bbcode}</div>
				<textarea name="short_story[{id}]" id="short_story{id}" onclick=setFieldName(this.id) style="width:365px;" rows="10" class="f_textarea" >{short-story}</textarea>
			</td>
		</tr>
[tags]
        <tr>
          <td class="label">����:</td>
          <td><input type="text" name="tags[{id}]" value="{tags}" class="gallery_tags f_input" /></td><!-- ��������! ����� gallery_tags � input-��� ������ ��� ������ ������ ��������������. �� �������� ���, ���� ������, ����� ������ ������� -->
        </tr>
[/tags]
		<tr>
			<td class="label">������������:<br /></td>
			<td>
			<input type="file" name="preview[{id}]" class="f_input" /><br /><span class=small>����������� � �������� "jpeg", "jpg", "png", "gif" �����</span>
			</td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td>{admin-tags}</td>
		</tr>
[admin-reason]
        <tr>
          <td class="label">������� ��������������:</td>
          <td><input type="text" name="edit_reason[{id}]" value="{edit_reason}" class="f_input" /></td>
        </tr>
[/admin-reason]
	</table>
			</td>
		 </tr>
[/foto]