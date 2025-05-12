<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wcbe-float-side-modal" id="wcbe-float-side-modal-variation-bulk-edit">
    <div class="wcbe-float-side-modal-container">
        <div class="wcbe-float-side-modal-box">
            <div class="wcbe-float-side-modal-content">
                <input type="hidden" id="filter-form-changed" value="">
                <div class="wcbe-float-side-modal-title">
                    <h2><?php esc_html_e('Variation Bulk Edit', 'ithemeland-woo-bulk-product-editor-lite'); ?></h2>
                    <button type="button" class="wcbe-float-side-modal-close" data-toggle="float-side-modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-float-side-modal-body">
                    <div class="wcbe-wrap">
                        <div class="wcbe-tabs">
                            <div class="wcbe-tabs-navigation">
                                <nav class="wcbe-tabs-navbar">
                                    <ul class="wcbe-tabs-list" data-content-id="wcbe-variation-bulk-edit-tabs">
                                        <li>
                                            <a class="selected wcbe-tab-item" data-content="set-variation" href="#"><?php esc_html_e('Set Variation', 'ithemeland-woo-bulk-product-editor-lite'); ?></a>
                                        </li>
                                        <li><a class="wcbe-tab-item" data-content="delete-variation" href="#"><?php esc_html_e('Delete Variation', 'ithemeland-woo-bulk-product-editor-lite'); ?></a></li>
                                        <li><a class="wcbe-tab-item" data-content="attach-variation" href="#"><?php esc_html_e('Attribute / Change Variation', 'ithemeland-woo-bulk-product-editor-lite'); ?></a></li>
                                    </ul>
                                </nav>
                            </div>
                            <div class="wcbe-tabs-contents" id="wcbe-variation-bulk-edit-tabs">
                                <div class="selected wcbe-tab-content-item wcbe-variations-bulk-edit-set-variation" data-content="set-variation">
                                    <div class="wcbe-alert wcbe-alert-default wcbe-variation-pro-alert wcbe-mt10">
                                        <span>
                                            <i class="dashicons dashicons-warning" style="width: 32px; height: 32px; font-size: 32px; margin-right: 0; vertical-align: middle;"></i>
                                            Discover our new and enhanced option for 'Variation Management' in our plugin! See more here!
                                            <a href="https://youtu.be/5th4dV57LkQ">Learn more here</a>
                                            <a href="https://ithemelandco.com/plugins/woocommerce-variations-bulk-edit/?utm_source=plugins&utm_medium=plugin_links&utm_campaign=variation_popup">Get It</a>
                                        </span>
                                    </div>
                                    <div class="wcbe-alert wcbe-alert-warning">
                                        <span>
                                            <i class="dashicons dashicons-warning" style="width: 32px; height: 32px; font-size: 32px; margin-right: 0; vertical-align: middle;"></i>
                                            This tab use for add new variations.
                                            <br>
                                            Note: if you change any fields, all of exist variations will be removed.
                                            <br>
                                            Please Goto "Attach Variation" if you want to change your combinations while keeping current data.
                                        </span>
                                    </div>
                                    <div class="wcbe-variation-bulk-edit-left">
                                        <div class="wcbe-variation-bulk-edit-product-variations">
                                            <label for="wcbe-variation-bulk-edit-attributes"><?php esc_html_e('Product Attributes', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                            <select id="wcbe-variation-bulk-edit-attributes" class="wcbe-select2" multiple>
                                                <?php if (!empty($attributes)) : ?>
                                                    <?php foreach ($attributes as $attribute) : ?>
                                                        <option value="<?php echo esc_attr($attribute->attribute_name); ?>"><?php echo esc_html($attribute->attribute_name); ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="wcbe-variation-bulk-edit-attributes">
                                            <span class="wcbe-variation-bulk-edit-attributes-title"><?php esc_html_e('Select Attributes', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                                            <div id="wcbe-variation-bulk-edit-attributes-added">

                                            </div>
                                        </div>
                                        <div class="wcbe-variation-bulk-edit-create">
                                            <div class="wcbe-variation-bulk-edit-create-mode">
                                                <div class="wcbe-pb20"><span><?php esc_html_e('How To Create Variations ?', 'ithemeland-woo-bulk-product-editor-lite'); ?></span></div>
                                                <label class="wcbe-variation-bulk-edit-create-mode">
                                                    <input type="radio" name="create_variation_mode" checked="checked" data-mode="all_combination">
                                                    <?php esc_html_e('All Combinations', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                                </label>
                                                <label>
                                                    <input type="radio" name="create_variation_mode" data-mode="individual_combination">
                                                    <?php esc_html_e('Individual Combination', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                                </label>
                                            </div>
                                            <div id="wcbe-variation-bulk-edit-individual" style="display: none">
                                                <div class="wcbe-variation-bulk-edit-individual-items">

                                                </div>
                                                <button type="button" id="wcbe-variation-bulk-edit-manual-add" disabled="disabled" class="wcbe-button wcbe-button-blue wcbe-button-md wcbe-mt20">
                                                    <i class="wcbe-icon-shuffle"></i>
                                                    <?php esc_html_e('Add', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                                </button>
                                            </div>
                                            <button type="button" id="wcbe-variation-bulk-edit-generate" disabled="disabled" class="wcbe-button wcbe-button-blue wcbe-button-md wcbe-mt20">
                                                <i class="wcbe-icon-shuffle"></i>
                                                <?php esc_html_e('Generate', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="wcbe-variation-bulk-edit-right">
                                        <div class="wcbe-variation-bulk-edit-right-title">
                                            <span><?php esc_html_e('Current Variations', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                                        </div>
                                        <div class="wcbe-variation-bulk-edit-current-variations">
                                            <div class="wcbe-variation-bulk-edit-current-items">

                                            </div>
                                            <div class="wcbe-variation-bulk-edit-right-footer">
                                                <button type="button" disabled="disabled" class="wcbe-button wcbe-button-md wcbe-button-blue wcbe-variation-bulk-edit-do-bulk" id="wcbe-variation-bulk-edit-do-bulk-variations">
                                                    <i class="wcbe-icon-shuffle"></i>
                                                    <?php esc_html_e('Do Bulk Variations', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="wcbe-variation-bulk-edit-loading">
                                        <p><img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" width="36"></p>
                                    </div>
                                </div>
                                <div class="wcbe-tab-content-item" data-content="delete-variation">
                                    <div class="wcbe-variation-delete-mode">
                                        <div class="wcbe-pb20 wcbe-pt40"><span><?php esc_html_e('How To Delete Variations ?', 'ithemeland-woo-bulk-product-editor-lite'); ?></span></div>
                                        <label class="wcbe-variation-bulk-edit-create-mode wcbe-mr20">
                                            <input type="radio" name="delete_variation_mode" checked="checked" data-mode="delete_all">
                                            <?php esc_html_e('All Variations', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                        </label>
                                        <label id="wcbe-variation-bulk-edit-single-delete">
                                            <input type="radio" name="delete_variation_mode" data-mode="single_delete">
                                            <?php esc_html_e('Single Variation', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                        </label>
                                    </div>
                                    <div id="wcbe-variation-delete-delete-all">
                                        <button type="button" id="wcbe-variation-delete-all" class="wcbe-button wcbe-button-blue wcbe-button-md wcbe-mt20">
                                            <i class="wcbe-icon-shuffle"></i>
                                            <?php esc_html_e('Delete All', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                        </button>
                                    </div>
                                    <div id="wcbe-variation-delete-single-delete" style="display: none">
                                        <div id="wcbe-variation-single-delete-variations">
                                            <div id="wcbe-variation-single-delete-items"></div>
                                            <button type="button" id="wcbe-variation-delete-selected" disabled="disabled" class="wcbe-button wcbe-button-blue wcbe-button-md wcbe-mt20">
                                                <i class="wcbe-icon-shuffle"></i>
                                                <?php esc_html_e('Delete Selected', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                            </button>
                                        </div>
                                        <div id="wcbe-variations-multiple-products-delete-variation">
                                            <div class="wcbe-variation-bulk-edit-product-variations">
                                                <label for="wcbe-variation-bulk-edit-delete-attributes">
                                                    <?php esc_html_e('Product Attributes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                                </label>
                                                <select id="wcbe-variation-bulk-edit-delete-attributes" class="wcbe-select2" multiple>
                                                    <?php if (!empty($attributes)) : ?>
                                                        <?php foreach ($attributes as $attribute) : ?>
                                                            <option value="<?php echo esc_attr($attribute->attribute_name); ?>"><?php echo esc_html($attribute->attribute_name); ?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                            <div class="wcbe-variation-bulk-edit-attributes">
                                                <span class="wcbe-variation-bulk-edit-attributes-title"><?php esc_html_e('Select Variation Items', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                                                <div id="wcbe-variation-bulk-edit-delete-attributes-added"></div>
                                            </div>
                                            <button type="button" id="wcbe-variation-delete-selected-variation" class="wcbe-button wcbe-button-blue wcbe-button-md wcbe-mt20">
                                                <i class="wcbe-icon-shuffle"></i>
                                                <?php esc_html_e('Delete Variation', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="wcbe-tab-content-item" data-content="attach-variation">
                                    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                                        <?php wp_nonce_field('wcbe_post_nonce'); ?>
                                        <input type="hidden" name="action" value="wcbe_variation_attaching">
                                        <div class="wcbe-variation-bulk-edit-product-variations">
                                            <label><?php esc_html_e('Select Attribute', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                            <select id="wcbe-variations-attaching-attributes" class="wcbe-w40p wcbe-mr10" name="attribute_key" title="Select Attribute">
                                                <option value=""><?php esc_html_e('Select Attribute', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                <?php if (!empty($attributes)) : ?>
                                                    <?php foreach ($attributes as $attribute) : ?>
                                                        <option value="<?php echo esc_attr($attribute->attribute_name); ?>"><?php echo esc_html($attribute->attribute_name); ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <div id="wcbe-variation-attaching-attribute-items"></div>
                                        </div>
                                        <div class="wcbe-variation-bulk-edit-product-variations">
                                            <label for="wcbe-variation-attaching-variable-id"><?php esc_html_e('Variable Product ID:', 'ithemeland-woo-bulk-product-editor-lite'); ?> </label>
                                            <input type="number" id="wcbe-variation-attaching-variable-id" class="wcbe-input-md" placeholder="Variable product id ...">
                                            <button type="button" disabled="disabled" id="wcbe-variation-attaching-get-variations" class="wcbe-button wcbe-button-md wcbe-button-blue wcbe-ml10">
                                                <?php esc_html_e('Get Variations', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                            </button>
                                        </div>
                                        <div class="wcbe-variation-bulk-edit-product-variations">
                                            <div id="wcbe-variations-attaching-product-variations"></div>
                                            <button type="button" disabled="disabled" id="wcbe-variation-attaching-start-attaching" class="wcbe-button wcbe-button-lg wcbe-button-blue wcbe-mt20">
                                                <?php esc_html_e('Update', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>