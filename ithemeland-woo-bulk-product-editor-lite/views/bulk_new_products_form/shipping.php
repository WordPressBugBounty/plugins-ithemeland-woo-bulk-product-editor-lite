<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$wcbel_items = $new_form_items['shipping'];

foreach ($wcbel_items as $wcbel_name => $wcbel_item):
    $wcbel_field_id = 'wcbe-bulk-new-form-' . esc_attr($wcbel_item['name']);
    $wcbel_field_type = isset($wcbel_item['field_type']) ? $wcbel_item['field_type'] : 'text';

    // Handle section headers
    if ($wcbel_field_type === 'section_header') {
        if (!empty($wcbel_item['label'])) {
            echo '<div><strong>' . esc_html($wcbel_item['label']) . '</strong></div>';
        }
        echo '<hr><div class="wcbe-mb20"></div>';
        continue;
    }
?>
    <div class="wcbe-form-group" data-name="<?php echo esc_attr($wcbel_name); ?>" data-type="<?php echo esc_attr($wcbel_item['update_type']); ?>">
        <div>
            <label for="<?php echo esc_attr($wcbel_field_id); ?>"><?php echo esc_html($wcbel_item['label']); ?></label>

            <?php
            $wcbel_field_type_path = WCBEL_VIEWS_DIR . 'bulk_new_products_form/fields_type/' . $wcbel_field_type . '.php';
            if (file_exists($wcbel_field_type_path)) {
                include $wcbel_field_type_path;
            } else {
                include WCBEL_VIEWS_DIR . 'bulk_new_products_form/fields_type/text.php';
            }
            ?>
        </div>
    </div>
<?php endforeach; ?>