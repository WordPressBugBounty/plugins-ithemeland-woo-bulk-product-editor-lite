<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<?php if (!empty($file_id)) : ?>
    <div class="wcbe-modal-select-files-file-item">
        <button type="button" class="wcbe-button wcbe-button-flat wcbe-select-files-sortable-btn" title="<?php esc_html_e('Drag', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
            <i class=" wcbe-icon-menu1"></i>
        </button>
        <input type="text" class="wcbe-inline-edit-file-name" placeholder="File Name ..." value="<?php echo !empty($file_item) ? esc_attr($file_item->get_name()) : ''; ?>">
        <input type="text" class="wcbe-inline-edit-file-url wcbe-w60p" id="url-<?php echo esc_attr($file_id); ?>" name="file_url" placeholder="File Url ..." value="<?php echo !empty($file_item) ? esc_attr($file_item->get_file()) : ''; ?>">
        <button type="button" class="wcbe-button wcbe-button-white wcbe-open-uploader wcbe-inline-edit-choose-file" data-type="single" data-target="inline-file" data-id="<?php echo esc_attr($file_id); ?>"><?php esc_html_e('Choose File', 'ithemeland-woo-bulk-product-editor-lite'); ?></button>
        <button type="button" class="wcbe-button wcbe-button-white wcbe-inline-edit-file-remove-item">x</button>
    </div>
<?php endif; ?>