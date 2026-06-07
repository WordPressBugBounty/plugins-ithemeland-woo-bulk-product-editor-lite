<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<?php if (!empty($attribute_name)) : ?>
    <div class='wcbe-variation-bulk-edit-attribute-item' data-id='<?php echo "wcbe-variation-bulk-edit-delete-attribute-item-" . esc_attr($attribute_name); ?>'>
        <label><?php echo esc_html($attribute_name); ?></label>
        <select title="Select attribute" data-name="<?php echo esc_attr($attribute_name); ?>" class="wcbe-w100p">
            <?php if (!empty($values)) : ?>
                <?php foreach ($values as $wcbel_value_item) : ?>
                    <option value="<?php echo esc_attr(urldecode($wcbel_value_item->slug)); ?>"><?php echo esc_html(urldecode($wcbel_value_item->name)); ?></option>';
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
<?php endif; ?>