<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<?php if (!empty($attribute_name)) : ?>
    <?php if (!empty($values)) : ?>
        <?php foreach ($values as $wcbel_value_item) : ?>
            <option value="<?php echo esc_attr($wcbel_value_item->term_id); ?>"><?php echo esc_html(urldecode($wcbel_value_item->name)); ?></option>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>