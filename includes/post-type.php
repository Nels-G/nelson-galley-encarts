<?php
/**
 * Type de contenu « Encart ».
 *
 * @package NelsonGalleyEncarts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function () {
	register_post_type( 'ng_encart', array(
		'labels'        => array(
			'name'               => 'Encarts',
			'singular_name'      => 'Encart',
			'add_new'            => 'Ajouter un encart',
			'add_new_item'       => 'Nouvel encart',
			'edit_item'          => "Modifier l'encart",
			'all_items'          => 'Tous les encarts',
			'search_items'       => 'Rechercher un encart',
			'not_found'          => 'Aucun encart.',
			'not_found_in_trash' => 'Aucun encart dans la corbeille.',
			'featured_image'     => "Image de l'encart",
			'set_featured_image' => "Choisir l'image de l'encart",
		),
		'public'        => false,
		'show_ui'       => true,
		'menu_icon'     => 'dashicons-megaphone',
		'menu_position' => 27,
		'supports'      => array( 'title', 'thumbnail' ),
	) );
} );
