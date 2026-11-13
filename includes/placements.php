<?php
/**
 * Emplacements : dans les articles, sur l'accueil et via le code court [encart].
 *
 * @package NelsonGalleyEncarts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Dans les articles : insertion dans le contenu brut (avant le rendu des blocs, priorité 8),
// comptée en paragraphes de premier niveau pour ne jamais casser un encadré ou une liste.
add_filter( 'the_content', function ( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() || ! has_blocks( $content ) ) {
		return $content;
	}
	$post_id = get_the_ID();
	$html    = function ( $markup ) {
		return array( 'blockName' => 'core/html', 'attrs' => array(), 'innerBlocks' => array(), 'innerHTML' => $markup, 'innerContent' => array( $markup ) );
	};
	$blocks = parse_blocks( $content );

	$mid = ng_enc_pick( 'middle', $post_id );
	if ( $mid ) {
		$target = (int) ng_enc_get( $mid, 'para', 4 );
		$count  = 0;
		$total  = count( array_filter( $blocks, fn( $b ) => 'core/paragraph' === $b['blockName'] ) );
		if ( $total > $target + 1 ) {
			foreach ( $blocks as $i => $b ) {
				if ( 'core/paragraph' === $b['blockName'] && ++$count === $target ) {
					array_splice( $blocks, $i + 1, 0, array( $html( ng_enc_render( $mid, 'middle' ) ) ) );
					break;
				}
			}
		}
	}
	$end = ng_enc_pick( 'end', $post_id );
	if ( $end && $end !== $mid ) {
		$blocks[] = $html( ng_enc_render( $end, 'end' ) );
	}
	return serialize_blocks( $blocks );
}, 8 );

// Accueil : après les thématiques, avant la grille (page 1 uniquement).
add_action( 'csco_main_content_before', function () {
	if ( ! is_home() || is_paged() ) return;
	$id = ng_enc_pick( 'home' );
	if ( $id ) echo '<div class="ng-encart-wrap ng-encart-wrap--home">' . ng_enc_render( $id, 'home' ) . '</div>';
}, 130 );

// Code court : [encart id="123"]
add_shortcode( 'encart', function ( $atts ) {
	$atts = shortcode_atts( array( 'id' => 0 ), $atts );
	return ng_enc_render( (int) $atts['id'], 'shortcode' );
} );
