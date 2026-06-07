<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

if (!defined('WCBE_ACTIVE')) {
    include WCBEL_VIEWS_DIR . 'alerts/warning-active-pro.php';
}
$wcbel_items = $new_form_items['general'];

foreach ($wcbel_items as $wcbel_name => $wcbel_item):

    $wcbel_field_id = 'wcbe-bulk-new-form-product-' . esc_attr($wcbel_item['name']);
    $wcbel_field_type = isset($wcbel_item['field_type']) ? $wcbel_item['field_type'] : 'text';
?>
    <div class="wcbe-form-group" data-name="<?php echo esc_attr($wcbel_name); ?>" data-type="<?php echo esc_attr($wcbel_item['update_type']); ?>">
        <div>
            <label for="<?php echo esc_attr($wcbel_field_id); ?>">
                <?php echo esc_html($wcbel_item['label']); ?>
            </label>

            <?php
            $wcbel_field_type_path = WCBEL_VIEWS_DIR . 'bulk_new_products_form/fields_type/' . $wcbel_field_type . '.php';
            if (file_exists($wcbel_field_type_path)) {
                include $wcbel_field_type_path;
            } else {
                include WCBEL_VIEWS_DIR . 'bulk_new_products_form/fields_type/text.php';
            }
            ?>
        </div>
        <?php if (isset($wcbel_item['disabled']) && $wcbel_item['disabled']): ?>
            <span class="wcbe-alert-pro-description"><?php esc_html_e('Upgrade to pro version!', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
        <?php endif; ?>
    </div>
<?php endforeach; ?>