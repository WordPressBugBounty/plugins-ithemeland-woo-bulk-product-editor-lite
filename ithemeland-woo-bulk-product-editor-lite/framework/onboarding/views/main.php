<div class="onboarding-section">
    <div class="ithemeland-onboarding-head">
        <img src="<?php echo esc_url(WCBEL_FW_URL . 'onboarding/assets/images/iThemeland-popup-icon.png'); ?>" style="width:250px" alt="onboarding-icon">
    </div>
    <div class="ithemeland-onboarding-container">
        <div class="ithemeland-onboarding-box">
            <h2><?php echo esc_html__('Welcome To iThemeland!', 'ithemeland-woo-bulk-product-editor-lite'); ?></h2>
            <p><?php echo esc_html__('It\'s great to have you here with us!', 'ithemeland-woo-bulk-product-editor-lite'); ?></p>
            <form method="POST" action="">
                <?php wp_nonce_field('ithemeland_onboarding_form', '_wpnonce'); ?>
                <div class="ithemeland-onboarding-checkbox">
                    <div class="row ithemeland-pa-t-b-10">
                        <input type="checkbox" name="ithemeland_opt_in" id="ithemeland_opt_in" class="ithemeland_opt_in_checkbox" value="1" checked>
                        <label for="ithemeland_opt_in">
                            <?php echo esc_html__('Opt-in to receive tips, discounts, and recommendations from the iThemeland team directly in your inbox.', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                        </label>
                    </div>
                    <div class="row ithemeland-pa-t-b-10">
                        <input type="checkbox" name="ithemeland_usage_track" id="ithemeland_usage_track" class="ithemeland_usage_track" value="1" checked>
                        <label for="ithemeland_usage_track">
                            <?php echo esc_html__('I agree to share my data to tailor my store setup experience, get more relevant content, and help make the plugin better for everyone. You can opt out at any time in settings.', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                            <a href="<?php echo esc_url('https://ithemelandco.com/usage-tracking/?utm_source=free_plugins&utm_medium=plugin_links&utm_campaign=telemetry'); ?>">
                                <?php echo esc_html__('Learn more about usage tracking.', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                            </a>
                        </label>

                    </div>
                </div>
                <div class="ithemeland-onboarding-buttons">
                    <button type="button" name="ithemeland_usage_track_submit" class="ithemeland-allow-continue">
                        <?php esc_html_e('Allow & Continue', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                    <button type="button" class="ithemeland-skip"><?php esc_html_e('Skip', 'ithemeland-woo-bulk-product-editor-lite'); ?></button>
                </div>
            </form>
            <div class="ithemeland-footer-note">
                <div class="ithemeland-links">
                    <a href="https://ithemelandco.com/privacy-policy/" target="_blank"><?php esc_html_e('Privacy Policy', 'ithemeland-woo-bulk-product-editor-lite'); ?></a>
                    |
                    <a href="https://ithemelandco.com/term-of-use/" target="_blank"><?php esc_html_e('Terms of Service', 'ithemeland-woo-bulk-product-editor-lite'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>