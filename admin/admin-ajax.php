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


/*
 * Check if files are compliant with the expected (sample) CSV
 */
add_action( 'wp_ajax_infer_form_compliance_files', 'infer_form_compliance_files' );
function infer_form_compliance_files() {

	$files = $_POST['files'];
	$valids = array();
	ini_set("auto_detect_line_endings", true);

	foreach ($files as $value) {
		
		$handle = @fopen( get_attached_file( $value ) , 'r');

		//Read CSV Header
		if (($header = fgetcsv($handle, 0, ';') ) !== FALSE) {

			//Check if sample header is present, if so ignore line
			if (array_search('form url', $header) !== FALSE) {
				$header = fgetcsv($handle, 0, ';');
			}

			//Check if first value is URL
			if (filter_var( $header[0], FILTER_VALIDATE_URL) !== FALSE) {
				$valids[] = $value;
			}

		}
		
	}

	echo json_encode($valids);
	wp_die();
}


/*
 * Register the test
 */
add_action( 'wp_ajax_infer_form_register_test', 'infer_form_register_test' );
function infer_form_register_test() {

	$files = $_POST['files'];
	$valids = 0;
	ini_set("auto_detect_line_endings", true);

	foreach ($files as $value) {
		
		$handle = @fopen( get_attached_file( $value ) , 'r');

		//Read CSV Header
		foreach ( fgetcsv($handle, 0, ';') as $row ) {
			
			//Check if first value is URL
			if (filter_var( $header[0], FILTER_VALIDATE_URL) !== FALSE) {
				
			}

		}
		
	}

	if ($valid > 0) {
		wp_die( '1' );
	} else {
		wp_die( '0' );
	}

}