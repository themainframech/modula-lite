<?php

$image = $data->image;

$image_full = wp_get_attachment_image_src( $image['id'], 'full' );
$image_url = '';

// If the image is not resized we will try to resized it now
// This is safe to call every time, as resize_image() will check if the image already exists, preventing thumbnails from being generated every single time.
if ( $image_full ) {
	if ( $image_full[1] > $image_full[2] ) {
		$image_url = $data->resizer->resize_image( $image_full[0], $data->size, 99999 );
	}else{
		$image_url = $data->resizer->resize_image( $image_full[0], 99999, $data->size );
	}
}

if ( '' == $data->lightbox ) {
	$data->link_attributes['href'] = '#';
}elseif ( 'attachment-page' == $data->lightbox ) {
	$data->link_attributes['href'] = get_attachment_link( $image['id'] );
}else{
	$data->link_attributes['href'] = $image_full[0];
}

$hasTitle = empty( $image['title'] ) ? ' notitle' : '';

?>


<div class="item<?php echo $hasTitle ?>">
	<a<?php echo Modula_Helper::generate_attributes( $data->link_attributes ) ?> class="tile-inner">
		<img data-valign='<?php echo esc_attr( $image['valign'] ) ?>' alt='<?php echo esc_attr( $image['alt'] ) ?>' data-halign='<?php echo esc_attr( $image['halign'] ) ?>' class='pic' src='<?php echo esc_url( $image_url ) ?>' data-src='<?php echo esc_url( $image_url ) ?>' />
	</a>
</div>