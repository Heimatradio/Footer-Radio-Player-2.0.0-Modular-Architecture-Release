<?php
/*
Plugin Name: Footer Radio Player
Description: A customizable sticky footer radio player supporting Shoutcast and Icecast streams.
Version: 2.0.0
Author: Martin Sievers
Text Domain: footer-radio-player
*/

if (!defined('ABSPATH')) {
    exit;
}

define('FRP_VERSION', '2.0.0');
define('FRP_PATH', plugin_dir_path(__FILE__));
define('FRP_URL', plugin_dir_url(__FILE__));

require_once FRP_PATH . 'includes/class-frp-core.php';

FRP_Core::instance();