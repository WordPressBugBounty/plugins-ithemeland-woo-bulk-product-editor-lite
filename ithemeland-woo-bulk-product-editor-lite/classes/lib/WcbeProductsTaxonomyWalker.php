<?php

namespace wcbel\classes\lib;

defined('ABSPATH') || exit(); // Exit if accessed directly

class WcbeProductsTaxonomyWalker extends \Walker_Category
{
    var $lev = -1;
    var $skip = 0;
    private $checked;

    public function __construct(array $checked = [])
    {
        $this->checked = $checked;
    }

    function start_lvl(&$output, $depth = 0, $args = array())
    {
        $this->lev = 0;
        $output .= "<ul>" . PHP_EOL;
    }

    function end_lvl(&$output, $depth = 0, $args = array())
    {
        $output .= "</ul>" . PHP_EOL;
        $this->lev = -1;
    }

    function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0)
    {
        $category_name = sanitize_text_field($category->name);
        $category_id = ($category->taxonomy != 'product_tag') ? sanitize_text_field(intval($category->term_id)) : sanitize_text_field($category->slug);
        $checked = (is_array($this->checked) && in_array($category_id, $this->checked)) ? 'checked="checked"' : '';
        $output .= "<li><label><input type='checkbox' data-category-name='" . sanitize_text_field($category_name) . "' value='" . sanitize_text_field($category_id) . "' " . sanitize_text_field($checked) . ">" . sanitize_text_field($category_name) . "</label>";
    }

    function end_el(&$output, $page, $depth = 0, $args = array())
    {
        $this->lev++;
        if ($this->skip == 1) {
            $this->skip = 0;
            return;
        }
        $output .= "</li>" . PHP_EOL;
    }
}
