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
                foreach ($job->edit_items as $item):
                    if (!isset($item['name']) || !isset($item['value'])) {
                        continue;
                    }

                    $name = (!empty($edit_columns[$item['name']]) && !empty($edit_columns[$item['name']]['label'])) ? $edit_columns[$item['name']]['label'] : $item['name'];
                    $value = '';
                    if (is_array($item['value'])) {
                        if (isset($item['type']) && $item['type'] == 'taxonomy') {
                            $name = (!empty($taxonomies[$item['name']]) && !empty($taxonomies[$item['name']]['label'])) ? $taxonomies[$item['name']]['label'] : $item['name'];
                            foreach ($item['value'] as $term_id) {
                                $term = get_term_by('term_id', intval($term_id), $item['name']);
                                if (!($term instanceof \WP_Term)) {
                                    continue;
                                }
                                if (!empty($value)) {
                                    $value .= ', ';
                                }
                                $value .= $term->name;
                            }
                        } else {
                            if (isset($item['value']['from']) && isset($item['value']['to'])) {
                                $value = 'From: ' . $item['value']['from'] . ' | To: ' . $item['value']['to'];
                            } else {
                                $value = implode(', ', $item['value']);
                            }
                        }
                    } else {
                        $value = $item['value'];
                    }
            ?>
                    <tr>
                        <td><?php echo esc_html($name); ?></td>
                        <?php if (!empty($item['operator'])): ?>
                            <td><?php echo (!empty($operators[$item['operator']])) ? esc_html($operators[$item['operator']]) : esc_html($item['operator']); ?></td>
                        <?php else: ?>
                            <td> </td>
                        <?php endif; ?>
                        <td><?php echo esc_html($value); ?></td>
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
        foreach ($job->filter_items['product_ids'] as $product_id) {
            $product = wc_get_product(intval($product_id));
            if (!($product instanceof \WC_Product)) {
                continue;
            }
            echo '<div class="wcbe-schedule-job-edit-items-selected-product-item">#' . esc_html($product->get_id()) . ' - ' . esc_html($product->get_title()) . '</div>';
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
                    foreach ($job->filter_items['fields'] as $filter_item) :
                        if (!isset($filter_item['name']) || !isset($filter_item['value'])) {
                            continue;
                        }

                        $value = '';
                        if (is_array($filter_item['value'])) {
                            if (isset($filter_item['value']['from']) && isset($filter_item['value']['to'])) {
                                $value = 'From: ' . $filter_item['value']['from'] . ' | To: ' . $filter_item['value']['to'];
                            } else {
                                $value = implode(', ', $filter_item['value']);
                            }
                        } else {
                            $value = $filter_item['value'];
                        }

                        $name = (!empty($filter_columns[$filter_item['name']]) && !empty($filter_columns[$filter_item['name']]['label'])) ? $filter_columns[$filter_item['name']]['label'] : $filter_item['name'];
                    ?>
                        <tr>
                            <td><?php echo esc_html($name); ?></td>
                            <?php if (!empty($filter_item['operator'])): ?>
                                <td><?php echo (!empty($operators[$filter_item['operator']])) ? esc_html($operators[$filter_item['operator']]) : esc_html($filter_item['operator']); ?></td>
                            <?php else: ?>
                                <td> </td>
                            <?php endif; ?>
                            <td><?php echo esc_html($value); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</div>