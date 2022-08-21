<?php 
function we_remote_shortcode_mc_citacion( $atts = array() ) {
	global $post;
	$atts = shortcode_atts( array(
		'post_id' => $post->ID,
	), $atts, 'mc-citacion' );
	if ( $atts['post_id'] ) {
		$citacion = get_post_meta($atts['post_id'], '_we_remote_citacion_editor', true);
		if ( $citacion ) {
			return '<div class="citacion">'. wpautop($citacion) .'</div>';
		}
	}
	return '';
}
