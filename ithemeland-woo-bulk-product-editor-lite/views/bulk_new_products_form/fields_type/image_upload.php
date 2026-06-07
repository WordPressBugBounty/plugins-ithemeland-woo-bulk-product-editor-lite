<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$wcbel_upload_type = isset($wcbel_item['upload_type']) ? $wcbel_item['upload_type'] : 'single';
$wcbel_target = $wcbel_upload_type === 'single' ? 'bulk-edit-image' : 'bulk-edit-gallery';
?>
<button <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?>
    type="button"
    data-type="<?php echo esc_attr($wcbel_upload_type); ?>"
    class="wcbe-button wcbe-button-blue wcbe-float-left wcbe-open-uploader"
    data-target="<?php echo esc_attr($wcbel_target); ?>">
    <?php echo ($wcbel_upload_type) === 'single' ? esc_html__('Choose image', 'ithemeland-woo-bulk-product-editor-lite') : esc_html__('Choose images', 'ithemeland-woo-bulk-product-editor-lite'); ?>
</button>

<?php if ($wcbel_upload_type === 'single'): ?>
    <input type="hidden" data-field="value" class="wcbe-bulk-edit-form-item-image">
    <div class="wcbe-bulk-edit-form-item-image-preview"></div>
<?php else: ?>
    <div class="wcbe-bulk-edit-form-item-gallery"></div>
    <div class="wcbe-bulk-edit-form-item-gallery-preview"></div>
    <span class="wcbe-bulk-edit-form-item-remove-all-images" style="display:none;">
        <?php esc_html_e('Remove all images', 'ithemeland-woo-bulk-product-editor-lite'); ?>
    </span>
<?php endif; ?>