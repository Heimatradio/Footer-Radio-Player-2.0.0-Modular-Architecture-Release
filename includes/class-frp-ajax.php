<?php

if (!defined('ABSPATH')) {
    exit;
}

class FRP_Ajax {

    private static $instance = null;

    public static function instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {

        add_action('wp_ajax_frp_get_title', [$this, 'ajax_get_title']);
        add_action('wp_ajax_nopriv_frp_get_title', [$this, 'ajax_get_title']);
    }

    public function ajax_get_title() {

        $title = $this->get_stream_title();

        if ($title) {
            wp_send_json_success($title);
        }

        wp_send_json_success(
            get_option('frp_fallback_text', 'Live Stream')
        );
    }

    private function get_stream_title() {

        $type = get_option('frp_stream_type', 'shoutcast');

        return ($type === 'icecast')
            ? $this->get_icecast_title()
            : $this->get_shoutcast_title();
    }

    private function get_shoutcast_title() {

        $server_url = get_option('frp_server_url', '');
        if (empty($server_url)) return false;

        $response = wp_remote_get($server_url . '/stats?sid=1', [
            'timeout' => 5,
            'sslverify' => false
        ]);

        if (is_wp_error($response)) return false;

        $body = wp_remote_retrieve_body($response);
        if (empty($body)) return false;

        $xml = @simplexml_load_string($body);

        return ($xml && isset($xml->SONGTITLE))
            ? (string)$xml->SONGTITLE
            : false;
    }

    private function get_icecast_title() {

        $server_url = get_option('frp_server_url', '');
        if (empty($server_url)) return false;

        $response = wp_remote_get($server_url . '/status-json.xsl', [
            'timeout' => 5,
            'sslverify' => false
        ]);

        if (is_wp_error($response)) return false;

        $body = wp_remote_retrieve_body($response);
        if (empty($body)) return false;

        $data = json_decode($body, true);

        if (!isset($data['icestats'])) return false;

        $source = $data['icestats']['source'];

        if (isset($source['title'])) return $source['title'];

        if (is_array($source)) {
            foreach ($source as $mount) {
                if (isset($mount['title'])) return $mount['title'];
            }
        }

        return false;
    }
}