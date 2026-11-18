<?php
namespace wcbel\classes\bootstrap;

use wcbel\framework\email_subscription\EmailSubscription;
use wcbel\framework\onboarding\Onboarding;

defined('ABSPATH') || exit();

/** Optional AI launch interest; no report data is collected. */
class AiInterest
{
    const DOMAIN = 'ithemeland-woo-bulk-product-editor-lite';
    const ACTION = 'wcbel_ai_interest';

    private static $registered = false;

    private static function product_id()
    {
        return defined('WCBE_ACTIVE') && WCBE_ACTIVE ? 'wcbe-ai' : 'wcbel-ai';
    }

    private static function subscription_key()
    {
        return self::product_id() . '_interest_' . md5(strtolower(sanitize_email(get_option('admin_email'))));
    }

    public static function register()
    {
        if (self::$registered) { return; }
        self::$registered = true;
        add_action('admin_enqueue_scripts', array(__CLASS__, 'assets'), 10000);
        add_action('wcbe_navigation_buttons_after_settings', array(__CLASS__, 'button'));
        add_action('wcbe_layout_footer', array(__CLASS__, 'modal'));
        add_action('wp_ajax_' . self::ACTION, array(__CLASS__, 'subscribe'));
    }

    private static function visible()
    {
        return (bool) apply_filters('wcbel_ai_interest_visible', current_user_can('manage_woocommerce') && Onboarding::is_completed()
            && isset($_GET['page']) && $_GET['page'] === 'wcbe');
    }

    public static function assets()
    {
        if (!self::visible()) { return; }
        wp_enqueue_style(self::ACTION, WCBEL_CSS_URL . 'ai-interest.css', array(), '1.0.7');
        wp_enqueue_script(self::ACTION, WCBEL_JS_URL . 'ai-interest.js', array('jquery'), '1.0.7', true);
        wp_localize_script(self::ACTION, 'WCBEL_AI_INTEREST', array(
            'url' => admin_url('admin-ajax.php'), 'action' => self::ACTION,
            'nonce' => wp_create_nonce(self::ACTION),
            'subscribed' => (bool) get_option(self::subscription_key()),
            'sending' => __('Sending…', self::DOMAIN),
            'error' => __('We could not save your request. Please try again.', self::DOMAIN)
        ));
    }

    public static function button()
    {
        if (!self::visible()) { return; }
        echo '<li class="wcbel-ai-toolbar-item"><button type="button" class="wcbel-ai-open" title="' . esc_attr__('AI Bulk Assistant — Coming soon', self::DOMAIN) . '" aria-label="' . esc_attr__('AI Bulk Assistant — Coming soon', self::DOMAIN) . '" aria-haspopup="dialog" aria-controls="wcbel-ai-dialog"><svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2c0 6-4 10-10 10 6 0 10 4 10 10 0-6 4-10 10-10-6 0-10-4-10-10Z"/><path fill="currentColor" d="M20 15c0 2-1 3-3 3 2 0 3 1 3 3 0-2 1-3 3-3-2 0-3-1-3-3Z"/></svg></button></li>';
    }

    public static function modal()
    {
        if (!self::visible()) { return; }
        $subscribed = (bool) get_option(self::subscription_key());
        include WCBEL_VIEWS_DIR . 'ai-interest.php';
    }

    public static function subscribe()
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array(), 403);
            return;
        }
        check_ajax_referer(self::ACTION, 'nonce');
        $email = sanitize_email(get_option('admin_email'));
        if (!is_email($email)) { wp_send_json_error(array(), 400); return; }
        // Keep AI opt-in separate from onboarding and license subscriptions.
        $key = self::subscription_key();
        if (get_option($key)) { wp_send_json_success(); return; }
        $lock = $key . '_lock';
        $started = get_option($lock);
        if ($started && (int) $started < time() - 120) { delete_option($lock); }
        if (!add_option($lock, time(), '', false)) { wp_send_json_error(array(), 409); return; }
        try {
            $service = new EmailSubscription();
            $response = $service->add_subscription(array(
                'type' => self::product_id() === 'wcbe-ai' ? 'pro' : 'lite',
                'email' => $email,
                'domain' => isset($_SERVER['SERVER_NAME']) ? sanitize_text_field(wp_unslash($_SERVER['SERVER_NAME'])) : '',
                'product_name' => self::product_id() === 'wcbe-ai' ? WCBE_LABEL : WCBEL_LABEL,
                'product_id' => self::product_id(),
            ), false);
            $success = is_array($response) && isset($response['success']) && $response['success'] === true;
            if ($success) { update_option($key, true, false); }
        } catch (\Throwable $error) {
            $success = false;
        } finally {
            delete_option($lock);
        }
        if (!$success) { wp_send_json_error(array(), 502); return; }
        wp_send_json_success();
    }
}
