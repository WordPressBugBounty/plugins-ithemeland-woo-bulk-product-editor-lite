<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

use wcbel\classes\helpers\Sanitizer;

if (!empty($history_count)) {
    $wcbel_per_page = (!empty($per_page)) ? $per_page : 10;
    $wcbel_current_page = (!empty($current_page)) ? $current_page : 1;
    $wcbel_max_num_pages = ($history_count > $wcbel_per_page) ? ceil($history_count / $wcbel_per_page) : 1;
    $wcbel_prev = max(1, $wcbel_current_page - 1);
    $wcbel_next = min($wcbel_max_num_pages, $wcbel_current_page + 1);
    $wcbel_max_display = 3;

    $wcbel_pagination = '<div style="float: right;">';
    if (isset($wcbel_max_num_pages)) {
        $wcbel_pagination .= "<a href='#' data-index='" . esc_attr($wcbel_prev) . "' class='wcbe-history-pagination-item'><</a>";
        if ($wcbel_current_page < $wcbel_max_display) {
            for ($wcbel_i = 1; $wcbel_i <= min($wcbel_max_display, $wcbel_max_num_pages); $wcbel_i++) {
                $wcbel_current = ($wcbel_i == $wcbel_current_page) ? 'current' : '';
                $wcbel_pagination .= "<a href='#' data-index='" . esc_attr($wcbel_i) . "' class='wcbe-history-pagination-item " . esc_attr($wcbel_current) . "'>" . esc_html($wcbel_i) . "</a>";
            }
            if ($wcbel_max_num_pages > $wcbel_max_display) {
                $wcbel_pagination .= "<span>...</span>";
                $wcbel_pagination .= "<a href='#' data-index='" . esc_attr($wcbel_max_num_pages) . "' class='wcbe-history-pagination-item'>" . esc_html($wcbel_max_num_pages) . "</a>";
            }
        } elseif ($wcbel_current_page == $wcbel_max_display) {
            $wcbel_max_num = ($wcbel_max_display < $wcbel_max_num_pages) ? $wcbel_max_display + 1 : $wcbel_max_display;
            for ($wcbel_i = 1; $wcbel_i <= $wcbel_max_num; $wcbel_i++) {
                $wcbel_current = ($wcbel_i) ? 'current' : '';
                $wcbel_pagination .= "<a href='#' data-index='" . esc_attr($wcbel_i) . "' class='wcbe-history-pagination-item " . esc_attr($wcbel_current) . "'>" . esc_html($wcbel_i) . "</a>";
            }
            if ($wcbel_max_num_pages > $wcbel_current_page) {
                $wcbel_pagination .= "<span>...</span>";
                $wcbel_pagination .= "<a href='#' data-index='" . esc_attr($wcbel_max_num_pages) . "' class='wcbe-history-pagination-item'>" . esc_html($wcbel_max_num_pages) . "</a>";
            }
        } else {
            $wcbel_pagination .= "<a href='#' data-index='1' class='wcbe-history-pagination-item'>1</a>";
            $wcbel_pagination .= "<span>...</span>";
            for ($wcbel_i = $wcbel_current_page - 2; $wcbel_i <= min($wcbel_current_page + 2, $wcbel_max_num_pages); $wcbel_i++) {
                $wcbel_current = ($wcbel_i == $wcbel_current_page) ? 'current' : '';
                $wcbel_pagination .= "<a href='#' data-index='" . esc_attr($wcbel_i) . "' class='wcbe-history-pagination-item " . esc_attr($wcbel_current) . "'>" . esc_html($wcbel_i) . "</a>";
            }
            if ($wcbel_current_page + 2 < $wcbel_max_num_pages) {
                $wcbel_pagination .= "<span>...</span>";
                $wcbel_pagination .= "<a href='#' data-index='" . esc_attr($wcbel_max_num_pages) . "' class='wcbe-history-pagination-item'>" . esc_html($wcbel_max_num_pages) . "</a>";
            }
        }
        $wcbel_pagination .= "<a href='#' data-index='" . esc_attr($wcbel_next) . "' class='wcbe-history-pagination-item'>></a>";
    }
    $wcbel_pagination .= "</div>";
    $wcbel_pagination .= "<div class='wcbe-history-pagination-loading'><img src=" . esc_url(WCBEL_IMAGES_URL . 'loading-2.gif') . " width='20' height='20'></div>";

    if (!empty($wcbel_pagination)) {
        echo wp_kses($wcbel_pagination, Sanitizer::allowed_html());
    }
}
