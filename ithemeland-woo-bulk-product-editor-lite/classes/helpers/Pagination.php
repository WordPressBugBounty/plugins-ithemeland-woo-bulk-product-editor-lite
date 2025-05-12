<?php

namespace wcbel\classes\helpers;

class Pagination
{
    public static function init($current_page, $max_num_pages)
    {
        $current_page = intval($current_page);
        $max_num_pages = intval($max_num_pages);
        $prev = max(1, $current_page - 1);
        $next = min($max_num_pages, $current_page + 1);
        $max_display = 3;
        $output = "<div class='wcbe-top-nav-filters-paginate' data-last-page='" . esc_attr($max_num_pages) . "'>";
        if (isset($max_num_pages)) {
            $output .= "<a href='#' data-index='" . esc_attr($prev) . "' class='wcbe-filter-form-action' data-search-action='pagination'><</a>";
            if ($current_page < $max_display) {
                for ($i = 1; $i <= min($max_display, $max_num_pages); $i++) {
                    $current = ($i == $current_page) ? 'current' : '';
                    $output .= "<a href='#' data-index='" . esc_attr($i) . "' class='wcbe-filter-form-action " . esc_attr($current) . "' data-search-action='pagination'>" . esc_html($i) . "</a>";
                }
                if ($max_num_pages > ($max_display + 1)) {
                    $output .= "<span>...</span>";
                }
                if ($max_num_pages > $max_display) {
                    $output .= "<a href='#' data-index='" . esc_attr($max_num_pages) . "' class='wcbe-filter-form-action' data-search-action='pagination'>" . esc_html($max_num_pages) . "</a>";
                }
            } elseif ($current_page == $max_display) {
                $max_num = ($max_display < $max_num_pages) ? $max_display + 1 : $max_display;
                for ($i = 1; $i <= $max_num; $i++) {
                    $current = ($i == $current_page) ? 'current' : '';
                    $output .= "<a href='#' data-index='" . esc_attr($i) . "' class='wcbe-filter-form-action " . esc_attr($current) . "' data-search-action='pagination'>" . esc_html($i) . "</a>";
                }
                if ($max_num_pages > ($current_page + 1)) {
                    $output .= "<span>...</span>";
                    $output .= "<a href='#' data-index='" . esc_attr($max_num_pages) . "' class='wcbe-filter-form-action' data-search-action='pagination'>" . esc_html($max_num_pages) . "</a>";
                }
            } else {
                $output .= "<a href='#' data-index='1' class='wcbe-filter-form-action' data-search-action='pagination'>1</a>";
                if ($max_num_pages > $max_display) {
                    $output .= "<span>...</span>";
                }
                for ($i = $current_page - 1; $i <= min($current_page + 1, $max_num_pages); $i++) {
                    $current = ($i == $current_page) ? 'current' : '';
                    $output .= "<a href='#' data-index='" . esc_attr($i) . "' class='wcbe-filter-form-action " . esc_attr($current) . "' data-search-action='pagination'>" . esc_html($i) . "</a>";
                }
                if ($current_page + 1 < $max_num_pages) {
                    $output .= "<span>...</span>";
                    $output .= "<a href='#' data-index='" . esc_attr($max_num_pages) . "' class='wcbe-filter-form-action' data-search-action='pagination'>" . esc_html($max_num_pages) . "</a>";
                }
            }
            $output .= "<a href='#' data-index='" . esc_attr($next) . "' class='wcbe-filter-form-action' data-search-action='pagination'>></a>";
        }
        $output .= "</div>";
        $output .= '<div class="wcbe-pagination-loading"><img src="' . WCBEL_IMAGES_URL . 'loading.gif" alt="Loading" width="20" height="20"></div>';
        return $output;
    }
}
