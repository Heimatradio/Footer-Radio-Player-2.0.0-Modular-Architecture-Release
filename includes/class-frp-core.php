<?php

if (!defined('ABSPATH')) {
    exit;
}

class FRP_Core {

    private static $instance = null;

    public static function instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {

        // Frontend
        $frontend = FRP_PATH . 'includes/class-frp-frontend.php';
        if (file_exists($frontend)) {
            require_once $frontend;
            if (class_exists('FRP_Frontend')) {
                FRP_Frontend::instance();
            }
        }

        // AJAX
        $ajax = FRP_PATH . 'includes/class-frp-ajax.php';
        if (file_exists($ajax)) {
            require_once $ajax;
            if (class_exists('FRP_Ajax')) {
                FRP_Ajax::instance();
            }
        }

        // Admin
        $admin = FRP_PATH . 'includes/class-frp-admin.php';
        if (file_exists($admin)) {
            require_once $admin;
            if (class_exists('FRP_Admin')) {
                FRP_Admin::instance();
            }
        }
    }
}
