<?php

/**
 * 
 */
class Modula_CPT_Fields_Helper {
	
	public static function get_tabs() {

		$general_description = '<p>' . esc_html__( 'Here you fill find general settings like what type of gallery you want or what type of lightbox gallery should use.' ) . '</p>';
		$general_description .= self::generate_more_help_links();

		$caption_description = '<p>' . esc_html__( 'The settings below adjust how we should output the image title and description.' ) . '</p>';
		$caption_description .= self::generate_more_help_links();

		$social_description = '<p>' . esc_html__( 'Here you can add social sharing buttons to your gallery images ( Twitter / Facebook / Google Plus / Pinterest ).' ) . '</p>';
		$social_description .= self::generate_more_help_links();

		$loadingeffects_description = '<p>' . esc_html__( 'The settings below adjust the effect of images after page is loaded.' ) . '</p>';
		$loadingeffects_description .= self::generate_more_help_links();

		$hover_description = '<p>' . esc_html__( 'Here you will select the how you images will comport on hover.' ) . '</p>';
		$hover_description .= self::generate_more_help_links();

		$style_description = '<p>' . esc_html__( 'Here you can style the look of your image.' ) . '</p>';
		$style_description .= self::generate_more_help_links();

		$customizations_description = '<p>' . esc_html__( 'Here you can add extras CSS & JS to your gallery' ) . '</p>';
		$customizations_description .= self::generate_more_help_links();

		return apply_filters( 'modula_gallery_tabs', array(
			'general' => array(
				'label'       => esc_html__( 'General', 'modula-gallery' ),
				'title'       => esc_html__( 'General Settings', 'modula-gallery' ),
				'description' => $general_description,
				"icon"        => "dashicons dashicons-admin-generic",
				'priority'    => 10,
			),
			'captions' => array(
				'label'       => esc_html__( 'Captions', 'modula-gallery' ),
				'title'       => esc_html__( 'Caption Settings', 'modula-gallery' ),
				'description' => $caption_description,
				"icon"        => "dashicons dashicons-text",
				'priority'    => 20,
			),
			'social' => array(
				'label'       => esc_html__( 'Social', 'modula-gallery' ),
				'title'       => esc_html__( 'Social Settings', 'modula-gallery' ),
				'description' => $social_description,
				"icon"        => "dashicons dashicons-admin-links",
				'priority'    => 30,
			),
			'image-loaded-effects' => array(
				'label'       => esc_html__( 'Loading effects', 'modula-gallery' ),
				'title'       => esc_html__( 'Loading Effects Settings', 'modula-gallery' ),
				'description' => $loadingeffects_description,
				"icon"        => "dashicons dashicons-image-rotate",
				'priority'    => 40,
			),
			'hover-effect' => array(
				'label'       => esc_html__( 'Hover effect', 'modula-gallery' ),
				'title'       => esc_html__( 'Hover Effect Settings', 'modula-gallery' ),
				'description' => $hover_description,
				"icon"        => "dashicons dashicons-layout",
				'priority'    => 50,
			),
			'style' => array(
				'label'       => esc_html__( 'Style', 'modula-gallery' ),
				'title'       => esc_html__( 'Style Settings', 'modula-gallery' ),
				'description' => $style_description,
				"icon"        => "dashicons dashicons-admin-appearance",
				'priority'    => 60,
			),
			'customizations' => array(
				'label'       => esc_html__( 'Customizations', 'modula-gallery' ),
				'title'       => esc_html__( 'Customization Settings', 'modula-gallery' ),
				'description' => $customizations_description,
				"icon"        => "dashicons dashicons-admin-tools",
				'priority'    => 70,
			),
		) );

	}

	public static function generate_more_help_links() {

		$output = '<p>' . esc_html__( 'Still stuck ?', 'modula-gallery' ) . ' <a href="#" target>' . esc_html__( 'Read our documentation', 'modula-gallery' ) . '</a> or <a href="#">' . esc_html__( 'Contact our support team.', 'modula-gallery' ) . '</a></p>';

		return $output;

	}

