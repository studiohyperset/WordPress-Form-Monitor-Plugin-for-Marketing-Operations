<?php

/*
 * Print log of tests on admin
 */
function infer_form_menu_admin_log_tests() {
?>

	<h2><?php _e("Most Recent Reports", INFERFORMNAME); ?> <a href="#"><?php // _e("Download reports", INFERFORMNAME); ?></a></h2>

	<table>
		<?php
		$logged = get_option( 'infer_form_monitor_log', array() );
		if ( count($logged) > 0 ) {
			foreach ($logged as $key => $value) {
				?>
				<tr>
					<td>
						<a href="#"><?php echo date('M d, Y', $value['date']); ?></a>
					</td>
					<td class="result">
						<?php
						foreach ($value['result'] as $key => $value) {
							echo '<span class="'. $value['result'] .'"></span>';
						}
						?>
					</td>
				</tr>
				<?php
			}
		} else {
			?>
			<tr>
				<td><?php _e("No tests performed yet.", INFERFORMNAME); ?></td>
			</tr>
			<?php
		}
		?>
	</table>
	<p><?php _e("Infer's automated form monitor plugin keeps a record of your site's three latest tests. To maintain a complete log of reports, please ensure you're using a valid, up-to-date email address.", INFERFORMNAME); ?></p>

<?php
}
/*
 * Print registered tests on admin
 */
function infer_form_menu_admin_registered_tests() {
?>

	<h2><?php _e("Active Tests", INFERFORMNAME); ?></h2>

	<table class="registered">
		<?php
		$count = 0;
		$cron = _get_cron_array();
		$schedules = wp_get_schedules();
		foreach ( $cron as $timestamp => $cronhooks ) {
			foreach ($cronhooks as $hook => $events) {
				if ($hook == 'infer_form_cron_execute') {
					$count++;
					foreach ($events as $event ) {
						$name = $event['args'][0];
						?>
						<tr>
							<td>
								Test <?php echo str_replace('infer_form_schedule_', '', $name); ?>
							</td>
							<td>
								<?php echo $schedules[$event['schedule']]['display']; ?>
							</td>
							<td>
								<a href="#" data-id="<?php echo $name; ?>" title="<?php _e("Run test", INFERFORMNAME); ?>" class="run">
									<span class="dashicons dashicons-controls-play"></span>
								</a>
								<a href="#" data-id="<?php echo $name; ?>" title="<?php _e("Delete test", INFERFORMNAME); ?>" class="delete">
									<span class="dashicons dashicons-no"></span>
								</a>
							</td>
						</tr>
						<?php
					}
				}
			}
		}

		if ( $count == 0 ) {
			?>
			<tr>
				<td><?php _e("No active tests.", INFERFORMNAME); ?></td>
			</tr>
			<?php
		}
		?>
	</table>

<?php
}