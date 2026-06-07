<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div>
    <div>
        <span style="display: inline-block; width: auto;"><?php esc_html_e('Processing', 'ithemeland-woo-bulk-product-editor-lite') ?></span>
        <span data-type="loading" style="display: inline-block; width: auto;">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="40px" height="40px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                <rect x="17.5" y="30" width="15" height="40" fill="#ffffff">
                    <animate attributeName="y" repeatCount="indefinite" dur="0.8s" calcMode="spline" keyTimes="0;0.5;1" values="18;30;30" keySplines="0 0.5 0.5 1;0 0.5 0.5 1" begin="-0.16s"></animate>
                    <animate attributeName="height" repeatCount="indefinite" dur="0.8s" calcMode="spline" keyTimes="0;0.5;1" values="64;40;40" keySplines="0 0.5 0.5 1;0 0.5 0.5 1" begin="-0.16s"></animate>
                </rect>
                <rect x="42.5" y="30" width="15" height="40" fill="#ffffff">
                    <animate attributeName="y" repeatCount="indefinite" dur="0.8s" calcMode="spline" keyTimes="0;0.5;1" values="20.999999999999996;30;30" keySplines="0 0.5 0.5 1;0 0.5 0.5 1" begin="-0.08s"></animate>
                    <animate attributeName="height" repeatCount="indefinite" dur="0.8s" calcMode="spline" keyTimes="0;0.5;1" values="58.00000000000001;40;40" keySplines="0 0.5 0.5 1;0 0.5 0.5 1" begin="-0.08s"></animate>
                </rect>
                <rect x="67.5" y="30" width="15" height="40" fill="#ffffff">
                    <animate attributeName="y" repeatCount="indefinite" dur="0.8s" calcMode="spline" keyTimes="0;0.5;1" values="20.999999999999996;30;30" keySplines="0 0.5 0.5 1;0 0.5 0.5 1"></animate>
                    <animate attributeName="height" repeatCount="indefinite" dur="0.8s" calcMode="spline" keyTimes="0;0.5;1" values="58.00000000000001;40;40" keySplines="0 0.5 0.5 1;0 0.5 0.5 1"></animate>
                </rect>
            </svg>
        </span>
    </div>
    <div>
        <span data-type="tasks" style="display: none;">
            <strong data-type="completed"></strong> <?php esc_html_e('rows from', 'ithemeland-woo-bulk-product-editor-lite') ?> <strong data-type="total"></strong> <?php esc_html_e('rows', 'ithemeland-woo-bulk-product-editor-lite') ?>
        </span>
    </div>
    <span data-type="time_remaining" style="display: none;">
        <?php esc_html_e('Time remaining', 'ithemeland-woo-bulk-product-editor-lite') ?>: <?php esc_html_e('About', 'ithemeland-woo-bulk-product-editor-lite') ?> <strong data-type="time"></strong>
    </span>
    <span class="small"><?php esc_html_e('You can leave the page. Have a cup of coffee, please.', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
</div>