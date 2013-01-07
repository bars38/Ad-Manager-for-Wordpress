<?php $message = ''; $error = array();
if( isset( $_POST['submitted'] ) ) {
	$name = stripslashes( $_POST['name'] );
	$ads  = $_POST['ads'];
	if( ! $name )
		$error[] = 'Name';
	if( count( $ads ) < 1 )
		$error[] = 'Ads';
	
	$ads_string = '';
	$i = 0;
	foreach( $ads as $ad ) {
		if( $i == 0 )
			$ads_string .= $ad;
		else
			$ads_string .= ";".$ad;
		$i++;
	}
	
	if( isset( $_GET['update'] ) )
		$wpdb->update( $wpdb->adm_zones, array( 'name' => $name, 'ads' => $ads_string ), array( 'zid' => $_POST['id'] ) );
	else
		$wpdb->insert( $wpdb->adm_zones, array( 'added' => time(), 'name' => $name, 'ads' => $ads_string ) );
	
	$i = 0;
	if( count( $error ) > 0 ) {
		$message = '<div id="message" class="error below-h2"><p>Увага: Ваша Рекламна Зона (РЗ) не має: ';
		foreach( $error as $item ) {
			if( $i === 0 )
				$message .= $item;
			else
				$message .= ', '.$item;
			$i++;
		}
		$message .= '</p></div>';
	} else
		$message = '<div id="message" class="updated below-h2"><p>РЗ збережена.</p></div>';
}

