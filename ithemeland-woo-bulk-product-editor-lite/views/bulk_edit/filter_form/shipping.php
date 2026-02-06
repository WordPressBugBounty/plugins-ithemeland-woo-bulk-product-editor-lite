<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$items = $filter_form_items['shipping'];

foreach ($items as $name => $item):
    $field_id = 'wcbe-filter-form-product-' . $item['id'];
?>
    <div class="wcbe-form-group" data-name="<?php echo esc_attr($name); ?>" data-filter-type="<?php echo esc_attr($item['filter_type']); ?>" data-field-type="<?php echo esc_attr($item['field_type']); ?>">
        <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($item['label']); ?></label>

        <?php
        $field_type_path = WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/fields_type/' . $item['field_type'] . '.php';
        if (file_exists($field_type_path)) {
            include $field_type_path;
        } else {
            include WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/fields_type/text.php';
        }
        ?>
    </div>
<?php endforeach; ?>