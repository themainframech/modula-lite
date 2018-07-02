<?php

$js_config = array(
	"margin"          => absint( $data->settings['margin'] ),
	"enableTwitter"   => boolval( $data->settings['enableTwitter'] ),
	"enableFacebook"  => boolval( $data->settings['enableFacebook'] ),
	"enablePinterest" => boolval( $data->settings['enablePinterest'] ),
	"enableGplus"     => boolval( $data->settings['enableGplus'] ),
	"randomFactor"    => ( $data->settings['randomFactor'] / 100 ),
	'type'            => isset( $data->settings['type'] ) ? $data->settings['type'] : 'creative-gallery',
	'columns'         => isset( $data->settings['columns'] ) ? $data->settings['columns'] : 6,
	'gutter'          => isset( $data->settings['gutter'] ) ? $data->settings['gutter'] : 10,
);

$js_config = apply_filters( 'modula_gallery_settings', $js_config );

// print_r( $data->settings );

?>

<div id="<?php echo $data->gallery_id ?>" class="modula modula-gallery" data-config="<?php echo esc_attr( json_encode( $js_config ) ) ?>">
	<div class='items'>
		<?php

		foreach ( $data->images as $image ) {

			// Create link attributes like : title/rel
			$link_attributes = array(
				'href' => '#',
			);
			if ( in_array( $data->settings['lightbox'], array( 'prettyphoto', 'fancybox', 'swipebox', 'lightbox2' ) ) ) {
				$link_attributes['title'] = $image['caption'];
			}else{
				$link_attributes['data-title'] = $image['caption'];
			}

			if ( 'prettyphoto' == $data->settings['lightbox'] ) {
				$link_attributes['rel'] = 'prettyPhoto[' . $data->gallery_id . ']';
			}elseif ( 'lightbox2' == $data->settings['lightbox'] ) {
				$link_attributes['data-lightbox'] = $data->gallery_id;
			}else{
				$link_attributes['rel'] = $data->gallery_id;
			}

			// Create array with data in order to send it to image template
			$item_data = array(
				'image'            => $image,
				'lightbox'         => $data->settings['lightbox'],
				'size'             => $data->settings['img_size'],
				'hide_title'       => boolval( $data->settings['hide_title'] ),
				'hide_description' => boolval( $data->settings['hide_description'] ),
				'resizer'          => $data->resizer,
				'gallery_id'       => $data->gallery_id,
				'link_attributes'  => $link_attributes,
			);

			$data->loader->set_template_data( $item_data );
			$data->loader->get_template_part( 'items/item', $data->settings['effect'] );
		}

		?>
	</div>
</div>