if( isset( $_POST['delete_submit'] ) ) {
	if( isset( $_POST['delete'] ) && count( $_POST['delete'] ) ) {
		$sql = "DELETE FROM $wpdb->adm_zones WHERE ";
		$i = 0;
		foreach( $_POST['delete'] as $del ) {
			if( $i == 0 )
				$sql .= "zid = '".$wpdb->escape( $del )."'";
			else
				$sql .= " OR zid = '".$wpdb->escape( $del )."'";
			$i++;
		}

		$wpdb->query( $sql );
		$message = '<div id="message" class="updated below-h2"><p>Рекламна Зона видалена.</p></div>';
	}
}
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
	case 'iddesc':
		$orderby .= 'zid DESC';
		$idordered = true;
		break;
	default:
		$orderby .= 'zid';
		$iddesc = true;
		$idordered = true;
}
$start = isset( $_GET['paged'] )? ( (int) $_GET['paged'] - 1 ) * 20 : '0';
$results = $wpdb->get_results( "SELECT zid, name, added
	FROM $wpdb->adm_zones
	$orderby
	LIMIT $start,20" );
?>
<div class="wrap">
	<div id="icon-themes" class="icon32"><br></div>
	<h2>Менеджер Рекламної Зони (РЗ) <a href="?page=ad-manager-zone-new" class="add-new-h2">Нова Рекламна Зона</a></h2>

	<?=$message?>
	
	<p>
		Ви можете використовувати РЗ, якщо ви хочете відображати лише певну кількість реклами чи певного змісту.<br>
		Нижче ви можете бачити список усіх створених РЗ, ви можете редагувати їх натиснувши на назву чи ID:
	</p>

	<form action="?page=ad-manager-zones" method="post">
	<table class="wp-list-table widefat fixed posts" cellspacing="0" style="margin-top: 15px">
		<thead><tr>
			<th scope="col" id="cb" class="manage-column column-cb check-column">
				<input type="checkbox" onclick="toggleCheckbox(this)">
			</th>
			<th scope="col" class="manage-column sortable <?=isset($idordered)?'sorted':''?> <?=isset($iddesc)?'asc':'desc'?>" style="width: 3.1em">
				<a href="?page=ad-manager-zones&amp;orderby=id<?=isset($iddesc)?'desc':''?>">
					<span>ID</span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col" class="manage-column sortable <?=isset($titleordered)?'sorted':''?> <?=isset($titledesc)?'asc':'desc'?>">
				<a href="?page=ad-manager-zones&amp;orderby=title<?=isset($titledesc)?'desc':''?>">
					<span>Назва</span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col" class="manage-column sortable <?=isset($dateordered)?'sorted':''?> <?=isset($datedesc)?'asc':'desc'?>" style="width: 9em">
				<a href="?page=ad-manager-zones&amp;orderby=date<?=isset($datedesc)?'desc':''?>">
					<span>Додано</span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
		</tr></thead>

		<tbody>
			<?php if( count( $results ) ) {
				foreach( $results as $ad ) {
					?>
					<tr>
						<td><input type="checkbox" name="delete[]" value="<?= $ad->zid ?>"></td>
						<td><a href="?page=ad-manager-zones&amp;edit=<?= $ad->zid ?>"><?= $ad->zid ?></a></td>
						<td><a href="?page=ad-manager-zones&amp;edit=<?= $ad->zid ?>"><?= $ad->name ?></a></td>
						<td><?= date( 'j. F Y', $ad->added ) ?></td>
					<?php
				}
			} else { ?>
				<tr>
					<td></td>
					<td></td>
					<td>Рекламні зони відсутні. Давайте <a href="?page=ad-manager-zone-new">створимо їх</a>!</td>
					<td></td>
				</tr>
			<?php } ?>
		</tbody>

		<tfoot><tr>
			<th scope="col" class="manage-column column-cb check-column">
				<input type="checkbox" onclick="toggleCheckbox(this)">
			</th>
			<th scope="col" class="manage-column sortable <?=isset($idordered)?'sorted':''?> <?=isset($iddesc)?'asc':'desc'?>" style="width: 3.1em">
				<a href="?page=ad-manager-zones&amp;orderby=id<?=isset($iddesc)?'desc':''?>">
					<span>ID</span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col" class="manage-column sortable <?=isset($titleordered)?'sorted':''?> <?=isset($titledesc)?'asc':'desc'?>">
				<a href="?page=ad-manager-zones&amp;orderby=title<?=isset($titledesc)?'desc':''?>">
					<span>Назва</span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col" class="manage-column sortable <?=isset($dateordered)?'sorted':''?> <?=isset($datedesc)?'asc':'desc'?>" style="width: 9em">
				<a href="?page=ad-manager-zones&amp;orderby=date<?=isset($datedesc)?'desc':''?>">
					<span>Додано</span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
		</tr></tfoot>
	</table>
	<?php
	$count   = $wpdb->get_var( "SELECT COUNT(zid) FROM $wpdb->adm_zones" );
	$current = isset( $_GET['paged'] )? (int) $_GET['paged'] : '1';
	$last    = ceil( $count / 20 );

	$prevprev = ( $current - 2 > 0 )? $current - 2 : 0;
	$prev     = ( $current - 1 > 0 )? $current - 1 : 0;
	$next     = ( ( $current ) * 20 <= $count )? $current + 1 : 0;
	$nextnext = ( ( $current + 1 ) * 20 <= $count )? $current + 2 : 0;

	$showfirst   = ( $prevprev > 1 )? true : false;
	$firstpoints = ( $prevprev > 2 )? true : false;
	$showlast    = ( $nextnext && $count > $nextnext * 20 )? true : false;
	$lastpoints  = ( $nextnext && $nextnext < $last - 1 )? true : false;
	?>
	<div class="tablenav">
		<?php if( $count ) { ?><input type="submit" name="delete_submit" value="Видалити помічене" class="button" style="margin-top: 5px"><?php } ?>
		<div class="tablenav-pages">
			<?php if( $count > 20 ): ?>
				<span class="displaying-num">Показано <?=$start+1?>–<?=$start+20?> з <?=$count?></span>
				<?php if( $prev ) { ?><a class="next page-numbers" href="?page=ad-manager&amp;paged=<?=$prev?>">&laquo;</a><?php } ?>
				<?php if( $showfirst ) { ?><a class="page-numbers" href="?page=ad-manager&amp;paged=1">1</a><?php } ?>
				<?php if( $firstpoints ) { ?><span class="page-numbers dots">...</span><?php } ?>
				<?php if( $prevprev ) { ?><a class="page-numbers" href="?page=ad-manager&amp;paged=<?=$prevprev?>"><?=$prevprev?></a><?php } ?>
				<?php if( $prev ) { ?><a class="page-numbers" href="?page=ad-manager&amp;paged=<?=$prev?>"><?=$prev?></a><?php } ?>
				<span class="page-numbers current"><?=$current?></span>
				<?php if( $next ) { ?><a class="page-numbers" href="?page=ad-manager&amp;paged=<?=$next?>"><?=$next?></a><?php } ?>
				<?php if( $nextnext ) { ?><a class="page-numbers" href="?page=ad-manager&amp;paged=<?=$nextnext?>"><?=$nextnext?></a><?php } ?>
				<?php if( $lastpoints ) { ?><span class="page-numbers dots">...</span><?php } ?>
				<?php if( $showlast ) { ?><a class="page-numbers" href="?page=ad-manager&amp;paged=<?=$last?>"><?=$last?></a><?php } ?>
				<?php if( $next ) { ?><a class="next page-numbers" href="?page=ad-manager&amp;paged=<?=$next?>">&raquo;</a><?php } ?>
			<?php elseif( $count ): ?>
				<span class="displaying-num"><?=$count?> <?=( $count>1 )? 'штук(і)' : 'штука'?></span>
			<?php endif; ?>

		</div>
	</div>
	</form>
</div>