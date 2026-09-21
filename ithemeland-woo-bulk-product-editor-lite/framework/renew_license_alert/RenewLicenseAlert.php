<?php

namespace wcbel\framework\renew_license_alert;

defined('ABSPATH') || exit();

class RenewLicenseAlert
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
            add_action('wp_ajax_wcbel_renew_license_alert_dismiss', [$this, 'dismiss']);

            add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        }
    }

    private function should_show_alert()
    {
        if (empty(get_option('wcbel_epbulkit'))) {
            update_option('wcbel_epbulkit', strtotime('+ ' . wp_rand(10, 20) . ' days'));
        }

        $pro_version = get_option('wcbe-pro-version');
        if (!empty($pro_version) && version_compare($pro_version, '4.0.6', '>=')) {
            return false;
        }

        return true;
    }

    public function display_alert()
    {
        include_once WCBEL_FW_DIR . 'renew_license_alert/views/alert.php';
    }

    public function dismiss()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbel_ajax_nonce')) {
            die();
        }

        // update_option('wcbel_renew_license_alert_dismissed', 'yes');
        wp_send_json([
            'success' => true
        ]);
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style('wcbel-renew-license-alert', WCBEL_FW_URL . 'renew_license_alert/assets/css/style.css', [], WCBEL_VERSION);

        wp_enqueue_script('wcbel-renew-license-alert', WCBEL_FW_URL . 'renew_license_alert/assets/js/renew-license-alert.js', [], WCBEL_VERSION); //phpcs:ignore
        wp_localize_script('wcbel-renew-license-alert', 'WCBEL_PRO_VERSION_ALERT', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce('wcbel_ajax_nonce'),
        ]);
    }

    public static function remove()
    {
        delete_option('wcbel_epbulkit');
        delete_option('wcbel_renew_license_alert_dismissed');
    }
}
