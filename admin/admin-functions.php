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
		$registered = get_option( 'infer_form_monitor_registered', array() );
		if ( count($registered) > 0 ) {
			foreach ($registered as $key => $value) {
				?>
				<tr>
					<td>
						<a href="<?php echo wp_get_attachment_url( $value['file'] ); ?>"><?php echo get_the_title( $value['file'] ); ?></a>
					</td>
					<td>
						<?php echo $value['frequency']; ?>
					</td>
					<td>
						<a href="#" data-id="<?php echo $key; ?>" title="<?php _e("Delete test", INFERFORMNAME); ?>" class="delete">
							<span class="dashicons dashicons-no"></span>
						</a>
					</td>
				</tr>
				<?php
			}
		} else {
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