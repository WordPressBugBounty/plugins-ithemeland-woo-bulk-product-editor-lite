<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

if (!empty($histories)) :
    $wcbel_i = 1;
    foreach ($histories as $wcbel_history) :
        $wcbel_user_data = get_userdata(intval($wcbel_history->user_id));
        $wcbel_user_id = get_current_user_id(); // Get the current user ID

        // Get the number of undo operations performed by the user
        $wcbel_user_undo_count = get_user_meta($wcbel_user_id, 'wcbe_undo_count', true);
        $wcbel_user_undo_count = empty($wcbel_user_undo_count) ? 0 : intval($wcbel_user_undo_count);
?>
        <tr>
            <td><?php echo esc_html($wcbel_i); ?></td>
            <td>
                <span class="wcbe-history-name wcbe-fw600">
                    <?php
                    switch ($wcbel_history->operation_type) {
                        case 'inline':
                            $wcbel_item = (new wcbel\classes\repositories\history\History_Main())->get_history_items($wcbel_history->id);
                            echo (!empty($wcbel_item[0]->post_title)) ? esc_html($wcbel_item[0]->post_title) : 'Inline Operation';
                            break;
                        case 'bulk':
                            echo 'Bulk Operation';
                            break;
                    }
                    ?>
                </span>
                <?php
                $wcbel_fields = '';
                if (is_array(unserialize($wcbel_history->fields)) && !empty(unserialize($wcbel_history->fields))) {
                    foreach (unserialize($wcbel_history->fields) as $wcbel_field) {
                        if (is_array($wcbel_field)) {
                            foreach ($wcbel_field as $wcbel_field_item) {
                                $wcbel_field_arr = explode('_-_', $wcbel_field_item);
                                if (!empty($wcbel_field_arr[0]) && !empty($wcbel_field_arr[1])) {
                                    $wcbel_field_item = esc_html($wcbel_field_arr[1]);
                                }

                                $wcbel_fields .= "[" . esc_html($wcbel_field_item) . "]";
                            }
                        } else {
                            $wcbel_field_arr = explode('_-_', $wcbel_field);
                            if (!empty($wcbel_field_arr[0]) && !empty($wcbel_field_arr[1])) {
                                $wcbel_field = esc_html($wcbel_field_arr[1]);
                            }

                            $wcbel_fields .= "[" . esc_html($wcbel_field) . "]";
                        }
                    }
                }
                ?>
                <span class="wcbe-history-text-sm"><?php echo esc_html($wcbel_fields); ?></span>
            </td>
            <td class="wcbe-fw600"><?php echo (!empty($wcbel_user_data)) ? esc_html($wcbel_user_data->display_name) : ''; ?></td>
            <td class="wcbe-fw600"><?php echo esc_html(gmdate('Y / m / d', strtotime($wcbel_history->operation_date))); ?></td>
            <td>
                <button type="button" class="wcbe-button wcbe-button-blue wcbe-history-revert-item"
                    value="<?php echo esc_attr($wcbel_history->id); ?>"
                    <?php echo !defined('WCBE_ACTIVE') || !WCBE_ACTIVE ? 'disabled="disabled"' : ''; ?>>
                    <i class="wcbe-icon-rotate-cw"></i>
                    <?php esc_html_e('Revert', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                </button>
                <button type="button" class="wcbe-button wcbe-button-red wcbe-history-delete-item"
                    value="<?php echo esc_attr($wcbel_history->id); ?>"
                    <?php echo !defined('WCBE_ACTIVE') || !WCBE_ACTIVE ? 'disabled="disabled"' : ''; ?>>
                    <i class="wcbe-icon-trash-2"></i>
                    <?php esc_html_e('Delete', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                </button>
            </td>
        </tr>
<?php
        $wcbel_i++;
    endforeach;
endif;
