<?php
global $wpdb;
adManager::db();

// Prepare results for current month
$time = date( 'Y-m-01 00:00:00' );
$results = $wpdb->get_results( "SELECT `day`, `views`, `clicks` FROM $wpdb->adm_stats WHERE `day` >= '$time' GROUP BY DAY(`day`)" );
if( count( $results ) ) {
	foreach( $results as $result ) {
		$temp = explode( '-', $result->day );
		$this_month[(int) substr( $temp[2], 0, 2 )]['views']  = $result->views;
		$this_month[(int) substr( $temp[2], 0, 2 )]['clicks'] = $result->clicks;
	}
	$month_empty = false;
} else
	$month_empty = true;

// Prepare results for current year
$time = date( 'Y-01-01 00:00:00' );
$results = $wpdb->get_results( "SELECT `day`, SUM(`views`) as `views`, SUM(`clicks`) as `clicks` FROM $wpdb->adm_stats WHERE `day` >= '$time' GROUP BY MONTH(`day`)" );
if( count( $results ) ) {
	foreach( $results as $result ) {
		$temp = explode( '-', $result->day );
		$this_year[(int) $temp[1]]['views']  = $result->views;
		$this_year[(int) $temp[1]]['clicks'] = $result->clicks;
	}
	$year_empty = false;
} else
	$year_empty = true;

$month_names = array(
	1  => 'Січ',
	2  => 'Лют',
	3  => 'Бер',
	4  => 'Кві',
	5  => 'Тра',
	6  => 'Чер',
	7  => 'Лип',
	8  => 'Сер',
	9  => 'Вер',
	10 => 'Жов',
	11 => 'Лис',
	12 => 'Груд',
);
?>
<div class="wrap">
	<div id="icon-themes" class="icon32"><br></div>
	<h2>Статистика</h2>
	<div style="margin-top: 15px; padding: 0 20px; border: 1px solid #d0dfe9; width: 650px; background: #f7fcfe">
		<p style="padding-top: 5px">Нижче ви можете побачити статистику до всієї вашої реклами. Якщо вихочете побачити статистику певної реклами, перейдіть у "<a href="?page=ad-manager">Менеджер реклами</a>" та натисніть на назву реклами чи ID.
		<h3>Цього місяця</h3>
		<h4 style="margin: 0">Переглядів</h4>
		<div id="this-month-chart-views"><? if( $month_empty ) echo '<i>Немає даних</i>'; else echo '<i>Завантажується...</i>'; ?></div>
		<h4 style="margin: 0">Переходів</h4>
		<div id="this-month-chart-clicks"><? if( $month_empty ) echo '<i>Немає даних</i>'; else echo '<i>Завантажується...</i>'; ?></div>
		<h3>Цього року</h3>
		<h4 style="margin: 0">Переглядів</h4>
		<div id="this-year-chart-views"><? if( $year_empty ) echo '<i>Немає даних</i>'; else echo '<i>Завантажується...</i>'; ?></div>
		<h4 style="margin: 0">Переходів</h4>
		<div id="this-year-chart-clicks"><? if( $month_empty ) echo '<i>Немає даних</i>'; else echo '<i>Завантажується...</i>'; ?></div>
	</div>
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript">
		chartOptions = {
			width: 640, 
			height: 200,
			chartArea : {
				left: 30,
				top: 15,
				width: 600,
				height: 140
			}, 
			legend: 'none',
			pointSize: 5,
			colors:['#21759B'],
			backgroundColor: '#f7fcfe'
		};
		// This month: viwes
		google.load("visualization", "1", {packages:["corechart"]});
		google.setOnLoadCallback(drawChartMonthViews);
		function drawChartMonthViews() {
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'День');
			data.addColumn('number', 'Переглядів');
			data.addRows(<?=max( array_keys( $this_month ) ) - min( array_keys( $this_month ) ) + 1?>);
			<?php
			$count = 0;
			for( $i = min( array_keys( $this_month ) ); $i <= max( array_keys( $this_month ) ); $i++ ) {
				$views  = isset( $this_month[$i] )? $this_month[$i]['views']  : '0';
				
				echo 'data.setValue('.$count.', 0, "'.$i.'");';
				echo 'data.setValue('.$count.', 1, '.$views.');';
				$count ++;
			}
			?>

			var chart = new google.visualization.LineChart(document.getElementById('this-month-chart-views'));
			chart.draw(data, chartOptions);
		}
		// This month: clicks
		google.setOnLoadCallback(drawChartMonthClicks);
		function drawChartMonthClicks() {
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'День');
			data.addColumn('number', 'Переходів');
			data.addRows(<?=max( array_keys( $this_month ) ) - min( array_keys( $this_month ) ) + 1?>);
			<?php
			$count = 0;
			for( $i = min( array_keys( $this_month ) ); $i <= max( array_keys( $this_month ) ); $i++ ) {
				$clicks = isset( $this_month[$i] )? $this_month[$i]['clicks'] : '0';
				
				echo 'data.setValue('.$count.', 0, "'.$i.'");';
				echo 'data.setValue('.$count.', 1, '.$clicks.');';
				$count ++;
			}
			?>

			var chart = new google.visualization.LineChart(document.getElementById('this-month-chart-clicks'));
			chart.draw(data, chartOptions);
		}
		// This year: views
		google.setOnLoadCallback(drawChartYearViews);
		function drawChartYearViews() {
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'Місяць');
			data.addColumn('number', 'Переглядів');
			data.addRows(<?=max( array_keys( $this_year ) ) - min( array_keys( $this_year ) ) + 1?>);
			<?php
			$count = 0;
			for( $i = min( array_keys( $this_year ) ); $i <= max( array_keys( $this_year ) ); $i++ ) {
				$views  = isset( $this_year[$i] )? $this_year[$i]['views']  : '0';
				
				echo 'data.setValue('.$count.', 0, "'.$month_names[$i].'");';
				echo 'data.setValue('.$count.', 1, '.$views.');';
				$count ++;
			}
			?>

			var chart = new google.visualization.LineChart(document.getElementById('this-year-chart-views'));
			chart.draw(data, chartOptions);
		}
		// This year: clicks
		google.setOnLoadCallback(drawChartYearClicks);
		function drawChartYearClicks() {
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'Місяць');
			data.addColumn('number', 'Переходів');
			data.addRows(<?=max( array_keys( $this_year ) ) - min( array_keys( $this_year ) ) + 1?>);
			<?php
			$count = 0;
			for( $i = min( array_keys( $this_year ) ); $i <= max( array_keys( $this_year ) ); $i++ ) {
				$clicks = isset( $this_year[$i] )? $this_year[$i]['clicks'] : '0';
				
				echo 'data.setValue('.$count.', 0, "'.$month_names[$i].'");';
				echo 'data.setValue('.$count.', 1, '.$clicks.');';
				$count ++;
			}
			?>

			var chart = new google.visualization.LineChart(document.getElementById('this-year-chart-clicks'));
			chart.draw(data, chartOptions);
		}
	</script>
</div>