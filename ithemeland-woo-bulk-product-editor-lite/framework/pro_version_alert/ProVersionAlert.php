<?php

namespace wcbel\framework\pro_version_alert;

defined('ABSPATH') || exit();

class ProVersionAlert
{
    private static $instance;

    public static function init()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
    }

    private function __construct()
    {
        if ($this->should_show_alert()) {
            add_action('admin_notices', [$this, 'display_alert']);
            add_action('wp_ajax_wcbel_pro_version_alert_dismiss', [$this, 'dismiss']);

            add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        }
    }

    private function should_show_alert()
    {
        return !(defined('WCBE_NAME'));
    }

    public function display_alert()
    {
        $dismissed = get_option('wcbel_pro_version_alert_dismissed', false);
        if (empty($dismissed)) {
            include_once WCBEL_FW_DIR . 'pro_version_alert/views/alert.php';
        }
    }

    public function dismiss()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbel_ajax_nonce')) {
            die();
        }

        update_option('wcbel_pro_version_alert_dismissed', 'yes');
        wp_send_json([
            'success' => true
        ]);
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style('wcbel-pro-version-alert', WCBEL_FW_URL . 'pro_version_alert/assets/css/style.css', [], WCBEL_VERSION);

        wp_enqueue_script('wcbel-pro-version-alert', WCBEL_FW_URL . 'pro_version_alert/assets/js/pro-version-alert.js', [], WCBEL_VERSION); //phpcs:ignore
        wp_localize_script('wcbel-pro-version-alert', 'WCBEL_PRO_VERSION_ALERT', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce('wcbel_ajax_nonce'),
        ]);
    }
}
