<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$items = $new_form_items['stock'];

foreach ($items as $name => $item):
    $field_id = 'wcbe-bulk-new-form-' . esc_attr($item['name']);
    $field_type = isset($item['field_type']) ? $item['field_type'] : 'text';

    // Handle section headers
    if ($field_type === 'section_header') {
        echo '<div><strong>' . esc_html($item['label']) . '</strong><hr><div class="wcbe-mb20"></div></div>';
        continue;
    }
?>
    <div class="wcbe-form-group" data-name="<?php echo esc_attr($name); ?>" data-type="<?php echo esc_attr($item['update_type']); ?>">
        <div>
            <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($item['label']); ?></label>

            <?php if ($field_type === 'select'): ?>
                <select <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?> class="wcbe-input-md" id="<?php echo esc_attr($field_id); ?>" data-field="value">
                    <?php if (isset($item['first_option'])): ?>
                        <option value=""><?php echo esc_html($item['first_option']); ?></option>
                    <?php endif; ?>
                    <?php if (!empty($item['options'])): ?>
                        <?php foreach ($item['options'] as $key => $value): ?>
                            <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            <?php else: ?>
                <?php
                $field_type_path = WCBEL_VIEWS_DIR . 'bulk_new_products_form/fields_type/' . $field_type . '.php';
                if (file_exists($field_type_path)) {
                    include $field_type_path;
                } else {
                    include WCBEL_VIEWS_DIR . 'bulk_new_products_form/fields_type/text.php';
                }
                ?>
            <?php endif; ?>
        </div>

        <?php if (isset($item['disabled']) && $item['disabled']): ?>
            <span class="wcbe-alert-pro-description"><?php esc_html_e('Upgrade to pro version!', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
        <?php endif; ?>
    </div>
<?php endforeach; ?>