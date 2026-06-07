<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<?php if (!empty($product_statuses) && is_array($product_statuses) && !empty($product_counts_by_status) && is_array($product_counts_by_status)) : ?>
    <ul>
        <li><a href="#" data-status="all" class="wcbe-bulk-edit-status-filter-item all"><?php esc_html_e('All', 'ithemeland-woo-bulk-product-editor-lite'); ?> (<?php echo isset($product_counts_by_status['all']) ? intval($product_counts_by_status['all']) : 0 ?>)</a></li>
        <?php foreach ($product_statuses as $wcbel_status_key => $wcbel_status_label) : ?>
            <?php if (isset($product_counts_by_status[$wcbel_status_key])) : ?>
                <li><a href="#" data-status="<?php echo esc_attr($wcbel_status_key); ?>" class="wcbe-bulk-edit-status-filter-item <?php echo esc_attr($wcbel_status_key); ?>"><?php echo esc_html($wcbel_status_label . ' (' . $product_counts_by_status[$wcbel_status_key] . ')'); ?></a></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>