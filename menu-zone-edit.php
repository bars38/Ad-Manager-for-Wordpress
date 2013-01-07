<?php
global $wpdb;

// Set additional WHERE
$zone = $wpdb->get_row( $wpdb->prepare( "SELECT name, ads FROM $wpdb->adm_zones WHERE zid = %s", $_GET['edit'] ) );
$ads = explode( ';', $zone->ads );
if( count( $ads ) ) {
	$i = 0;
	$where = '';
	foreach( $ads as $ad ) {
		if( $i == 0 )
			$where .= 'id = '.$wpdb->escape( $ad );
		else
			$where .= ' OR id = '.$wpdb->escape( $ad );
		$i++;
	}
	// We just wrap this
	$thewhere = 'AND ( '.$where.' )';
} else $thewhere = '';

// Set ORDER BY
$order = isset( $_GET['orderby'] )? $_GET['orderby'] : '';
$orderby = 'ORDER BY ';
switch( $order ) {
	case 'title':
		$orderby .= 'name';
		$titledesc = true;
		$titleordered = true;
		break;
	case 'titledesc':
		$orderby .= 'name DESC';
		$titleordered = true;
		break;
	case 'date':
		$orderby .= 'added';
		$datedesc = true;
		$dateordered = true;
		break;
	case 'datedesc':
		$orderby .= 'added DESC';
		$dateordered = true;
		break;
	case 'views':
		$orderby .= 'views DESC';
		$viewsasc = true;
		$viewsordered = true;
		break;
	case 'viewsasc':
		$orderby .= 'views ASC';
		$viewsordered = true;
		break;
	case 'clicks':
		$orderby .= 'clicks DESC';
		$clicksasc = true;
		$clicksordered = true;
		break;
	case 'clicksasc':
		$orderby .= 'clicks ASC';
		$clicksordered = true;
		break;
	case 'ctr':
		$orderby .= 'clicks / views DESC';
		$ctrasc = true;
		break;
	case 'ctrasc':
		$orderby .= 'clicks / views ASC';
		$ctrordered = true;
		break;
	case 'iddesc':
		$orderby .= 'id DESC';
		$idordered = true;
		break;
	default:
		$orderby .= 'id';
		$iddesc = true;
		$idordered = true;
}

// Set starting point
$start = isset( $_GET['paged'] )? ( (int) $_GET['paged'] - 1 ) * 20 : '0';

$results = $wpdb->get_results( "SELECT id, name, added, SUM(views) as views, SUM(clicks) as clicks 
	FROM $wpdb->adm_ads, $wpdb->adm_stats 
	WHERE ad = $wpdb->adm_ads.id $thewhere
	GROUP BY $wpdb->adm_ads.id $orderby 
	LIMIT $start,20" );
?>
<div class="wrap">
	<div id="icon-themes" class="icon32"><br></div>
	<h2>Нова РЗ</h2>
	<form action="?page=ad-manager-zones&amp;update=true" method ="post">
		Назва РЗ: <input type="text" name="name" value="<?=$zone->name?>" style="width: 347px; margin: 10px"/>
		<input type="hidden" name="id" value="<?=$_GET['edit']?>">
		<div style="margin-top: 5px"></div>
		
		<table class="wp-list-table widefat fixed posts" cellspacing="0">
			<thead><tr>
				<th scope="col" id="cb" class="manage-column column-cb check-column">
					<input type="checkbox" onclick="toggleCheckbox(this)">
				</th>
				<th scope="col" class="manage-column" style="width: 3.1em">
					<span>ID</span>
				</th>
				<th scope="col" class="manage-column">
					<span>Назва</span>
				</th>
				<th scope="col" class="manage-column" style="width: 9em">
					<span>Додано</span>
				</th>
				<th scope="col" class="manage-column" style="width: 7em">
					<span>Переглядів</span>
				</th>
				<th scope="col" class="manage-column" style="width: 7em">
					<span>Переходів</span>
				</th>
				<th scope="col" class="manage-column" style="width: 5em">
					<span>CTR</span>
				</th>
			</tr></thead>

			<tbody id="ads-table">
				<?php if( count( $results ) ) {
					foreach( $results as $ad ) {
						?>
						<tr>
							<td><input type="checkbox" name="delete[]" value="<?= $ad->id ?>"></td>
							<td><?= $ad->id ?></td>
							<td><?= $ad->name ?></td>
							<td><?= date( 'j. F Y', $ad->added ) ?></td>
							<td><?= $ad->views? $ad->views : 0 ?></td>
							<td><?= $ad->clicks? $ad->clicks : 0 ?></td>
							<td><?= $ad->views? round($ad->clicks / $ad->views * 100, 2).'%' : '0%' ?></td>
							<input type="hidden" name="ads[]" value="<?=$ad->id?>">
						</tr>
						<?php
					}
					echo '<script type="text/javascript" charset="utf-8">noadsyet = false;</script>';
				} else { ?>
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
				<?php } ?>
			</tbody>

			<tfoot><tr>
				<th scope="col" id="cb" class="manage-column column-cb check-column">
					<input type="checkbox" onclick="toggleCheckbox(this)">
				</th>
				<th scope="col" class="manage-column" style="width: 3.1em">
					<span>ID</span>
				</th>
				<th scope="col" class="manage-column">
					<span>Назва</span>
				</th>
				<th scope="col" class="manage-column" style="width: 9em">
					<span>Додано</span>
				</th>
				<th scope="col" class="manage-column" style="width: 7em">
					<span>Перелядів</span>
				</th>
				<th scope="col" class="manage-column" style="width: 7em">
					<span>Переходів</span>
				</th>
				<th scope="col" class="manage-column" style="width: 5em">
					<span>CTR</span>
				</th>
			</tr></tfoot>
		</table>
		<input type="button" value="Додати рекламу" onclick="generatePopup()" class="button" style="margin: 10px 0px">
		<input type="button" name="delete_submit" value="Видалити помічене" class="button" onclick="deleteSelected()"/><br>
		<input type="submit" name="submitted" value="Зберегти" class="button"/>
	</form>
	<script type="text/javascript" charset="utf-8">
		adm_popup_path       = "<?=plugins_url( 'ajax-popup.php?noadsyet=', __FILE__ )?>" + noadsyet;
		adm_search_path      = "<?=plugins_url( 'ajax-search.php?search=', __FILE__ )?>";
		adm_loading_gif_path = "<?=plugins_url( 'loading.gif', __FILE__ )?>";
	</script>
</div>