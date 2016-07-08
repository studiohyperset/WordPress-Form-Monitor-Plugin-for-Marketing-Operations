<?php

/**
 * Registering Styles
 */
add_action('admin_enqueue_scripts', 'infer_form_admin_style');
function infer_form_admin_style() {
	wp_enqueue_style('infer-admin-dashicon', INFERFORMURL . '/assets/css/dashicon.css');

	if ($_GET['page'] == 'form_monitor') {
		wp_enqueue_style('infer-admin-style', INFERFORMURL . '/assets/css/style.css');
		wp_enqueue_script('infer-admin-script', INFERFORMURL . '/assets/js/form-script.js');

		$translation_array = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'errorNoName' => __( 'Please type your first and last name.', INFERFORMNAME ),
			'errorNoEmail' => __( 'Please add your email address.', INFERFORMNAME ),
			'errorInvalidEmail' => __( 'Please insert a valid email address.', INFERFORMNAME ),
			'errorNoFile' => __( 'You must upload at least one CSV file.', INFERFORMNAME ),
			'errorUploadingFile' => __( 'Your file is being uploaded. Please wait and try again.', INFERFORMNAME ),
			'errorUploadedFile' => __( 'The uploaded file does not seems to be a CSV file.', INFERFORMNAME ),
			'errorUploadedFileInvalid' => __( 'You must upload a valid CSV file. Please refer to the CSV template above.', INFERFORMNAME ),
			'errorInvalidFrequency' => __( 'Please select a test frequency.', INFERFORMNAME ),
		);
		wp_localize_script( 'infer-admin-script', 'messages', $translation_array );

	}
}

/**
 * Registering Menus
 */
add_action( 'admin_menu', 'infer_form_menu_admin' );
function infer_form_menu_admin(  ) { 

	add_menu_page( __("Form Monitor", INFERFORMNAME), 'Form Monitor', 'manage_options', 'form_monitor', '', 'none' );
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
			<a href="https://www.infer.com/" target="_blank"></a>
		</div>
		
		<h2><?php _e("Automated Form Monitor for Marketing Operations", INFERFORMNAME); ?></h2>

		<p><?php echo sprintf( __("%s automated form monitor plugin can help marketing teams monitor site forms and ensure they're operating properly. To begin testing the forms on your site, please upload a list of pages that contain forms. This %s should help you get started. If you have any other questions, this %s should help. You can also post a question in the %s.", INFERFORMNAME), '<a href="https://www.infer.com/" target="_blank">'. __("Infer's", INFERFORMNAME) . '</a>', '<a href="'. INFERFORMURL .'/assets/sample-csv.csv">'. __('CSV template', INFERFORMNAME) .'</a>','<a href="https://www.infer.com/automated-form-monitor-wordpress-plugin-for-marketing-operations/" target="_blank">'. __('blog post', INFERFORMNAME) .'</a>','<a href="https://wordpress.org/support/plugin/infer-automated-form-monitor-for-marketing-operations/" target="_blank">'. __('plugin support forum', INFERFORMNAME) .'</a>'); ?></p>

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
                
                <div class="postbox ">
					<h2><?php _e("I want to test my forms", THEMENAME); ?></h2>
					<div class="inside">
						<fieldset>
							<input type="radio" name="frequency" value="0" id="frequency-0" checked="checked"> <label for="frequency-0"><?php _e("Daily", THEMENAME); ?></label>
							<br><input type="radio" name="frequency" value="1" id="frequency-1"> <label for="frequency-1"><?php _e("Weekly", THEMENAME); ?></label>
							<br><input type="radio" name="frequency" value="2" id="frequency-2"> <label for="frequency-2"><?php _e("Monthly", THEMENAME); ?></label>
						</fieldset>
						<p><?php _e("Please email my report here:", THEMENAME); ?></p>
						<input type="text" id="report-name" value="<?php echo get_option('infer_form_last_name_saved', ''); ?>" placeholder="<?php _e("Full name", THEMENAME); ?>" />
						<input type="text" id="report-email" value="<?php echo get_option('infer_form_last_email_saved', ''); ?>" placeholder="E-mail" />
						<button id="report-submit"><?php _e("Submit", THEMENAME); ?></button>
						<p class="error-message"></p>
					</div>
				</div>

				<?php infer_form_menu_admin_log_tests(); ?>

				<?php infer_form_menu_admin_registered_tests(); ?>
				
			</div>

			<div class="second-column">

				<div class="postbox about">
					<h2><?php _e("About Infer", THEMENAME); ?></h2>
					<div class="inside">
						<p><?php echo sprintf(__("Infer is a predictive sales and marketing platform. We help B2B companies transform their data into actionable intelligence to guide you what to do next. Learn more at %s", THEMENAME), '<a href="https://www.infer.com/" target="_blank">infer.com</a>' ); ?></p>

						<p><strong><?php _e("Latest Blog Post", THEMENAME); ?></strong></p>
						<ul>
							<?php
							require_once(ABSPATH . 'wp-includes/rss.php');
							$feed = fetch_rss('http://feeds.feedburner.com/InferBlog');
							if (count($feed->items) > 0) {
								$i = 0;
								foreach ($feed->items as $key => $value) {
									?><li><a href="<?php echo $value['link']; ?>" target="_blank"><?php echo $value['title']; ?></a></li><?php
									$i++;
									if ($i >= 3) {
										break;
									}
								}
							} else {
								?><li><?php _e('Could not access our blog feed', THEMENAME); ?></li><?php
							}
							?>
						</ul>

						<p><strong><?php _e("Learn More", THEMENAME); ?></strong></p>
						<ul>
							<li><a href="https://www.infer.com/guide-to-predictive-lead-scoring/" target="_blank"><?php _e("Guide to Predictive Lead Scoring", THEMENAME); ?></a></li>
							<li><a href="https://www.infer.com/webinar/predictive-analytics-and-content-marketing/" target="_blank"><?php _e("B2B Predictive Analytics & Content Marketing", THEMENAME); ?></a></li>
							<li><a href="https://www.infer.com/webinar/predictive-analytics-and-content-marketing/" target="_blank"><?php _e("B2B Predictive Marketing Industry Report", THEMENAME); ?></a></li>
						</ul>
					</div>
				</div>

				
			</div>

		</div>

	</div>
	<?php

}


add_action( 'admin_init', 'infer_form_generate_pdf' );
function infer_form_generate_pdf() {
	if (isset($_GET['page']) && $_GET['page']=='form_monitor') {
		if (isset($_GET['generate']) && $_GET['generate']=='pdf') {
			if (isset($_GET['report']) ) {
				include_once('report-export.php');
				die();
			}
		}
	}

	return;
}