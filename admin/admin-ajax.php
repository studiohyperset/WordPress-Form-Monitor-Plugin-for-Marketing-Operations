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
		if (($header = fgetcsv($handle, 0, ',') ) !== FALSE) {

			//Check if sample header is present, if so ignore line
			if (array_search('form url', $header) !== FALSE) {
				$header = fgetcsv($handle, 0, ',');
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

	$test = array();

	//Validate email
	$email = (isset($_POST['email'])) ? $_POST['email'] : '' ;
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	    wp_die( '0' );
	}

	//Validate name
	$name = (isset($_POST['name'])) ? $_POST['name'] : '' ;
	if ( $name == '' ) {
	    wp_die( '1' );
	}

	//Validate frequency
	$frequency = (isset($_POST['frequency'])) ? $_POST['frequency'] : '' ;
	if ( $frequency != '0' && $frequency != '1' && $frequency != '2' ) {
	    wp_die( '2' );
	}


	$files = $_POST['files'];
	ini_set("auto_detect_line_endings", true);
	foreach ($files as $value) {
		
		$handle = @fopen( get_attached_file( $value ) , 'r');

		//Read CSV Header
		while (($row = fgetcsv($handle, 0, ',')) !== FALSE) {
			//Check if first value is URL
			if (filter_var( $row[0], FILTER_VALIDATE_URL) !== false) {
				
				//Save URL
				$url = $row[0];
				$total = count($row);

				$fields = array();
				$values = array();

				//If row have fields and values set
				if ( ($total > 2) && ($total % 2 != 0) ) {
					$breakpoint = ($total-1)/2;
					$fields = array_slice( $row, 1, $breakpoint );
					$values = array_slice( $row, 1 + $breakpoint );
				}

				$test[] = array(
					'url' => $url,
					'fields' => $fields,
					'values' => $values
				);

			}

		}
		
	}

	if ( !empty($test) ) {

		infer_form_create_test_cron( $name, $email, $frequency, $test );
		wp_die( '100' );

	} else {

		wp_die( '3' );

	}

}


/*
 * Delete the test
 */
add_action( 'wp_ajax_infer_form_delete_test', 'infer_form_delete_test' );
function infer_form_delete_test() {

	$test = (isset($_POST['test'])) ? $_POST['test'] : false ;
	if ($test !== false) {
		delete_option( $test );
		wp_clear_scheduled_hook( 'infer_form_cron_execute', array( $test ) );
	}

	wp_die();

}