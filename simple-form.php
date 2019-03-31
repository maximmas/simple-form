<?php
/*
Plugin Name: Simple Form
Plugin URI: https://bitbucket.org/maximmas/simple-form
Description: Simple Form WordPress Plugin 
Version: 1.0
Author: Maxim Maslov
Author URI: maximmaslov.ru
Text domain: sf
Domain Path: /languages
*/


require_once ( plugin_dir_path( __FILE__ ) . '/includes/post-registration.php' );


/**
* Подключение скриптов 
*
*/
add_action( 'wp_enqueue_scripts', 'sf_scripts' );
function sf_scripts() {

    wp_enqueue_script( 'sf_magnific', plugin_dir_url( __FILE__ ) . 'assets/libs/magnific-popup/magnific-popup.min.js', array( 'jquery'), null, 'footer' );
    wp_enqueue_script( 'sf_script', plugin_dir_url( __FILE__ ) . 'assets/js/common.js', array( 'jquery', 'sf_magnific' ), null, 'footer' );
    wp_localize_script( 'jquery', 'SF_AjaxHandler', array( 'sf_ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    
    $style_libs = array('sf_magnific_style');
    wp_enqueue_style( 'sf_style', plugin_dir_url( __FILE__ ) . 'assets/css/main.css', $style_libs );
    wp_enqueue_style( 'sf_magnific_style', plugin_dir_url( __FILE__ ) . 'assets/libs/magnific-popup/magnific-popup.min.css' );
   
};


/***
 * Регистрация шорткода 
 * [sf_form]
 * 
 */
add_shortcode('sf_form', 'sf_show_form');
function sf_show_form( $atts ){
    
    $template_name = 'simple_form_template.php'; 
    ob_start();
    include ( plugin_dir_path( __FILE__ ) . 'templates/' . $template_name );
    $section = ob_get_contents();
    ob_end_clean();
    return $section;
};


/**
 * Обработчик Ajax - запросов
 * 
 */

add_action( 'wp_ajax_handler', 'sf_form_handler' );
add_action( 'wp_ajax_nopriv_handler', 'sf_form_handler' );
function sf_form_handler(){

    $name       = isset( $_POST['name'] ) ? esc_html( $_POST['name'] ) : 'no name';
    $email      = isset( $_POST['email'] ) ? esc_html( $_POST['email'] ) : 'no email';
    $message    = isset( $_POST['message'] ) ? esc_html( $_POST['message'] ) : 'no message';

    $data = array(
        'name'      => $name,
        'email'     => $email, 
        'message'   => $message, 
    );
    
    $is_saved = sf_save_data( $data );
    echo(1);
    wp_die();

};

/**
 * Запись данных
 * 
 */
function sf_save_data( $data ){

        $post_data  = array(
            'post_title'    => $data['name'],
            'post_content'  => $data['message'],
            'post_status'   => 'publish',
            'post_author'       => 1,
            'comment_status'    => 'closed',
            'ping_status'       => 'closed',
            'post_type'         => 'client',
            'meta_input'    => array(
                'client_email' => $data['email'],
            ),     
        );
        $post_id = wp_insert_post( $post_data );
        if ( $post_id ){
            wp_set_object_terms( $post_id, 'regular', 'client-type', false );
            return true;
        } else{
            return false;
        };
    }


/**
 * Установка прав для пользовтаелей
 * 
 */
add_action( 'admin_init', 'sf_set_capabilities' );
function sf_set_capabilities() {
      
    $roles = array( 'author', 'editor', 'subscriber', 'contributor', 'administrator' );
    foreach( $roles as $role ){
        $user = get_role( $role );

        $user->add_cap( 'read' );
        $user->add_cap( 'read_client');
        $user->add_cap( 'read_clients' );
        $user->add_cap( 'edit_client' );
        $user->add_cap( 'edit_clients' );
        $user->add_cap( 'edit_others_clients' );
        $user->add_cap( 'edit_published_clients' );
        $user->add_cap( 'publish_clients' );

        if ( 'administrator' !== $role ){
            $user->remove_cap( 'delete_client' );
            $user->remove_cap( 'delete_clients' );
            $user->remove_cap( 'delete_private_clients' );
            $user->remove_cap( 'delete_published_clients' );
            $user->remove_cap( 'delete_published_client' );
            $user->remove_cap( 'delete_others_clients' );
        } else {
            $user->add_cap( 'delete_client' );
            $user->add_cap( 'delete_clients' );
            $user->add_cap( 'delete_private_clients' );
            $user->add_cap( 'delete_published_clients' );
            $user->add_cap( 'delete_published_client' );
            $user->add_cap( 'delete_others_clients' );
        };
    };
}

