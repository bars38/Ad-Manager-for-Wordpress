<?php
global $wpdb;
$result = $wpdb->get_row( $wpdb->prepare( "SELECT id, name, img, url, code FROM $wpdb->adm_ads WHERE id = %s", $_GET['edit'] ) );

// Prepare stats for current month
$time = date( 'Y-m-01 00:00:00' );
$stat_results = $wpdb->get_results( $wpdb->prepare( 
	"SELECT `day`, `views`, `clicks` FROM $wpdb->adm_stats WHERE `day` >= '$time' AND `ad` = %s GROUP BY DAY(`day`)", 
	$_GET['edit']
) );
if( count( $stat_results ) ) {
	foreach( $stat_results as $day ) {
		$temp = explode( '-', $day->day );
		$this_month[(int) substr( $temp[2], 0, 2 )]['views']  = $day->views;
		$this_month[(int) substr( $temp[2], 0, 2 )]['clicks'] = $day->clicks;
	}
	$month_empty = false;
} else
	$month_empty = true;

// Prepare stats for current year
$time = date( 'Y-01-01 00:00:00' );
$stat_results = $wpdb->get_results( $wpdb->prepare( 
	"SELECT `day`, SUM(`views`) as `views`, SUM(`clicks`) as `clicks` FROM $wpdb->adm_stats WHERE `day` >= '$time' AND `ad` = %s GROUP BY MONTH(`day`)", 
	$_GET['edit']
) );
if( count( $stat_results ) ) {
	foreach( $stat_results as $day ) {
		$temp = explode( '-', $day->day );
		$this_year[(int) $temp[1]]['views']  = $day->views;
		$this_year[(int) $temp[1]]['clicks'] = $day->clicks;
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
	<h2>Менеджер реклами</h2>
	<form action="?page=ad-manager&amp;update=true" method ="post" style="margin-top: 15px; padding: 0 12px; border: 1px solid #d0dfe9; width: 500px; background: #f7fcfe">
		<input type="hidden" name="id" value="<?=$result->id?>" id="id">
		<p>Заповніть форму нижче, щоб створити нову рекламу. Якщо ви завантажите зображення (або оберете одне з Wordpress бібліотеки) натисніть кнопку "Вставити в пост", URL зображення буде вставлено автоматично.</p>
		<p>
			<span style="margin-bottom: 4px; display: block">Тип реклами:</span>
			<input type="radio" name="ad_type" value="img" <?php if( ! $result->code ) echo 'checked="checked"' ?> onclick="jQuery('#image').show();jQuery('#code').hide();" id="img-checkbox"> Зображення&nbsp;&nbsp;&nbsp;
			<input type="radio" name="ad_type" value="code" onclick="jQuery('#image').hide();jQuery('#code').show();" id="code-checkbox" <?php if( $result->code ) echo 'checked="checked"' ?>> HTML-код</p>
		<p>
			Назва: <input type="text" name="name" value="<?=$result->name?>" style="margin: 5px 0 0 51px; width: 347px" />
		</p>
		<p id="image" <?php if( $result->code ) echo 'style="display: none"' ?>>
			Завантажити зображення: <input id="upload-image-button" type="button" value="Оберіть файл" class="button" style="margin: 12px 0 5px 5px"/><br>
			Посилання на зображення: <input id="upload-image" type="text" name="image_url" value="<?=$result->img?>" style="margin: 3px 0 5px 20px; width: 347px" onkeyup="generatePreview()"/><br>
			Посилання на іншу сторінку: <input id="target-url" type="text" name="target_url" value="<?=$result->url?>" style="margin: 3px 0 5px 19px; width: 347px" onkeyup="generatePreview()"/>
		</p>
		
		<p id="code" <?php if( ! $result->code ) echo 'style="display: none"' ?>>
			HTML-код:<br>
			<textarea name="code" id="the-code" rows="8" cols="50" style="width: 439px" onkeyup="generatePreview()"><?=$result->code?></textarea>
		</p>
		
		<p><input type="submit" name="submitted" value="Зберігти" class="button" style="margin: 10px 0px" /> чи <a href="?page=ad-manager">Відмінити</a></p>
	</form>
	<p style="font-weight: bold">Переглянути: <small>(<a style="font-weight: normal" href="#" onclick="generatePreview()">згенерувати</a>)</small></p>
	<div style="margin-top: 5px" id="preview-box"></div>
	<div style="padding: 8px 10px; font-size: 15px; margin-top: 20px; border: 1px solid #d0dfe9; background-image: -ms-linear-gradient(top,#ECF8FE,#f7fcfe);background-image: -moz-linear-gradient(top,#ECF8FE,#f7fcfe);background-image: -o-linear-gradient(top,#ECF8FE,#f7fcfe);background-image: -webkit-gradient(linear,left top,left bottom,from(#ECF8FE),to(#f7fcfe));background-image: -webkit-linear-gradient(top,#ECF8FE,#f7fcfe);background-image: linear-gradient(top,#ECF8FE,#f7fcfe);-moz-box-shadow: inset 0 1px 0 #fff;-webkit-box-shadow: inset 0 1px 0 #fff;box-shadow: inset 0 1px 0 #fff;font-family: Georgia,'Times New Roman','Bitstream Charter',Times,serif;-moz-border-radius-topleft: 3px;-moz-border-radius-topright: 3px;-webkit-border-top-right-radius: 3px;-webkit-border-top-left-radius: 3px;-khtml-border-top-right-radius: 3px;-khtml-border-top-left-radius: 3px;border-top-right-radius: 3px;border-top-left-radius: 3px;width:670px;border-bottom:none">Статистика</div>
	<div style="padding: 0 20px; border: 1px solid #d0dfe9; width: 650px; background: #f7fcfe">
		<h3>Цього місяця</h3>
		<? if( $month_empty ) { echo '<i>Немає даних</i>'; } else { ?>
		<h4 style="margin: 0">Переглядів</h4>
		<div id="this-month-chart-views"><i>Завантажується...</i></div>
		<h4 style="margin: 0">Переходів</h4>
		<div id="this-month-chart-clicks"><i>Завантажується...</i></div>
		<?php } ?>
		<h3>Цього року</h3>
		<? if( $month_empty ) { echo '<i>Немає даних</i>'; } else { ?>
		<h4 style="margin: 0">Переглядів</h4>
		<div id="this-year-chart-views"><i>Завантажується...</i></div>
		<h4 style="margin: 0">Переходів</h4>
		<div id="this-year-chart-clicks"><i>Завантажується...</i></div>
		<?php } ?>
	</div>
	
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript" charset="utf-8">
		generatePreview();
		<?php if( ! $month_empty ): // Ensure that no errors occure because we have no data ?>
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
		<?php endif; ?>
	</script>
</div>