<?php 
function we_remote_cron_schedules($schedules){
	if(!isset($schedules["5min"])){
		$schedules["5min"] = array(
			'interval' => 5*60,
			'display' => __('Once every 5 minutes')
		);
	}
	if(!isset($schedules["1min"])){
		$schedules["1min"] = array(
			'interval' => 1*60,
			'display' => __('Once every 1 minutes')
		);
	}
	return $schedules;
}

function we_remote_scans_posts() {
	
	$scans_posts_ids = get_option( 'scans_posts_ids', '' );
	if ( !( $scans_posts_ids && is_array( $scans_posts_ids ) && count( $scans_posts_ids ) ) ) {
		$scans_posts_ids = [];
	}

	$all_posts = get_posts([
		'post_type' => 'post',
		'post_status' => 'publish',
		'meta_query' => [
			'relation' => 'OR',
			[
				'key' => '_modified_content',
				'value' => ['1'],
				'compare' => 'IN'
			],
			[
				'key' => '_modified_content',
				'compare' => 'NOT EXISTS'
			],
		]
	]);

	if ( $all_posts && is_array( $all_posts ) && count( $all_posts ) ) {
		foreach ($all_posts as $keyPost => $_post) {
			update_post_meta( $_post->ID, 'all_urls_bads', '' );
			if (($key = array_search($_post->ID, $scans_posts_ids)) !== false) {
			    unset($scans_posts_ids[$key]);
			}
			$data = $_post->post_content;
			$data = strip_tags($data,"<a>");
			$d = preg_split("/<\/a>/",$data);
			$all_urls = [];
			foreach ( $d as $k=>$u ){
				if( strpos($u, "<a href=") !== FALSE ){
					$u = preg_replace("/.*<a\s+href=\"/sm","",$u);
					$u = preg_replace("/\".*/","",$u);
					$valid_url = wp_http_validate_url( $u );

					$incluir = false;
					$estado = '';

					if ( !$valid_url ) {
						$incluir = true;
						$estado = 'Enlace malformado';
						$parse_url = parse_url($u);
						if ( strpos($u, ' ') === false ) {
							if ( !isset( $parse_url['scheme'] ) ) {
								$estado = 'Protocolo no especificado';
							}
						} else {
							$u = str_replace( ['%2F', '%3A'], ['/', ':'], rawurlencode( $u ) );
						}
					} else {
						$is_ssl = strpos($u, 'https://') === 0;
						if ( !$is_ssl ) {
							$incluir = true;
							$parse_url = parse_url($u);
							if ( isset( $parse_url['scheme'] ) ) {
								$estado = 'Enlace inseguro';
							} else {
								$estado = 'Protocolo no especificado';
							}
						}
					}
					if ( !$incluir ) {
						$response = wp_remote_get( $u );
						if ( is_array( $response ) && ! is_wp_error( $response ) ) {
							$response_code = wp_remote_retrieve_response_code( $response );
							if ( $response_code > 400 ) {
								$incluir = true;
								$estado = "Error {$response_code}";
							}
						} else {
							$incluir = true;
							$estado = "Error {$result->get_error_message()}";
						}
					}
					if ( $incluir ) {
						$all_urls[] = [
							'post_id' => $_post->ID,
							'url' => $u,
							'estado' => $estado,
						];
					}
				}
			}
			if ( $all_urls && is_array( $all_urls ) && count( $all_urls ) ) {
				$scans_posts_ids[] = $_post->ID;
				update_post_meta( $_post->ID, 'all_urls_bads', $all_urls );
			}
			update_post_meta($_post->ID, '_modified_content', '');
		}
		update_option( 'scans_posts_ids', $scans_posts_ids );
	}
}