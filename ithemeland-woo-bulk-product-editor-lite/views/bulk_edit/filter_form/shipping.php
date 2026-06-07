<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$wcbel_items = $filter_form_items['shipping'];

foreach ($wcbel_items as $wcbel_name => $wcbel_item):
    $wcbel_field_id = 'wcbe-filter-form-product-' . $wcbel_item['id'];
?>
    <div class="wcbe-form-group" data-name="<?php echo esc_attr($wcbel_name); ?>" data-filter-type="<?php echo esc_attr($wcbel_item['filter_type']); ?>" data-field-type="<?php echo esc_attr($wcbel_item['field_type']); ?>">
        <label for="<?php echo esc_attr($wcbel_field_id); ?>"><?php echo esc_html($wcbel_item['label']); ?></label>

        <?php
        $wcbel_field_type_path = WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/fields_type/' . $wcbel_item['field_type'] . '.php';
        if (file_exists($wcbel_field_type_path)) {
            include $wcbel_field_type_path;
        } else {
            include WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/fields_type/text.php';
        }
        ?>
    </div>
<?php endforeach; ?>