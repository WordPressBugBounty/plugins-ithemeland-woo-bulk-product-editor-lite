<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-modal" id="wcbe-modal-it-wc-dynamic-pricing-select-roles">
    <div class="wcbe-modal-container">
        <div class="wcbe-modal-box wcbe-modal-box-sm">
            <div class="wcbe-modal-content">
                <div class="wcbe-modal-title">
                    <h2><?php esc_html_e('Dynamic pricing by roles', 'ithemeland-woo-bulk-product-editor-lite'); ?> - <span id="wcbe-modal-it-wc-dynamic-pricing-select-roles-title" class="wcbe-modal-item-title"></span></h2>
                    <button type="button" class="wcbe-modal-close" data-toggle="modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-modal-body">
                    <div class="wcbe-wrap">
                        <div class="wcbe-form-group">
                            <label for="wcbe-user-roles"><?php esc_html_e('Select Roles', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                            <select id="wcbe-user-roles" class="wcbe-select2" data-placeholder="<?php esc_html_e('Select ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" multiple>
                                <?php
                                if (!empty($wp_roles) && !empty($wp_roles->roles)) :
                                    foreach ($wp_roles->roles as $role_key => $role) :
                                ?>
                                        <option value="<?php echo esc_attr($role_key); ?>"><?php echo esc_html($role['name']); ?></option>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="wcbe-modal-footer">
                    <button type="button" id="wcbe-modal-it-wc-dynamic-pricing-select-roles-apply" data-item-id="" data-content-type="" class="wcbe-button wcbe-button-blue" data-toggle="modal-close">
                        <?php esc_html_e('Apply Changes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>