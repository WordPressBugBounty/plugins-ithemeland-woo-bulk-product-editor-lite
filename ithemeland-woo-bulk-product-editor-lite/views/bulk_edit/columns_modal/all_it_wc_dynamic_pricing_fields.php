<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-modal" id="wcbe-modal-it-wc-dynamic-pricing-all-fields">
    <div class="wcbe-modal-container">
        <div class="wcbe-modal-box wcbe-modal-box-lg">
            <div class="wcbe-modal-content">
                <div class="wcbe-modal-title">
                    <h2><?php esc_html_e('All Fields', 'ithemeland-woo-bulk-product-editor-lite'); ?> - <span id="wcbe-modal-it-wc-dynamic-pricing-all-fields-title" class="wcbe-modal-item-title"></span></h2>
                    <button type="button" class="wcbe-modal-close" data-toggle="modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-modal-body">
                    <div class="wcbe-wrap">
                        <div class="wcbe-col-half wcbe-modal-section" data-type="simple,variation">
                            <div class="wcbe-form-group">
                                <label>
                                    <input type="checkbox" id="wcbe-it-wc-dynamic-pricing-disable-discount" value="yes">
                                    <?php esc_html_e('Disable Discount', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                </label>
                            </div>
                            <div class="wcbe-form-group">
                                <h3><?php esc_html_e('Set Price For Each Role', 'ithemeland-woo-bulk-product-editor-lite'); ?></h3>
                                <div class="wcbe-form-group" id="wcbe-it-pricing-roles">
                                    <?php
                                    if (!empty($wp_roles) && !empty($wp_roles->roles)) :
                                        foreach ($wp_roles->roles as $role_key => $role) :
                                    ?>
                                            <div class="wcbe-form-group">
                                                <label for="role_<?php echo esc_attr($role_key); ?>"><?php echo esc_html($role['name']); ?></label>
                                                <input type="number" id="role_<?php echo esc_attr($role_key); ?>" data-type="value" data-name="<?php echo esc_attr($role_key); ?>" placeholder="<?php esc_html_e('Amount ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                                            </div>
                                    <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="wcbe-col-half wcbe-modal-section" data-type="simple,variable">
                            <div class="wcbe-form-group">
                                <label>
                                    <input type="checkbox" id="wcbe-it-wc-dynamic-pricing-hide-price-unregistered" value="yes">
                                    <?php esc_html_e('Hide Price (unregistered)', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                </label>
                            </div>
                            <div class="wcbe-form-group">
                                <label for="wcbe-select-roles-hide-price"><?php esc_html_e('Hide Price', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <select id="wcbe-select-roles-hide-price" class="wcbe-select2" data-placeholder="<?php esc_html_e('Select ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" multiple>
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
                            <div class="wcbe-form-group">
                                <label for="wcbe-select-roles-hide-add-to-cart"><?php esc_html_e('Hide Add to cart', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <select id="wcbe-select-roles-hide-add-to-cart" class="wcbe-select2" data-placeholder="<?php esc_html_e('Select ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" multiple>
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
                            <div class="wcbe-form-group">
                                <label for="wcbe-select-roles-hide-product"><?php esc_html_e('Hide Product', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <select id="wcbe-select-roles-hide-product" class="wcbe-select2" data-placeholder="<?php esc_html_e('Select ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" multiple>
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
                </div>
                <div class="wcbe-modal-footer">
                    <button type="button" id="wcbe-modal-it-wc-dynamic-pricing-all-fields-apply" data-item-id="" data-content-type="gallery" class="wcbe-button wcbe-button-blue" data-toggle="modal-close">
                        <?php esc_html_e('Apply Changes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>