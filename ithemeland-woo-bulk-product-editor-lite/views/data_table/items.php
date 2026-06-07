<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

use wcbel\classes\helpers\Sanitizer;
?>

<table id="wcbe-items-list" class="widefat">
    <thead>
        <tr>
            <?php
            if (isset($show_id_column) && $show_id_column === true) : ?>
                <?php
                if ('id' == $sort_by) {
                    if ($sort_type == 'ASC') {
                        $wcbel_sortable_icon = "<i class='dashicons dashicons-arrow-up'></i>";
                    } else {
                        $wcbel_sortable_icon = "<i class='dashicons dashicons-arrow-down'></i>";
                    }
                } else {
                    $wcbel_sortable_icon = "<img src='" . esc_url(WCBEL_IMAGES_URL . "/sortable.png") . "' alt=''>";
                }
                ?>
                <th class="wcbe-td70 <?php echo ($sticky_first_columns == 'yes') ? 'wcbe-td-sticky wcbe-td-sticky-id' : ''; ?>">
                    <div class="wcbe-table-item-selector-container">
                        <input type="checkbox" class="wcbe-table-item-selector-checkbox">
                        <div class="wcbe-table-item-selector" title="<?php esc_attr_e('Select All', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                            <ul>
                                <li>
                                    <label>
                                        <input type="checkbox" value="all" class="wcbe-check-item-main">
                                        <?php esc_html_e('Select All', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="checkbox" value="visible" class="wcbe-check-item-main">
                                        <span><?php esc_html_e('Select Visible', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <label data-column-name="id" class="wcbe-sortable-column"><?php esc_html_e('ID', 'ithemeland-woo-bulk-product-editor-lite'); ?><span class="wcbe-sortable-column-icon"><?php echo wp_kses($wcbel_sortable_icon, Sanitizer::allowed_html()); ?></span></label>
                </th>
            <?php endif; ?>
            <?php if (!empty($next_static_columns)) : ?>
                <?php foreach ($next_static_columns as $wcbel_static_column) : ?>
                    <?php
                    if ($wcbel_static_column['field'] == $sort_by) {
                        if ($sort_type == 'ASC') {
                            $wcbel_sortable_icon = "<i class='dashicons dashicons-arrow-up'></i>";
                        } else {
                            $wcbel_sortable_icon = "<i class='dashicons dashicons-arrow-down'></i>";
                        }
                    } else {
                        $wcbel_sortable_icon = "<img src='" . esc_url(WCBEL_IMAGES_URL . "/sortable.png") . "' alt=''>";
                    }
                    ?>
                    <th data-column-name="<?php echo esc_attr($wcbel_static_column['field']) ?>" class="wcbe-sortable-column wcbe-td120 <?php echo ($sticky_first_columns == 'yes') ? 'wcbe-td-sticky wcbe-td-sticky-title' : ''; ?>"><?php echo esc_html($wcbel_static_column['title']); ?><span class="wcbe-sortable-column-icon"><?php echo wp_kses($wcbel_sortable_icon, Sanitizer::allowed_html()); ?></span></th>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if (!empty($columns) && is_array($columns)) :
                foreach ($columns as $wcbel_column_name => $wcbel_column) :
                    $title = (!empty($columns_title) && isset($columns_title[$wcbel_column_name])) ? $columns_title[$wcbel_column_name] : '';
                    $wcbel_sortable_icon = '';
                    if (isset($wcbel_column['sortable']) && $wcbel_column['sortable'] === true) {
                        if ($wcbel_column_name == $sort_by) {
                            if ($sort_type == 'ASC') {
                                $wcbel_sortable_icon = "<i class='dashicons dashicons-arrow-up'></i>";
                            } else {
                                $wcbel_sortable_icon = "<i class='dashicons dashicons-arrow-down'></i>";
                            }
                        } else {
                            $wcbel_sortable_icon = "<img src='" . esc_url(WCBEL_IMAGES_URL . "/sortable.png") . "' alt=''>";
                        }
                    }

                    if (isset($display_full_columns_title) && $display_full_columns_title == 'yes') {
                        $wcbel_column_title = $wcbel_column['title'];
                    } else {
                        $wcbel_column_title = (strlen($wcbel_column['title']) > 12) ? mb_substr($wcbel_column['title'], 0, 12) . '.' : $wcbel_column['title'];
                    }
            ?>
                    <th data-column-name="<?php echo esc_attr($wcbel_column_name); ?>" <?php echo (!empty($wcbel_column['sortable'])) ? 'class="wcbe-sortable-column"' : ''; ?>><?php echo (!empty($title)) ? "<span class='wcbe-column-title dashicons dashicons-info' title='" . esc_attr($title) . "'></span>" : "" ?> <?php echo esc_html($wcbel_column_title); ?> <span class="wcbe-sortable-column-icon"><?php echo wp_kses($wcbel_sortable_icon, Sanitizer::allowed_html()); ?></span></th>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if (!empty($after_dynamic_columns)) : ?>
                <?php foreach ($after_dynamic_columns as $wcbel_last_column_item) : ?>
                    <th data-column-name="<?php echo esc_attr($wcbel_last_column_item['field']) ?>" class="wcbe-td120"><?php echo esc_html($wcbel_last_column_item['title']); ?></th>
                <?php endforeach; ?>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($items_loading)) : ?>
            <tr>
                <td colspan="8" class="wcbe-text-alert"><?php esc_html_e('Loading ...', 'ithemeland-woo-bulk-product-editor-lite'); ?></td>
            </tr>
        <?php
        elseif (!empty($items) && count($items) > 0) :
            if (!empty($item_provider && is_object($item_provider))) :
                $wcbel_items_result = $item_provider->get_items($items, $columns);
                if (!empty($wcbel_items_result)) :
                    echo (is_array($wcbel_items_result) && !empty($wcbel_items_result['items'])) ? wp_kses($wcbel_items_result, Sanitizer::allowed_html()['items']) : wp_kses($wcbel_items_result, Sanitizer::allowed_html());
                endif;
            endif;
        else :
        ?>
            <tr>
                <td colspan="8" class="wcbe-text-alert"><?php esc_html_e('No Data Available!', 'ithemeland-woo-bulk-product-editor-lite'); ?></td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
if (!empty($wcbel_items_result['includes']) && is_array($wcbel_items_result['includes'])) {
    foreach (wcbel\classes\helpers\Others::array_flatten($wcbel_items_result['includes']) as $wcbel_include_item) {
        echo !empty($wcbel_include_item) ? wp_kses($wcbel_include_item, Sanitizer::allowed_html()) : '';
    }
}
