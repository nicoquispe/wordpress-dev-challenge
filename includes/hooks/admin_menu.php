<?php 
function we_remote_admin_menu() {
	add_menu_page(
		__( 'Bads urls', 'we_remote' ),
		'Bads urls',
		'manage_options',
		'urls_bads',
		'menu_page_bads_urls',
	);
}

function menu_page_bads_urls() {
	we_remote_scans_posts();
	$scans_posts_ids = get_option( 'scans_posts_ids', '' );
	if ( !( $scans_posts_ids && is_array( $scans_posts_ids ) && count( $scans_posts_ids ) ) ) {
		$scans_posts_ids = [];
	}

	$all_urls = [];

	if ( $scans_posts_ids && is_array( $scans_posts_ids ) && count( $scans_posts_ids ) ) {
		foreach ($scans_posts_ids as $keyPost => $_post) {
			$all_urls_bads =  get_post_meta( $_post, 'all_urls_bads' );
			if ( !( $all_urls_bads && is_array( $all_urls_bads ) && count( $all_urls_bads ) ) ) {
				$all_urls_bads = [];
			}
			if( is_array( $all_urls_bads[0] ) )
				$all_urls = array_merge( $all_urls, $all_urls_bads[0] );
			else 
				$all_urls = array_merge( $all_urls, $all_urls_bads );
		}
	}
	?>
	<div class="wrap">
		<table class="wp-list-table widefat fixed striped table-view-list posts">
			<thead>
				<tr>
					<td>URL</td>
					<td>Estado</td>
					<td>Origen</td>
				</tr>
			</thead>
			<tbody>
				<?php if ( $all_urls && is_array( $all_urls ) && count( $all_urls ) ): ?>
					<?php foreach ($all_urls as $key => $url): ?>
						<tr>
							<td><?= $url['url'] ?></td>
							<td><?= $url['estado'] ?></td>
							<td><a href="<?= get_the_permalink( $url['post_id'] ); ?>"><?= get_the_title( $url['post_id'] ) ?></a></td>
						</tr>
					<?php endforeach ?>
				<?php else: ?>
					<tr>
						<td colspan="3">
							No hay urls fallidas
						</td>
					</tr>
				<?php endif ?>
			</tbody>
		</table>
	</div>
	<?php 
}