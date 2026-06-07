<?php

use wcbel\classes\helpers\Sanitizer;

if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<table id="wcbe-items-list" class="widefat">
    <thead>
        <tr>
            <?php if (isset($show_id_column) && $show_id_column === true) : ?>
                <?php
                if ('id' == $settings['default_sort_by']) {
                    if ($settings['default_sort'] == 'ASC') {
                        $wcbel_sortable_icon = "<i class='dashicons dashicons-arrow-up'></i>";
                    } else {
                        $wcbel_sortable_icon = "<i class='dashicons dashicons-arrow-down'></i>";
                    }
                } else {
                    $wcbel_sortable_icon = "<img src='" . esc_url(WCBEL_IMAGES_URL . "sortable.png") . "' alt=''>";
                }
                ?>
                <th class="wcbe-td70 <?php echo ($settings['sticky_first_columns'] == 'yes') ? 'wcbe-td-sticky wcbe-td-sticky-id' : ''; ?>">
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
                    if ($wcbel_static_column['name'] == $settings['default_sort_by']) {
                        if ($settings['default_sort'] == 'ASC') {
                            $wcbel_sortable_icon = "<i class='dashicons dashicons-arrow-up'></i>";
                        } else {
                            $wcbel_sortable_icon = "<i class='dashicons dashicons-arrow-down'></i>";
                        }
                    } else {
                        $wcbel_sortable_icon = "<img src='" . esc_url(WCBEL_IMAGES_URL . "/sortable.png") . "' alt=''>";
                    }
                    ?>
                    <th data-column-name="<?php echo esc_attr($wcbel_static_column['name']) ?>" class="wcbe-sortable-column wcbe-td120 <?php echo ($settings['sticky_first_columns'] == 'yes') ? 'wcbe-td-sticky wcbe-td-sticky-title' : ''; ?>"><?php echo esc_html($wcbel_static_column['label']); ?><span class="wcbe-sortable-column-icon"><?php echo wp_kses($wcbel_sortable_icon, Sanitizer::allowed_html()); ?></span></th>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if (!empty($columns) && is_array($columns)) :
                foreach ($columns as $wcbel_column_name => $wcbel_column) :
                    $wcbel_title = (!empty($columns_title) && isset($columns_title[$wcbel_column_name])) ? $columns_title[$wcbel_column_name] : '';
                    $wcbel_sortable_icon = '';
                    if (isset($wcbel_column['sortable']) && $wcbel_column['sortable'] === true) {
                        if ($wcbel_column_name == $settings['default_sort_by']) {
                            if ($settings['default_sort'] == 'ASC') {
                                $wcbel_sortable_icon = "<i class='dashicons dashicons-arrow-up'></i>";
                            } else {
                                $wcbel_sortable_icon = "<i class='dashicons dashicons-arrow-down'></i>";
                            }
                        } else {
                            $wcbel_sortable_icon = "<img src='" . esc_url(WCBEL_IMAGES_URL . "/sortable.png") . "' alt=''>";
                        }
                    }

                    if (isset($settings['display_full_columns_title']) && $settings['display_full_columns_title'] == 'yes') {
                        $wcbel_column_title = $wcbel_column['title'];
                    } else {
                        $wcbel_column_title = (strlen($wcbel_column['title']) > 12) ? mb_substr($wcbel_column['title'], 0, 12) . '.' : $wcbel_column['title'];
                    }
            ?>
                    <th data-column-name="<?php echo esc_attr($wcbel_column_name); ?>" <?php echo (!empty($wcbel_column['sortable'])) ? 'class="wcbe-sortable-column"' : ''; ?>><?php echo (!empty($wcbel_title)) ? "<span class='wcbe-column-title dashicons dashicons-info' title='" . esc_attr($wcbel_title) . "'></span>" : "" ?> <?php echo esc_html($wcbel_column_title); ?> <span class="wcbe-sortable-column-icon"><?php echo wp_kses($wcbel_sortable_icon, Sanitizer::allowed_html()); ?></span></th>
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
        <tr data-type="loading">
            <td colspan="100%" style="text-align: center;"><img style="vertical-align: middle; padding: 5px 0;" width="22" height="22" src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>"></td>
        </tr>
    </tbody>
</table>