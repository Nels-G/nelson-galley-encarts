<?php
/**
 * Suivi des clics : /?ng_go=ID compte le clic puis redirige vers le lien de l'encart.
 *
 * @package NelsonGalleyEncarts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'template_redirect', function () {
	if ( ! isset( $_GET['ng_go'] ) ) return;
	$id   = (int) $_GET['ng_go'];
	$post = get_post( $id );
	$url  = $post && 'ng_encart' === $post->post_type && 'publish' === $post->post_status ? ng_enc_get( $id, 'url' ) : '';
	if ( ! $url ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
	if ( ng_enc_should_count() ) {
		update_post_meta( $id, '_ng_clicks', (int) get_post_meta( $id, '_ng_clicks', true ) + 1 );
	}
	// L'URL vient de l'encart enregistré par un administrateur, jamais de la requête : pas de redirection ouverte.
	header( 'X-Robots-Tag: noindex, nofollow' );
	wp_redirect( $url, 302 );
	exit;
}, 1 );
