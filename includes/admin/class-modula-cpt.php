<?php

/**
 * The cpt plugin class.
 *
 * This is used to define the custom post type that will be used for galleries
 *
 * @since      2.0.0
 */
class Modula_CPT {

	private $labels    = array();
	private $args      = array();
	private $metaboxes = array();
	private $cpt_name;
	private $builder;

	public function __construct() {

		$this->labels = apply_filters( 'modula_cpt_labels', array(
			'name'                  => _x( 'Galleries', 'Gallery', 'text_domain' ),
			'singular_name'         => _x( 'Gallery', 'Gallery', 'text_domain' ),
			'menu_name'             => __( 'Modula', 'text_domain' ),
			'name_admin_bar'        => __( 'Modula', 'text_domain' ),
			'archives'              => __( 'Item Archives', 'text_domain' ),
			'attributes'            => __( 'Item Attributes', 'text_domain' ),
			'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
			'all_items'             => __( 'Galleries', 'text_domain' ),
			'add_new_item'          => __( 'Add New Item', 'text_domain' ),
			'add_new'               => __( 'Add New', 'text_domain' ),
			'new_item'              => __( 'New Item', 'text_domain' ),
			'edit_item'             => __( 'Edit Item', 'text_domain' ),
			'update_item'           => __( 'Update Item', 'text_domain' ),
			'view_item'             => __( 'View Item', 'text_domain' ),
			'view_items'            => __( 'View Items', 'text_domain' ),
			'search_items'          => __( 'Search Item', 'text_domain' ),
			'not_found'             => __( 'Not found', 'text_domain' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
			'featured_image'        => __( 'Featured Image', 'text_domain' ),
			'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
			'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
			'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
			'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
			'items_list'            => __( 'Items list', 'text_domain' ),
			'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
			'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
		) );

		$this->args = apply_filters( 'modula_cpt_args', array(
			'label'                 => __( 'Modula Gallery', 'text_domain' ),
			'description'           => __( 'Modula is one of the best & most creative WordPress gallery plugins. Use it to create a great grid or masonry image gallery.', 'text_domain' ),
			'supports'              => array( 'title' ),
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 25,
			'menu_icon'             => 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="20" viewBox="0 0 32 32"><path fill="#f0f5fa" d="M9.3 25.3c-2.4-0.7-4.7-1.4-7.1-2.1 2.4-3.5 4.7-7 7-10.5C9.3 12.9 9.3 24.9 9.3 25.3z"/><path fill="#f0f5fa" d="M9.6 20.1c3.7 2 7.4 3.9 11.1 5.9 -0.1 0.1-5 5-5.2 5.2C13.6 27.5 11.6 23.9 9.6 20.1 9.6 20.2 9.6 20.2 9.6 20.1z"/><path fill="#f0f5fa" d="M22.3 11.9c-3.7-2-7.4-4-11-6 0 0 0 0 0 0 0 0 0 0 0 0 1.7-1.7 3.4-3.3 5.1-5 0 0 0 0 0.1-0.1C18.5 4.5 20.4 8.2 22.3 11.9 22.4 11.9 22.3 11.9 22.3 11.9z"/><path fill="#f0f5fa" d="M4.7 15c-0.6-2.4-1.2-4.7-1.8-7 0.2 0 11.9 0.6 12.7 0.6 0 0 0 0 0 0 0 0 0 0 0 0 -3.6 2.1-7.2 4.2-10.7 6.3C4.8 15 4.8 15 4.7 15z"/><path fill="#f0f5fa" d="M22.9 19.6c-0.2-4.2-0.3-8.3-0.5-12.5 2.4 0.6 4.8 1.2 7.1 1.8C27.4 12.4 25.1 16 22.9 19.6 22.9 19.6 22.9 19.6 22.9 19.6z"/><path fill="#f0f5fa" d="M27.7 16.8c0.6 2.4 1.2 4.7 1.9 7.1 -4.2-0.2-8.5-0.4-12.7-0.5 0 0 0 0 0 0C20.5 21.2 24.1 19 27.7 16.8z"/></svg>'),
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'rewrite'               => false,
		) );

		$this->metaboxes = apply_filters( 'modula_cpt_metaboxes', array(
			'modula-preview-gallery' => array(
				'title' => esc_html__( 'Gallery', 'text_domain' ),
				'callback' => 'output_gallery_images',
				'context' => 'normal',
			),
			'modula-settings' => array(
				'title' => esc_html__( 'Settings', 'text_domain' ),
				'callback' => 'output_gallery_settings',
				'context' => 'normal',
			),
			'modula-shortcode' => array(
				'title' => esc_html__( 'Shortcode', 'text_domain' ),
				'callback' => 'output_gallery_shortcode',
				'context' => 'side',
			),
		) );

		$this->cpt_name = apply_filters( 'modula_cpt_name', 'modula-gallery' );
		
		add_action( 'init', array( $this, 'register_cpt' ) );

		/* Fire our meta box setup function on the post editor screen. */
		add_action( 'load-post.php', array( $this, 'meta_boxes_setup' ) );
		add_action( 'load-post-new.php', array( $this, 'meta_boxes_setup' ) );

		/* Load Fields Helper */
		require_once MODULA_PATH . 'includes/admin/class-modula-cpt-fields-helper.php';

		/* Load Builder */
		require_once MODULA_PATH . 'includes/admin/class-modula-field-builder.php';
		$this->builder = Modula_Field_Builder::get_instance();

		/* Initiate Image Resizer */
		$this->resizer = new Modula_Image();

	}

	public function register_cpt() {

		$args = $this->args;
		$args['labels'] = $this->labels;

		register_post_type( $this->cpt_name, $args );

	}

	public function meta_boxes_setup() {

		/* Add meta boxes on the 'add_meta_boxes' hook. */
  		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

  		/* Save post meta on the 'save_post' hook. */
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 10, 2 );
	}

