<?php

/**
 * Registering Styles
 */
add_action('admin_enqueue_scripts', 'infer_form_admin_style');
function infer_form_admin_style() {
	if ($_GET['page'] == 'form_monitor') {
		wp_enqueue_style('infer-admin-style', INFERFORMURL . '/assets/css/style.css');
		wp_enqueue_script('infer-admin-script', INFERFORMURL . '/assets/js/form-script.js');

		$translation_array = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'errorNoName' => __( 'Please insert your name.', INFERFORMNAME ),
			'errorNoEmail' => __( 'Please insert your email.', INFERFORMNAME ),
			'errorInvalidEmail' => __( 'Please insert a valid email.', INFERFORMNAME ),
			'errorNoFile' => __( 'You must send at least one CSV file.', INFERFORMNAME ),
			'errorUploadingFile' => __( 'Your file is being uploaded, please wait and try again.', INFERFORMNAME ),
			'errorUploadedFile' => __( 'You must upload valid CSV files. Please refer to the CSV Template.', INFERFORMNAME ),
		);
		wp_localize_script( 'infer-admin-script', 'messages', $translation_array );

	}
}

/**
 * Registering Menus
 */
add_action( 'admin_menu', 'infer_form_menu_admin' );
function infer_form_menu_admin(  ) { 

	add_menu_page( __("Form Monitor", INFERFORMNAME), 'Form Monitor', 'manage_options', 'form_monitor', '', INFERFORMURL . '/assets/img/icon-infer.png' );
	add_submenu_page( 'form_monitor', __("Form Monitor", INFERFORMNAME), 'Form Monitor', 'manage_options', 'form_monitor', 'form_monitor_plugin_page' );

}



/**
 * Main Menu page
 */
function form_monitor_plugin_page(  ) { 

	wp_enqueue_script('plupload-handlers');

	?>
	<div class="wrap">
		
		<div class="infer-banner">
			<img src="<?php echo INFERFORMURL; ?>/assets/img/logo-infer.png" alt="Infer" />
		</div>
		
		<h2><?php _e("Predictive Lead Scoring & Analytics for Sales & Marketing", INFERFORMNAME); ?></h2>

		<p>Infer's form tester plugin can help you monitor the forms on your site and ensure they're operating properly. To begin testing the form on your site, please upload a list of pages that contain forms. This <a href="<?php echo INFERFORMURL; ?>/assets/sample-csv.csv">CSV Template</a> should help you get started.</p>

		<div class="row">

			<div class="first-column">
				
				<form enctype="multipart/form-data" method="post" action="<?php echo admin_url('media-new.php'); ?>" class="<?php echo esc_attr( $form_class ); ?>" id="file-form">

					<?php media_upload_form(); ?>

					<script type="text/javascript">
					var post_id = 0, shortform = 3;
					</script>
					<input type="hidden" name="post_id" id="post_id" value="0" />
					<?php wp_nonce_field('media-form'); ?>
					<div id="media-items" class="hide-if-no-js"></div>
				</form>

				<h2>Latest reports <a href="#">Download reports</a></h2>

				<table>
					<tr><td>May 1, 2016</td></tr>
					<tr><td>May 15, 2016</td></tr>
					<tr><td>May 31, 2016</td></tr>
				</table>
				To maintain a complete log of reports, please submit your email adress.
			</div>

			<div class="second-column">
				<div class="postbox ">
					<h2>I want to test my forms</h2>
					<div class="inside">
						<fieldset>
							<input type="radio" name="frequency" value="0" id="frequency-0" checked="checked"> <label for="frequency-0">Daily</label>
							<br><input type="radio" name="frequency" value="1" id="frequency-1"> <label for="frequency-1">Weekly</label>
							<br><input type="radio" name="frequency" value="2" id="frequency-2"> <label for="frequency-2">Monthly</label>
						</fieldset>
						<p>Please email my report here:</p>
						<input type="text" id="report-name" placeholder="Full name" />
						<input type="text" id="report-email" placeholder="E-mail" />
						<button id="report-submit">Submit</button>
						<p class="error-message"></p>
					</div>
				</div>
			</div>

		</div>

	</div>
	<?php

}

