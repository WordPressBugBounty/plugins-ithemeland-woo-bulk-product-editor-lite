<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$items = $new_form_items['pricing'];

foreach ($items as $name => $item):
    $field_id = 'wcbe-bulk-new-form-' . esc_attr($item['name']);
    $field_type = isset($item['field_type']) ? $item['field_type'] : 'text';

    // Skip if this is a section header (handled separately)
    if ($field_type === 'section_header') {
        echo '<div><strong>' . esc_html($item['label']) . '</strong><hr><div class="wcbe-mb20"></div></div>';
        continue;
    }
?>
    <div class="wcbe-form-group" data-name="<?php echo esc_attr($name); ?>" data-type="<?php echo esc_attr($item['update_type']); ?>">
        <div>
            <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($item['label']); ?></label>

            <?php
            $field_type_path = WCBEL_VIEWS_DIR . 'bulk_new_products_form/fields_type/' . $field_type . '.php';
            if (file_exists($field_type_path)) {
                include $field_type_path;
            } else {
                include WCBEL_VIEWS_DIR . 'bulk_new_products_form/fields_type/text.php';
            }
            ?>
        </div>

        <?php if (isset($item['disabled']) && $item['disabled']): ?>
            <span class="wcbe-alert-pro-description"><?php esc_html_e('Upgrade to pro version!', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
        <?php endif; ?>
    </div>
<?php endforeach; ?>