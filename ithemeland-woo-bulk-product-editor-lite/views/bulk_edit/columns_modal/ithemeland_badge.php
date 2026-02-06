<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<?php $ithemeland_badge_plugin_url = (function_exists('iThemeland_WooCommerce_Advanced_Product_Labels_Pro')) ? plugins_url('/', iThemeland_WooCommerce_Advanced_Product_Labels_Pro()->file) : ''; ?>
<div class="wcbe-modal" id="wcbe-modal-ithemeland-badge">
    <div class="wcbe-modal-container">
        <div class="wcbe-modal-box wcbe-modal-box-lg">
            <div class="wcbe-modal-content">
                <div class="wcbe-modal-title">
                    <h2><?php esc_html_e('iThemeland badge', 'ithemeland-woo-bulk-product-editor-lite'); ?> - <span id="wcbe-modal-ithemeland-badge-item-title" class="wcbe-modal-item-title"></span></h2>
                    <button type="button" class="wcbe-modal-close" data-toggle="modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-modal-body">
                    <form id="ithemeland-badge-form">
                        <div id="it-woocommerce-advanced-product-labels-pro" class="panel woocommerce_options_panel">
                            <div class="options_group">
                                <p class="form-field _unique_label_exclude_field">
                                    <label for="_unique_label_exclude"><?php esc_html_e('Exclude Global Labels', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                    <input type="checkbox" class="checkbox ithemeland-badge-form-item" data-value-position="self" name="_unique_label_exclude" id="_unique_label_exclude" value="yes">
                                    <span class="description"><?php esc_html_e('When checked, global labels will be excluded', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                                </p>
                            </div>
                            <div class="options_group">
                                <div class="it_column_setting">
                                    <nav class="it-unique-tab nav-tab-wrapper">
                                        <a href="general" data-tab="general" class="it_unique_nav_for_general nav-tab">
                                            <span class="icon-nav dashicons dashicons-admin-generic"></span>
                                            <?php esc_html_e('General', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                        </a>
                                        <a href="other" data-tab="customize" class="it_unique_nav_for_customize nav-tab nav-tab-active">
                                            <span class="icon-nav dashicons dashicons-welcome-write-blog"></span>
                                            <?php esc_html_e('Customize', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                        </a>
                                        <a href="schedule" data-tab="schedule" class="it_unique_nav_for_schedule nav-tab">
                                            <span class="icon-nav dashicons dashicons-clock"></span>
                                            <?php esc_html_e('Schedule', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                        </a>
                                    </nav>
                                    <div class="tab-content" id="general">
                                        <div class="fieldwrap">
                                            <p class="form-field _unique_label_type_field">
                                                <label for="_unique_label_type"><?php esc_html_e('LABEL TYPE', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <select id="_unique_label_type" name="_unique_label_type" class="update-preview ithemeland-badge-form-item" data-value-position="self">
                                                    <option value="none"><?php esc_html_e('Choose One', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                    <option value="label"><?php esc_html_e('Label', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                    <option value="flash"><?php esc_html_e('Pre Define Shape', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                    <option value="count-down"><?php esc_html_e('Count Down', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                    <option value="image"><?php esc_html_e('Image', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                </select>
                                            </p>
                                            <div class="unique-alert unique-alert-notice it_hide">
                                                <span class="unique-closebtn" onclick="this.parentElement.style.display='none';">×</span>
                                                <strong><?php esc_html_e('Notice!', 'ithemeland-woo-bulk-product-editor-lite'); ?></strong> <?php esc_html_e('You need to set the time in the schedule tab.', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                            </div>
                                            <div class="unique-global-option" style="padding: 5px 20px 5px 162px !important;">
                                                <label for="_unique_label_shape"><?php esc_html_e('Label Shape', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <div class="itbd-label-shape-wrap" id="_unique_label_shape">
                                                    <div class="itbd-label-option-wrap" style="padding-left: 0px;">
                                                        <div class="itbd-label-field-wrap ithemeland-badge-form-item" data-value-position="child" data-name="_unique_label_shape" data-type="radio">
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="1" name="_unique_label_shape" class="itbd-text-design update-preview" value="cut-diamond">
                                                                <label class="itbd-existing-images-demo" for="1">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/predefine/1.png') ?>">
                                                                </label>
                                                            </div>
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="2" name="_unique_label_shape" class="itbd-text-design update-preview" value="round-star">
                                                                <label class="itbd-existing-images-demo" for="2">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/predefine/2.png') ?>">
                                                                </label>
                                                            </div>
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="3" data-name="_unique_label_shape" class="itbd-text-design update-preview" value="ribbon">
                                                                <label class="itbd-existing-images-demo" for="3">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/predefine/3.png') ?>">
                                                                </label>
                                                            </div>
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="4" name="_unique_label_shape" class="itbd-text-design update-preview" value="circle-ribbon">
                                                                <label class="itbd-existing-images-demo" for="4">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/predefine/4.png') ?>">
                                                                </label>
                                                            </div>
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="5" name="_unique_label_shape" class="itbd-text-design update-preview" value="diamond">
                                                                <label class="itbd-existing-images-demo" for="5">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/predefine/5.png') ?>">
                                                                </label>
                                                            </div>
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="6" name="_unique_label_shape" class="itbd-text-design update-preview" value="triangle-topleft">
                                                                <label class="itbd-existing-images-demo" for="6">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/predefine/6.png') ?>">
                                                                </label>
                                                            </div>
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="7" name="_unique_label_shape" class="itbd-text-design update-preview" value="triangle-topright">
                                                                <label class="itbd-existing-images-demo" for="7">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/predefine/7.png') ?>">
                                                                </label>
                                                            </div>
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="8" name="_unique_label_shape" class="itbd-text-design update-preview" value="heart">
                                                                <label class="itbd-existing-images-demo" for="8">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/predefine/8.png') ?>">
                                                                </label>
                                                            </div>
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="9" name="_unique_label_shape" class="itbd-text-design update-preview" value="loophole-1" checked="checked">
                                                                <label class="itbd-existing-images-demo" for="9">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/predefine/9.png') ?>">
                                                                </label>
                                                            </div>
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="10" name="_unique_label_shape" class="itbd-text-design update-preview" value="loophole-2">
                                                                <label class="itbd-existing-images-demo" for="10">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/predefine/10.png') ?>">
                                                                </label>
                                                            </div>
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="11" name="_unique_label_shape" class="itbd-text-design update-preview" value="loophole-3">
                                                                <label class="itbd-existing-images-demo" for="11">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/predefine/11.png') ?>">
                                                                </label>
                                                            </div>
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="12" name="_unique_label_shape" class="itbd-text-design update-preview" value="loophole-4">
                                                                <label class="itbd-existing-images-demo" for="12">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/predefine/12.png') ?>">
                                                                </label>
                                                            </div>
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="13" name="_unique_label_shape" class="itbd-text-design update-preview" value="loophole-5">
                                                                <label class="itbd-existing-images-demo" for="13">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/predefine/13.png') ?>">
                                                                </label>
                                                            </div>
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="14" name="_unique_label_shape" class="itbd-text-design update-preview" value="corner-ribbons">
                                                                <label class="itbd-existing-images-demo" for="14">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/predefine/14.png') ?>">
                                                                </label>
                                                            </div>
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="15" name="_unique_label_shape" class="itbd-text-design update-preview" value="corner-ribbons-two">
                                                                <label class="itbd-existing-images-demo" for="15">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/predefine/15.png') ?>">
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="unique-global-option" style="padding-top: 5px; padding-right: 20px !important; padding-bottom: 5px; padding-left: 162px !important; display: none;">
                                                <label for="_unique_label_style"><?php esc_html_e('Count Down style', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <div class="itbd-label-shape-wrap" id="_unique_label_style">
                                                    <div class="itbd-label-option-wrap" style="padding-left: 0px;">
                                                        <div class="itbd-label-field-wrap ithemeland-badge-form-item" data-value-position="child" data-name="_unique_label_style" data-type="radio">
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="100" name="_unique_label_style" class="itbd-text-design itbd-text-design-style" value="style-1">
                                                                <label class="itbd-existing-images-demo" for="100">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/countdown/100.png') ?>">
                                                                </label>
                                                            </div>
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="101" name="_unique_label_style" class="itbd-text-design itbd-text-design-style" value="style-2">
                                                                <label class="itbd-existing-images-demo" for="101">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/countdown/101.png') ?>">
                                                                </label>
                                                            </div>
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="102" name="_unique_label_style" class="itbd-text-design itbd-text-design-style" value="style-3">
                                                                <label class="itbd-existing-images-demo" for="102">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/countdown/102.png') ?>">
                                                                </label>
                                                            </div>
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="103" name="_unique_label_style" class="itbd-text-design itbd-text-design-style" value="style-4">
                                                                <label class="itbd-existing-images-demo" for="103">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/countdown/103.png') ?>">
                                                                </label>
                                                            </div>
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="104" name="_unique_label_style" class="itbd-text-design itbd-text-design-style" value="style-5">
                                                                <label class="itbd-existing-images-demo" for="104">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/countdown/104.png') ?>">
                                                                </label>
                                                            </div>
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="105" name="_unique_label_style" class="itbd-text-design itbd-text-design-style" value="style-6">
                                                                <label class="itbd-existing-images-demo" for="105">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/countdown/105.png') ?>">
                                                                </label>
                                                            </div>
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="106" name="_unique_label_style" class="itbd-text-design itbd-text-design-style" value="style-7">
                                                                <label class="itbd-existing-images-demo" for="106">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/countdown/106.png') ?>">
                                                                </label>
                                                            </div>
                                                            <div class="itbd-hide-radio">
                                                                <input type="radio" id="107" name="_unique_label_style" class="itbd-text-design itbd-text-design-style" value="style-8">
                                                                <label class="itbd-existing-images-demo" for="107">
                                                                    <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'images/countdown/107.png') ?>">
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class=" form-field _unique_label_advanced_field">
                                                <label for="_unique_label_advanced"><?php esc_html_e('Use Intelligent label', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <select id="_unique_label_advanced" name="_unique_label_advanced" class="update-preview ithemeland-badge-form-item" data-value-position="self">
                                                    <option value="none"><?php esc_html_e('None', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                    <option value="percentage" selected="selected"><?php esc_html_e('Percentage', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                    <option value="discount"><?php esc_html_e('Discount', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                    <option value="price"><?php esc_html_e('Regular Price', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                    <option value="saleprice"><?php esc_html_e('Sale Price', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                    <option value="delprice"><?php esc_html_e('Delete Price', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                </select>
                                            </p>
                                            <p class="form-field _unique_label_text_field">
                                                <label for="_unique_label_text"><?php esc_html_e('Label Text', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <input type="text" class="update-preview ithemeland-badge-form-item" data-value-position="self" name="_unique_label_text" id="_unique_label_text" value="50%" placeholder="">
                                            </p>
                                            <p class="form-field unique-global-option">
                                                <label for="_unique_label_show_icon"><?php esc_html_e('Show icon', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <input class="update-preview as-toggle-checkbox ithemeland-badge-form-item" data-value-position="self" type="checkbox" value="1" name="_unique_label_show_icon" id="_unique_label_show_icon">
                                                <label class="as-toggle-span-label as-show-icon" for="_unique_label_show_icon"></label>
                                            </p>
                                            <div class="unique-global-option custom-icon" style="padding-top: 5px; padding-right: 20px !important; padding-bottom: 5px; padding-left: 162px !important; display: none;">
                                                <label for="_unique_label_badge_icon"><?php esc_html_e('Choose icon', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <div data-target="icon-picker" class="button icon-picker dashicons dashicons-plus"></div>
                                                <input class="icon-picker-input it-picker-icon ithemeland-badge-form-item" data-value-position="self" type="text" id="_unique_label_badge_icon" name="_unique_label_badge_icon" value="">
                                            </div>
                                            <p class="form-field custom-colors it_hide" style="display: block;">
                                                <span class="custom_color_chose">
                                                    <label for="_unique-custom-background"><?php esc_html_e('Background color', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                    <input type='text' name='_unique-custom-background' value='#D9534F' id='_unique-custom-background' class='color-picker background-picker ithemeland-badge-form-item' data-value-position="self" data-alpha-enabled="true" />
                                                </span>
                                                <span class="custom_color_chose">
                                                    <label for="_unique-custom-text"><?php esc_html_e('Text Color', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                    <input type='text' name='_unique-custom-text' value='#ffffff' id='_unique-custom-text' class='color-picker text-picker ithemeland-badge-form-item' data-value-position="self" data-alpha-enabled="true" />
                                                </span>
                                            </p>
                                            <p class=" form-field _unique_label_align_field" style="display: none;">
                                                <label for="_unique_label_align"><?php esc_html_e('Label text align', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <select id="_unique_label_align" name="_unique_label_align" class="unique-select ithemeland-badge-form-item" data-value-position="self">
                                                    <option value="center" selected="selected"><?php esc_html_e('Center', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                    <option value="left"><?php esc_html_e('Left', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                    <option value="right"><?php esc_html_e('Right', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                </select>
                                            </p>
                                            <p class="form-field unique-global-option custom-image it_hide">
                                                <label for="_unique_label_image"><?php esc_html_e('Label Image', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <span class="form-field" style="min-width:210px;display:inline-block;">
                                                    <span id="unique_thumbnail" style="float:left;margin-right:10px;">
                                                        <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'assets/admin/images/placeholder.png') ?>" width="60px" height="60px">
                                                    </span>
                                                    <span style="line-height:60px;">
                                                        <input type="hidden" id="_unique_label_image" name="_unique_label_image" value="<?php echo esc_url($ithemeland_badge_plugin_url . 'assets/admin/images/placeholder.png') ?>" class="update-preview ithemeland-badge-form-item" data-value-position="self">
                                                        <button type="button" class="it_upload_image_button button">+</button>
                                                        <button type="button" class="it_remove_image_button button">x</button>
                                                    </span>

                                                    <span class="clear"></span>
                                                </span>
                                            </p>
                                            <p class="form-field _unique_label_class_field">
                                                <label for="_unique_label_class"><?php esc_html_e('Label css class', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <input type="text" class="update-preview ithemeland-badge-form-item" data-value-position="self" name="_unique_label_class" id="_unique_label_class" value="" placeholder="">
                                            </p>
                                        </div>
                                    </div>
                                    <div class="tab-content tab-content-active" id="customize">
                                        <div class="fieldwrap">
                                            <p class="form-field _unique_label_font_size_field" style="display: none;">
                                                <label for="_unique_label_font_size"><?php esc_html_e('Font size', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <input type="text" class="short ithemeland-badge-form-item" data-value-position="self" name="_unique_label_font_size" id="_unique_label_font_size" value="12" placeholder="">
                                            </p>
                                            <p class="form-field _unique_label_line_height_field" style="display: none;">
                                                <label for="_unique_label_line_height"><?php esc_html_e('Line height', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <input type="text" class="short ithemeland-badge-form-item" data-value-position="self" name="_unique_label_line_height" id="_unique_label_line_height" value="-1" placeholder="">
                                            </p>
                                            <hr class="hr-under-line-height" style="border: 0px none; height: 1px; background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0)); margin-right: 15px; display: none;">
                                            <p class="form-field _unique_label_width_field" style="display: none;">
                                                <label for="_unique_label_width"><?php esc_html_e('Width', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <input type="text" class="short ithemeland-badge-form-item" data-value-position="self" name="_unique_label_width" id="_unique_label_width" value="80" placeholder="">
                                            </p>
                                            <p class="form-field _unique_label_height_field" style="display: none;">
                                                <label for="_unique_label_height"><?php esc_html_e('Height', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <input type="text" class="short ithemeland-badge-form-item" data-value-position="self" name="_unique_label_height" id="_unique_label_height" value="30" placeholder="">
                                            </p>
                                            <hr class="hr-under-height" style="border: 0px none; height: 1px; background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0)); margin-right: 15px; display: none;">
                                            <p class=" form-field _unique_label_border_style_field" style="display: none;">
                                                <label for="_unique_label_border_style"><?php esc_html_e('Border style', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <select id="_unique_label_border_style" name="_unique_label_border_style" class="select short ithemeland-badge-form-item" data-value-position="self">
                                                    <option value="none" selected="selected"><?php esc_html_e('None', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                    <option value="dotted"><?php esc_html_e('Dotted', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                    <option value="dashed"><?php esc_html_e('Dashed', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                    <option value="solid"><?php esc_html_e('Solid', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                    <option value="double"><?php esc_html_e('Double', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                    <option value="groove"><?php esc_html_e('Groove', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                    <option value="ridge"><?php esc_html_e('Ridge', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                    <option value="inset"><?php esc_html_e('Inset', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                    <option value="outset"><?php esc_html_e('Outset', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                </select>
                                            </p>
                                            <p class="form-field" style="display: none;">
                                                <label for="_unique_label_border_width"><?php esc_html_e('Border width', 'ithemeland-woo-bulk-product-editor-lite'); ?> <small><?php esc_html_e('(px)', 'ithemeland-woo-bulk-product-editor-lite'); ?></small></label>
                                                <input type="text" value="" name="_unique_label_border_width_top" id="_unique_label_border_width_top" size="25" class="update-preview radius_input ithemeland-badge-form-item" data-value-position="self" placeholder="Top">
                                                <input type="text" value="" name="_unique_label_border_width_right" id="_unique_label_border_width_right" size="25" class="update-preview radius_input ithemeland-badge-form-item" data-value-position="self" placeholder="Right">
                                                <input type="text" value="" name="_unique_label_border_width_bottom" id="_unique_label_border_width_bottom" size="25" class="update-preview radius_input ithemeland-badge-form-item" data-value-position="self" placeholder="Bottom">
                                                <input type="text" value="" name="_unique_label_border_width_left" id="_unique_label_border_width_left" size="25" class="update-preview radius_input ithemeland-badge-form-item" data-value-position="self" placeholder="Left">
                                            </p>
                                            <p class="form-field custom_color_chose_b" style="display: none;">
                                                <label for="_unique_label_border_color"><?php esc_html_e('Border color', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <input type='text' name='_unique_label_border_color' value='#ffffff' id='_unique_label_border_color' class='color-picker ithemeland-badge-form-item' data-value-position="self" data-alpha-enabled="true" />
                                            </p>
                                            <p class="form-field" style="display: none;">
                                                <label for="_unique_label_border_radius"><?php esc_html_e('Border radius', 'ithemeland-woo-bulk-product-editor-lite'); ?> <small><?php esc_html_e('(px)', 'ithemeland-woo-bulk-product-editor-lite'); ?></small></label>
                                                <input type="text" value="" name="_unique_label_border_r_tl" id="_unique_label_border_r_tl" size="25" class="update-preview radius_input ithemeland-badge-form-item" data-value-position="self" placeholder="Top Left">
                                                <input type="text" value="" name="_unique_label_border_r_tr" id="_unique_label_border_r_tr" size="25" class="update-preview radius_input ithemeland-badge-form-item" data-value-position="self" placeholder="Top Right">
                                                <input type="text" value="" name="_unique_label_border_r_br" id="_unique_label_border_r_br" size="25" class="update-preview radius_input ithemeland-badge-form-item" data-value-position="self" placeholder="Bottom Right">
                                                <input type="text" value="" name="_unique_label_border_r_bl" id="_unique_label_border_r_bl" size="25" class="update-preview radius_input ithemeland-badge-form-item" data-value-position="self" placeholder="Bottom Left">
                                            </p>
                                            <p class="form-field" style="display: none;">
                                                <label for="_unique_label_padding"><?php esc_html_e('Padding', 'ithemeland-woo-bulk-product-editor-lite'); ?> <small><?php esc_html_e('(px)', 'ithemeland-woo-bulk-product-editor-lite'); ?></small></label>
                                                <input type="text" value="" name="_unique_label_padding_top" id="_unique_label_padding_top" size="25" class="update-preview radius_input ithemeland-badge-form-item" data-value-position="self" placeholder="Top">
                                                <input type="text" value="" name="_unique_label_padding_right" id="_unique_label_padding_right" size="25" class="update-preview radius_input ithemeland-badge-form-item" data-value-position="self" placeholder="Right">
                                                <input type="text" value="" name="_unique_label_padding_bottom" id="_unique_label_padding_bottom" size="25" class="update-preview radius_input ithemeland-badge-form-item" data-value-position="self" placeholder="Bottom">
                                                <input type="text" value="" name="_unique_label_padding_left" id="_unique_label_padding_left" size="25" class="update-preview radius_input ithemeland-badge-form-item" data-value-position="self" placeholder="Left">
                                            </p>
                                            <hr class="hr-under-padding" style="border: 0px none; height: 1px; background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0)); margin-right: 15px; display: none;">
                                            <p class="form-field range-slider">
                                                <label for="_unique_label_opacity"><?php esc_html_e('Opacity', 'ithemeland-woo-bulk-product-editor-lite') ?></label>
                                                <input class="update-preview range-slider__range ithemeland-badge-form-item" data-value-position="self" type="range" value="100" min="0" max="100" name="_unique_label_opacity" id="_unique_label_opacity">
                                                <span class="range-slider__value">100</span>
                                            </p>
                                            <p class="form-field range-slider rotation_range">
                                                <label for="_unique_label_rotation"><?php esc_html_e('Rotation', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <span class="range-item">
                                                    <input class="update-preview range-slider__range ithemeland-badge-form-item" data-value-position="self" type="range" value="360" min="0" max="360" name="_unique_label_rotation_x" id="_unique_label_rotation_x">
                                                    <span class="range-slider__value">360</span>
                                                </span>
                                                <span class="range-item">
                                                    <input class="update-preview range-slider__range ithemeland-badge-form-item" data-value-position="self" type="range" value="360" min="0" max="360" name="_unique_label_rotation_y" id="_unique_label_rotation_y">
                                                    <span class="range-slider__value">360</span>
                                                </span>
                                                <span class="range-item">
                                                    <input class="update-preview range-slider__range ithemeland-badge-form-item" data-value-position="self" type="range" value="360" min="0" max="360" name="_unique_label_rotation_z" id="_unique_label_rotation_z">
                                                    <span class="range-slider__value">360</span>
                                                </span>
                                            </p>
                                            <p class="form-field">
                                                <label for="_unique_label_flip_text"><?php esc_html_e('Flip text', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <input class="update-preview as-toggle-checkbox ithemeland-badge-form-item" data-value-position="self" type="checkbox" value="1" name="_unique_label_flip_text_h" id="_unique_label_flip_text_h"><label class="as-toggle-span-label" for="_unique_label_flip_text_h"></label>
                                                <span class="as-toggle-span"><?php esc_html_e('Flip horizontally', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                                                <input class="update-preview as-toggle-checkbox ithemeland-badge-form-item" data-value-position="self" type="checkbox" value="1" name="_unique_label_flip_text_v" id="_unique_label_flip_text_v"><label class="as-toggle-span-label" for="_unique_label_flip_text_v"></label>
                                                <span class="as-toggle-span"><?php esc_html_e('Flip Vertically', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                                            </p>
                                            <hr style="border: 0;height: 1px;background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0));margin-right: 15px;">
                                            <p class="form-field _unique_label_pos_top_field">
                                                <label for="_unique_label_pos_top"><?php esc_html_e('Top', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <input type="text" class="short ithemeland-badge-form-item" data-value-position="self" name="_unique_label_pos_top" id="_unique_label_pos_top" value="0" placeholder="">
                                            </p>
                                            <p class="form-field _unique_label_pos_right_field">
                                                <label for="_unique_label_pos_right"><?php esc_html_e('Right', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <input type="text" class="short ithemeland-badge-form-item" data-value-position="self" name="_unique_label_pos_right" id="_unique_label_pos_right" value="0" placeholder="">
                                            </p>
                                            <p class="form-field _unique_label_pos_bottom_field">
                                                <label for="_unique_label_pos_bottom"><?php esc_html_e('Button', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <input type="text" class="short ithemeland-badge-form-item" data-value-position="self" name="_unique_label_pos_bottom" id="_unique_label_pos_bottom" value="auto" placeholder="">
                                            </p>
                                            <p class="form-field _unique_label_pos_left_field">
                                                <label for="_unique_label_pos_left"><?php esc_html_e('Left', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <input type="text" class="short ithemeland-badge-form-item" data-value-position="self" name="_unique_label_pos_left" id="_unique_label_pos_left" value="auto" placeholder="">
                                            </p>
                                            <p class="form-field custom-pos">
                                                <label for="unique-custom-position"><?php esc_html_e('Positioning', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <span class="unique-table">
                                                    <span class="unique-table-row">
                                                        <span class="unique-table-col"><img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'assets/admin/images/top-left.png') ?>" id="unique-pos-top-left" width="40px"></span>
                                                        <span class="unique-table-col"><img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'assets/admin/images/top-center.png') ?>" id="unique-pos-top-center" width="40px"></span>
                                                        <span class="unique-table-col"><img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'assets/admin/images/top-right.png') ?>" id="unique-pos-top-right" width="40px"></span>
                                                    </span>
                                                    <span class="unique-table-row">
                                                        <span class="unique-table-col"><img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'assets/admin/images/center-left.png') ?>" id="unique-pos-center-left" width="40px"></span>
                                                        <span class="unique-table-col"><img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'assets/admin/images/center.png') ?>" id="unique-pos-center" width="40px"></span>
                                                        <span class="unique-table-col"><img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'assets/admin/images/center-right.png') ?>" id="unique-pos-center-right" width="40px"></span>
                                                    </span>
                                                    <span class="unique-table-row">
                                                        <span class="unique-table-col"><img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'assets/admin/images/bottom-left.png') ?>" id="unique-pos-bottom-left" width="40px"></span>
                                                        <span class="unique-table-col"><img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'assets/admin/images/bottom-center.png') ?>" id="unique-pos-bottom-center" width="40px"></span>
                                                        <span class="unique-table-col"><img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'assets/admin/images/bottom-right.png') ?>" id="unique-pos-bottom-right" width="40px"></span>
                                                    </span>
                                                </span>
                                            </p>
                                            <div class="unique-alert unique-alert-info">
                                                <span class="unique-closebtn" onclick="this.parentElement.style.display='none';">×</span>
                                                <strong><?php esc_html_e('Pivot point', 'ithemeland-woo-bulk-product-editor-lite') ?>:</strong> <?php esc_html_e('Used to drag and drop positioning.', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-content" id="schedule">
                                        <div class="fieldwrap">
                                            <p class=" form-field _unique_label_time_field">
                                                <label for="_unique_label_time"><?php esc_html_e('Use schedule', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <select id="_unique_label_time" name="_unique_label_time" class="update-preview ithemeland-badge-form-item" data-value-position="self">
                                                    <option value="none"><?php esc_html_e('None', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                    <option value="product"><?php esc_html_e('Set From Sale Product Schedule', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                    <option value="global"><?php esc_html_e('Set in Here', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                                </select>
                                            </p>
                                            <p class="form-field">
                                                <label for="_unique_label_start_date"><?php esc_html_e('Label date', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                                <input type="text" class="short regular-text schedule-datepicker update-preview hasDatepicker wcbe-datepicker-with-dash ithemeland-badge-form-item" data-value-position="self" name="_unique_label_start_date" id="_unique_label_start_date" placeholder="From… YYYY-MM-DD" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])">
                                                <input type="text" class="short regular-text schedule-datepicker update-preview hasDatepicker wcbe-datepicker-with-dash ithemeland-badge-form-item" data-value-position="self" name="_unique_label_end_date" id="_unique_label_end_date" placeholder="To…  YYYY-MM-DD" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])">
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="it_column_preview">
                                    <div class="preview_titel">
                                        <h2 class="unique-preview"><?php esc_html_e('Preview Box', 'ithemeland-woo-bulk-product-editor-lite'); ?><br><small><?php esc_html_e('(Drag and drop to position the label)', 'ithemeland-woo-bulk-product-editor-lite'); ?></small></h2>
                                    </div>
                                    <div class="preview_image" style="top: 0px;">
                                        <div id="unique-global-preview">
                                            <img src="<?php echo esc_url($ithemeland_badge_plugin_url . 'assets/admin/images/thumb.gif') ?>">
                                            <div class="tooltiptester-no  unique-label-id-1516 label-wrap unique-flash unique-shape-loophole-1   ui-draggable ui-draggable-handle" style="inset: 0px 0px auto auto; opacity: 1; transform: rotateX(360deg) rotateY(360deg) rotateZ(360deg);">
                                                <div class="woocommerce-advanced-product-label product-label" style="color: rgb(255, 255, 255); background-color: rgb(217, 83, 79);">
                                                    <div class="unique-label-text">50%</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="loading-label"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="wcbe-modal-footer">
                    <button type="button" id="wcbe-modal-ithemeland-badge-apply" data-item-id="" class="wcbe-button wcbe-button-blue" data-toggle="modal-close">
                        <?php esc_html_e('Apply Changes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                    <button type="button" class="wcbe-button wcbe-button-gray wcbe-float-right" data-toggle="modal-close">
                        <?php esc_html_e('Close', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>