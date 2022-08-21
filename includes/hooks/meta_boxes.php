<?php 
function we_remote_meta_boxes() {
	add_meta_box( 'citacion-post', __( 'Citacion', 'we_remote' ), 'citacion_post_callback', 'post' );
}

function citacion_post_callback( $post ) {
	$_we_remote_citacion_editor = get_post_meta($post->ID, '_we_remote_citacion_editor', true); 
	wp_editor( esc_html($_we_remote_citacion_editor), '_we_remote_citacion_editor', array() );
	?>
	<?php 
}
function we_remote_save_wp_editor_content(){
	global $post;
	if(isset($_POST['_we_remote_citacion_editor'])){
		update_post_meta($post->ID, '_we_remote_citacion_editor', $_POST['_we_remote_citacion_editor']);
	}
	update_post_meta( $post->ID, '_modified_content', '1' );
}
