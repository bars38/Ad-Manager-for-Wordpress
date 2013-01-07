<?php 
error_reporting( 0 );
require '../../../wp-config.php';

// Be sure we are allowed to visit the page
current_user_can( 'manage_options' ) or wp_die( 'Ви не повинні бачити цю сторінку, вибачте.' );
adManager::ad_search( $_GET['search'] );