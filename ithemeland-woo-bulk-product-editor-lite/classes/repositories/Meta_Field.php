<?php

namespace wcbel\classes\repositories;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\repositories\meta_field\Meta_Field_Main;

class Meta_Field extends Meta_Field_Main
{
    private static $instance;

    protected $meta_fields_option_name = "wcbe_meta_fields";

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get_reserved_field_names()
    {
        $column_repository = Column::get_instance();
        return $column_repository->get_main_column_keys();
    }
}
