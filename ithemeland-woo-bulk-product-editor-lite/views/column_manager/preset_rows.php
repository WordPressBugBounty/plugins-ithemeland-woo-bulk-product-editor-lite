<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

if (!empty($column_manager_presets)) :
?>
    <?php $wcbel_i = 1 ?>
    <?php foreach ($column_manager_presets as $wcbel_key => $wcbel_column_manager_preset) : ?>
        <tr>
            <td><?php echo esc_html($wcbel_i); ?></td>
            <td>
                <span class="wcbe-history-name"><?php echo (isset($wcbel_column_manager_preset['name'])) ? esc_html($wcbel_column_manager_preset['name']) : ''; ?></span>
            </td>
            <td><?php echo (isset($wcbel_column_manager_preset['date_modified'])) ? esc_html(gmdate('d M Y', strtotime($wcbel_column_manager_preset['date_modified']))) : ''; ?></td>
            <td>
                <?php if (!in_array($wcbel_key, \wcbel\classes\repositories\Column::get_default_columns_name())) : ?>
                    <button type="button" class="wcbe-button wcbe-button-blue wcbe-column-manager-edit-field-btn" data-toggle="modal" data-target="#wcbe-modal-column-manager-edit-preset" value="<?php echo esc_attr($wcbel_key); ?>" data-preset-name="<?php echo (isset($column_manager_preset['name'])) ? esc_attr($column_manager_preset['name']) : ''; ?>">
                        <i class="wcbe-icon-pencil"></i>
                        <?php esc_html_e('Edit', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                    <button type="button" name="delete_preset" class="wcbe-button wcbe-button-red wcbe-column-manager-delete-preset" value="<?php echo esc_attr($wcbel_key); ?>">
                        <i class="wcbe-icon-trash-2"></i>
                        <?php esc_html_e('Delete', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                <?php else : ?>
                    <i class="wcbe-icon-lock1"></i>
                <?php endif; ?>
            </td>
        </tr>
        <?php $wcbel_i++; ?>
    <?php endforeach; ?>
<?php endif; ?>