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

	//Get data to run tests
	$data = get_option( $args[0], '0' );
	if ($data != '0') {

		

	} else {

		//Data does not exits. Let's clear the cron
		wp_clear_scheduled_hook( 'infer_form_cron_execute', array( $args[0] ) );

	}
}