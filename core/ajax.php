<?php

/*
 * Check if files are valid and are CSV
 */
add_action( 'wp_ajax_infer_form_sanitize_files', 'infer_form_sanitize_files' );
function infer_form_sanitize_files() {

	$files = $_POST['files'];
	$valids = array();

	foreach ($files as $value) {
		
		$file = get_post( intval($value) );
		
		if ($file->post_type != 'attachment')
			continue;
		
		if ($file->post_mime_type != 'text/csv')
			continue;
		
		$valids[] = $value;
	}

	echo json_encode($valids);
	wp_die();
}