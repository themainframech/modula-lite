<?php

function modula_generate_image_links( $item_data, $item, $settings ){

	$image_full = wp_get_attachment_image_src( $item['id'], 'full' );
	$image_url = '';

	// If the image is not resized we will try to resized it now
	// This is safe to call every time, as resize_image() will check if the image already exists, preventing thumbnails from being generated every single time.
	if ( $image_full ) {
		$resizer = new Modula_Image();
		
		if ( $image_full[1] > $image_full[2] ) {
			$image_url = $resizer->resize_image( $image_full[0], $settings['img_size'], 99999 );
		}else{
			$image_url = $resizer->resize_image( $image_full[0], 99999, $settings['img_size'] );
		}
	}

	$item_data['image_full'] = $image_full[0];
	$item_data['image_url']  = $image_url;

	// Add src/data-src attributes to img tag
	$item_data['img_attributes']['src'] = $image_url;
	$item_data['img_attributes']['data-src'] = $image_url;

	return $item_data;
}

function modula_check_lightboxes_and_links( $item_data, $item, $settings ) {

	// Create link attributes like : title/rel
	$item_data['link_attributes']['href'] = '#';

	if ( '' == $settings['lightbox'] ) {
		$item_data['link_attributes']['href'] = '#';
	}elseif ( 'attachment-page' == $settings['lightbox'] ) {
		$item_data['link_attributes']['href'] = get_attachment_link( $item['id'] );
	}else{
		$item_data['link_attributes']['href'] = $item_data['image_full'];
	}

	if ( in_array( $settings['lightbox'], array( 'prettyphoto', 'fancybox', 'swipebox', 'lightbox2' ) ) ) {
		$item_data['link_attributes']['title'] = $item['caption'];
	}else{
		$item_data['link_attributes']['data-title'] = $item['caption'];
	}

	if ( 'prettyphoto' == $settings['lightbox'] ) {
		$item_data['link_attributes']['rel'] = 'prettyPhoto[' . $settings['gallery_id'] . ']';
	}elseif ( 'lightbox2' == $settings['lightbox'] ) {
		$item_data['link_attributes']['data-lightbox'] = $settings['gallery_id'];
	}else{
		$item_data['link_attributes']['rel'] = $settings['gallery_id'];
	}

	return $item_data;
}

function modula_check_hover_effect( $item_data, $item, $settings ){

	$hover_effect_elements = Modula_Helper::hover_effects_elements( $settings['effect'] );

	if ( ! $hover_effect_elements['title'] ) {
		$item_data['hide_title'] = true;
	}

	if ( ! $hover_effect_elements['description'] ) {
		$item_data['hide_description'] = true;
	}

	if ( ! $hover_effect_elements['social'] ) {
		$item_data['hide_socials'] = true;
	}

	if ( 'none' != $settings['effect'] ) {
		$item_data['item_classes'][] = 'effect-' . $settings['effect'];
	}

	return $item_data;
}

function modula_check_custom_grid( $item_data, $item, $settings ) {

	if ( 'custom-grid' != $settings['type'] ) {
		return $item_data;
	}

	$item_data['item_attributes']['data-width'] = $item['width'];
	$item_data['item_attributes']['data-height'] = $item['height'];

	return $item_data;

}