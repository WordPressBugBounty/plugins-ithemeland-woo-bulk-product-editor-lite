<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<?php if (!empty($file_id)) : ?>
    <div class="wcbe-bulk-edit-custom-field-file-item">
        <button type="button" class="wcbe-button wcbe-button-flat wcbe-bulk-edit-custom-field-files-sortable-btn" title="<?php esc_html_e('Drag', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
            <i class=" wcbe-icon-menu1"></i>
        </button>
        <input type="text" class="wcbe-bulk-edit-file-name" placeholder="File Name ..." value="<?php echo (!empty($file['name'])) ? esc_attr($file['name']) : ''; ?>">
        <input type="hidden" id="id-<?php echo esc_attr($file_id); ?>">
        <input type="text" class="wcbe-bulk-edit-file-url wcbe-w60p" name="file_url" id="id-<?php echo esc_attr($file_id); ?>-url" placeholder="File Url ..." value="<?php echo (!empty($file['url'])) ? esc_attr($file['url']) : ''; ?>">
        <button type="button" class="wcbe-button wcbe-button-white wcbe-open-uploader wcbe-bulk-edit-choose-file" data-type="single" data-target="#id-<?php echo esc_attr($file_id); ?>"><?php esc_html_e('Choose File', 'ithemeland-woo-bulk-product-editor-lite'); ?></button>
        <button type="button" class="wcbe-button wcbe-button-white wcbe-bulk-edit-custom-field-file-remove-item">x</button>
    </div>
<?php endif; ?>