<div class="wrap">
	<div id="icon-themes" class="icon32"><br></div>
	<h2>Нова РЗ</h2>
	<form action="?page=ad-manager-zones" method ="post">
		
		<p></p>
		<p>Назва РЗ: <input type="text" name="name" value="" style="width: 347px; margin: 10px"/></p>
		
		<table class="wp-list-table widefat fixed posts" cellspacing="0">
			<thead><tr>
				<th scope="col" id="cb" class="manage-column column-cb check-column">
					<input type="checkbox" onclick="toggleCheckbox(this)">
				</th>
				<th scope="col" class="manage-column sortable <?=isset($idordered)?'sorted':''?> <?=isset($iddesc)?'asc':'desc'?>" style="width: 3.1em">
					<a href="?page=ad-manager&amp;orderby=id<?=isset($iddesc)?'desc':''?>">
						<span>ID</span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th scope="col" class="manage-column sortable <?=isset($titleordered)?'sorted':''?> <?=isset($titledesc)?'asc':'desc'?>">
					<a href="?page=ad-manager&amp;orderby=title<?=isset($titledesc)?'desc':''?>">
						<span>Назва</span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th scope="col" class="manage-column sortable <?=isset($dateordered)?'sorted':''?> <?=isset($datedesc)?'asc':'desc'?>" style="width: 9em">
					<a href="?page=ad-manager&amp;orderby=date<?=isset($datedesc)?'desc':''?>">
						<span>Додано</span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th scope="col" class="manage-column sortable <?=isset($viewsordered)?'sorted':''?> <?=isset($viewsasc)?'desc':'asc'?> " style="width: 7em">
					<a href="?page=ad-manager&amp;orderby=views<?=isset($viewsasc)?'asc':''?>">
						<span>Переглядів</span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th scope="col" class="manage-column sortable <?=isset($clicksordered)?'sorted':''?> <?=isset($clicksasc)?'desc':'asc'?>" style="width: 7em">
					<a href="?page=ad-manager&amp;orderby=clicks<?=isset($clicksasc)?'asc':''?>">
						<span>Переходів</span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th scope="col" class="manage-column sortable <?=isset($ctrordered)?'sorted':''?> <?=isset($ctrasc)?'desc':'asc'?>" style="width: 5em">
					<a href="?page=ad-manager&amp;orderby=ctr<?=isset($ctrasc)?'asc':''?>">
						<span>CTR</span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
			</tr></thead>

			<tbody id="ads-table">
				<tr>
					<td></td>
					<td></td>
					<td>Реклама відсутня. Давайте <a href="#" onclick="generatePopup()">створимо її</a>!</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<script type="text/javascript" charset="utf-8">
					noadsyet = true;
				</script>
			</tbody>

			<tfoot><tr>
				<th scope="col" id="cb" class="manage-column column-cb check-column">
					<input type="checkbox" onclick="toggleCheckbox(this)">
				</th>
				<th scope="col" class="manage-column sortable <?=isset($idordered)?'sorted':''?> <?=isset($iddesc)?'asc':'desc'?>" style="width: 3.1em">
					<a href="?page=ad-manager&amp;orderby=id<?=isset($iddesc)?'desc':''?>">
						<span>ID</span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th scope="col" class="manage-column sortable <?=isset($titleordered)?'sorted':''?> <?=isset($titledesc)?'asc':'desc'?>">
					<a href="?page=ad-manager&amp;orderby=title<?=isset($titledesc)?'desc':''?>">
						<span>Назва</span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th scope="col" class="manage-column sortable <?=isset($dateordered)?'sorted':''?> <?=isset($datedesc)?'asc':'desc'?>" style="width: 9em">
					<a href="?page=ad-manager&amp;orderby=date<?=isset($datedesc)?'desc':''?>">
						<span>Додано</span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th scope="col" class="manage-column sortable <?=isset($viewsordered)?'sorted':''?> <?=isset($viewsasc)?'desc':'asc'?> " style="width: 7em">
					<a href="?page=ad-manager&amp;orderby=views<?=isset($viewsasc)?'asc':''?>">
						<span>Переглядів</span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th scope="col" class="manage-column sortable <?=isset($clicksordered)?'sorted':''?> <?=isset($clicksasc)?'desc':'asc'?>" style="width: 7em">
					<a href="?page=ad-manager&amp;orderby=clicks<?=isset($clicksasc)?'asc':''?>">
						<span>Переходів</span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th scope="col" class="manage-column sortable <?=isset($ctrordered)?'sorted':''?> <?=isset($ctrasc)?'desc':'asc'?>" style="width: 5em">
					<a href="?page=ad-manager&amp;orderby=ctr<?=isset($ctrasc)?'asc':''?>">
						<span>CTR</span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
			</tr></tfoot>
		</table>
		<p>
			<input type="button" value="Додати рекламу" onclick="generatePopup()" class="button">
			<input type="submit" name="submitted" value="Зберігти" class="button" style="margin: 10px 0px" />
			чи <a href="?page=ad-manager-zones">Відмінити</a>
		</p>
	</form>
	<script type="text/javascript" charset="utf-8">
		adm_popup_path       = "<?=plugins_url( 'ajax-popup.php?noadsyet=', __FILE__ )?>" + noadsyet;
		adm_search_path      = "<?=plugins_url( 'ajax-search.php?search=', __FILE__ )?>";
		adm_loading_gif_path = "<?=plugins_url( 'loading.gif', __FILE__ )?>";
	</script>
</div>