	public static function get_fields( $tab ) {

		$fields = apply_filters( 'modula_gallery_fields', array(
			'general' => array(
				'type'           => array(
					"name"        => esc_html__( 'Gallery Type', 'modula-gallery' ),
					"type"        => "select",
					"description" => esc_html__( 'Choose what type of gallery you want to use.', 'modula-gallery' ),
					'default'     => 'creative-gallery',
					"values"      => array(
						'creative-gallery' => esc_html__( 'Creative Gallery', 'modula-gallery' ),
						'custom-grid'      => esc_html__( 'Custom Grid', 'modula-gallery' ),
					),
					'priority' => 10,
				),
				"columns"   => array(
					"name"        => esc_html__( 'Number of columns', 'modula-gallery' ),
					"type"        => "ui-slider",
					"description" => "",
					"min"         => 1,
					"max"         => 6,
					"step"        => 1,
					"default"     => 6,
					'priority'    => 20,
				),
				"gutter"   => array(
					"name"        => esc_html__( 'Gutter', 'modula-gallery' ),
					"type"        => "ui-slider",
					"description" => "",
					"min"         => 0,
					"max"         => 100,
					"step"        => 5,
					"default"     => 10,
					'priority'    => 30,
				),
				"width"          => array(
					"name"        => esc_html__( 'Width', 'modula-gallery' ),
					"type"        => "text",
					"description" => esc_html__( 'Width of the gallery (i.e.: 100% or 500px)', 'modula-gallery' ),
					'default'     => '100%',
					'priority' => 30,
				),
				"height"         => array(
					"name"        => esc_html__( 'Height', 'modula-gallery' ),
					"type"        => "text",
					"description" => esc_html__( 'Height of the gallery in pixels', 'modula-gallery' ),
					'default'     => '800px',
					'priority' => 40,
				),
				"img_size"       => array(
					"name"        => esc_html__( 'Minimum image size', 'modula-gallery' ),
					"type"        => "text",
					'default'     => 500,
					"description" => esc_html__( 'Minimum width or height of the images (i.e.: 500 that means 500px )', 'modula-gallery' ),
					'priority'    => 50,
				),
				"margin"         => array(
					"name"        => esc_html__( 'Margin', 'modula-gallery' ),
					"type"        => "text",
					'default'     => 10,
					"description" => esc_html__( 'Margin between images (i.e.: 10 that means 500px)', 'modula-gallery' ),
					'priority' => 60,
				),
				"randomFactor"   => array(
					"name"        => esc_html__( 'Random factor', 'modula-gallery' ),
					"type"        => "ui-slider",
					"description" => "",
					"min"         => 0,
					"max"         => 100,
					"step"        => 1,
					"default"     => 50,
					'priority' => 70,
				),
				"lightbox"       => array(
					"name"        => esc_html__( 'Lightbox &amp; Links', 'modula-gallery' ),
					"type"        => "select",
					"description" => esc_html__( 'Define here what happens when user click on the images.', 'modula-gallery' ),
					'default'     => 'lightbox2',
					"values"      => array(
						esc_html__( 'Link', 'modula-gallery' ) => array( 
							"no-link"         => esc_html__( 'No link', 'modula-gallery' ),
							"direct"          => esc_html__( 'Direct link to image', 'modula-gallery' ),
							"attachment-page" => esc_html__( 'Attachment page', 'modula-gallery' )
						),
						esc_html__( 'Lightboxes', 'modula-gallery' ) => array(
							'lightbox2' => esc_html__( 'Lightbox2', 'modula-gallery' ),
						),
					),
					"disabled" => array(
						'title'  => esc_html__( 'Lightboxes with PRO license', 'modula-gallery' ),
						'values' => array(
							"magnific"     => esc_html__( 'Magnific popup', 'modula-gallery' ),
							"prettyphoto"  => esc_html__( 'PrettyPhoto', 'modula-gallery' ),
							"fancybox"     => esc_html__( 'FancyBox', 'modula-gallery' ),
							"swipebox"     => esc_html__( 'SwipeBox', 'modula-gallery' ),
							"lightgallery" => esc_html__( 'LightGallery', 'modula-gallery' ),
						),
					),
					'priority' => 110,
				),
				"shuffle"         => array(
					"name"        => esc_html__( 'Shuffle images', 'modula-gallery' ),
					"type"        => "toggle",
					"default"     => 0,
					"description" => esc_html__( 'Flag it if you want to shuffle the gallery at each page load', 'modula-gallery' ),
					'priority'    => 120,
				),
			),
			'captions' => array(
				"captionColor"     => array(
					"name"        => esc_html__( 'Caption color', 'modula-gallery' ),
					"type"        => "color",
					"description" => esc_html__( 'Color of the caption.', 'modula-gallery' ),
					"default"     => "#ffffff",
					'priority'    => 10,
				),
				"wp_field_caption" => array(
					"name"        => esc_html__( 'Populate caption from', 'modula-gallery' ),
					"type"        => "select",
					"description" => __( '<strong>This field is used ONLY when images are added to the gallery. </strong> If you don\'t want to automatically populate the caption field select <i>Don\'t Populate</i>', 'modula-gallery' ),
					"values"      => array(
						"none"        => esc_html__( 'Don\'t Populate', 'modula-gallery' ),
						"title"       => esc_html__( 'WP Image title', 'modula-gallery' ),
						"caption"     => esc_html__( 'WP Image caption', 'modula-gallery' ),
						"description" => esc_html__( 'WP Image description', 'modula-gallery' ),
					),
					'priority' => 20,
				),
				"wp_field_title"   => array(
					"name"        => esc_html__( 'Populate title from', 'modula-gallery' ),
					"type"        => "select",
					"description" => __( '<strong>This field is used ONLY when images are added to the gallery. </strong> If you don\'t want to automatically populate the title field select <i>Don\'t Populate</i>', 'modula-gallery' ),
					"values"      => array(
						'none'        => esc_html__( 'Don\'t Populate', 'modula-gallery' ),
						'title'       => esc_html__( 'WP Image title', 'modula-gallery' ),
						'description' => esc_html__( 'WP Image description', 'modula-gallery' ),
					),
					'priority' => 30,
				),
				"hide_title"        => array(
					"name"        => esc_html__( 'Image Title', 'modula-gallery' ),
					"type"        => "toggle",
					"default"     => 0,
					"description" => esc_html__( 'Hide image title from frontend', 'modula-gallery' ),
					'priority'    => 40,
				),
				"hide_description"        => array(
					"name"        => esc_html__( 'Image Description', 'modula-gallery' ),
					"type"        => "toggle",
					"default"     => 0,
					"description" => esc_html__( 'Hide image description from frontend', 'modula-gallery' ),
					'priority'    => 50,
				),
				"captionFontSize"  => array(
					"name"        => esc_html__( 'Caption Font Size', 'modula-gallery' ),
					"type"        => "ui-slider",
					"min"         => 0,
					"max"         => 100,
					"default"     => 14,
					"description" => esc_html__( 'The caption font size in pixel ( 0 means inherit from theme )', 'modula-gallery' ),
					'priority'    => 60,
				),
				"titleFontSize"    => array(
					"name"        => esc_html__( 'Title Font Size', 'modula-gallery' ),
					"type"        => "ui-slider",
					"min"         => 0,
					"max"         => 100,
					"default"     => 0,
					"description" => esc_html__( 'The title font size in pixel ( 0 means inherit from theme )', 'modula-gallery' ),
					'priority'    => 70,
				),
			),
			'social' => array(
				"enableTwitter"   => array(
					"name"        => esc_html__( 'Add Twitter icon', 'modula-gallery' ),
					"type"        => "toggle",
					"default"     => 1,
					"description" => esc_html__( 'Enable Twitter Sharing', 'modula-gallery' ),
					'priority'    => 10,
				),
				"enableFacebook"  => array(
					"name"        => esc_html__( 'Add Facebook icon', 'modula-gallery' ),
					"type"        => "toggle",
					"default"     => 1,
					"description" => esc_html__( 'Enable Facebook Sharing', 'modula-gallery' ),
					'priority'    => 20,
				),
				"enableGplus"     => array(
					"name"        => esc_html__( 'Add Google Plus icon', 'modula-gallery' ),
					"type"        => "toggle",
					"default"     => 1,
					"description" => esc_html__( 'Enable Google Plus Sharing', 'modula-gallery' ),
					'priority'    => 30,
				),
				"enablePinterest" => array(
					"name"        => esc_html__( 'Add Pinterest  icon', 'modula-gallery' ),
					"type"        => "toggle",
					"default"     => 1,
					"description" => esc_html__( 'Enable Pinterest Sharing', 'modula-gallery' ),
					'priority'    => 40,
				),
				"socialIconColor" => array(
					"name"        => esc_html__( 'Color of social sharing icons', 'modula-gallery' ),
					"type"        => "color",
					"description" => esc_html__( 'Set the color of the social sharing icons', 'modula-gallery' ),
					"default"     => "#ffffff",
					'priority'    => 50,
				),
			),
			'image-loaded-effects' => array(
				"loadedScale"  => array(
					"name"        => esc_html__( 'Scale', 'modula-gallery' ),
					"description" => esc_html__( 'Choose a value below 100% for a zoom-in effect. Choose a value over 100% for a zoom-out effect', 'modula-gallery' ),
					"type"        => "ui-slider",
					"min"         => 0,
					"max"         => 200,
					"default"     => 100,
					'priority' => 10,
				),
			),
			'hover-effect' => array(
				"effect" => array(
					"name"        => esc_html__( 'Hover effect', 'modula-gallery' ),
					"type"        => "hover-effect",
					'default'     => 'pufrobo',
					'priority'    => 10,
				),
			),
			'style' => array(
				"borderSize"   => array(
					"name"        => esc_html__( 'Border Size', 'modula-gallery' ),
					"type"        => "ui-slider",
					"description" => "",
					"min"         => 0,
					"max"         => 10,
					"default"     => 0,
					'priority'    => 10,
				),
				"borderRadius" => array(
					"name"        => esc_html__( 'Border Radius', 'modula-gallery' ),
					"type"        => "ui-slider",
					"description" => "",
					"min"         => 0,
					"max"         => 100,
					"default"     => 0,
					'priority'    => 20,
				),
				"borderColor"  => array(
					"name"        => esc_html__( 'Border Color', 'modula-gallery' ),
					"type"        => "color",
					"description" => "",
					"default"     => "#ffffff",
					'priority'    => 30,
				),
				"shadowSize"   => array(
					"name"        => esc_html__( 'Shadow Size', 'modula-gallery' ),
					"type"        => "ui-slider",
					"description" => "",
					"min"         => 0,
					"max"         => 20,
					"default"     => 0,
					'priority'    => 40,
				),
				"shadowColor"  => array(
					"name"        => esc_html__( 'Shadow Color', 'modula-gallery' ),
					"type"        => "color",
					"description" => "",
					"default"     => "#ffffff",
					'priority'    => 50,
				),
			),
			'customizations' => array(
				"script" => array(
					"name"        => esc_html__( 'Custom scripts', 'modula-gallery' ),
					"type"        => "custom_code",
					"syntax"      => 'js',
					"description" => esc_html__( 'This script will be called after the gallery initialization. Useful for custom lightboxes.', 'modula-gallery' ) . "
                        <br />
                        <strong>Write just the code without using the &lt;script&gt;&lt;/script&gt; tags</strong>",
					'priority' => 10,
				),
				"style"  => array(
					"name"        => esc_html__( 'Custom css', 'modula-gallery' ),
					"type"        => "custom_code",
					"syntax"      => 'css',
					"description" => '<strong>' . esc_html__( 'Write just the code without using the &lt;style&gt;&lt;/style&gt; tags', 'modula-gallery' ) . '</strong>',
					'priority' => 20,
				),
			),
		) );

		if ( 'all' == $tab ) {
			return $fields;
		}

		if ( isset( $fields[ $tab ] ) ) {
			return $fields[ $tab ];
		} else {
			return array();
		}

	}

