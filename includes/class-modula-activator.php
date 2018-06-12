<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      2.0.0
 */
class Modula_Activator {
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    2.0.0
	 */
	public static function activate() {

		/* Backward Compatibility of Modula 1.x to Modula 2.x */
		$make_backward = get_option( 'modula-backward-compatibility' );

		if ( ! $make_backward ) {
			global $wpdb;

			$toggles = array(
				'enableFacebook',
				'enableGplus',
				'enablePinterest',
				'enableTwitter',
				'filterClick',
				'shuffle',
			);

			$default_gallery_settings = apply_filters( 'modula_lite_default_settings', array(
				'width' => '100%',
				'height' => '800',
				'img_size' => 300,
				'margin' => '10',
				'randomFactor' => '50',
				'lightbox' => 'lightbox2',
				'shuffle' => 0,
				'captionColor' => '#ffffff',
				'wp_field_caption' => 'caption',
			    'wp_field_title' => 'title',
			    'hide_title' => 0,
			    'hide_description' => 0,
			    'captionFontSize' => '14',
			    'titleFontSize' => '16',
			    'enableFacebook' => 1,
			    'enableGplus' => 1,
			    'enablePinterest' => 1,
			    'enableTwitter' => 1,
			    'filterClick' => 0,
			    'socialIconColor' => '#ffffff',
			    'loadedScale' => '100',
			    'effect' => 'pufrobo',
			    'borderColor' => '#ffffff',
			    'borderRadius' => '0',
			    'borderSize' => '0',
			    'shadowColor' => '#ffffff',
			    'shadowSize' => 0,
			    'script' => '',
			    'style' => '',
			) );

			$galleries_query = 'SELECT * FROM ' . $wpdb->prefix . 'modula';
			$galleries = $wpdb->get_results( $galleries_query );

			foreach ( $galleries as $gallery ) {
				$id = $gallery->Id;
				$config = json_decode( $gallery->configuration, true );

				$images_query = "SELECT * FROM {$wpdb->prefix}modula_images WHERE gid={$id}";
				$images = $wpdb->get_results( $images_query, ARRAY_A );

				// Insert the gallery post
				$galery_data = array(
					'post_type' => 'modula-gallery',
					'post_status' => 'publish',
				);

				if ( isset( $config['name'] ) ) {
					$galery_data['post_title'] = $config['name'];
				}

				$gallery_id = wp_insert_post( $galery_data );

				/* Parse gallery settings. The toggles have another values now. */
				$modula_settings = $config;
				foreach ( $toggles as $toggle ) {
					$modula_settings[ $toggle ] = ( 'T' == $modula_settings[ $toggle ] ) ? 1 : 0;
				}

				// In modula 2.0 the hoverEffect it's renamed to effect.
				$modula_settings[ 'effect' ] = $modula_settings['hoverEffect'];
				unset( $modula_settings['hoverEffect'] );

				$modula_settings = wp_parse_args( $modula_settings, $default_gallery_settings );

				add_post_meta( $gallery_id, 'modula-settings', $modula_settings, true );

				// Add images to gallery
				$new_images = array();
				require_once MODULA_PATH . 'includes/admin/class-modula-image.php';

				$img_size = absint( $modula_settings['img_size'] );
				$resizer = new Modula_Image();

				foreach ( $images as $image ) {

					$sizes = $resizer->get_image_size( $image['imageId'], $img_size );
					if ( ! is_wp_error( $sizes ) ) {
						$resizer->resize_image( $sizes['url'], $sizes['width'], $sizes['height'] );
					}

					$new_images[] = array(
						'id'      => $image['imageId'],
						'alt'     => '',
						'title'   => $image['title'],
						'caption' => $image['description'],
						'halign'  => $image['halign'],
						'valign'  => $image['valign'],
						'link'    => $image['link'],
						'target'  => $image['target'],
					);

				}

				add_post_meta( $gallery_id, 'modula-images', $new_images, true );
				add_post_meta( $gallery_id, 'modula-id', $id, true );

			}

			update_option( 'modula-backward-compatibility', 1 );

		}

	}
}