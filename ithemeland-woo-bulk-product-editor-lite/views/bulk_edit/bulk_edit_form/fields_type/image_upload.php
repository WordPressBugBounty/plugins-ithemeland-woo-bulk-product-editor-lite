<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$upload_type = isset($item['upload_type']) ? $item['upload_type'] : 'single';
$target = $upload_type === 'single' ? 'bulk-edit-image' : 'bulk-edit-gallery';
?>
<button <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?>
    type="button"
    data-type="<?php echo esc_attr($upload_type); ?>"
    class="wcbe-button wcbe-button-blue wcbe-float-left wcbe-open-uploader"
    data-target="<?php echo esc_attr($target); ?>">
    <?php echo ($upload_type === 'single') ? esc_html__('Choose image', 'ithemeland-woo-bulk-product-editor-lite') : esc_html__('Choose images', 'ithemeland-woo-bulk-product-editor-lite'); ?>
</button>

<?php if ($upload_type === 'single'): ?>
    <input type="hidden" data-field="value" class="wcbe-bulk-edit-form-item-image">
    <div class="wcbe-bulk-edit-form-item-image-preview"></div>
<?php else: ?>
    <div class="wcbe-bulk-edit-form-item-gallery"></div>
    <div class="wcbe-bulk-edit-form-item-gallery-preview"></div>
    <span class="wcbe-bulk-edit-form-item-remove-all-images" style="display:none;">
        <?php esc_html_e('Remove all images', 'ithemeland-woo-bulk-product-editor-lite'); ?>
    </span>
<?php endif; ?>