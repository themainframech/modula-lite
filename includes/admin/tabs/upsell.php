<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Features
 */

$features = array(
	array(
		'title'       => esc_html__( 'Lightboxes', 'modula-lite' ),
		'description' => esc_html__( 'Lightgallery, Lightbox2, PrettyPhoto, Magnific Gallery, SwipeBox, FancyBox', 'modula-lite' ),
		'lite'        => '1',
		'pro'         => '6',
	),
	array(
		'title'       => esc_html__( 'Hover Effects', 'modula-lite' ),
		'description' => esc_html__( 'Choose from 12 different hover effects.', 'modula-lite' ),
		'lite'        => '1',
		'pro'         => '12',
	),
	array(
		'title'       => esc_html__( 'Loading effects', 'modula-lite' ),
		'description' => esc_html__( 'Build your own effects with these new customizations.', 'modula-lite' ),
		'lite'        => '1',
		'pro'         => '4',
	),
	array(
		'title'       => esc_html__( 'Filters', 'modula-lite' ),
		'description' => esc_html__( 'Let visitors filter your gallery items with a single click', 'modula-lite' ),
		'lite'        => '<span class="dashicons dashicons-no-alt"></span>',
		'pro'         => '<span class="dashicons dashicons-yes"></span></i>',
	),
	array(
		'title'       => esc_html__( 'Number of images', 'modula-lite' ),
		'description' => esc_html__( '', 'modula-lite' ),
		'lite'        => '20',
		'pro'         => 'Unlimited',
	),
	array(
		'title'       => esc_html__( 'Video Addon', 'modula-lite' ),
		'description' => esc_html__( 'You asked we implemented, now you can add videos to your gallery.', 'modula-lite' ),
		'lite'        => '<span class="dashicons dashicons-no-alt"></span>',
		'pro'         => '<span class="dashicons dashicons-yes"></span></i>',
	),
	array(
		'title'       => esc_html__( 'Support', 'modula-lite' ),
		'description' => esc_html__( 'You can access our dedicated support team.', 'modula-lite' ),
		'lite'        => 'WordPress.org Forums',
		'pro'         => 'Dedicated Support Team',
	),

);
?>
<div class="featured-section features">
	<table class="free-pro-table">
		<thead>
		<tr>
			<th></th>
			<th><?php _e( 'Lite', 'modula-lite' ); ?></th>
			<th><?php _e( 'PRO', 'modula-lite' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ( $features as $feature ) : ?>
			<tr>
				<td class="feature">
					<h3><?php echo $feature['title']; ?></h3>
					<?php if ( isset( $feature['description'] ) ) : ?>
						<p><?php echo $feature['description']; ?></p>
					<?php endif ?>
				</td>
				<td class="lite-feature">
					<?php echo $feature['lite']; ?>
				</td>
				<td class="pro-feature">
					<?php echo $feature['pro']; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td></td>
			<td colspan="2" class="text-right">
				<a href="https://wp-modula.com/?utm_source=modula-lite&utm_medium=about-page&utm_campaign=upsell" target="_blank" class="button button-primary button-hero"><span class="dashicons dashicons-cart"></span><?php _e( 'Get The Pro Version Now!', 'modula-lite' ); ?>
				</a></td>
		</tr>
		</tbody>
	</table>
</div>
