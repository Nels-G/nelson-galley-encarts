<?php
/**
 * Mises à jour depuis les « Releases » GitHub.
 *
 * Utilise le mécanisme natif de WordPress 5.8+ pour les extensions hors WordPress.org :
 * l'en-tête « Update URI: https://github.com/… » déclenche le filtre « update_plugins_github.com ».
 * La dernière release publiée doit contenir le fichier « nelson-galley-encarts.zip »
 * (généré automatiquement par l'action GitHub .github/workflows/release.yml).
 *
 * @package NelsonGalleyEncarts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Dernière release GitHub (mise en cache 6 h ; 1 h en cas d'échec). */
function nge_latest_release( $force = false ) {
	$key    = 'nge_github_release';
	$cached = $force ? false : get_site_transient( $key );
	if ( false !== $cached ) {
		return $cached ? $cached : null;
	}

	$response = wp_remote_get(
		'https://api.github.com/repos/' . NGE_GITHUB_REPO . '/releases/latest',
		array(
			'timeout' => 10,
			'headers' => array(
				'Accept'     => 'application/vnd.github+json',
				'User-Agent' => 'WordPress-nelson-galley-encarts',
			),
		)
	);
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		set_site_transient( $key, '', HOUR_IN_SECONDS );
		return null;
	}

	$data    = json_decode( wp_remote_retrieve_body( $response ), true );
	$package = '';
	foreach ( (array) ( $data['assets'] ?? array() ) as $asset ) {
		if ( 'nelson-galley-encarts.zip' === ( $asset['name'] ?? '' ) ) {
			$package = $asset['browser_download_url'];
		}
	}
	if ( ! $package || empty( $data['tag_name'] ) ) {
		set_site_transient( $key, '', HOUR_IN_SECONDS );
		return null;
	}

	$release = array(
		'version'   => ltrim( $data['tag_name'], 'vV' ),
		'package'   => $package,
		'url'       => $data['html_url'] ?? 'https://github.com/' . NGE_GITHUB_REPO,
		'notes'     => (string) ( $data['body'] ?? '' ),
		'published' => (string) ( $data['published_at'] ?? '' ),
	);
	set_site_transient( $key, $release, 6 * HOUR_IN_SECONDS );
	return $release;
}

// Propose la mise à jour dans Tableau de bord › Mises à jour et dans la liste des extensions.
add_filter( 'update_plugins_github.com', function ( $update, $plugin_data, $plugin_file ) {
	if ( plugin_basename( NGE_FILE ) !== $plugin_file ) {
		return $update;
	}
	$release = nge_latest_release();
	if ( ! $release ) {
		return $update;
	}
	return array(
		'slug'    => 'nelson-galley-encarts',
		'version' => $release['version'],
		'url'     => $release['url'],
		'package' => $release['package'],
	);
}, 10, 3 );

// Fenêtre « Voir les détails de la version ».
add_filter( 'plugins_api', function ( $result, $action, $args ) {
	if ( 'plugin_information' !== $action || 'nelson-galley-encarts' !== ( $args->slug ?? '' ) ) {
		return $result;
	}
	$release = nge_latest_release();
	return (object) array(
		'name'          => 'Nelson Galley — Encarts',
		'slug'          => 'nelson-galley-encarts',
		'version'       => $release ? $release['version'] : NGE_VERSION,
		'author'        => '<a href="https://www.nelsongalley.online/">Nelson Galley</a>',
		'homepage'      => 'https://github.com/' . NGE_GITHUB_REPO,
		'requires'      => '6.0',
		'requires_php'  => '7.4',
		'last_updated'  => $release ? $release['published'] : '',
		'download_link' => $release ? $release['package'] : '',
		'sections'      => array(
			'description' => 'Publicités maison : offres, formations, applications, événements, partenaires et espaces sponsorisés, avec statistiques.',
			'changelog'   => $release ? nl2br( esc_html( $release['notes'] ) ) : '',
		),
	);
}, 10, 3 );

// Après une mise à jour, on oublie la release en cache.
add_action( 'upgrader_process_complete', function () {
	delete_site_transient( 'nge_github_release' );
} );
