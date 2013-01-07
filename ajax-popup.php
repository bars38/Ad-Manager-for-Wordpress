<?php 
error_reporting( 0 );
require '../../../wp-config.php';

// Be sure we are allowed to visit the page
current_user_can( 'manage_options' ) or wp_die( 'Ви не повинні бачити цю сторінку, вибачте.' );
?>
<input type="text" name="search" value="" id="search-term" style="width: 535px; margin: 15px 1px 10px 1px" onkeyup="ajaxResults()">
<input type="button" name="submitted" value="Пошук" class="button" onclick="ajaxResults()">
<table class="wp-list-table widefat fixed posts" cellspacing="0" style="margin-bottom: 5px">
	<thead><tr>
		<th scope="col" class="manage-column" style="width: 3.1em">
			<span>ID</span>
		</th>
		<th scope="col" class="manage-column">
			<span>Назва</span>
		</th>
		<th scope="col" class="manage-column" style="width: 9em">
			<span>Додано</span>
		</th>
	</tr></thead>

	<tbody id="popup-ads-table">
		<?php adManager::ad_search(); ?>
	</tbody>

	<tfoot><tr>
		<th scope="col" class="manage-column" style="width: 3.1em">
			<span>ID</span>
		</th>
		<th scope="col" class="manage-column">
			<span>Назва</span>
		</th>
		<th scope="col" class="manage-column" style="width: 9em">
			<span>Додано</span>
		</th>
	</tr></tfoot>
</table>
Шукайте за назвою або ID реклами, потім натисніть на неї, щоб додати її до списоку. (Підказка: Показані тільки перші 20 результатів, впорядковані по даті додавання)