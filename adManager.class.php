<?php
/**
 * We put all the functions of the plugin into a class to avoid function
 * name collisions, we call them as adManager::function() then.
 **/
class adManager {
	// Sets up the DB if table doesn't exist
	function db() {
		global $wpdb;
		
		if( ! isset( $wpdb->ads ) ) {
			$wpdb->adm_ads   = $wpdb->prefix.'adm_ads';
			$wpdb->adm_stats = $wpdb->prefix.'adm_stats';
			$wpdb->adm_zones = $wpdb->prefix.'adm_zones';
		}

		if( $wpdb->get_var("show tables like '$wpdb->adm_ads'") != $wpdb->adm_ads ) {
			$sql_ads = "CREATE TABLE $wpdb->adm_ads (
					  id mediumint(9) NOT NULL AUTO_INCREMENT,
					  added int(10) NOT NULL,
					  name tinytext NOT NULL,
					  url tinytext,
					  img tinytext,
					  code text,
					  UNIQUE KEY id (id)
					);";
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql_ads );
		}

		if( $wpdb->get_var("show tables like '$wpdb->adm_stats'") != $wpdb->adm_stats ) {
			$sql_stats = "CREATE TABLE $wpdb->adm_stats (
					  sid bigint(20) NOT NULL AUTO_INCREMENT,
					  ad mediumint(9) NOT NULL,
					  day tinytext NOT NULL,
					  views bigint(20) DEFAULT 0,
					  clicks bigint(20) DEFAULT 0,
					  UNIQUE KEY id (sid)
					);";
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql_stats );
		}
		
		if( $wpdb->get_var("show tables like '$wpdb->adm_zones'") != $wpdb->adm_stats ) {
			$sql_stats = "CREATE TABLE $wpdb->adm_zones (
					  zid bigint(9) NOT NULL AUTO_INCREMENT,
					  added int(10) NOT NULL,
					  name tinytext NOT NULL,
					  ads text,
					  UNIQUE KEY id (zid)
					);";
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql_stats );
		}

		if( ! get_option( 'adm_db_version' ) )
			add_option( 'adm_db_version', '1.0' );
	}
	
	// Adds the admin menu to the list
	function add_admin_menu() {
		add_menu_page( 'Ad-Manager', 'Реклама', 'manage_options', 'ad-manager', array( 'adManager', 'admin_menu' ) );
		add_submenu_page( 'ad-manager', 'Створити рекламу', 'Нова реклама', 'manage_options', 'ad-manager-new', array( 'adManager', 'admin_menu_new' ) );
		add_submenu_page( 'ad-manager', 'Менеджер РЗ', 'Менеджер РЗ', 'manage_options', 'ad-manager-zones', array( 'adManager', 'admin_menu_zone' ) );
		add_submenu_page( 'ad-manager', 'Створити РЗ', 'Нова РЗ', 'manage_options', 'ad-manager-zone-new', array( 'adManager', 'admin_menu_zone_new' ) );
		add_submenu_page( 'ad-manager', 'Статистика', 'Статистика', 'manage_options', 'ad-manager-stats', array( 'adManager', 'admin_menu_stats' ) );
	}
	
	// Generates the admin menu
	function admin_menu() {
		global $wpdb;
		adManager::db();
		
		if( isset( $_GET['edit'] ) ) {
			require 'menu-edit.php';
			exit;
		}
		
		require 'menu-admin.php';
	}
	
	// Generates the admin menu "New Ad"
	function admin_menu_new() {
		require 'menu-new.php';
	}
	
	// Generates the admin menu "Manage Ad-Zones"
	function admin_menu_zone() {
		global $wpdb;
		adManager::db();
		
		if( isset( $_GET['edit'] ) ) {
			require 'menu-zone-edit.php';
			exit;
		}
		
		require 'menu-zones.php';
	}
	
	// Generates the admin menu "New Ad-Zone"
	function admin_menu_zone_new() {
		require 'menu-zone-new.php';
	}
	
	// Generates the admin menu "Statistics"
	function admin_menu_stats() {
		require 'menu-stats.php';
	}
	
	// Replaces [ad ##] in posts and pages
	function filter_content( $content ) {
		$content = preg_replace_callback(
			'%\[ads((w[0-9]+)|(h[0-9]+)|[\s]+)*\]%',
			create_function( '$match','
				$style = \'style="\';
				if( isset( $match[2] ) && $match[2] )
					$style .= \'max-width: \'.substr( $match[2], 1 ).\'px;\';
				if( isset( $match[3] ) && $match[3] )
					$style .= \'max-height: \'.substr( $match[3], 1 ).\'px;\';
				$style .= \'"\';
				return \'<div class="ad" \'.$style.\'>\'.adManager::random_ad().\'</div>\';
			'),
			$content
		);
		$content = preg_replace_callback(
			'%\[ad ((z?[0-9]+)|(w[0-9]+)|(h[0-9]+)|[\s]+)+\]%',
			create_function( '$match','
				$style = \'style="\';
				if( isset( $match[3] ) && $match[3] )
					$style .= \'max-width: \'.substr( $match[3], 1 ).\'px;\';
				if( isset( $match[4] ) && $match[4] )
					$style .= \'max-height: \'.substr( $match[4], 1 ).\'px;\';
				$style .= \'"\';
				return \'<div class="ad" \'.$style.\'>\'.adManager::specific_ad( $match[2] ).\'</div>\';
			'),
			$content
		);

		return $content;
	}

	// Gets one random ad out of the database
	function random_ad() {
		global $wpdb;
		adManager::db();

		$ad = $wpdb->get_row( "SELECT id, url, img, code FROM $wpdb->adm_ads ORDER BY RAND() LIMIT 1" );

		return adManager::ad_code( $ad );
	}

	// Gets a specific ad out of the database
	function specific_ad( $id ) {
		if( substr( $id, 0, 1 ) == 'z' )
			return adManager::ad_zone( $id );
		
		global $wpdb;
		adManager::db();

		$ad = $wpdb->get_row( "SELECT id, url, img, code FROM $wpdb->adm_ads WHERE id = '".$wpdb->escape( $id )."'" );

		return adManager::ad_code( $ad );
	}
	
	// Gets a random ad out of a specific zone
	function ad_zone( $id ) {
		global $wpdb;
		adManager::db();
		
		$id = $wpdb->prepare( substr( $id, 1 ) );
		$ads_string = $wpdb->get_var(  "SELECT ads FROM $wpdb->adm_zones WHERE zid = '$id'" );
		$ads = explode( ';', $ads_string );
		
		$rand = rand( 0, max( array_keys( $ads ) ) );
		$ad = $wpdb->get_row( "SELECT id, url, img, code FROM $wpdb->adm_ads WHERE id = '".$wpdb->escape( $ads[$rand] )."'" );

		return adManager::ad_code( $ad );
	}
	
	// Get Code of the ad
	function ad_code( $ad ) {
		if( $ad === NULL )
			return '<b>Ad not found</b>';
		
		adManager::update_counter( $ad->id );

		if( $ad->code )
			return $ad->code;
		else
			return '<a href="'.plugins_url( 'track-click.php?out='.urlencode( $ad->url ).'&id='.$ad->id, __FILE__ ).'" style="max-height:inherit;max-width:inherit"><img src="'.$ad->img.'" style="max-height:inherit;max-width:inherit" alt="Ad"></a>';
	}
	
	// Used in the AJAX-Search for Ad-Zones
	function ad_search( $string = '' ) {
		global $wpdb;
		adManager::db();
		
		$string = $wpdb->escape( stripslashes( $string ) );
		$results = $wpdb->get_results( "SELECT $wpdb->adm_ads.id, name, added, SUM(views) as views, SUM(clicks) as clicks 
			FROM $wpdb->adm_ads, $wpdb->adm_stats 
			WHERE ad = $wpdb->adm_ads.id AND ( id = '$string' OR name LIKE '%$string%' )
			GROUP BY $wpdb->adm_ads.id
			ORDER BY added
			LIMIT 20" );
		if( count( $results ) > 0 ) {
			foreach( $results as $ad ) { ?>
				<tr>
					<td class="id"><a href="#" onclick="saveAd(this)"><?= $ad->id ?></a></td>
					<td class="name"><a href="#" onclick="saveAd(this)"><?= $ad->name ?></a></td>
					<td class="added"><?= date( 'j. F Y', $ad->added ) ?></td>
					<input type="hidden" name="views" value="<?=$ad->views?>" class="views">
					<input type="hidden" name="clicks" value="<?=$ad->clicks?>" class="clicks">
				</tr>
			<?php }
		} else echo '<td></td><td>Nothing found.</td><td></td>';
	}

	// Updates the view/click count
	function update_counter( $ad_id, $is_click = false ) {
		global $wpdb;
		adManager::db();

		if( $is_click )
			$what = 'clicks';
		else
			$what = 'views';

		$day = date('Y-m-d 00:00:00');
		if( $wpdb->query( "UPDATE $wpdb->adm_stats SET $what = $what + 1 WHERE ad = '$ad_id' AND `day` = '$day'" ) === 0 )
			$wpdb->insert( $wpdb->adm_stats, array( 'ad' => $ad_id, 'day' => $day, $what => 1 ) );
	}
	
	// Adds functionality to the upload panel
	function admin_upload_scripts() {
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
		wp_register_script( 'ad-manager-upload', plugins_url( 'script-upload.js', __FILE__ ), array( 'jquery', 'media-upload', 'thickbox' ) );
		wp_enqueue_script( 'ad-manager-upload' );
	}
	
	// Adds functionality to the upload panel
	function admin_zone_scripts() {
		wp_enqueue_script( 'thickbox' );
		wp_register_script( 'ad-manager-upload', plugins_url( 'script-zones.js', __FILE__ ), array( 'jquery', 'thickbox' ) );
		wp_enqueue_script( 'ad-manager-upload' );
	}
	
	// Adds the style to the upload panel
	function admin_styles() {
		wp_enqueue_style( 'thickbox' );
	}

	// Register the Widget
	function register_widget() {
		require 'adManagerWidget.class.php';
		register_widget( 'adManagerWidget' );
	}
}