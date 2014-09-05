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
				<input class="f_input" type="text" value="{cat_title}" id="cat_title{id}" name="cat_title[{id}]">
			</td>
		</tr>
		<tr>
			<td class="label">
				Альтернативное имя:
			</td>
			<td>
				<input class="f_input" type="text" value="{cat_alt_name}" id="cat_alt_name{id}" name="cat_alt_name[{id}]">
			</td>
		</tr>
		<tr>
			<td class="label">
				Основная категория:
			</td>
			<td>{category}<br /><span class=small>Будет выведена внутри указанной категории</span></td>
		</tr>
		<tr>
			<td class="label">Описание категории: <span class="impot">*</span></b></td>
			<td>
					<div>{bbcode}</div>
					<textarea name="cat_short_desc[{id}]" id="cat_short_desc{id}" onclick=setFieldName(this.id) style="width:465px;" rows="10" class="f_textarea" >{cat_short_desc}</textarea>
			</td>
		</tr>
		<tr>
			<td class="label">
				Задействовать профиль[create]<br />Сменить режим[/create]
			</td>
			<td>{profiles}</td>
		</tr>
		<tr>
			<td class="label">
				Мета-название:
			</td>
			<td>
				<input class="f_input" type="text" value="{metatitle}" id="metatitle{id}" name="metatitle[{id}]">
			</td>
		</tr>
		<tr>
			<td class="label">
				Мета-описание:
			</td>
			<td>
				<input class="f_input" type="text" value="{meta_descr}" id="meta_descr{id}" name="meta_descr[{id}]">
			</td>
		</tr>
		<tr>
			<td class="label">
				Ключевые слова:
			</td>
			<td>
				<textarea name="keywords[{id}]" id="keywords{id}" style="width:345px;height:50px;" class="f_textarea">{keywords}</textarea>
			</td>
		</tr>
		<tr>
			<td class="label">
				Установить другой шаблон:
			</td>
			<td>{skinlist}</td>
		</tr>
		<tr>
			<td class="label">
				Закрыта:
			</td>
			<td>{categorystat}</td>
		</tr>
        <tr>
          <td class="label">Порядок сортировки файлов:</td>
          <td>{c_sort}</td>
        </tr>
        <tr>
          <td class="label">Метод сортировки файлов:</td>
          <td>{c_msort}</td>
        </tr>
        <tr>
          <td class="label">Максимальная ширина изображения:</td>
          <td><input value="{width_max}" type="text" size="5" id="width_max{id}" name="width_max[{id}]"><br /><span class=small>Не обязательно. Будут использованы настройки по умолчанию. Используется как ограничение (если запрещено сжатие оригинала) или как значение, на которое будет сжато изображение</span></td>
        </tr>
        <tr>
          <td class="label">Максимальная высота изображения:</td>
          <td><input value="{height_max}" type="text" size="5" id="height_max{id}" name="height_max[{id}]"><br /><span class=small>Не обязательно. Будут использованы настройки по умолчанию. Используется как ограничение (если запрещено сжатие оригинала) или как значение, на которое будет сжато изображение</span></td>
        </tr>
        <tr>
          <td class="label">Ширина изображения на странице с комментариями:</td>
          <td><input value="{com_thumb_max}" type="text" size="8" id="com_thumb_max{id}" name="com_thumb_max[{id}]"><br /><span class=small>Не обязательно. Будут использованы настройки по умолчанию.</span></td>
        </tr>
        <tr>
          <td class="label">Ширина наибольшей из сторон thumb-a:</td>
          <td><input value="{thumb_max}" type="text" size="8" id="thumb_max{id}" name="thumb_max[{id}]"><br /><span class=small>Не обязательно. Будут использованы настройки по умолчанию.</span></td>
        </tr>
        <tr>
          <td class="label">Расширения файлов, разрешённых к загрузке:</td>
          <td><select class="f_input" name="extensions[{id}][]" id="extensions{id}" style="width:100px;height:100px;" multiple>{allowed_extensions}</select><br /><span class=small>Не обязательно. Будут использованы настройки по умолчанию</span></td>
        </tr>
		<tr>
          <td class="label">Коэффициент размера файлов:</td>
          <td><input value="{size_factor}" type="text" size="8" id="size_factor{id}" name="size_factor[{id}]">%<br /><span class=small>Не обязательно. Будут использованы настройки по умолчанию.</span></td>
        </tr>
        <tr>
           <td colspan="2">{admin-tags}</td>
        </tr>
		<tr>
			<td class="label">Иконка:<br /></td>
			<td>
			<input type="file" name="image[{id}]" class="f_input" /><br /><span class=small>Допускаются к загрузке "jpeg", "png", "gif" файлы размером до 500Кб</span><br />
			[ifdelete]
			<div class="checkbox">{icon} <input type="checkbox" name="delete_icon[{id}]" id="delete_icon{id}" value="1" /> <label for="delete_icon{id}">Удалить старую иконку</label></div>
			[/ifdelete]
			</td>
		</tr>
        <tr>
          <td class="label">Размеры максимальной стороны иконки:</td>
          <td><input value="{icon_max_size}" type="text" size="8" id="icon_max_size{id}" name="icon_max_size[{id}]"><br /><span class=small>Иконка будет сжата до указаных размеров</span></td>
        </tr>
         <tr>
          <td class="label">Столбцов подкатегорий:</td>
          <td><input value="{subcats_td}" type="text" size="5" id="subcats_td{id}" name="subcats_td[{id}]"><br /><span class=small>Не обязательно. Будут использованы настройки по умолчанию</span></td>
        </tr>
        <tr>
          <td class="label">Рядов подкатегорий:</td>
          <td><input value="{subcats_tr}" type="text" size="5" id="subcats_tr{id}" name="subcats_tr[{id}]"><br /><span class=small>Не обязательно. Будут использованы настройки по умолчанию</span></td>
        </tr>
        <tr>
          <td class="label">Столбцов файлов:</td>
          <td><input  value="{foto_td}" type="text" size="5" id="foto_td{id}" name="foto_td[{id}]"><br /><span class=small>Не обязательно. Будут использованы настройки по умолчанию</span></td>
        </tr>
        <tr>
          <td class="label">Рядов файлов:</td>
          <td><input value="{foto_tr}" type="text" size="5" id="foto_tr{id}" name="foto_tr[{id}]"><br /><span class=small>Не обязательно. Будут использованы настройки по умолчанию</span></td>
        </tr>
        <tr>
          <td class="label">Главный шаблон вывода категории (category.tpl):</td>
          <td><input class="f_input" type="text" name="maincatskin[{id}]" id="maincatskin{id}" value="{maincatskin}">.tpl<br /><span class=small>Не обязательно. Будет использован шаблон по умолчанию, category.tpl</span></td>
        </tr>
        <tr>
          <td class="label">Шаблон подкатегории внутри категории (short_category.tpl):</td>
          <td><input class="f_input" type="text" name="subcatskin[{id}]" id="subcatskin{id}" value="{subcatskin}">.tpl<br /><span class=small>Не обязательно. Будет использован шаблон по умолчанию, short_category.tpl</span></td>
        </tr>
        <tr>
          <td class="label">Шаблон уменьшенных файлов в категории (short_image.tpl):</td>
          <td><input class="f_input" type="text" name="smallfotoskin[{id}]" id="smallfotoskin{id}" value="{smallfotoskin}">.tpl<br /><span class=small>Не обязательно. Будет использован шаблон по умолчанию, short_image.tpl</span></td>
        </tr>
        <tr>
          <td class="label">Шаблон полного файла (full_image.tpl):</td>
          <td><input class="f_input" type="text" name="bigfotoskin[{id}]" id="bigfotoskin{id}" value="{bigfotoskin}">.tpl<br /><span class=small>Не обязательно. Будет использован шаблон по умолчанию, full_image.tpl</span></td>
        </tr>
        <tr>
          <td class="label">Шаблон загрузки файлов (upload.tpl):</td>
          <td><input class="f_input" type="text" name="uploadskin[{id}]" id="uploadskin{id}" value="{uploadskin}">.tpl<br /><span class=small>Не обязательно. Будет использован шаблон по умолчанию, upload.tpl</span></td>
        </tr>
		<tr>
          <td class="label">Количество дней, через которое нужно удалять загруженные файлы:</td>
          <td><input type="text" size="5" name="exprise_delete[{id}]" id="exprise_delete{id}" value="{exprise_delete}"><br /><span class=small>Не обязательно. Укажите 0 для отключения функции. При указании числа, файлы этой категории будут автоматически удалятся через указаное количество дней после загрузки</span></td>
        </tr>
	</table>
	<div class="fieldsubmit">
		<input class="fbutton" type="submit" name="submit" value="Отправить" />
		<input name="submit" type="hidden" id="submit" value="submit" />
	</div>
</div>