<div class="<?php echo implode( ' ', $data->item_classes ) ?>"<?php echo Modula_Helper::generate_attributes( $data->item_attributes ) ?> >
	<a<?php echo Modula_Helper::generate_attributes( $data->link_attributes ) ?> class="<?php echo implode( ' ', $data->link_classes ) ?>"></a>
	<img class='<?php echo implode( ' ', $data->img_classes ) ?>'<?php echo Modula_Helper::generate_attributes( $data->img_attributes ) ?>/>
</div>