<?php
/**
 * Sélection d'un encart et rendu HTML.
 *
 * @package NelsonGalleyEncarts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Un encart au hasard parmi ceux actifs pour cet emplacement (et cette rubrique). */
function ng_enc_pick( $location, $post_id = 0 ) {
	$ids = get_posts( array(
		'post_type'   => 'ng_encart',
		'post_status' => 'publish',
		'numberposts' => 50,
		'fields'      => 'ids',
		'meta_key'    => '_ng_loc_' . $location,
		'meta_value'  => '1',
	) );
	$post_cats = $post_id ? wp_get_post_categories( $post_id ) : array();
	$ids       = array_filter( $ids, function ( $id ) use ( $post_cats, $post_id ) {
		if ( ! ng_enc_is_live( $id ) ) return false;
		$cats = array_map( 'intval', (array) ng_enc_get( $id, 'cats', array() ) );
		return ! $post_id || ! $cats || array_intersect( $cats, $post_cats );
	} );
	if ( ! $ids ) return 0;
	$ids = array_values( $ids );
	return $ids[ wp_rand( 0, count( $ids ) - 1 ) ];
}

function ng_enc_render( $id, $placement = '' ) {
	$post = get_post( $id );
	if ( ! $post || 'ng_encart' !== $post->post_type || 'publish' !== $post->post_status || ! ng_enc_is_live( $id ) ) return '';

	$type  = ng_enc_get( $id, 'type', 'offre' );
	$types = ng_enc_types();
	$url   = ng_enc_get( $id, 'url' );
	$ext   = $url && wp_parse_url( $url, PHP_URL_HOST ) && wp_parse_url( $url, PHP_URL_HOST ) !== wp_parse_url( home_url(), PHP_URL_HOST );
	$rel   = ng_enc_is_commercial( $type ) ? 'sponsored nofollow noopener' : ( $ext ? 'noopener' : '' );
	$go    = add_query_arg( 'ng_go', $id, home_url( '/' ) );
	$img   = get_the_post_thumbnail( $id, 'csco-medium', array( 'class' => 'ng-encart__img', 'loading' => 'lazy', 'alt' => '' ) );
	$adv   = ng_enc_get( $id, 'advertiser' );

	if ( ng_enc_should_count() ) {
		update_post_meta( $id, '_ng_views', (int) get_post_meta( $id, '_ng_views', true ) + 1 );
	}

	ob_start();
	?>
	<aside class="ng-encart ng-encart--<?php echo esc_attr( $type ); ?><?php echo $img ? '' : ' ng-encart--no-img'; ?> ng-encart--<?php echo esc_attr( $placement ); ?>" aria-label="<?php echo esc_attr( $types[ $type ]['badge'] ); ?>">
		<?php if ( $img ) : ?><div class="ng-encart__media"><?php echo $img; ?></div><?php endif; ?>
		<div class="ng-encart__body">
			<span class="ng-encart__badge"><?php echo esc_html( $types[ $type ]['badge'] ); ?></span>
			<p class="ng-encart__title"><?php echo esc_html( get_the_title( $id ) ); ?></p>
			<?php if ( ng_enc_get( $id, 'text' ) ) : ?><p class="ng-encart__text"><?php echo esc_html( ng_enc_get( $id, 'text' ) ); ?></p><?php endif; ?>
			<?php if ( ng_enc_get( $id, 'info' ) ) : ?><p class="ng-encart__info"><?php echo esc_html( ng_enc_get( $id, 'info' ) ); ?></p><?php endif; ?>
			<?php if ( $url ) : ?>
				<a class="ng-encart__btn" href="<?php echo esc_url( $go ); ?>"<?php echo $rel ? ' rel="' . esc_attr( $rel ) . '"' : ''; ?><?php echo $ext ? ' target="_blank"' : ''; ?>><?php echo esc_html( ng_enc_get( $id, 'btn', 'En savoir plus' ) ); ?> <span aria-hidden="true">→</span></a>
			<?php endif; ?>
			<?php if ( $adv && ng_enc_is_commercial( $type ) ) : ?><p class="ng-encart__advertiser">Proposé par <?php echo esc_html( $adv ); ?></p><?php endif; ?>
		</div>
	</aside>
	<?php
	return trim( ob_get_clean() );
}
