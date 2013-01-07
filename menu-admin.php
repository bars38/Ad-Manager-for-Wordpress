<?php $message = ''; $error = array();
if( isset( $_POST['submitted'] ) ) {
	if( $_POST['ad_type'] == 'img' ) {
		$name = stripslashes( $_POST['name'] );
		$img  = stripslashes( $_POST['image_url'] );
		$url  = stripslashes( $_POST['target_url'] );
		if( ! $name )
			$error[] = 'Name';
		if( ! $img )
			$error[] = 'Image';
		if( ! $url )
			$error[] = 'Target Link';
		
		if( isset( $_GET['update'] ) )
			$wpdb->update( $wpdb->adm_ads, array( 'added' => time(), 'name' => $name, 'url' => $url, 'img' => $img ), array( 'id' => $_POST['id'] ) );
		else {
			$wpdb->insert( $wpdb->adm_ads, array( 'added' => time(), 'name' => $name, 'url' => $url, 'img' => $img ) );
			$wpdb->insert( $wpdb->adm_stats, array( 'ad' => $wpdb->insert_id, 'day' => date('Y-m-d 00:00:00') ) );
		}
	} else {
		$name = stripslashes( $_POST['name'] );
		$code = stripslashes( $_POST['code'] );
		if( ! $name )
			$error[] = 'Name';
		if( ! $code )
			$error[] = 'HTML-Snippet';
		
		if( isset( $_GET['update'] ) )
			$wpdb->update( $wpdb->adm_ads, array( 'added' => time(), 'name' => $name, 'code' => $code ), array( 'id' => $_POST['id'] ) );
		else {
			$wpdb->insert( $wpdb->adm_ads, array( 'added' => time(), 'name' => $name, 'code' => $code ) );
			$wpdb->insert( $wpdb->adm_stats, array( 'ad' => $wpdb->insert_id, 'day' => date('Y-m-d 00:00:00') ) );
		}
	}
	$i = 0;
	if( count( $error ) > 0 ) {
		$message = '<div id="message" class="error below-h2"><p>Увага: Ваша реклама не має: ';
		foreach( $error as $item ) {
			if( $i === 0 )
				$message .= $item;
			else
				$message .= ', '.$item;
			$i++;
		}
		$message .= '</p></div>';
	} else
		$message = '<div id="message" class="updated below-h2"><p>Реклама збережена.</p></div>';
}

if( isset( $_POST['delete_submit'] ) ) {
	if( isset( $_POST['delete'] ) && count( $_POST['delete'] ) ) {
		$sql_ads   = "DELETE FROM $wpdb->adm_ads WHERE ";
		$sql_stats = "DELETE FROM $wpdb->adm_stats WHERE ";
		$i = 0;
		foreach( $_POST['delete'] as $del ) {
			if( $i === 0 ) {
				$sql_ads   .= "id = '".$wpdb->escape( $del )."'";
				$sql_stats .= "ad = '".$wpdb->escape( $del )."'";
			} else {
				$sql_ads   .= " OR id = '".$wpdb->escape( $del )."'";
				$sql_stats .= " OR ad = '".$wpdb->escape( $del )."'";
			}
			$i++;
		}

		$wpdb->query( $sql_ads );
		$wpdb->query( $sql_stats );
		$message = '<div id="message" class="updated below-h2"><p>Реклама видалена.</p></div>';
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
$start = isset( $_GET['paged'] )? ( (int) $_GET['paged'] - 1 ) * 20 : '0';
$results = $wpdb->get_results( "SELECT $wpdb->adm_ads.id, name, added, SUM(views) as views, SUM(clicks) as clicks 
	FROM $wpdb->adm_ads, $wpdb->adm_stats 
	WHERE ad = $wpdb->adm_ads.id 
	GROUP BY $wpdb->adm_ads.id $orderby 
	LIMIT $start,20" );
?>
<div class="wrap">
	<div id="icon-themes" class="icon32"><br></div>
	<h2>Менеджер реклами <a href="?page=ad-manager-new" class="add-new-h2">Нова реклама</a></h2>
	
	<?=$message?>
	
	<p>Це список всіх оголошень, що є у вас. Ви можете змінити або переглянути його статистику, натиснувши на його назву чи ID.</p>
	<p>Підказка: Якщо ви використовуєте для оголошень HTML-код, кількість переходів та CTR (співвідношення кліків до показів) завжди буде дорівнювати нулю!</p>

	<form action="?page=ad-manager" method="post">
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
				
		<tbody>
			<?php if( count( $results ) ) {
				foreach( $results as $ad ) {
					?>
					<tr>
						<td><input type="checkbox" name="delete[]" value="<?= $ad->id ?>"></td>
						<td><a href="?page=ad-manager&amp;edit=<?= $ad->id ?>"><?= $ad->id ?></a></td>
						<td><a href="?page=ad-manager&amp;edit=<?= $ad->id ?>"><?= $ad->name ?></a></td>
						<td><?= date( 'j. F Y', $ad->added ) ?></td>
						<td><?= $ad->views? $ad->views : 0 ?></td>
						<td><?= $ad->clicks? $ad->clicks : 0 ?></td>
						<td><?= $ad->views? round($ad->clicks / $ad->views * 100, 2).'%' : '0%' ?></td>
					</tr>
					<?php
				}
			} else { ?>
				<tr>
					<td></td>
					<td></td>
					<td>Реклама відсутня. Давайте <a href="?page=ad-manager-new">створимо її</a>!</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			<?php } ?>
		</tbody>
			
		<tfoot><tr>
			<th scope="col" class="manage-column column-cb check-column">
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
	<?php
	$count   = $wpdb->get_var( "SELECT COUNT(id) FROM $wpdb->adm_ads" );
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