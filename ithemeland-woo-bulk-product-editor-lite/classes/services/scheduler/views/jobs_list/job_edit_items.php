<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-schedule-job-edit-items-section">
    <table class="wcbe-modal-schedule-job-edit-items-table">
        <thead>
            <tr>
                <th><?php esc_html_e('Column Name', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
                <th><?php esc_html_e('Operator', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
                <th><?php esc_html_e('Value', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($job->edit_items)):
                foreach ($job->edit_items as $wcbel_item):
                    if (!isset($wcbel_item['name']) || !isset($wcbel_item['value'])) {
                        continue;
                    }

                    $wcbel_name = (!empty($edit_columns[$wcbel_item['name']]) && !empty($edit_columns[$wcbel_item['name']]['label'])) ? $edit_columns[$wcbel_item['name']]['label'] : $wcbel_item['name'];
                    $wcbel_value = '';
                    if (is_array($wcbel_item['value'])) {
                        if (isset($wcbel_item['type']) && $wcbel_item['type'] == 'taxonomy') {
                            $wcbel_name = (!empty($taxonomies[$wcbel_item['name']]) && !empty($taxonomies[$wcbel_item['name']]['label'])) ? $taxonomies[$wcbel_item['name']]['label'] : $wcbel_item['name'];
                            foreach ($wcbel_item['value'] as $wcbel_term_id) {
                                $term = get_term_by('term_id', intval($wcbel_term_id), $wcbel_item['name']);
                                if (!($term instanceof \WP_Term)) {
                                    continue;
                                }
                                if (!empty($wcbel_value)) {
                                    $wcbel_value .= ', ';
                                }
                                $wcbel_value .= $term->name;
                            }
                        } else {
                            if (isset($item['value']['from']) && isset($item['value']['to'])) {
                                $wcbel_value = 'From: ' . $item['value']['from'] . ' | To: ' . $item['value']['to'];
                            } else {
                                $wcbel_value = implode(', ', $item['value']);
                            }
                        }
                    } else {
                        $wcbel_value = $item['value'];
                    }
            ?>
                    <tr>
                        <td><?php echo esc_html($wcbel_name); ?></td>
                        <?php if (!empty($item['operator'])): ?>
                            <td><?php echo (!empty($operators[$item['operator']])) ? esc_html($operators[$item['operator']]) : esc_html($item['operator']); ?></td>
                        <?php else: ?>
                            <td> </td>
                        <?php endif; ?>
                        <td><?php echo esc_html($wcbel_value); ?></td>
                    </tr>
            <?php
                endforeach;
            endif;
            ?>
        </tbody>
    </table>
</div>

<div class="wcbe-schedule-job-edit-items-section">
    <?php if (isset($job->filter_items['product_ids'])): ?>
        <h3><?php esc_html_e('Selected Products', 'ithemeland-woo-bulk-product-editor-lite'); ?></h3>
        <?php
        foreach ($job->filter_items['product_ids'] as $wcbel_product_id) {
            $wcbel_product = wc_get_product(intval($wcbel_product_id));
            if (!($wcbel_product instanceof \WC_Product)) {
                continue;
            }
            echo '<div class="wcbe-schedule-job-edit-items-selected-product-item">#' . esc_html($wcbel_product->get_id()) . ' - ' . esc_html($wcbel_product->get_title()) . '</div>';
        }
        ?>
    <?php else: ?>
        <h3><?php esc_html_e('Filter Items', 'ithemeland-woo-bulk-product-editor-lite'); ?></h3>
        <?php if (empty($job->filter_items['fields'])): ?>
            <span style="font-size: 14px"><?php esc_html_e('All Products', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
        <?php else: ?>
            <table class="wcbe-modal-schedule-job-edit-items-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Filter Name', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
                        <th><?php esc_html_e('Operator', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
                        <th><?php esc_html_e('Value', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($job->filter_items['fields'] as $wcbel_filter_item) :
                        if (!isset($wcbel_filter_item['name']) || !isset($wcbel_filter_item['value'])) {
                            continue;
                        }

                        $wcbel_value = '';
                        if (is_array($wcbel_filter_item['value'])) {
                            if (isset($wcbel_filter_item['value']['from']) && isset($wcbel_filter_item['value']['to'])) {
                                $wcbel_value = 'From: ' . $wcbel_filter_item['value']['from'] . ' | To: ' . $wcbel_filter_item['value']['to'];
                            } else {
                                $wcbel_value = implode(', ', $wcbel_filter_item['value']);
                            }
                        } else {
                            $wcbel_value = $wcbel_filter_item['value'];
                        }

                        $wcbel_name = (!empty($filter_columns[$wcbel_filter_item['name']]) && !empty($filter_columns[$wcbel_filter_item['name']]['label'])) ? $filter_columns[$wcbel_filter_item['name']]['label'] : $wcbel_filter_item['name'];
                    ?>
                        <tr>
                            <td><?php echo esc_html($wcbel_name); ?></td>
                            <?php if (!empty($wcbel_filter_item['operator'])): ?>
                                <td><?php echo (!empty($operators[$wcbel_filter_item['operator']])) ? esc_html($operators[$wcbel_filter_item['operator']]) : esc_html($wcbel_filter_item['operator']); ?></td>
                            <?php else: ?>
                                <td> </td>
                            <?php endif; ?>
                            <td><?php echo esc_html($wcbel_value); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</div>