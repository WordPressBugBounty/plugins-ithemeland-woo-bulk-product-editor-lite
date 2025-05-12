<?php

use wcbel\classes\helpers\Sanitizer;

if (!defined('ABSPATH')) exit; // Exit if accessed directly 
if (!empty($column_items)) :
?>
    <?php foreach ($column_items as $column_key => $column_field) : ?>
        <li data-name="<?php echo esc_attr($column_key); ?>" data-added="false">
            <label>
                <input type="checkbox" data-type="field" data-name="<?php echo esc_attr($column_key); ?>" value="<?php echo esc_attr($column_field['label']); ?>">
                <?php echo wp_kses($column_field['label'], Sanitizer::allowed_html()); ?>
            </label>
        </li>
    <?php endforeach; ?>
<?php endif; ?>