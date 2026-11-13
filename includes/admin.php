<?php
/**
 * Admin : formulaire d'édition, enregistrement et colonnes de la liste.
 *
 * @package NelsonGalleyEncarts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'add_meta_boxes', function () {
	add_meta_box( 'ng_encart_box', "Contenu et diffusion de l'encart", 'ng_enc_metabox', 'ng_encart', 'normal', 'high' );
} );

function ng_enc_metabox( $post ) {
	wp_nonce_field( 'ng_enc_save', 'ng_enc_nonce' );
	$id    = $post->ID;
	$type  = ng_enc_get( $id, 'type', 'offre' );
	$cats  = (array) ng_enc_get( $id, 'cats', array() );
	$field = 'style="width:100%;max-width:640px"';
	?>
	<style>
		#ng_encart_box th { width: 190px; text-align: left; vertical-align: top; padding-top: 10px; }
		#ng_encart_box td { padding: 6px 0 12px; }
		#ng_encart_box .description { color: #646970; margin-top: 4px; }
		#ng_encart_box fieldset label { display: inline-block; margin: 0 16px 6px 0; }
	</style>
	<p>Le <strong>titre</strong> ci-dessus s'affiche en gros dans l'encart. L'<strong>image</strong> se choisit dans le panneau « Image de l'encart » (format paysage conseillé, 1200 × 675 px).</p>
	<table>
		<tr><th><label for="ng_type">Type</label></th><td>
			<select id="ng_type" name="ng_type">
				<?php foreach ( ng_enc_types() as $k => $t ) : ?>
					<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $type, $k ); ?>><?php echo esc_html( $t['label'] ); ?></option>
				<?php endforeach; ?>
			</select>
			<p class="description">Détermine le badge affiché. « Partenaire » et « Sponsorisé » ajoutent les mentions et le lien commercial requis.</p>
		</td></tr>
		<tr><th><label for="ng_text">Texte</label></th><td>
			<textarea id="ng_text" name="ng_text" rows="3" <?php echo $field; ?>><?php echo esc_textarea( ng_enc_get( $id, 'text' ) ); ?></textarea>
			<p class="description">Une ou deux phrases : le bénéfice pour le lecteur.</p>
		</td></tr>
		<tr><th><label for="ng_info">Infos pratiques</label></th><td>
			<input id="ng_info" name="ng_info" type="text" value="<?php echo esc_attr( ng_enc_get( $id, 'info' ) ); ?>" <?php echo $field; ?> placeholder="Ex. : Samedi 6 décembre · Lomé · Gratuit">
			<p class="description">Facultatif. Date et lieu d'un événement, prix d'une formation, plateformes d'une application…</p>
		</td></tr>
		<tr><th><label for="ng_btn">Texte du bouton</label></th><td>
			<input id="ng_btn" name="ng_btn" type="text" value="<?php echo esc_attr( ng_enc_get( $id, 'btn', 'En savoir plus' ) ); ?>" style="width:320px">
		</td></tr>
		<tr><th><label for="ng_url">Lien du bouton</label></th><td>
			<input id="ng_url" name="ng_url" type="url" value="<?php echo esc_attr( ng_enc_get( $id, 'url' ) ); ?>" <?php echo $field; ?> placeholder="https://… ou /contacts/">
		</td></tr>
		<tr><th><label for="ng_advertiser">Annonceur</label></th><td>
			<input id="ng_advertiser" name="ng_advertiser" type="text" value="<?php echo esc_attr( ng_enc_get( $id, 'advertiser' ) ); ?>" style="width:320px">
			<p class="description">Pour un partenaire ou un espace sponsorisé : nom de l'entreprise, affiché dans l'encart.</p>
		</td></tr>
		<tr><th>Emplacements</th><td><fieldset>
			<?php foreach ( ng_enc_locations() as $k => $label ) : ?>
				<label><input type="checkbox" name="ng_loc[]" value="<?php echo esc_attr( $k ); ?>" <?php checked( '1', ng_enc_get( $id, 'loc_' . $k ) ); ?>> <?php echo esc_html( $label ); ?></label><br>
			<?php endforeach; ?>
			<label>Au milieu : après le paragraphe n° <input type="number" min="1" max="20" name="ng_para" value="<?php echo esc_attr( ng_enc_get( $id, 'para', 4 ) ); ?>" style="width:60px"></label>
			<p class="description">Sans emplacement coché, l'encart n'apparaît que là où vous collez son code court (voir ci-dessous).</p>
		</fieldset></td></tr>
		<tr><th>Rubriques ciblées</th><td><fieldset>
			<?php foreach ( get_categories( array( 'hide_empty' => false, 'exclude' => array( 1 ) ) ) as $c ) : ?>
				<label><input type="checkbox" name="ng_cats[]" value="<?php echo (int) $c->term_id; ?>" <?php checked( in_array( $c->term_id, array_map( 'intval', $cats ), true ) ); ?>> <?php echo esc_html( $c->name ); ?></label>
			<?php endforeach; ?>
			<p class="description">Aucune case cochée = toutes les rubriques. S'applique aux emplacements dans les articles.</p>
		</fieldset></td></tr>
		<tr><th>Période de diffusion</th><td>
			du <input type="date" name="ng_start" value="<?php echo esc_attr( ng_enc_get( $id, 'start' ) ); ?>">
			au <input type="date" name="ng_end" value="<?php echo esc_attr( ng_enc_get( $id, 'end' ) ); ?>">
			<p class="description">Facultatif. Idéal pour un événement ou un espace vendu pour une durée précise.</p>
		</td></tr>
		<?php if ( 'publish' === $post->post_status ) : ?>
		<tr><th>Code court</th><td>
			<code>[encart id="<?php echo (int) $id; ?>"]</code>
			<p class="description">À coller dans un article (bloc « Code court ») pour afficher cet encart à un endroit précis, par exemple dans l'article qui présente votre application.</p>
		</td></tr>
		<tr><th>Statistiques</th><td>
			<?php echo ng_enc_stats_html( $id ); ?>
			<p class="description">Les visites des administrateurs connectés et des robots ne sont pas comptées.</p>
		</td></tr>
		<?php endif; ?>
	</table>
	<?php
}

add_action( 'save_post_ng_encart', function ( $post_id ) {
	if ( ! isset( $_POST['ng_enc_nonce'] ) || ! wp_verify_nonce( $_POST['ng_enc_nonce'], 'ng_enc_save' ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	$type = sanitize_key( $_POST['ng_type'] ?? 'offre' );
	update_post_meta( $post_id, '_ng_type', array_key_exists( $type, ng_enc_types() ) ? $type : 'offre' );
	update_post_meta( $post_id, '_ng_text', sanitize_textarea_field( wp_unslash( $_POST['ng_text'] ?? '' ) ) );
	update_post_meta( $post_id, '_ng_info', sanitize_text_field( wp_unslash( $_POST['ng_info'] ?? '' ) ) );
	update_post_meta( $post_id, '_ng_btn', sanitize_text_field( wp_unslash( $_POST['ng_btn'] ?? '' ) ) );
	update_post_meta( $post_id, '_ng_url', esc_url_raw( wp_unslash( $_POST['ng_url'] ?? '' ) ) );
	update_post_meta( $post_id, '_ng_advertiser', sanitize_text_field( wp_unslash( $_POST['ng_advertiser'] ?? '' ) ) );
	update_post_meta( $post_id, '_ng_para', max( 1, min( 20, (int) ( $_POST['ng_para'] ?? 4 ) ) ) );
	$locs = array_map( 'sanitize_key', (array) ( $_POST['ng_loc'] ?? array() ) );
	foreach ( array_keys( ng_enc_locations() ) as $k ) {
		update_post_meta( $post_id, '_ng_loc_' . $k, in_array( $k, $locs, true ) ? '1' : '0' );
	}
	update_post_meta( $post_id, '_ng_cats', array_map( 'intval', (array) ( $_POST['ng_cats'] ?? array() ) ) );
	foreach ( array( 'start', 'end' ) as $d ) {
		$v = sanitize_text_field( $_POST[ 'ng_' . $d ] ?? '' );
		update_post_meta( $post_id, '_ng_' . $d, preg_match( '/^\d{4}-\d{2}-\d{2}$/', $v ) ? $v : '' );
	}
} );

add_filter( 'manage_ng_encart_posts_columns', function ( $cols ) {
	return array(
		'cb'        => $cols['cb'],
		'title'     => 'Encart',
		'ng_type'   => 'Type',
		'ng_where'  => 'Emplacements',
		'ng_period' => 'Diffusion',
		'ng_stats'  => 'Statistiques',
	);
} );

add_action( 'manage_ng_encart_posts_custom_column', function ( $col, $id ) {
	switch ( $col ) {
		case 'ng_type':
			echo esc_html( ng_enc_types()[ ng_enc_get( $id, 'type', 'offre' ) ]['label'] ?? '' );
			break;
		case 'ng_where':
			$w = array();
			foreach ( ng_enc_locations() as $k => $label ) {
				if ( '1' === ng_enc_get( $id, 'loc_' . $k ) ) $w[] = $label;
			}
			echo esc_html( $w ? implode( ', ', $w ) : 'Code court uniquement' );
			break;
		case 'ng_period':
			$start = ng_enc_get( $id, 'start' );
			$end   = ng_enc_get( $id, 'end' );
			$today = current_time( 'Y-m-d' );
			$state = $start && $start > $today ? 'Programmé' : ( $end && $end < $today ? 'Terminé' : 'En cours' );
			$fmt   = fn( $d ) => $d ? date_i18n( 'j M Y', strtotime( $d ) ) : '…';
			echo esc_html( $state . ( $start || $end ? ' (' . $fmt( $start ) . ' → ' . $fmt( $end ) . ')' : '' ) );
			break;
		case 'ng_stats':
			echo esc_html( ng_enc_stats_html( $id ) );
			break;
	}
}, 10, 2 );
