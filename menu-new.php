<div class="wrap">
	<div id="icon-themes" class="icon32"><br></div>
	<h2>Нова реклама</h2>
	<form action="?page=ad-manager" method ="post" style="margin-top: 15px; padding: 0 12px; border: 1px solid #d0dfe9; width: 500px; background: #f7fcfe">
		<p>Заповніть форму нижче, щоб створити нову рекламу. Якщо ви завантажите зображення (або оберете одне з Wordpress бібліотеки) натисніть кнопку "Вставити в пост", URL зображення буде вставлено автоматично.</p>
		<p>
			<span style="margin-bottom: 4px; display: block">Тип реклами:</span>
			<input type="radio" name="ad_type" value="img" checked="checked" onclick="jQuery('#image').show();jQuery('#code').hide();" id="img-checkbox"> Зображення&nbsp;&nbsp;&nbsp;
			<input type="radio" name="ad_type" value="code" onclick="jQuery('#image').hide();jQuery('#code').show();" id="code-checkbox"> HTML-код</p>
		<p>
			Назва: <input type="text" name="name" value="" style="margin: 5px 0 0 51px; width: 347px" />
		</p>
		<p id="image">
			Завантажити зображення: <input id="upload-image-button" type="button" value="Оберіть файл" class="button" style="margin: 12px 0 5px 5px"/><br>
			Посилання на зображення: <input id="upload-image" type="text" name="image_url" value="" style="margin: 3px 0 5px 20px; width: 347px" onkeyup="generatePreview()"/><br>
			Посилання на іншу сторінку: <input id="target-url" type="text" name="target_url" value="" style="margin: 3px 0 5px 19px; width: 347px" onkeyup="generatePreview()"/>
		</p>
		
		<p id="code" style="display: none">
			HTML-код:<br>
			<textarea name="code" id="the-code" rows="8" cols="50" style="width: 439px" onkeyup="generatePreview()"></textarea>
		</p>
		
		<p><input type="submit" name="submitted" value="Зберігти" class="button" style="margin: 10px 0px" /> чи <a href="?page=ad-manager">Відмінити</a></p>
	</form>
	<p style="font-weight: bold">Переглянути: <small>(<a style="font-weight: normal" href="#" onclick="generatePreview()">згенерувати</a>)</small></p>
	<div style="margin-top: 5px" id="preview-box"></div>
</div>