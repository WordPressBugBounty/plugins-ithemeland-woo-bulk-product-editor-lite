<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

if (!empty($histories)) :
    $i = 1;
    foreach ($histories as $history) :
        $user_data = get_userdata(intval($history->user_id));
        $user_id = get_current_user_id(); // Get the current user ID

        // Get the number of undo operations performed by the user
        $user_undo_count = get_user_meta($user_id, 'wcbe_undo_count', true);
        $user_undo_count = empty($user_undo_count) ? 0 : intval($user_undo_count);
?>
        <tr>
            <td><?php echo esc_html($i); ?></td>
            <td>
                <span class="wcbe-history-name wcbe-fw600">
                    <?php
                    switch ($history->operation_type) {
                        case 'inline':
                            $item = (new wcbel\classes\repositories\history\History_Main())->get_history_items($history->id);
                            echo (!empty($item[0]->post_title)) ? esc_html($item[0]->post_title) : 'Inline Operation';
                            break;
                        case 'bulk':
                            echo 'Bulk Operation';
                            break;
                    }
                    ?>
                </span>
                <?php
                $fields = '';
                if (is_array(unserialize($history->fields)) && !empty(unserialize($history->fields))) {
                    foreach (unserialize($history->fields) as $field) {
                        if (is_array($field)) {
                            foreach ($field as $field_item) {
                                $field_arr = explode('_-_', $field_item);
                                if (!empty($field_arr[0]) && !empty($field_arr[1])) {
                                    $field_item = esc_html($field_arr[1]);
                                }

                                $fields .= "[" . esc_html($field_item) . "]";
                            }
                        } else {
                            $field_arr = explode('_-_', $field);
                            if (!empty($field_arr[0]) && !empty($field_arr[1])) {
                                $field = esc_html($field_arr[1]);
                            }

                            $fields .= "[" . esc_html($field) . "]";
                        }
                    }
                }
                ?>
                <span class="wcbe-history-text-sm"><?php echo esc_html($fields); ?></span>
            </td>
            <td class="wcbe-fw600"><?php echo (!empty($user_data)) ? esc_html($user_data->user_login) : ''; ?></td>
            <td class="wcbe-fw600"><?php echo esc_html(gmdate('Y / m / d', strtotime($history->operation_date))); ?></td>
            <td>
                <button type="button" class="wcbe-button wcbe-button-blue wcbe-history-revert-item"
                    value="<?php echo esc_attr($history->id); ?>"
                    <?php echo !defined('WCBE_ACTIVE') || !WCBE_ACTIVE ? 'disabled="disabled"' : ''; ?>>
                    <i class="wcbe-icon-rotate-cw"></i>
                    <?php esc_html_e('Revert', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                </button>
                <button type="button" class="wcbe-button wcbe-button-red wcbe-history-delete-item"
                    value="<?php echo esc_attr($history->id); ?>"
                    <?php echo !defined('WCBE_ACTIVE') || !WCBE_ACTIVE ? 'disabled="disabled"' : ''; ?>>
                    <i class="wcbe-icon-trash-2"></i>
                    <?php esc_html_e('Delete', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                </button>
            </td>
        </tr>
<?php
        $i++;
    endforeach;
endif;
