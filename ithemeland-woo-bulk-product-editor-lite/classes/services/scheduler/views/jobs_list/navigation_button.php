<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<li class="wcbe-schedule-jobs-list-navigation-button">
    <a href="#" title="<?php esc_html_e('Schedule jobs', 'ithemeland-woo-bulk-product-editor-lite'); ?>" data-toggle="float-side-modal" data-target="#wcbe-float-side-modal-schedule-jobs">
        <svg width="15px" height="15px" viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <g id="Free-Icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round">
                <g transform="translate(-303.000000, -748.000000)" id="Group" stroke="#e38e42" stroke-width="2">
                    <g transform="translate(301.000000, 746.000000)" id="Shape">
                        <circle cx="15.5" cy="15.5" r="5.5"></circle>
                        <polyline points="15.5 13.3440934 15.5 15.5 17 17"></polyline>
                        <line x1="17" y1="3" x2="17" y2="5"></line>
                        <line x1="7" y1="3" x2="7" y2="5"></line>
                        <path d="M8.03064542,21 C7.42550126,21 6.51778501,21 5.30749668,21 C4.50512981,21 4.2141722,20.9218311 3.92083887,20.7750461 C3.62750553,20.6282612 3.39729582,20.4128603 3.24041943,20.1383964 C3.08354305,19.8639324 3,19.5916914 3,18.8409388 L3,7.15906122 C3,6.4083086 3.08354305,6.13606756 3.24041943,5.86160362 C3.39729582,5.58713968 3.62750553,5.37173878 3.92083887,5.22495386 C4.2141722,5.07816894 4.50512981,5 5.30749668,5 L18.6925033,5 C19.4948702,5 19.7858278,5.07816894 20.0791611,5.22495386 C20.3724945,5.37173878 20.6027042,5.58713968 20.7595806,5.86160362 C20.9164569,6.13606756 21,7.24671889 21,7.99747152"></path>
                    </g>
                </g>
            </g>
        </svg>
    </a>
    <?php if (!empty($awaiting_jobs)): ?>
        <span class="wcbe-jobs-list-button-number"><?php echo intval($awaiting_jobs); ?></span>
    <?php endif; ?>
</li>