	public function add_meta_boxes() {

		global $post;

		foreach ( $this->metaboxes as $metabox_id => $metabox ) {

			if ( 'modula-shortcode' == $metabox_id && 'auto-draft' == $post->post_status ) {
				break;
			}

			add_meta_box(
			    $metabox_id,      // Unique ID
			    $metabox['title'],    // Title
			    array( $this, $metabox['callback'] ),   // Callback function
			    'modula-gallery',         // Admin page (or post type)
			    $metabox['context'],         // Context
			    'high'         // Priority
			);
		}

	}

	public function output_gallery_images() {
		$this->builder->render( 'gallery' );
	}
	
	public function output_gallery_settings() {
		$this->builder->render( 'settings' );
	}

	public function output_gallery_shortcode( $post ) {
		$this->builder->render( 'shortcode', $post );
	}

	public function save_meta_boxes( $post_id, $post ) {

		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );

		/* Check if the current user has permission to edit the post. */
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) ) {
			return $post_id;
		}

		// Here we will save gallery images
		if ( isset( $_POST['modula-images'] ) ) {

			// This list will not contain id because we save our images based on image id.
			$image_attributes = apply_filters( 'modula_gallery_image_attributes', array(
				'alt',
				'title',
				'caption',
				'halign',
				'valign',
				'link',
				'target',
				'width',
				'height',
			) );

			$modula_images = array();

			$gallery_type = isset( $_POST['modula-settings']['type'] ) ? $_POST['modula-settings']['type'] : 'creative-gallery';
			for ( $index=0; $index < count( $_POST['modula-images']['id'] ); $index++ ) { 
			// foreach ( $_POST['modula-images']['id'] as $index => $image_id ) {
				$new_image = array();
				$grid_sizes = array(
					'width' => isset( $_POST['modula-images']['width'][ $index ] ) ? $_POST['modula-images']['width'][ $index ] : 1,
					'height' => isset( $_POST['modula-images']['height'][ $index ] ) ? $_POST['modula-images']['height'][ $index ] : 1,
				);

				// Save the image's id
				$new_image['id'] = $_POST['modula-images']['id'][ $index ];

				// Get from the current image only accepted attributes
				foreach ( $image_attributes as $attribute ) {
					if ( isset( $_POST['modula-images'][ $attribute ][ $index ] ) ) {
						// @todo: Create a sanitization function
						$new_image[ $attribute ] = $_POST['modula-images'][ $attribute ][ $index ];
					}else{
						$new_image[ $attribute ] = '';
					}
				}

				// Check if we need to resize this image
				if ( isset( $_POST['modula-settings']['img_size'] ) ) {
					$img_size = absint( $_POST['modula-settings']['img_size'] );
					$sizes = $this->resizer->get_image_size( $new_image['id'], $img_size, $gallery_type, $grid_sizes );
					if ( ! is_wp_error( $sizes ) ) {
						$this->resizer->resize_image( $sizes['url'], $sizes['width'], $sizes['height'] );
					}
				}

				// Add new image to modula images
				$modula_images[ $index ] = $new_image;
			}

			// Add images to gallery meta
			update_post_meta( $post_id, 'modula-images', $modula_images );

		}

		if ( isset( $_POST['modula-settings'] ) ) {
			
			$fields_with_tabs = Modula_CPT_Fields_Helper::get_fields( 'all' );

			// Here we will save all our settings
			$modula_settings = array();

			// We will save only our settings.
			foreach ( $fields_with_tabs as $tab => $fields ) {

				// We will iterate throught all fields of current tab
				foreach ( $fields as $field_id => $field ) {

					if ( isset( $_POST['modula-settings'][ $field_id ] ) ) {

						// @todo: find a method to sanitize modula settings
						$modula_settings[ $field_id ] = $_POST['modula-settings'][ $field_id ];

					}else{
						$modula_settings[ $field_id ] = '0';
					}

				}

			}

			// Add settings to gallery meta
			update_post_meta( $post_id, 'modula-settings', $modula_settings );

		}

	}
}
