<?php

/**
 * Регистрация типа записи
 * 
 */

add_action( 'init', 'sf_post_registration' );
function sf_post_registration() {	
	
	$labels = array(
		'name'								=> 'Clients',
		'menu_name'						=> 'Clients',
		'singular_name'				=> 'Client',
		'search_items'				=> 'Search Client',
		'name_admin_bar'			=> 'Clients',
		'all_items'						=> 'All Clients',
		'add_new'							=> 'Add Client',
		'edit_item'						=> 'Edit Client',
    'add_new_item'				=> 'Add New Client',
    'view_item'						=> 'View Client',
		'not_found'						=> 'Not found',
		'not_found_in_trash'	=>'No Clients in the trash'
	); 
		
  $args = array(
  	'labels'							=>$labels,
    'public'							=> true,
		'query_var'						=> true,
		'publicly_queryable'	=> true,
    'show_ui'							=> true,
		'show_in_menu'				=> true,
		'exclude_from_search'	=> false,
		'show_in_nav_menus'		=> true,
    'show_in_admin_bar'		=> true,
		'menu_position'				=> 5,
		'menu_icon'						=> 'dashicons-groups',
    'hierarchical'				=> true,
    'rewrite'							=> array( 'slug' => 'client', 'with_front' => false ), 
		'taxonomies'					=> array( 'client-type' ),
		'has_archive'					=> true,
		'supports'				=> array( 'title', 'editor' ),
		'capability_type' => array( 'client','clients' ),
		'map_meta_cap' => true
	); 

	register_post_type( 'client' , $args );

};


/**
 * Регистрация таксономии
 * 
 */
add_action( 'init', 'sf_taxonomy_registration' );
function sf_taxonomy_registration(){
	
	$labels_types = array(
		'name'							=> 'Clients types',
		'singular_label'		=> 'Type',
		'singular_name'			=> 'Clients type',
		'search_items'			=> 'Search Clients types',
		'all_items'					=> 'All Clients types',
		'parent_item'				=> 'Parent type',
		'parent_item_colon'	=> 'Parent type:',
		'edit_item'					=> 'Edit Clients type',
		'update_item'				=> 'Update Clients type',
		'add_new_item'			=> 'Add Clients type',
		'new_item_name'			=> 'New type',
		'menu_name'					=> 'Clients types'
	);
	
	$args_types = array (
		'labels'							=> $labels_types,
		'public'							=> true,
		'show_ui'							=> true,
		'show_in_menu'				=> true,
		'show_in_nav_menus'		=> true,
		'hierarchical'				=> true,
		'publicly_queryable'	=> false,
		'exclude_from_search'	=> true,
		'show_in_admin_bar'		=> true,
		'map_meta_cap'				=> true,
		'rewrite'							=> array( 'slug' => 'client-type', 'with_front' => false ),
		'query_var'						=> true,
		'meta_box_cb'					=> 'post_categories_meta_box'
	);	

	register_taxonomy( 'client-type', 'client', $args_types );		

};


/**
 * Регистрация терминов в таксономии
 * 
 */
add_action( 'init', 'sf_terms_taxonomy_insert' );
function sf_terms_taxonomy_insert(){
	
	wp_insert_term(
		'Regular client',
		'client-type',
		array(
			'description' => 'This is a regular client',
			'slug'        => 'regular'
		)
	);

	wp_insert_term(
		'Key client',
		'client-type',
		array(
			'description' => 'This is a key client. It has a high priority.',
			'slug'        => 'key'
		)
	);
	
}


/**
 * Регистрация метабоксов
 * 
 */
add_action( 'add_meta_boxes', 'sf_client_metaboxes' );
function sf_client_metaboxes() {
	add_meta_box( 'show_client_meta', 'Additional Client fields', 'sf_show_client_meta', 'client', 'advanced', 'high' );
};

function sf_show_client_meta() {
	
	global $post;
	$data['sf_client_nonce']	= wp_create_nonce( plugin_basename(__FILE__) );
	$data['client_email']			= get_post_meta( $post->ID, 'client_email', true );
	
	include_once( plugin_dir_path( __FILE__ )."../templates/post_metabox_template.php" );		
};


/**
 * Запись метабоксов
 * 
 */
add_action( 'save_post', 'sf_save_client_meta', 1, 2 ); 
function sf_save_client_meta( $post_id, $post ) {

	if ( array_key_exists( 'sf_client_nonce', $_POST ) ){
		if ( !wp_verify_nonce( $_POST['sf_client_nonce'], plugin_basename(__FILE__) )) { 	return;	};
	};
			
	if ( !current_user_can( 'edit_post', $post->ID ) || 'revision' == $post->post_type  ){ return;	};

	$meta_key		= 'client_email';
	$meta_value = ( array_key_exists( $meta_key, $_POST ) ) ? $_POST[$meta_key] : '';

	if( get_post_meta( $post->ID, $meta_key, false ) ) {
			update_post_meta( $post->ID, $meta_key, $meta_value );
		} else {
			add_post_meta( $post->ID, $meta_key, $meta_value );
		};
	if( !$meta_value ) delete_post_meta( $post->ID, $meta_key );
	
};
