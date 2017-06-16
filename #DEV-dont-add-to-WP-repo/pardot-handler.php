<?php

if (isset($_GET['test']) && $_GET['test']=='do_test') {
	$reportdata = array(
		array(
			'url' => 'http://google.com',
			'status' => 'green',
               'result' => 'form_online'
		),
		array(
			'url' => 'http://youtube.com',
			'status' => 'red',
               'result' => 'form_offline'
		),
		array(
			'url' => 'http://facebook.com',
			'status' => 'yellow',
               'result' => 'url_offline'
		),
	);
	our_api_handler('bruno@cantuaria.net.br', 'Test Report', $reportdata, 'https://google.com');
}

//A list of domains blacklisted on our script
$blacklist = array();

//First a simple key check
if (!isset($_POST['secret']))
	die("0");
if ($_POST['secret'] != 'infer-pardot-secret')
	die("0");

//Secondly lets ensure the domain is sent
if (!isset($_POST['domain']))
	die("0");

//Check if referer is blacklisted
if ( in_array($_POST['domain'], $blacklist) )
	die("0");

//Action will handle the correct function
if (!isset($_POST['action']))
	die("0");

//Return a secret key to be sent again to validate access
if ($_POST['action'] == 'secret') {

	//Return the hash to be sent in deeper call
	echo our_hash_creator();
	die();

} elseif ($_POST['action'] == 'pardot') {

	//Lets check if key was sent
	if (!isset($_POST['key']))
		die("0");

	//Check if hash matches
	$hash = our_hash_creator();
	if ($hash != $_POST['key'])
		die("0");

	if ( ( isset($_POST['email']) && isset($_POST['title']) && isset($_POST['result']) && isset($_POST['url'])  && isset($_POST['date']) ) ) {

		//Everything ok. Let's send emails!
		our_api_handler( $_POST['email'], $_POST['title'], json_decode($_POST['result']), $_POST['url'], $_POST['date'] );

	} else {
		die("0");
	}

} else {
	die("0");
}

function our_hash_creator() {

	//Let's create a secret random passphrase
	$hash = 'pardot' . $_POST['domain'] . 'infer';

	//Return the hash to be sent in deeper call
	return crypt($hash, 'infer-secret-salt');

}


function our_api_handler( $to, $title, $result, $link ) {

	//Mounting the URL and Data for retrieving API
	$url = 'https://pi.pardot.com/api/login/version/3';
	$data = array(
        'email' => 'quimby@studiohyperset.com',
        'password' => '8@QL(>k!B#',
	);

	$response = our_api_caller( $url, $data );

	//Some error ocurred while retrieveing key. Sending an email to quimby
	if (!$response->api_key) {
		$headers = 'From: noreply@infer.com' . "\r\n" .
				'Reply-To: noreply@infer.com' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
		mail('quimby@studiohyperset.com', '[PARDOT-API-ERROR] Could not retrieve API Key.', 'Could not retrieve API Key.', $headers);
		die("0");
	}
	$apikey = $response->api_key;

	//Let's get the email template
	$url = 'https://pi.pardot.com/api/emailTemplate/version/3/do/read/id/61740';
	$data = array(
        'api_key' => $apikey,
        'emailTemplateId' => 61740
	);

	$response = our_api_caller( $url, $data );

	//Some error ocurred while retrieveing the template. Sending an email to quimby
	if ($response->emailTemplate->error != 0) {
		$headers = 'From: noreply@infer.com' . "\r\n" .
				'Reply-To: noreply@infer.com' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
		mail('quimby@studiohyperset.com', '[PARDOT-API-ERROR] Could not retrieve Email Template.', 'Could not retrieve Email Template.', $headers);
		die("0");
	}
	$content = $response->emailTemplate->htmlMessage;

	//Date defined. Maybe get the timezone of user?
	date_default_timezone_set('America/Los_Angeles');

	//Let's mount the table result
	$report = '';
	foreach ($result as $value) {
          
		if (isset($value->result)) {
			if ($value->result == 'form_online')
				$color = '#81c784';
			else if ($value->result == 'form_offline')
				$color = '#f8e05d';
			else if ($value->result == 'form_not_present')
				$color = '#ff7043';
			else if ($value->result == 'url_offline')
				$color = '#bb502e';
			else
				$color = '#fff';
		} else {
			$color = '#fff';
		}

		$report .= '<tr align="left" style="border: 0; border-collapse: collapse; border-spacing: 0"><td><table cellspacing="0" cellpadding="0" border="0" style="border: 0; border-collapse: collapse; border-spacing: 0; margin-top: 0px !important; min-width: 520px; padding-top: 0px !important"><tbody><tr style="border: 0; border-collapse: collapse; border-spacing: 0"><td width="405" align="left" valign="middle" height="67" class=""><p style="color: #000000 !important; font-family: Arial; font-size: 16px; font-style: normal; line-height: 24px; margin: 0; padding: 0 0 0 22px; text-shadow: none">'. $value->url .'</p></td><td width="115" align="center" valign="middle" height="67" class=""><span style="background: '. $color .'; border-radius: 100%; display: inline-block; height: 16px; width: 16px"></span></td></tr></tbody></table></td></tr>';
	}

	//Let's replace the variables
	$find = array( '###REPORT-TITLE###', '###REPORT-DATE###', '###REPORT-ENTRY-URL###', '###REPORT-LINK###' );
	$replace = array( $title, date('l jS \of F Y'), $report, $link );
	$content = str_replace($find, $replace, $content);

	//Let's create the prospect
	$url = 'https://pi.pardot.com/api/prospect/version/3/do/create/email/'. $to;
	$data = array(
        'api_key' => $apikey,
	   'prospect_email' => $to,
	   'campaign_id' => 35580,
	   'email_template_id' => 61740,
	);

	$response = our_api_caller( $url, $data );

	//Let's prep the data for sending email
	$url = 'https://pi.pardot.com/api/email/version/3/do/send/prospect_email/'. $to;
	$data = array(
        'api_key' => $apikey,
        'campaign_id' => 35580,
        'prospect_email' => $to,
        'email_template_id' => 61740,
        'html_content' => $content
	);

	$response = our_api_caller( $url, $data );

	//Email sent!
	if ($response->email->id > 0)
		die("1");
	else {
		$headers = 'From: noreply@infer.com' . "\r\n" .
				'Reply-To: noreply@infer.com' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
          mail('quimby@studiohyperset.com', '[PARDOT-API-ERROR] Error while sending an email.', 'An error ocurred after the access to the Pardot API: '. $response->err, $headers);
		die("0");
     }

	die("0");
}

function our_api_caller( $url, $data ) {

	$data['user_key'] = 'eb98d03abbc4e263cdc299d282766adc';
	$data['format'] = 'json';

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

	$response = json_decode(file_get_contents($url, false, stream_context_create($arrContextOptions)));
	return $response;

}


//It should never get here
die("0");
