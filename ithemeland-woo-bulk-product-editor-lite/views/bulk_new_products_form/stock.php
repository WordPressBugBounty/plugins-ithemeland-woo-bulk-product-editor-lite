<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$wcbel_items = $new_form_items['stock'];

foreach ($wcbel_items as $wcbel_name => $wcbel_item):
    $wcbel_field_id = 'wcbe-bulk-new-form-' . esc_attr($wcbel_item['name']);
    $wcbel_field_type = isset($wcbel_item['field_type']) ? $wcbel_item['field_type'] : 'text';

    // Handle section headers
    if ($wcbel_field_type === 'section_header') {
        echo '<div><strong>' . esc_html($wcbel_item['label']) . '</strong><hr><div class="wcbe-mb20"></div></div>';
        continue;
    }
?>
    <div class="wcbe-form-group" data-name="<?php echo esc_attr($wcbel_name); ?>" data-type="<?php echo esc_attr($wcbel_item['update_type']); ?>">
        <div>
            <label for="<?php echo esc_attr($wcbel_field_id); ?>"><?php echo esc_html($wcbel_item['label']); ?></label>

            <?php if ($wcbel_field_type === 'select'): ?>
                <select <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?> class="wcbe-input-md" id="<?php echo esc_attr($wcbel_field_id); ?>" data-field="value">
                    <?php if (isset($wcbel_item['first_option'])): ?>
                        <option value=""><?php echo esc_html($wcbel_item['first_option']); ?></option>
                    <?php endif; ?>
                    <?php if (!empty($wcbel_item['options'])): ?>
                        <?php foreach ($wcbel_item['options'] as $wcbel_key => $wcbel_value): ?>
                            <option value="<?php echo esc_attr($wcbel_key); ?>"><?php echo esc_html($wcbel_value); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            <?php else: ?>
                <?php
                $wcbel_field_type_path = WCBEL_VIEWS_DIR . 'bulk_new_products_form/fields_type/' . $wcbel_field_type . '.php';
                if (file_exists($wcbel_field_type_path)) {
                    include $wcbel_field_type_path;
                } else {
                    include WCBEL_VIEWS_DIR . 'bulk_new_products_form/fields_type/text.php';
                }
                ?>
            <?php endif; ?>
        </div>

        <?php if (isset($wcbel_item['disabled']) && $wcbel_item['disabled']): ?>
            <span class="wcbe-alert-pro-description"><?php esc_html_e('Upgrade to pro version!', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
        <?php endif; ?>
    </div>
<?php endforeach; ?>