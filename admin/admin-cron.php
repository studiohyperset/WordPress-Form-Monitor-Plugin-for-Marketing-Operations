<?php

/*
 * Register custom recurrence
 */
function infer_form_register_recurrence( $schedules ) {

	$schedules['weekly'] = array(
		'display' => __( 'Weekly', INFERFORMNAME ),
		'interval' => 604800,
	);

	$schedules['monthly'] = array(
		'display' => __( 'Monthly', INFERFORMNAME ),
		'interval' => 2635200,
	);

	return $schedules;

}
add_filter( 'cron_schedules', 'infer_form_register_recurrence' );


/*
 * Register Cron Job for a test
 */
function infer_form_create_test_cron( $name, $email, $frequency, $test ) {


	//Get latest schedule name
	$latest = intval(get_option( 'infer_form_latest_schedule', '0' )) + 1;
	update_option( 'infer_form_latest_schedule', $latest );

	//Save current args in DB so cron get data from DB
	$scheduleName = 'infer_form_schedule_'. $latest;
	update_option( $scheduleName, array( $name, $email, $test ) );

	//Set frequency
	switch ($frequency) {
		case '0': $frequency = 'daily'; break;
		case '1': $frequency = 'weekly'; break;
		default: $frequency = 'monthly'; break;
	}

	wp_schedule_event( time(), $frequency, 'infer_form_cron_execute', array( $scheduleName ) );

}


/*
 * Execute cron
 */
add_action('infer_form_cron_execute', 'infer_form_cron_do_execute');
function infer_form_cron_do_execute( $args ) {

	$arrContextOptions=array(
	    "ssl"=>array(
	        "verify_peer"=>false,
	        "verify_peer_name"=>false,
	    ),
	);

	//Get data to run tests
	$data = get_option( $args[0], '0' );
	if ($data != '0') {

		$result = array();
		$name = $data[0];
		$email = $data[1];

		foreach ($data[2] as $key => $value) {
			set_time_limit(60);

			$response = file_get_contents($value['formurl'], false, stream_context_create($arrContextOptions));
			if ($response !== false) {

				//Form URL online
				$findurl = str_replace( array('https:', 'http:'), '', $value['targeturl'] );
				if ( strpos($response, $findurl) !== false ) {

					//Form Target is present on URL
					if (count($value['fields']) > 0) {

						$testData = array();
						for ($i=0; $i < count($value['fields']); $i++) { 
							$testData[$value['fields'][$i]] = $value['values'][$i];
						}

						$arrContextOptions = array('http' =>
						    array(
						        'method'  => 'POST',
						        'header'  => 'Content-type: application/x-www-form-urlencoded',
						        'content' => http_build_query( $testData ),
						    )
						);

					}

					$response = file_get_contents($value['targeturl'], false, stream_context_create($arrContextOptions));
					if ($response !== false) {

						//Form Target is online. Let's try guess the result
						$result[] = array(
							'url' => $value['formurl'],
							'result' => 'form_online'
						);

					} else {

						//Form Target is offline
						$result[] = array(
							'url' => $value['formurl'],
							'result' => 'form_offline'
						);

					}

				} else {

					//Form Target is not present on URL		
					$result[] = array(
						'url' => $value['formurl'],
						'result' => 'form_not_present'
					);

				}


			} else {

				//Form URL offline
				$result[] = array(
					'url' => $value['formurl'],
					'result' => 'url_offline'
				);

			}

		}

		$logged = get_option( 'infer_form_monitor_log', array() );
		$logged[] = array(
			'date' => date('U'),
			'result' => $result,
			'link' => '#'
		);
		update_option( 'infer_form_monitor_log', $logged );

	} else {

		//Data does not exits. Let's clear the cron
		wp_clear_scheduled_hook( 'infer_form_cron_execute', array( $args[0] ) );

	}
}