	public static function get_defaults() {
		return apply_filters( 'modula_lite_default_settings', array(
			'type'             => 'creative-gallery',
			'width'            => '100%',
			'height'           => '800',
			'img_size'         => 300,
			'margin'           => '10',
			'randomFactor'     => '50',
			'lightbox'         => 'lightbox2',
			'shuffle'          => 0,
			'captionColor'     => '#ffffff',
			'wp_field_caption' => 'caption',
		    'wp_field_title'   => 'title',
		    'hide_title'       => 0,
		    'hide_description' => 0,
		    'captionFontSize'  => '14',
		    'titleFontSize'    => '16',
		    'enableFacebook'   => 1,
		    'enableGplus'      => 1,
		    'enablePinterest'  => 1,
		    'enableTwitter'    => 1,
		    'filterClick'      => 0,
		    'socialIconColor'  => '#ffffff',
		    'loadedScale'      => '100',
		    'effect'           => 'pufrobo',
		    'borderColor'      => '#ffffff',
		    'borderRadius'     => '0',
		    'borderSize'       => '0',
		    'shadowColor'      => '#ffffff',
		    'shadowSize'       => 0,
		    'script'           => '',
		    'style'            => '',
		    'columns'          => 6,
		    'gutter'           => 10,
		) );
	}

}