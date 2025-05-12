<?php

use wcbel\classes\helpers\Sanitizer;

if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$tab_title = (!empty($tab['title'])) ? $tab['title'] : esc_html__('Tab title', 'ithemeland-woo-bulk-product-editor-lite');
$tab_content = (!empty($tab['content'])) ? wp_kses($tab['content'], Sanitizer::allowed_html()) : '';
$unique_id = (!empty($tab_unique_id)) ? $tab_unique_id : 'editor-' . wp_rand(100, 999);
?>

<div class="wcbe-yikes-tab-item" <?php echo (!empty($duplicate_item)) ? 'id="duplicate-item"' : ''; ?>>
    <div class="wcbe-yikes-tab-item-header">
        <strong class="wcbe-yikes-tab-item-header-title"><?php echo esc_html($tab_title); ?></strong>
        <button type="button" class="wcbe-yikes-tab-item-sort"><i class=" wcbe-icon-menu1"></i></button>
        <button type="button" class="wcbe-yikes-tab-item-remove"><i class="wcbe-icon-x"></i></button>
        <?php if (!empty($tab['global'])) : ?>
            <label class="wcbe-yikes-tab-override"><input type="checkbox" class="wcbe-yikes-override-tab" name="override_tab" value="1"> <?php esc_html_e('Override Saved Tab', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
        <?php endif; ?>
    </div>
    <div class="wcbe-yikes-tab-item-body">
        <input type="hidden" name="global_tab" value="<?php echo (!empty($tab['global'])) ? esc_attr($tab['global']) : '' ?>" data-global-id="<?php echo (!empty($tab['global'])) ? esc_attr($tab['global']) : '' ?>">
        <div class="wcbe-yikes-tab-title">
            <label><?php esc_html_e('Tab title', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
            <input type="text" placeholder="<?php esc_html_e('Tab title ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" value="<?php echo esc_attr($tab_title); ?>">
        </div>
        <div class="wcbe-yikes-tab-content" data-id="<?php echo esc_attr($unique_id); ?>">
            <label><?php esc_html_e('Tab content', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
            <?php if (!empty($duplicate_item)) : ?>
                <textarea placeholder="<?php esc_html_e('Content ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>"></textarea>
            <?php
            else :
                wp_editor($tab_content, $unique_id);
            endif;
            ?>
        </div>
    </div>
</div>