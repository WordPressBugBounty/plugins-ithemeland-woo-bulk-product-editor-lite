<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$items = $new_form_items['shipping'];

foreach ($items as $name => $item):
    $field_id = 'wcbe-bulk-new-form-' . esc_attr($item['name']);
    $field_type = isset($item['field_type']) ? $item['field_type'] : 'text';

    // Handle section headers
    if ($field_type === 'section_header') {
        if (!empty($item['label'])) {
            echo '<div><strong>' . esc_html($item['label']) . '</strong></div>';
        }
        echo '<hr><div class="wcbe-mb20"></div>';
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
    </div>
<?php endforeach; ?>