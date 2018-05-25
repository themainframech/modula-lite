<?php

/**
 * 
 */
class Modula_Field_Builder {

	function __construct() {

		/* Add templates for our plugin */
		add_action( 'admin_footer', array( $this, 'print_modula_templates' ) );

	}

	/**
	 * Get an instance of the field builder
	 */
	public static function get_instance() {
		static $inst;
		if ( ! $inst ) {
			$inst = new Modula_Field_Builder();
		}
		return $inst;
	}

	public function get_id(){
		global $id, $post;

        // Get the current post ID. If ajax, grab it from the $_POST variable.
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX && array_key_exists( 'post_id', $_POST ) ) {
            $post_id = absint( $_POST['post_id'] );
        } else {
            $post_id = isset( $post->ID ) ? $post->ID : (int) $id;
        }

        return $post_id;
	}

	/**
     * Helper method for retrieving settings values.
     *
     * @since 2.0.0
     *
     * @global int $id        The current post ID.
     * @global object $post   The current post object.
     * @param string $key     The setting key to retrieve.
     * @param string $default A default value to use.
     * @return string         Key value on success, empty string on failure.
     */
    public function get_setting( $key, $default = false ) {

        // Get config
        $settings = get_post_meta( $this->get_id(), 'modula-settings', true );

        // Check config key exists
        if ( isset( $settings[ $key ] ) ) {
            return $settings[ $key ];
        } else {
            return $default ? $default : '';
        }

    }

	public function render( $metabox ) {

		switch ( $metabox ) {
			case 'gallery':
				$this->_render_gallery_metabox();
				break;
			case 'settings':
				$this->_render_settings_metabox();
				break;
			case 'shortcode':
				$this->_render_shortcode_metabox();
				break;
			default:
				do_action( "modula_metabox_fields_{$metabox}" );
				break;
		}

	}

	/* Create HMTL for gallery metabox */
	private function _render_gallery_metabox() {

		$images = get_post_meta( $this->get_id(), 'modula-images', true );

		$max_upload_size = wp_max_upload_size();
	    if ( ! $max_upload_size ) {
	        $max_upload_size = 0;
	    }

		echo '<div class="modula-uploader-container">';
		echo '<div class="modula-upload-actions">Drop files anywhere to upload or <a href="#" id="modula-uploader-browser" class="button">Select Files</a> or <a href="#" id="modula-wp-gallery" class="button">Select from Media Library</a>. Drag the images in order to sort them.</div>';
		echo '<div id="modula-uploader-container" class="modula-uploader-inline">';
			echo '<div class="modula-error-container"></div>';
			echo '<div class="modula-uploader-inline-content">';
				echo '<h2 class="modula-upload-message">No images.</h2>';

				if ( is_array( $images ) && ! empty( $images ) ) {
					
					foreach ( $images as $image ) {
						$image_url = wp_get_attachment_image_src( $image['id'], 'thumbnail' );
						$image_full = wp_get_attachment_image_src( $image['id'], 'full' );

						echo '<div class="modula-single-image" data-id="' . esc_attr( $image['id'] ) . '">';
					        echo '<div class="modula-single-image-content">';
				                echo '<img src="' . esc_url( $image_url[0] ) . '" alt="' . esc_attr( $image['alt'] ) . '" data-image="' . esc_url( $image_full[0] ) . '" />';
					            echo '<div class="actions">';
					                echo ' <a href="#" class="modula-edit-image""><span class="dashicons dashicons-edit"></span></a>';
					                echo '<a href="#" class="modula-delete-image""><span class="dashicons dashicons-trash"></span></a>';
					            echo '</div>';
					            echo '<div class="values">';
					                echo '<input type="hidden" name="modula-images[id][]" class="modula-image-id" value="' . esc_attr( $image['id'] ) . '">';
					                echo '<input type="hidden" name="modula-images[alt][]" class="modula-image-alt" value="' . esc_attr( $image['alt'] ) . '">';
					                echo '<input type="hidden" name="modula-images[title][]" class="modula-image-title" value="' . esc_attr( $image['title'] ) . '">';
					                echo '<input type="hidden" name="modula-images[caption][]" class="modula-image-caption" value="' . esc_attr( $image['caption'] ) . '">';
					                echo '<input type="hidden" name="modula-images[halign][]" class="modula-image-halign" value="' . esc_attr( $image['halign'] ) . '">';
					                echo '<input type="hidden" name="modula-images[valign][]" class="modula-image-valign" value="' . esc_attr( $image['valign'] ) . '">';
					                echo '<input type="hidden" name="modula-images[link][]" class="modula-image-link" value="' . esc_attr( $image['link'] ) . '">';
					                echo '<input type="hidden" name="modula-images[target][]" class="modula-image-target" value="' . esc_attr( $image['target'] ) . '">';
					            echo '</div>';
					        echo '</div>';
					    echo '</div>';
					}

				}

			echo '</div>';
			echo '<div class="modula-uploader-footer">';
			echo '<div class="modula-progress-bar"><div class="modula-progress-bar-inner"></div></div>';
			echo '<p class="max-upload-size">' . sprintf( __( 'Maximum upload file size: %s.', 'text-domain' ), esc_html( size_format( $max_upload_size ) ) ) . '</p>';
			echo '<p class="modula-upload-numbers">' . esc_html( 'Uploading image', 'text-domain' ) . ' <span class="modula-current"></span> ' . esc_html( 'of', 'text-domain' ) . ' <span class="modula-total"></span>';
			echo '</div>';
			echo '<div id="modula-dropzone-container"><div class="modula-uploader-window-content"><h1>Drop files to upload</h1></div></div>';
		echo '</div>';
		echo '</div>';
	}

	/* Create HMTL for settings metabox */
	private function _render_settings_metabox() {
		$tabs = Modula_CPT_Fields_Helper::get_tabs();

		// Sort tabs based on priority.
		uasort( $tabs, array( 'Modula_CPT_Fields_Helper', 'sort_data_by_priority' ) );

		$tabs_html = '';
		$tabs_content_html = '';
		$first = true;

		// Generate HTML for each tab.
		foreach ( $tabs as $tab_id => $tab ) {
			$tab['id'] = $tab_id;
			$tabs_html .= $this->_render_tab( $tab, $first );

			$fields = Modula_CPT_Fields_Helper::get_fields( $tab_id );
			// Sort fields based on priority.
			uasort( $fields, array( 'Modula_CPT_Fields_Helper', 'sort_data_by_priority' ) );

			$current_tab_content = '<div id="modula-' . esc_attr( $tab['id'] ) . '" class="' . ( $first ? 'active-tab' : '' ) . '">';

			// Check if our tab have title & description
			if ( isset( $tab['title'] ) || isset( $tab['description'] ) ) {
				$current_tab_content .= '<div class="tab-content-header">';
				if ( isset( $tab['title'] ) && '' != $tab['title'] ) {
					$current_tab_content .= '<h2>' . esc_html( $tab['title'] ) . '</h2>';
				}
				if ( isset( $tab['description'] ) && '' != $tab['description'] ) {
					$current_tab_content .= '<div class="tab-header-description">' . wp_kses_post( $tab['description'] ) . '</div>';
				}
				$current_tab_content .= '</div>';
			}

			// Generate all fields for current tab
			$current_tab_content .= '<table class="form-table"><tbody>';
			foreach ( $fields as $field_id => $field ) {
				$field['id'] = $field_id;
				$current_tab_content .= $this->_render_row( $field );
			}
			$current_tab_content .= '</tbody></table>';
			// Filter to add extra content to a specific tab
			$current_tab_content .= apply_filters( 'modula_' . $tab_id . '_tab_content', '' );
			$current_tab_content .= '</div>';
			$tabs_content_html .= $current_tab_content;

			if ( $first ) {
				$first = false;
			}

		}

		$html = '<div class="modula-settings-container"><div class="modula-tabs">%s</div><div class="modula-tabs-content">%s</div>';
		printf( $html, $tabs_html, $tabs_content_html );
	}

	/* Create HMTL for shortcode metabox */
	private function _render_shortcode_metabox() {
		
	}

	/* Create HMTL for a tab */
	private function _render_tab( $tab, $first = false ) {
		$icon = '';
		if ( isset( $tab['icon'] ) ) {
			$icon = '<i class="' . $tab['icon'] . '"></i>';
		}
		return '<div class="modula-tab' . ( $first ? ' active-tab' : '' ) . '" data-tab="modula-' . esc_attr( $tab['id'] ) . '">' . $icon . wp_kses_post( $tab['label'] ) . '</div>';
	}

	/* Create HMTL for a row */
	private function _render_row( $field ) {
		$format = '<tr><th scope="row"><label>%s</label></th><td>%s</td></tr>';

		$default = '';

		// Check if our field have a default value
		if ( isset( $field['default'] ) ) {
			$default = $field['default'];
		}

		// Get the current value of the field
		$value = $this->get_setting( $field['id'], $default );

		return sprintf( $format, wp_kses_post( $field['name'] ), $this->_render_field( $field, $value ) );
	}

	/* Create HMTL for a field */
	private function _render_field( $field, $value = '' ) {
		$html = '';
		$value = ( '' == $value && isset( $field['default'] ) ) ? $field['default'] : $value;

		switch ( $field['type'] ) {
			case 'text':
				$html = '<input type="text" class="regular-text" name="modula-settings[' . $field['id'] . ']" value="' . esc_attr( $value ) . '">';
				break;
			case 'select' :
				$html = '<select name="modula-settings[' . $field['id'] . ']" class="regular-text">';
				foreach ( $field['values'] as $key => $option ) {
					if ( is_array( $option ) ) {
						$html .= '<optgroup label="' . $key . '">';
						foreach ( $option as $key_subvalue => $subvalue ) {
							$html .= '<option value="' . $key_subvalue . '" ' . selected( $key_subvalue, $value, false ) . '>' . $subvalue . '</option>';
						}
						$html .= '</optgroup>';
					}else{
						$html .= '<option value="' . $key . '" ' . selected( $key, $option, false ) . '>' . $option . '</option>';
					}
				}
				if ( isset( $field['disabled'] ) && is_array( $field['disabled'] ) ) {
					$html .= '<optgroup label="' . $field['disabled']['title'] . '">';
					foreach ( $field['disabled']['values'] as $key => $disabled ) {
						$html .= '<option value="' . $key . '" disabled >' . $disabled . '</option>';
					}
					$html .= '</optgroup>';
				}
				$html .= '</select>';
				break;
			case 'ui-slider':
				$min  = isset( $field['min'] ) ? $field['min'] : 0;
				$max  = isset( $field['max'] ) ? $field['max'] : 100;
				$step = isset( $field['step'] ) ? $field['step'] : 1;
				$attributes = 'data-min="' . $min . '" data-max="' . $max . '" data-step="' . $step . '"';
				$html .= '<div class="slider-container modula-ui-slider-container">';
					$html .= '<input readonly="readonly"  name="modula-settings[' . $field['id'] . ']" type="text" class="rl-slider modula-ui-slider-input" id="input_' . $field['id'] . '" value="' . $value . '" ' . $attributes . '/>';
					$html .= '<div id="slider_' . $field['id'] . '" class="ss-slider modula-ui-slider"></div>';
				$html .= '</div>';
				break;
			case 'color' :
				$html .= '<div class="modula-colorpickers">';
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" class="modula-color" name="modula-settings[' . $field['id'] . ']" value="' . esc_attr( $value ) . '">';
				$html .= '</div>';
				break;
			case "toggle":
				$html .= '<div class="onoffswitch">';
					$html .= '<input type="checkbox" id="' . $field['id'] . '" name="modula-settings[' . $field['id'] . ']" class="onoffswitch-checkbox" value="1" ' . checked( 1, $value, false ) . ' >';
					$html .= '<label class="onoffswitch-label" for="' . $field['id'] . '"></label>';
				$html .= '</div>';
				break;
			case "custom_code":
				$html = '<div class="modula-code-editor" data-syntax="' . $field['syntax'] . '"><textarea name="modula-settings[' . $field['id'] . ']" id="modula-' . $field['id'] . '" class="large-text code"  rows="10" cols="50">' . $value . '</textarea></div>';
				break;
			case "hover-effect":
				$hovers = array( 
					'none'    => esc_html__( 'None', 'modula-gallery' ),
					'pufrobo' => esc_html__( 'Pufrobo', 'modula-gallery' ),
				);
				$pro_hovers = array(
					'fluid-up'  => esc_html__( 'Fluid Up', 'modula-gallery' ),
					'hide'      => esc_html__( 'Hide', 'modula-gallery' ),
					'quiet'     => esc_html__( 'Quiet', 'modula-gallery' ),
					'catinelle' => esc_html__( 'Catinelle', 'modula-gallery' ),
					'reflex'    => esc_html__( 'Reflex', 'modula-gallery' ),
					'curtain'   => esc_html__( 'Curtain', 'modula-gallery' ),
					'lens'      => esc_html__( 'Lens', 'modula-gallery' ),
					'appear'    => esc_html__( 'Appear', 'modula-gallery' ),
					'crafty'    => esc_html__( 'Crafty', 'modula-gallery' ),
					'seemo'     => esc_html__( 'Seemo', 'modula-gallery' ),
					'comodo'    => esc_html__( 'Comodo', 'modula-gallery' ),
				);
				$html .= '<select name="modula-settings[' . $field['id'] . ']"  class="regular-text">';
				foreach ( $hovers as $key => $option ) {
					$html .= '<option value="' . $key . '" ' . selected( $key, $value, false ) . '>' . $option . '</option>';
				}
				$html .= '<optgroup label="' . esc_html__( 'Hover Effects with PRO license', 'modula-gallery' ) . '">';
				foreach ( $pro_hovers as $key => $option ) {
					$html .= '<option value="' . $key . '" disabled>' . $option . '</option>';
				}
				$html .= '</optgroup></select>';
				$html .= '<p class="description">' . esc_html__( 'Select an hover effect', 'modula-gallery' ) . '</p>';

				// Creates effects preview
				$html .= '<div class="modula-effects-preview modula">';

				// Pufrobo Effect
				$html .= '<div class="panel panel-pufrobo items clearfix">';
				$html .= '<div class="item effect-pufrobo"><img src="' . MODULA_URL . '/assets/images/effect.jpg" class="pic"><div class="figc"><div class="figc-inner"><h2>Lorem ipsum</h2><p class="description">Quisque diam erat, mollisvitae enim eget</p><div class="jtg-social"><a class="fa fa-twitter" href="#"></a><a class="fa fa-facebook" href="#"></a><a class="fa fa-google-plus" href="#"></a><a class="fa fa-pinterest" href="#"></a></div></div></div></div>';
				$html .= '<div class="effect-compatibility">';
				$html .= '<p class="description">' . esc_html__( 'This effect is compatible with:', 'modula-gallery' );
				$html .= '<span><strong> ' . esc_html__( 'Title', 'modula-gallery' ) . '</strong></span>,';
				$html .= '<span><strong> ' . esc_html__( 'Social Icons', 'modula-gallery' ) . '</strong></span></p>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
				// Hook to change how hover effects field is rendered
				$html = apply_filters( "modula_render_hover_effect_field_type", $html, $field );
				break;
			default:
				/* Filter for render custom field types */
				$html = apply_filters( "modula_render_{$field['type']}_field_type", $html, $field );
				break;
		}

		if ( isset( $field['description'] ) && '' != $field['description'] ) {
			$html .= '<p class="description">' . $field['description'] . '</p>';
		}
		return $html;

	}

	public function print_modula_templates() {
		include 'modula-js-templates.php';
	}

}