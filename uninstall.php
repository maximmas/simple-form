<?php

if( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit ();

/**
 * Удаление записей из БД
 */
$args = array(
    'post_type' => 'client', 
    'posts_per_page' => -1,
);

$clients = get_posts( $args );
foreach( $clients as $client ){
  wp_delete_post( $client->ID );
};




