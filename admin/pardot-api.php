<?php

function infer_form_send_report_pardot($key, $result, $email, $date) {
	
	//We need some time to comunicate with the API
	set_time_limit(60);

	//We must first get the token
	$data = array( 'action' => 'secret' );
	$data['key'] = infer_form_file_get_contents( $data );

	//Then send info
	$data['action'] = 'pardot';
	$data['title'] = 'Infer Form Report';
	$data['email'] = $email;
	$data['result'] = json_encode($result);
	$data['url'] = admin_url() . 'admin.php?page=form_monitor&generate=pdf&report='. $key;
	$data['date'] = date('m/d/Y', $date);

	if (infer_form_file_get_contents( $data ) == '0') {
		//Something went wrong
	}

}


function infer_form_file_get_contents( $data ) {

	$ourdomain = apache_request_headers();

	$data['secret'] = 'infer-pardot-secret';
	$data['domain'] = $ourdomain['Host'];

	$postdata = http_build_query( $data );

	$arrContextOptions = array(
		'http' => array(
			'method' => 'POST',
			'header'  => 'Content-type: application/x-www-form-urlencoded',
			'content' => $postdata
		),
	    'ssl' => array(
	        'verify_peer' => false,
	        'verify_peer_name' => false
	    ),
	);

	$response = file_get_contents('https://www.infer.com/form-tester-plugin/pardot-handler.php', false, stream_context_create($arrContextOptions));
	
	return $response;

}