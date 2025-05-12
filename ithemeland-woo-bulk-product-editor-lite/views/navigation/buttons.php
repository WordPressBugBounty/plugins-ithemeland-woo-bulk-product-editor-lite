<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<li>
    <a href="#" title="<?php esc_attr_e('Filter', 'ithemeland-woo-bulk-product-editor-lite'); ?>" data-toggle="float-side-modal" data-target="#wcbe-float-side-modal-filter">
        <i class="wcbe-icon-filter1"></i>
    </a>
</li>

<li class="wcbe-quick-filter">
    <a href="#" title="<?php esc_attr_e('Quick Search', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
        <i class="wcbe-icon-search1"></i>
    </a>
    <?php include_once WCBEL_VIEWS_DIR . "bulk_edit/filter_bar.php"; ?>
</li>

<li>
    <a href="#" title="<?php esc_attr_e('Bulk Edit', 'ithemeland-woo-bulk-product-editor-lite'); ?>" data-toggle="float-side-modal" data-target="#wcbe-float-side-modal-bulk-edit">
        <i class="wcbe-icon-edit"></i>
    </a>
</li>

<li class="wcbe-has-sub-tab">
    <a href="#" title="<?php esc_attr_e('Variations', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
        <i class="wcbe-icon-tree"></i>
    </a>

    <ul class="wcbe-sub-tab" style="width: 180px !important;">
        <li>
            <a href="#" class="wcbe-bulk-edit-variations" data-toggle="float-side-modal-after-confirm" data-target="#wcbe-float-side-modal-variation-bulk-edit">
                <i class="wcbe-icon-tree"></i>
                <span><?php esc_html_e('Manage Variations', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
            </a>
        </li>
        <li>
            <a href="#" class="wcbe-bulk-edit-show-variations-button">
                <i class="wcbe-icon-eye1"></i>
                <span><?php esc_html_e('Show Variations', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
            </a>
            <input type="hidden" id="wcbe-bulk-edit-show-variations">
        </li>
        <li>
            <a href="#" class="wcbe-bulk-edit-select-all-variations-button">
                <i class="wcbe-icon-check-square"></i>
                <span><?php esc_html_e('Select All Variations', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
            </a>
            <input type="hidden" id="wcbe-bulk-edit-select-all-variations">
        </li>
    </ul>
</li>

<li>
    <a href="#" title="<?php esc_attr_e('Bind Edit', 'ithemeland-woo-bulk-product-editor-lite'); ?>" class="wcbe-bind-edit-switch">
        <span class="default-icon">
            <svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="15px" height="15px" viewBox="0 0 201.000000 199.000000" preserveAspectRatio="xMidYMid meet">
                <g transform="translate(0.000000,199.000000) scale(0.100000,-0.100000)" fill="#444" stroke="none">
                    <path d="M1420 1970 c-74 -13 -156 -49 -217 -95 -32 -24 -157 -144 -279 -267
                        -190 -193 -225 -233 -256 -295 -80 -161 -72 -347 23 -494 64 -100 129 -134
                        194 -103 70 33 86 115 34 180 -74 92 -93 172 -64 270 16 57 26 68 253 297 244
                        245 278 271 363 283 117 16 249 -71 283 -185 20 -68 20 -94 0 -161 -13 -45
                        -31 -69 -121 -161 l-105 -108 21 -27 c24 -30 51 -113 51 -156 0 -57 49 -27
                        187 115 117 121 151 170 182 258 80 235 -27 494 -249 602 -102 50 -198 65
                        -300 47z" />
                    <path d="M1132 1264 c-68 -35 -82 -116 -31 -180 74 -92 93 -172 64 -270 -16
                        -57 -26 -68 -253 -297 -244 -245 -278 -271 -363 -283 -117 -16 -249 71 -283
                        185 -20 68 -20 94 0 161 13 45 31 69 121 161 l105 108 -21 27 c-24 30 -51 113
                        -51 156 0 57 -49 27 -187 -115 -117 -121 -151 -170 -182 -258 -43 -127 -35
                        -254 26 -379 135 -277 493 -362 740 -175 32 24 157 144 279 267 196 198 225
                        231 257 298 84 171 68 372 -40 514 -67 87 -120 110 -181 80z" />
                </g>
            </svg>
        </span>
        <span class="active-icon">
            <svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="15px" height="15px" viewBox="0 0 229.000000 229.000000" preserveAspectRatio="xMidYMid meet">
                <g transform="translate(0.000000,229.000000) scale(0.100000,-0.100000)" fill="#28a745">
                    <path d="M1515 2245 c-27 -8 -83 -31 -125 -51 -68 -33 -98 -59 -326 -288 -229
                        -229 -254 -258 -287 -326 -49 -100 -67 -177 -67 -280 0 -103 18 -180 66 -279
                        41 -84 140 -191 217 -236 l49 -28 55 54 c62 61 92 116 93 171 0 30 -5 41 -27
                        55 -50 30 -94 77 -121 128 -23 43 -27 63 -27 135 0 124 15 146 274 403 244
                        241 260 252 386 252 164 -1 288 -128 288 -295 0 -107 -29 -158 -181 -313
                        l-128 -132 19 -70 c14 -55 17 -97 15 -192 -3 -107 -2 -120 12 -113 8 4 114
                        109 235 232 225 228 265 280 307 394 30 82 37 253 13 344 -53 206 -227 382
                        -431 434 -75 19 -239 20 -309 1z" />
                    <path d="M1198 1463 c-90 -92 -116 -185 -60 -220 49 -30 93 -77 120 -128 23
                        -43 27 -63 27 -135 0 -124 -15 -147 -269 -397 -244 -242 -268 -258 -386 -258
                        -174 0 -294 121 -294 295 0 114 20 150 175 306 l131 132 -17 73 c-18 77 -24
                        230 -11 287 4 17 4 32 -1 32 -5 0 -112 -105 -239 -232 -299 -304 -334 -362
                        -342 -573 -7 -187 45 -322 173 -451 115 -115 258 -174 425 -174 103 0 180 18
                        280 66 69 34 97 58 326 288 230 229 254 257 288 326 48 100 66 177 66 280 0
                        203 -98 390 -261 499 -34 23 -64 41 -68 41 -4 0 -32 -26 -63 -57z" />
                </g>
            </svg>
        </span>
    </a>
    <input type="checkbox" style="display: none;" id="wcbe-bind-edit">
</li>

<li>
    <a href="#" class="wcbe-top-nav-duplicate-button" title="<?php esc_attr_e('Duplicate', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
        <i class="wcbe-icon-copy"></i>
    </a>
</li>

<li>
    <a href="#" class="wcbe-reload-table" title="<?php esc_attr_e('Reload', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
        <i class="wcbe-icon-refresh-cw"></i>
    </a>
</li>

<li>
    <a href="#" title="<?php esc_attr_e('Bulk New Product Form', 'ithemeland-woo-bulk-product-editor-lite'); ?>" class="wcbe-new-item-button" data-toggle="float-side-modal" data-target="#wcbe-float-side-modal-bulk-new-products">
        <i class="wcbe-icon-plus-circle" style="width: 19px; height: 19px; font-size: 19px;"></i>
    </a>
</li>

<li class="wcbe-has-sub-tab wcbe-nav-trash-button">
    <a href="#" class="wcbe-tab-icon-red" title="<?php esc_attr_e('Delete', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
        <i class="wcbe-icon-trash-2"></i>
    </a>

    <ul class="wcbe-sub-tab">
        <div data-page="general">
            <li>
                <a href="#" class="wcbe-bulk-edit-delete-action" data-delete-type="trash">
                    <i class="wcbe-icon-trash-2"></i>
                    <span><?php esc_html_e('Move to trash', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                </a>
            </li>
            <li>
                <a href="#" class="wcbe-bulk-edit-delete-action" data-delete-type="permanently">
                    <i class="wcbe-icon-delete"></i>
                    <span><?php esc_html_e('Permanently', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                </a>
            </li>
            <li>
                <a href="#" class="wcbe-bulk-edit-delete-action" data-delete-type="all">
                    <i class="wcbe-icon-x-square"></i>
                    <span><?php esc_html_e('Delete All Products', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                </a>
            </li>
            <li>
                <h6>Remove Duplicate By Title</h6>
            </li>
            <li>
                <a href="#" class="wcbe-bulk-edit-delete-duplicate-action <?php echo (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE) ? 'disabled' : ''; ?>" data-delete-type="duplatest_title">
                    <i class="wcbe-icon-x"></i>
                    <span><?php esc_html_e('Remove Latest', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                    <?php if (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE) : ?>
                        <span class="wcbe-alert-pro-description">Upgrade to pro version!</span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="#" class="wcbe-bulk-edit-delete-duplicate-action <?php echo (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE) ? 'disabled' : ''; ?>" data-delete-type="dupoldest_title">
                    <i class="wcbe-icon-x"></i>
                    <span><?php esc_html_e('Remove Oldest', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                    <?php if (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE) : ?>
                        <span class="wcbe-alert-pro-description">Upgrade to pro version!</span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <h6>Remove Duplicate By Content</h6>
            </li>
            <li>
                <a href="#" class="wcbe-bulk-edit-delete-duplicate-action <?php echo (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE) ? 'disabled' : ''; ?>" data-delete-type="duplatest_content">
                    <i class="wcbe-icon-x"></i>
                    <span><?php esc_html_e('Remove Latest', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                    <?php if (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE) : ?>
                        <span class="wcbe-alert-pro-description">Upgrade to pro version!</span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="#" class="wcbe-bulk-edit-delete-duplicate-action <?php echo (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE) ? 'disabled' : ''; ?>" data-delete-type="dupoldest_content">
                    <i class="wcbe-icon-x"></i>
                    <span><?php esc_html_e('Remove Oldest', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                    <?php if (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE) : ?>
                        <span class="wcbe-alert-pro-description">Upgrade to pro version!</span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <h6>Remove Duplicate By Title & Content</h6>
            </li>
            <li>
                <a href="#" class="wcbe-bulk-edit-delete-duplicate-action <?php echo (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE) ? 'disabled' : ''; ?>" data-delete-type="duplatest_title_content">
                    <i class="wcbe-icon-x"></i>
                    <span><?php esc_html_e('Remove Latest', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                    <?php if (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE) : ?>
                        <span class="wcbe-alert-pro-description">Upgrade to pro version!</span>
                    <?php endif; ?>
                </a>
            </li>

            <li>
                <a href="#" class="wcbe-bulk-edit-delete-duplicate-action <?php echo (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE) ? 'disabled' : ''; ?>" data-delete-type="dupoldest_title_content">
                    <i class="wcbe-icon-x"></i>
                    <span><?php esc_html_e('Remove Oldest', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                    <?php if (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE) : ?>
                        <span class="wcbe-alert-pro-description">Upgrade to pro version!</span>
                    <?php endif; ?>
                </a>
            </li>
        </div>
        <div data-page="trash">
            <li>
                <a href="#" class="wcbe-trash-option-restore-selected-items">
                    <i class="wcbe-icon-rotate-ccw"></i>
                    <span><?php esc_html_e('Restore Selected Items', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                </a>
            </li>
            <li>
                <a href="#" class="wcbe-trash-option-restore-all">
                    <i class="wcbe-icon-rotate-ccw"></i>
                    <span><?php esc_html_e('Restore All', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                </a>
            </li>
            <li>
                <a href="#" class="wcbe-trash-option-delete-selected-items">
                    <i class="wcbe-icon-x-square"></i>
                    <span><?php esc_html_e('Delete Permanently', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                </a>
            </li>
            <li>
                <a href="#" class="wcbe-trash-option-delete-all">
                    <i class="wcbe-icon-trash-2"></i>
                    <span><?php esc_html_e('Empty Trash', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                </a>
            </li>
        </div>
    </ul>
</li>

<li>
    <a href="#" title="<?php esc_attr_e('Column Profile', 'ithemeland-woo-bulk-product-editor-lite'); ?>" data-toggle="float-side-modal" data-target="#wcbe-float-side-modal-column-profiles">
        <i class="wcbe-icon-table2"></i>
    </a>
</li>

<li>
    <a href="#" title="<?php esc_attr_e('Filter Profile', 'ithemeland-woo-bulk-product-editor-lite'); ?>" data-toggle="float-side-modal" data-target="#wcbe-float-side-modal-filter-profiles">
        <i class="wcbe-icon-insert-template"></i>
    </a>
</li>

<li>
    <a href="#" title="<?php esc_attr_e('Column Manager', 'ithemeland-woo-bulk-product-editor-lite'); ?>" data-toggle="float-side-modal" data-target="#wcbe-float-side-modal-column-manager">
        <i class="wcbe-icon-columns"></i>
    </a>
</li>

<li>
    <a href="#" title="<?php esc_attr_e('Meta fields', 'ithemeland-woo-bulk-product-editor-lite'); ?>" data-toggle="float-side-modal" data-target="#wcbe-float-side-modal-meta-fields">
        <i class="wcbe-icon-list"></i>
    </a>
</li>

<li class="wcbe-has-sub-tab">
    <a href="#" class="wcbe-tab-item" title="<?php esc_attr_e('History', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
        <i class="wcbe-icon-clock"></i>
    </a>

    <ul class="wcbe-sub-tab">
        <li>
            <a href="#" data-toggle="float-side-modal" data-target="#wcbe-float-side-modal-history">
                <i class="wcbe-icon-clock"></i>
                <span><?php esc_html_e('History', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
            </a>
        </li>
        <li>
            <button <?php echo !defined('WCBE_ACTIVE') ? 'disabled="disabled" class="wcbe-lite-version"' : ''; ?> type="button" id="wcbe-bulk-edit-undo">
                <svg fill="#444" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="14px" height="14px" viewBox="0 0 454.839 454.839" xml:space="preserve">
                    <g>
                        <path d="M404.908,283.853c0,94.282-76.71,170.986-170.986,170.986h-60.526c-10.03,0-18.158-8.127-18.158-18.157v-6.053
                        c0-10.031,8.127-18.158,18.158-18.158h60.526c70.917,0,128.618-57.701,128.618-128.618c0-70.917-57.701-128.618-128.618-128.618
                        H122.255l76.905,76.905c8.26,8.257,8.26,21.699,0,29.956c-8.015,8.009-21.964,7.997-29.961,0L56.137,149.031
                        c-4.001-4.001-6.206-9.321-6.206-14.981c0-5.656,2.205-10.979,6.206-14.978L169.205,6.002c7.997-8.003,21.958-8.003,29.956,0
                        c8.26,8.255,8.26,21.699,0,29.953l-76.905,76.911h111.666C328.198,112.866,404.908,189.573,404.908,283.853z" />
                    </g>
                </svg>
                <span><?php esc_html_e('Undo', 'ithemeland-woo-bulk-product-editor-lite') ?></span>
                <?php if (!defined('WCBE_ACTIVE')): ?>
                    <span class="wcbe-alert-pro-description"><?php esc_html_e('Upgrade to pro version!', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                <?php endif; ?>
            </button>
        </li>
        <li>
            <button <?php echo !defined('WCBE_ACTIVE') ? 'disabled="disabled" class="wcbe-lite-version"' : ''; ?> type="button" id="wcbe-bulk-edit-redo">
                <svg style="transform: scaleX(-1); -moz-transform: scaleX(-1); -webkit-transform: scaleX(-1); -ms-transform: scaleX(-1);" fill="#444" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="14px" height="14px" viewBox="0 0 454.839 454.839" xml:space="preserve">
                    <g>
                        <path d="M404.908,283.853c0,94.282-76.71,170.986-170.986,170.986h-60.526c-10.03,0-18.158-8.127-18.158-18.157v-6.053
                        c0-10.031,8.127-18.158,18.158-18.158h60.526c70.917,0,128.618-57.701,128.618-128.618c0-70.917-57.701-128.618-128.618-128.618
                        H122.255l76.905,76.905c8.26,8.257,8.26,21.699,0,29.956c-8.015,8.009-21.964,7.997-29.961,0L56.137,149.031
                        c-4.001-4.001-6.206-9.321-6.206-14.981c0-5.656,2.205-10.979,6.206-14.978L169.205,6.002c7.997-8.003,21.958-8.003,29.956,0
                        c8.26,8.255,8.26,21.699,0,29.953l-76.905,76.911h111.666C328.198,112.866,404.908,189.573,404.908,283.853z" />
                    </g>
                </svg>
                <span><?php esc_html_e('Redo', 'ithemeland-woo-bulk-product-editor-lite') ?></span>
                <?php if (!defined('WCBE_ACTIVE')): ?>
                    <span class="wcbe-alert-pro-description"><?php esc_html_e('Upgrade to pro version!', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                <?php endif; ?>
            </button>
        </li>
    </ul>
</li>

<li>
    <a href="#" title="<?php esc_attr_e('Import/Export', 'ithemeland-woo-bulk-product-editor-lite'); ?>" data-toggle="float-side-modal" data-target="#wcbe-float-side-modal-import-export">
        <svg version="1.1" width="15px" height="15px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 256 256" enable-background="new 0 0 256 256" xml:space="preserve">
            <g>
                <g>
                    <path fill="#444" d="M171.8,241.8c-5.4,5.3-14.1,5.3-19.4,0c-5.4-5.3-5.4-14,0-19.3l46.8-46.1h-174c-7.6,0-13.7-6.1-13.7-13.6c0-7.6,6.1-13.7,13.7-13.7h205.7c1.2,0,2.3,0.4,3.3,0.7c2.8,0.4,5.6,1.4,7.8,3.6c5.4,5.3,5.4,14,0,19.3L171.8,241.8L171.8,241.8z" />
                    <path fill="#444" d="M232.1,107.8H25.3c-1.2,0-2.3-0.4-3.5-0.7c-2.8-0.5-5.5-1.4-7.7-3.6C8.7,98.1,8.7,89.4,14,84l70.6-69.8c5.4-5.4,14.1-5.4,19.5,0c5.4,5.4,5.4,14.1,0,19.5L57,80.2h175c7.7,0,13.8,6.2,13.8,13.8C245.8,101.6,239.7,107.8,232.1,107.8L232.1,107.8z" />
                    <path fill="#444" d="M232.1,107.8" />
                </g>
            </g>
        </svg>
    </a>
</li>

<?php do_action('wcbe_navigation_buttons_before_settings'); ?>

<li>
    <a href="#" title="<?php esc_attr_e('Settings', 'ithemeland-woo-bulk-product-editor-lite'); ?>" data-toggle="float-side-modal" data-target="#wcbe-float-side-modal-settings">
        <i class="wcbe-icon-settings"></i>
    </a>
</li>

<li style="display: none;">
    <a href="#" class="wcbe-tab-icon-red wcbe-reset-filter-form" title="<?php esc_attr_e('Reset Filter', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
        <svg width="16px" height="16px" fill="#dc3545" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg">
            <path d="M524.8 106.666667c-106.666667 0-209.066667 42.666667-285.866667 110.933333l-8.533333-68.266667c0-25.6-21.333333-42.666667-46.933333-38.4-25.6 0-42.666667 21.333333-38.4 46.933334l8.533333 115.2c4.266667 55.466667 51.2 98.133333 106.666667 98.133333h8.533333L384 362.666667c25.6 0 42.666667-21.333333 38.4-46.933334 0-25.6-21.333333-42.666667-46.933333-38.4l-85.333334 4.266667c64-55.466667 145.066667-89.6 230.4-89.6 187.733333 0 341.333333 153.6 341.333334 341.333333s-153.6 341.333333-341.333334 341.333334-341.333333-153.6-341.333333-341.333334c0-25.6-17.066667-42.666667-42.666667-42.666666s-42.666667 17.066667-42.666666 42.666666c0 234.666667 192 426.666667 426.666666 426.666667s426.666667-192 426.666667-426.666667c4.266667-234.666667-187.733333-426.666667-422.4-426.666666z" />
        </svg>
    </a>
</li>