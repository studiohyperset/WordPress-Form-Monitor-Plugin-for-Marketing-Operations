<?php

/*
 * Print log of tests on admin
 */
function infer_form_menu_admin_log_tests() {
?>

	<h2><?php _e("Latest reports", INFERFORMNAME); ?> <a href="#"><?php _e("Download reports", INFERFORMNAME); ?></a></h2>

	<table>
		<?php
		$logged = get_option( 'infer_form_monitor_log', array() );
		if ( count($logged) > 0 ) {
			foreach ($logged as $key => $value) {
				?>
				<tr>
					<td>
						<?php echo date('M d, Y', $value['date']); ?>
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
	<p><?php _e("To maintain a complete log of reports, please submit your email adress.", INFERFORMNAME); ?></p>

<?php
}
/*
 * Print registered tests on admin
 */
function infer_form_menu_admin_registered_tests() {
?>

	<h2><?php _e("Registered tests", INFERFORMNAME); ?></h2>

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
						?>
						<tr>
							<td>
								Report X
							</td>
							<td>
								<?php echo $schedules[$event['schedule']]['display']; ?>
							</td>
							<td>
								<a href="#" data-id="<?php echo $event['args'][0]; ?>" title="<?php _e("Delete test", INFERFORMNAME); ?>" class="delete">
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
				<td><?php _e("No tests registered", INFERFORMNAME); ?></td>
			</tr>
			<?php
		}
		?>
	</table>

<?php
}