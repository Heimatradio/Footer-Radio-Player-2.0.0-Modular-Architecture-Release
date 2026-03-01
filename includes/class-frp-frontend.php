<?php

if (!defined('ABSPATH')) {
    exit;
}

class FRP_Frontend {

    private static $instance = null;

    public static function instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {

        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_footer', [$this, 'render_player']);
    }

    public function enqueue_assets() {

        wp_enqueue_style(
            'frp-player-style',
            FRP_URL . 'assets/player.css',
            [],
            FRP_VERSION
        );

        wp_enqueue_script(
            'frp-player-script',
            FRP_URL . 'assets/player.js',
            [],
            FRP_VERSION,
            true
        );

        wp_localize_script(
            'frp-player-script',
            'frpData',
            [
                'ajax_url'  => admin_url('admin-ajax.php'),
                'popup_url' => get_option('frp_popup_url', '')
            ]
        );
    }

    public function render_player() {

        $stream_url = get_option('frp_stream_url', '');
        if (empty($stream_url)) return;

        $station_name = get_option('frp_station_name', 'Live Radio');
        $show_cover   = get_option('frp_show_cover', '0');
        $cover_url    = get_option('frp_cover_url', '');

        if (empty($cover_url)) {
            $cover_url = FRP_URL . 'assets/default-cover.png';
        }

        $bg_color     = get_option('frp_color_bg', '#7c1212');
        $text_color   = get_option('frp_color_text', '#ffffff');
        $button_color = get_option('frp_color_button', '#ffffff');
        ?>

        <style>
        :root {
            --frp-bg: <?php echo esc_attr($bg_color); ?>;
            --frp-text: <?php echo esc_attr($text_color); ?>;
            --frp-button: <?php echo esc_attr($button_color); ?>;
        }
        </style>

        <div id="frp-player">

            <?php if ($show_cover === '1' && !empty($cover_url)) : ?>
                <div class="frp-cover">
                    <img src="<?php echo esc_url($cover_url); ?>"
                         data-fallback="<?php echo esc_url($cover_url); ?>"
                         alt="Cover">
                </div>
            <?php endif; ?>

            <div class="frp-left">
                <div class="frp-title">
                    <?php echo esc_html($station_name); ?>
                </div>
                <div class="frp-track">
                    <span id="frp-current-track">Loading...</span>
                </div>
            </div>

            <div class="frp-controls">
                <button id="frp-play" class="frp-btn frp-play">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="11" fill="white" opacity="0.15"/>
                        <polygon id="frp-play-shape"
                            points="10,8 17,12 10,16"
                            fill="white"/>
                    </svg>
                </button>

                <button id="frp-popup" class="frp-btn">
                    <svg viewBox="0 0 24 24">
                        <path d="M14 3h7v7M10 14L21 3M21 14v7h-7"
                              stroke="white"
                              stroke-width="2"
                              fill="none"/>
                    </svg>
                </button>
            </div>

            <audio id="frp-audio" preload="none">
                <source src="<?php echo esc_url($stream_url); ?>" type="audio/mpeg">
            </audio>

        </div>

        <?php
    }
}