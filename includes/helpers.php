<?php
/**
 * Réglages et fonctions utilitaires.
 *
 * @package NelsonGalleyEncarts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Types d'encarts : libellé dans l'admin et badge affiché aux lecteurs.
 * Filtre « nge_types » pour en ajouter.
 */
function ng_enc_types() {
	return apply_filters( 'nge_types', array(
		'offre'       => array( 'label' => 'Mon offre de service', 'badge' => 'Mon offre' ),
		'formation'   => array( 'label' => 'Formation', 'badge' => 'Formation' ),
		'application' => array( 'label' => 'Application / produit / système', 'badge' => 'Nouveau' ),
		'evenement'   => array( 'label' => 'Événement', 'badge' => 'Événement' ),
		'partenaire'  => array( 'label' => 'Partenaire', 'badge' => 'Partenaire' ),
		'sponsorise'  => array( 'label' => 'Sponsorisé (espace payant)', 'badge' => 'Sponsorisé' ),
	) );
}

/** Emplacements automatiques. */
function ng_enc_locations() {
	return array(
		'middle' => 'Au milieu des articles',
		'end'    => 'À la fin des articles',
		'home'   => "Accueil (avant la grille d'articles)",
	);
}

/** Types dont le lien doit porter rel="sponsored" (exigence Google pour les liens commerciaux). */
function ng_enc_is_commercial( $type ) {
	return in_array( $type, array( 'partenaire', 'sponsorise' ), true );
}

/** Lecture d'une donnée d'encart (post meta « _ng_* »). */
function ng_enc_get( $id, $key, $default = '' ) {
	$v = get_post_meta( $id, '_ng_' . $key, true );
	return '' === $v ? $default : $v;
}

/** L'encart est-il dans sa période de diffusion ? */
function ng_enc_is_live( $id ) {
	$today = current_time( 'Y-m-d' );
	$start = ng_enc_get( $id, 'start' );
	$end   = ng_enc_get( $id, 'end' );
	return ( ! $start || $start <= $today ) && ( ! $end || $end >= $today );
}

function ng_enc_is_bot() {
	$ua = strtolower( isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '' );
	return '' === $ua || (bool) preg_match( '/bot|crawl|spider|slurp|facebookexternalhit|whatsapp|preview|headless|curl|wget/', $ua );
}

/** On ne compte ni les administrateurs/rédacteurs connectés, ni les robots. */
function ng_enc_should_count() {
	return ! is_admin() && ! current_user_can( 'edit_posts' ) && ! ng_enc_is_bot();
}

function ng_enc_stats_html( $id ) {
	$v = (int) get_post_meta( $id, '_ng_views', true );
	$c = (int) get_post_meta( $id, '_ng_clicks', true );
	$r = $v ? round( $c / $v * 100, 1 ) : 0;
	return sprintf( '%s affichages · %s clics · %s %% de clics', number_format_i18n( $v ), number_format_i18n( $c ), number_format_i18n( $r, 1 ) );
}
