<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

use wcbel\classes\helpers\Sanitizer;

include WCBEL_VIEWS_DIR . "layouts/header.php"; ?>

<div id="wcbe-body">
    <div class="wcbe-tabs wcbe-tabs-main">
        <div class="wcbe-tabs-navigation">
            <nav class="wcbe-tabs-navbar">
                <ul class="wcbe-tabs-list" data-type="url" data-content-id="wcbe-main-tabs-contents">
                    <?php echo wp_kses(apply_filters('wcbe_top_navigation_buttons', ''), Sanitizer::allowed_html()); ?>
                </ul>
            </nav>

            <div class="wcbe-top-nav-filters-per-page">
                <select id="wcbe-quick-per-page" title="The number of products per page">
                    <?php
                    if (!empty($count_per_page_items)) :
                        $current_value = (!empty($current_settings['count_per_page'])) ? $current_settings['count_per_page'] : $settings['count_per_page'];
                        foreach ($count_per_page_items as $count_per_page_item) :
                    ?>
                            <option value="<?php echo intval($count_per_page_item); ?>" <?php echo ($settings['count_per_page'] == intval($count_per_page_item)) ? 'selected' : ''; ?>>
                                <?php echo esc_html($count_per_page_item); ?>
                            </option>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>

            <div class="wcbe-top-nav-filters-go-to-page">
                <input type="number" id="wcbe-top-nav-filters-go-to-page" title="Go to page" min="1" max="" placeholder="Page">
            </div>

            <div class="wcbe-items-pagination"></div>
        </div>

        <div class="wcbe-tabs-contents" id="wcbe-main-tabs-contents">
            <div class="wcbe-wrap">
                <div class="wcbe-tab-middle-content">
                    <div class="wcbe-table" id="wcbe-items-table">
                        <?php
                        if (!empty($table) && file_exists(sanitize_text_field($table))) :
                            include $table;
                        else :
                        ?>
                            <p style="width: 100%; text-align: center; padding: 10px 0;"><img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading.gif'); ?>" width="30" height="30"></p>
                        <?php endif; ?>
                    </div>
                    <div class="wcbe-items-count"></div>
                </div>
            </div>
        </div>
        <div class="wcbe-table-loading">
            <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" width="18" height="18">
        </div>
        <div class="wcbe-created-by">
            <a href="https://ithemelandco.com" target="_blank">Created by iThemelandCo</a>
        </div>
    </div>
</div>

<?php include_once  WCBEL_VIEWS_DIR . "layouts/footer.php"; ?>