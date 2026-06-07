<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

use wcbel\classes\helpers\Sanitizer;

$wcbel_item = (!empty($wcbel_item)) ? $wcbel_item : $parent;
echo (!empty($column_provider) && is_object($column_provider)) ? wp_kses($column_provider->get_item_columns($wcbel_item, $columns), Sanitizer::allowed_html()) : '';
