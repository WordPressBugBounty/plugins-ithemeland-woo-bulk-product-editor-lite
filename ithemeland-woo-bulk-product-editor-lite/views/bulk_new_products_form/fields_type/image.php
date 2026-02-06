<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<button <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?> type="button" data-type="single" class="wcbe-button wcbe-button-blue wcbe-float-left wcbe-open-uploader" data-target="bulk-edit-image"><?php esc_html_e('Choose image', 'ithemeland-woo-bulk-product-editor-lite'); ?></button>
<input type="hidden" data-field="value" class="wcbe-bulk-edit-form-item-image">
<div class="wcbe-bulk-edit-form-item-image-preview"></div>