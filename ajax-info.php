<?php 
error_reporting( 0 );
require '../../../wp-config.php';

// Be sure we are allowed to visit the page
current_user_can( 'manage_options' ) or die( 'Ви не повинні бачити цю сторінку, вибачте.' );
adManager::ad_info( $_GET['id'] );
?>