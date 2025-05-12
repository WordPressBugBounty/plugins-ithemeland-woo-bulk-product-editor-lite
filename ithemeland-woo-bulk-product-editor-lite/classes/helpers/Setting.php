<?php

namespace wcbel\classes\helpers;

defined('ABSPATH') || exit(); // Exit if accessed directly

class Setting
{
    public static function get_arg_order_by($default_sort, $args)
    {
        switch ($default_sort) {
            case 'id':
                $args['orderby'] = 'ID';
                break;
            case 'title':
                $args['orderby'] = 'post_title';
                break;
            case 'post_date':
                $args['orderby'] = 'post_date';
                break;
            case 'regular_price':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_price'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'sale_price':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_price'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'sku':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_sku'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'manage_stock':
                $args['orderby'] = 'meta_value';
                $args['meta_key'] = '_manage_stock'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'stock_quantity':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_stock'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'stock_status':
                $args['orderby'] = 'meta_value';
                $args['meta_key'] = '_stock_status'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'width':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_width'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'height':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_height'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'length':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_length'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'weight':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_weight'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'review_count':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_wc_review_count'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'average_rating':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_wc_average_rating'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'date_on_sale_from':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_sale_price_dates_from'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'date_on_sale_to':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_sale_price_dates_to'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
        }

        return $args;
    }
}
