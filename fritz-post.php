<?php

/**
 * Plugin Name: Fritz Post
 * Description: Fritz Custom Post Example
 * Version:     1.0.0
 * Author:      Robin Furrer
 * Author URI:  https://agentur-fritz.ch
 * License:     Apache License, Version 2.0
 * License URI: https://www.apache.org/licenses/LICENSE-2.0
 * Text Domain: fritz-post
 * Domain Path: /languages
 *
 * @package           create-block
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/*
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function create_fritz_block_init()
{
	register_block_type(__DIR__ . '/build/text');

}
add_action('init', 'create_fritz_block_init');

function fritz_post_enqueue()
{
	wp_enqueue_style(
		'fritz_post_css',
		plugin_dir_url(__FILE__) . 'styles.css'
	);
}

add_action('wp_enqueue_scripts', 'fritz_post_enqueue');
add_action('admin_enqueue_scripts', 'fritz_post_enqueue');

function example() {
	return "true";
}

// rest endpoint preparation, for now just an example
function fritz_post_rest_api_routes()
{
	add_action('rest_api_init', function () {
		register_rest_route('fritz-post/v2', '/example', array(
			'methods' => 'GET',
			'callback' => 'example',
			'permission_callback' => '__return_true'
		)
		);
	});
}

add_action('init', 'fritz_post_rest_api_routes');

function fritz_post_load_textdomain() {
	load_plugin_textdomain( 'fritz-post', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action('init', 'fritz_post_load_textdomain');


$customPosts = array(
    array(
        'id'        => 'fritz-sponsor',
        'label'     => 'Fritz Sponsor',
        'fields'    => array('title', 'thumbnail'),
        'icon'      => 'dashicons-admin-tools',
        'taxonomies'=> array( 'category' ),
        'meta'     => array(
            array(
                'id'       => 'url',
                'title'    => 'Url',
                'position' => 'normal',
                'priority' => 'high',
                'type'     => 'text'
            )
        )
    )
);

function add_custom_post() {
    global $customPosts;
    foreach($customPosts as $customPost) {
        $args = array(
            'public' => true,
            'label'  => $customPost['label'],
            'supports'=>$customPost['fields'],
            'taxonomies'=>$customPost['taxonomies'],
            'menu_icon' =>$customPost['icon'],
            'show_in_rest' => true
        );
        register_post_type($customPost['id'], $args);
    }
}
add_action('init', 'add_custom_post');

function add_custom_post_metas() {
    global $customPosts;
    foreach($customPosts as $customPost) {
        foreach($customPost['meta'] as $meta) {
            add_meta_box(
                $meta['id'],                // ID der Metabox
                $meta['title'],                 // Titel der Metabox
                'display_custom_post_meta_field',    // Callback-Funktion zum Anzeigen des Felds
                $customPost['id'],              // Der Beitragstyp, dem die Metabox hinzugefügt wird
                $meta['position'],              // Position der Metabox ('normal', 'advanced', oder 'side')
                $meta['priority'],                  // Priorität ('high', 'core', 'default', oder 'low')
                array('type' => $meta['type'])
            );
        }
    }
}

function display_custom_post_meta_field($post, $meta) {
    // Hier den Meta-Inhalt basierend auf den übergebenen Parametern anzeigen
    $value = get_post_meta($post->ID, $meta['id'], true);

    echo '<label for="' . esc_attr($meta['id']) . '">' . esc_html($meta['title']) . ':</label>';
    if($meta['args']['type'] == "textarea") {
        echo '<textarea type="' . esc_attr($meta['args']['type']) . '" id="' . esc_attr($meta['id']) . '" name="' . esc_attr($meta['id']) . '" style="width: 100%;">' . esc_attr($value) . '</textarea>';
    } else {        
        echo '<input type="' . esc_attr($meta['args']['type']) . '" id="' . esc_attr($meta['id']) . '" name="' . esc_attr($meta['id']) . '" value="' . esc_attr($value) . '" style="width: 100%;">';
    }
}
add_action('add_meta_boxes', 'add_custom_post_metas');

function save_custom_type_meta_fields($post_id) {
    global $customPosts;
    foreach($customPosts as $customPost) {
         foreach($customPost['meta'] as $meta) {
            
             if (isset($_POST[$meta['id']])) {
                update_post_meta($post_id, $meta['id'], sanitize_text_field($_POST[$meta['id']]));
            }
         }
    }
}
add_action('save_post', 'save_custom_type_meta_fields');
?>