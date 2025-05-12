<label>
    <input
        type="checkbox"
        id="wcbel_usage_track"
        name="wcbel_usage_track"
        value="yes"
        <?php checked(1, $option); ?> />
    <span><?php esc_html_e('iThemeland WooCommerce Bulk Product Editing', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
    <p class="description">
        <?php echo esc_html($description); ?>
        <a href="https://ithemelandco.com/usage-tracking?utm_source=free_plugins&utm_medium=plugin_links&utm_campaign=telemetry"><?php esc_html_e('Learn More', 'ithemeland-woo-bulk-product-editor-lite'); ?></a>
    </p>
</label>