<?php
/*
Plugin Name: Infer Form Monitor
Plugin URI: #
Description: #
Author: Studio Hyperset
Author URI: http://studiohyperset.com
Version: 0.1
*/


define(INFERFORMPATH, plugin_dir_path( __FILE__ ));
define(INFERFORMURL, plugins_url( '', __FILE__ ));
define(INFERFORMNAME, 'infer-form-monitor');


require_once( INFERFORMPATH . '/admin/menu.php');
require_once( INFERFORMPATH . '/admin/admin-functions.php');
require_once( INFERFORMPATH . '/admin/admin-ajax.php');
require_once( INFERFORMPATH . '/admin/admin-cron.php');
require_once( INFERFORMPATH . '/admin/pardot-api.php');
