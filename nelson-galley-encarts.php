<?php
/**
 * Plugin Name:       Nelson Galley — Encarts
 * Plugin URI:        https://github.com/Nels-G/nelson-galley-encarts
 * Description:       Publicités maison pour WordPress : offres de service, formations, applications, événements, partenaires et espaces sponsorisés. Emplacements automatiques, ciblage par rubrique, période de diffusion, statistiques d'affichages et de clics.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Nelson Galley
 * Author URI:        https://www.nelsongalley.online/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       nelson-galley-encarts
 * Update URI:        https://github.com/Nels-G/nelson-galley-encarts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NGE_VERSION', '1.0.0' );
define( 'NGE_FILE', __FILE__ );
define( 'NGE_DIR', plugin_dir_path( __FILE__ ) );
define( 'NGE_URL', plugin_dir_url( __FILE__ ) );
define( 'NGE_GITHUB_REPO', 'Nels-G/nelson-galley-encarts' );

require_once NGE_DIR . 'includes/helpers.php';
require_once NGE_DIR . 'includes/post-type.php';
require_once NGE_DIR . 'includes/admin.php';
require_once NGE_DIR . 'includes/render.php';
require_once NGE_DIR . 'includes/placements.php';
require_once NGE_DIR . 'includes/tracking.php';
require_once NGE_DIR . 'includes/updater.php';

// Styles des encarts (front-end uniquement).
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'nelson-galley-encarts', NGE_URL . 'assets/css/encarts.css', array(), NGE_VERSION );
} );

// Lien « Encarts » dans la liste des extensions.
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $links ) {
	array_unshift( $links, '<a href="' . esc_url( admin_url( 'edit.php?post_type=ng_encart' ) ) . '">Gérer les encarts</a>' );
	return $links;
} );
