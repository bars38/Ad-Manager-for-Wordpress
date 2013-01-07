<?php
error_reporting( 0 );
require '../../../wp-config.php';

adManager::update_counter( $_GET['id'], true );
header( 'Location: '.$_GET['out'] );