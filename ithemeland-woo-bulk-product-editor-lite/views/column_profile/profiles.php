<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

if (!empty($column_manager_presets)) :
    foreach ($column_manager_presets as $wcbel_column_manager_preset) :
?>
        <option value="<?php echo esc_attr($wcbel_column_manager_preset['key']); ?>" <?php echo (!empty($active_columns_key) && $active_columns_key == $wcbel_column_manager_preset['key']) ? 'selected' : ''; ?>><?php echo esc_html($wcbel_column_manager_preset['name']); ?></option>
<?php
    endforeach;
endif;
?>