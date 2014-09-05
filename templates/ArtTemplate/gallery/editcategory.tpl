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
				<input class="f_input" type="text" value="{cat_title}" id="cat_title{id}" name="cat_title[{id}]">
			</td>
		</tr>
		<tr>
			<td class="label">
				�������������� ���:
			</td>
			<td>
				<input class="f_input" type="text" value="{cat_alt_name}" id="cat_alt_name{id}" name="cat_alt_name[{id}]">
			</td>
		</tr>
		<tr>
			<td class="label">
				�������� ���������:
			</td>
			<td>{category}<br /><span class=small>����� �������� ������ ��������� ���������</span></td>
		</tr>
		<tr>
			<td class="label">�������� ���������: <span class="impot">*</span></b></td>
			<td>
					<div>{bbcode}</div>
					<textarea name="cat_short_desc[{id}]" id="cat_short_desc{id}" onclick=setFieldName(this.id) style="width:465px;" rows="10" class="f_textarea" >{cat_short_desc}</textarea>
			</td>
		</tr>
		<tr>
			<td class="label">
				������������� �������[create]<br />������� �����[/create]
			</td>
			<td>{profiles}</td>
		</tr>
		<tr>
			<td class="label">
				����-��������:
			</td>
			<td>
				<input class="f_input" type="text" value="{metatitle}" id="metatitle{id}" name="metatitle[{id}]">
			</td>
		</tr>
		<tr>
			<td class="label">
				����-��������:
			</td>
			<td>
				<input class="f_input" type="text" value="{meta_descr}" id="meta_descr{id}" name="meta_descr[{id}]">
			</td>
		</tr>
		<tr>
			<td class="label">
				�������� �����:
			</td>
			<td>
				<textarea name="keywords[{id}]" id="keywords{id}" style="width:345px;height:50px;" class="f_textarea">{keywords}</textarea>
			</td>
		</tr>
		<tr>
			<td class="label">
				���������� ������ ������:
			</td>
			<td>{skinlist}</td>
		</tr>
		<tr>
			<td class="label">
				�������:
			</td>
			<td>{categorystat}</td>
		</tr>
        <tr>
          <td class="label">������� ���������� ������:</td>
          <td>{c_sort}</td>
        </tr>
        <tr>
          <td class="label">����� ���������� ������:</td>
          <td>{c_msort}</td>
        </tr>
        <tr>
          <td class="label">������������ ������ �����������:</td>
          <td><input value="{width_max}" type="text" size="5" id="width_max{id}" name="width_max[{id}]"><br /><span class=small>�� �����������. ����� ������������ ��������� �� ���������. ������������ ��� ����������� (���� ��������� ������ ���������) ��� ��� ��������, �� ������� ����� ����� �����������</span></td>
        </tr>
        <tr>
          <td class="label">������������ ������ �����������:</td>
          <td><input value="{height_max}" type="text" size="5" id="height_max{id}" name="height_max[{id}]"><br /><span class=small>�� �����������. ����� ������������ ��������� �� ���������. ������������ ��� ����������� (���� ��������� ������ ���������) ��� ��� ��������, �� ������� ����� ����� �����������</span></td>
        </tr>
        <tr>
          <td class="label">������ ����������� �� �������� � �������������:</td>
          <td><input value="{com_thumb_max}" type="text" size="8" id="com_thumb_max{id}" name="com_thumb_max[{id}]"><br /><span class=small>�� �����������. ����� ������������ ��������� �� ���������.</span></td>
        </tr>
        <tr>
          <td class="label">������ ���������� �� ������ thumb-a:</td>
          <td><input value="{thumb_max}" type="text" size="8" id="thumb_max{id}" name="thumb_max[{id}]"><br /><span class=small>�� �����������. ����� ������������ ��������� �� ���������.</span></td>
        </tr>
        <tr>
          <td class="label">���������� ������, ����������� � ��������:</td>
          <td><select class="f_input" name="extensions[{id}][]" id="extensions{id}" style="width:100px;height:100px;" multiple>{allowed_extensions}</select><br /><span class=small>�� �����������. ����� ������������ ��������� �� ���������</span></td>
        </tr>
		<tr>
          <td class="label">����������� ������� ������:</td>
          <td><input value="{size_factor}" type="text" size="8" id="size_factor{id}" name="size_factor[{id}]">%<br /><span class=small>�� �����������. ����� ������������ ��������� �� ���������.</span></td>
        </tr>
        <tr>
           <td colspan="2">{admin-tags}</td>
        </tr>
		<tr>
			<td class="label">������:<br /></td>
			<td>
			<input type="file" name="image[{id}]" class="f_input" /><br /><span class=small>����������� � �������� "jpeg", "png", "gif" ����� �������� �� 500��</span><br />
			[ifdelete]
			<div class="checkbox">{icon} <input type="checkbox" name="delete_icon[{id}]" id="delete_icon{id}" value="1" />�<label for="delete_icon{id}">������� ������ ������</label></div>
			[/ifdelete]
			</td>
		</tr>
        <tr>
          <td class="label">������� ������������ ������� ������:</td>
          <td><input value="{icon_max_size}" type="text" size="8" id="icon_max_size{id}" name="icon_max_size[{id}]"><br /><span class=small>������ ����� ����� �� �������� ��������</span></td>
        </tr>
         <tr>
          <td class="label">�������� ������������:</td>
          <td><input value="{subcats_td}" type="text" size="5" id="subcats_td{id}" name="subcats_td[{id}]"><br /><span class=small>�� �����������. ����� ������������ ��������� �� ���������</span></td>
        </tr>
        <tr>
          <td class="label">����� ������������:</td>
          <td><input value="{subcats_tr}" type="text" size="5" id="subcats_tr{id}" name="subcats_tr[{id}]"><br /><span class=small>�� �����������. ����� ������������ ��������� �� ���������</span></td>
        </tr>
        <tr>
          <td class="label">�������� ������:</td>
          <td><input  value="{foto_td}" type="text" size="5" id="foto_td{id}" name="foto_td[{id}]"><br /><span class=small>�� �����������. ����� ������������ ��������� �� ���������</span></td>
        </tr>
        <tr>
          <td class="label">����� ������:</td>
          <td><input value="{foto_tr}" type="text" size="5" id="foto_tr{id}" name="foto_tr[{id}]"><br /><span class=small>�� �����������. ����� ������������ ��������� �� ���������</span></td>
        </tr>
        <tr>
          <td class="label">������� ������ ������ ��������� (category.tpl):</td>
          <td><input class="f_input" type="text" name="maincatskin[{id}]" id="maincatskin{id}" value="{maincatskin}">.tpl<br /><span class=small>�� �����������. ����� ����������� ������ �� ���������, category.tpl</span></td>
        </tr>
        <tr>
          <td class="label">������ ������������ ������ ��������� (short_category.tpl):</td>
          <td><input class="f_input" type="text" name="subcatskin[{id}]" id="subcatskin{id}" value="{subcatskin}">.tpl<br /><span class=small>�� �����������. ����� ����������� ������ �� ���������, short_category.tpl</span></td>
        </tr>
        <tr>
          <td class="label">������ ����������� ������ � ��������� (short_image.tpl):</td>
          <td><input class="f_input" type="text" name="smallfotoskin[{id}]" id="smallfotoskin{id}" value="{smallfotoskin}">.tpl<br /><span class=small>�� �����������. ����� ����������� ������ �� ���������, short_image.tpl</span></td>
        </tr>
        <tr>
          <td class="label">������ ������� ����� (full_image.tpl):</td>
          <td><input class="f_input" type="text" name="bigfotoskin[{id}]" id="bigfotoskin{id}" value="{bigfotoskin}">.tpl<br /><span class=small>�� �����������. ����� ����������� ������ �� ���������, full_image.tpl</span></td>
        </tr>
        <tr>
          <td class="label">������ �������� ������ (upload.tpl):</td>
          <td><input class="f_input" type="text" name="uploadskin[{id}]" id="uploadskin{id}" value="{uploadskin}">.tpl<br /><span class=small>�� �����������. ����� ����������� ������ �� ���������, upload.tpl</span></td>
        </tr>
		<tr>
          <td class="label">���������� ����, ����� ������� ����� ������� ����������� �����:</td>
          <td><input type="text" size="5" name="exprise_delete[{id}]" id="exprise_delete{id}" value="{exprise_delete}"><br /><span class=small>�� �����������. ������� 0 ��� ���������� �������. ��� �������� �����, ����� ���� ��������� ����� ������������� �������� ����� �������� ���������� ���� ����� ��������</span></td>
        </tr>
	</table>
	<div class="fieldsubmit">
		<input class="fbutton" type="submit" name="submit" value="���������" />
		<input name="submit" type="hidden" id="submit" value="submit" />
	</div>
</div>