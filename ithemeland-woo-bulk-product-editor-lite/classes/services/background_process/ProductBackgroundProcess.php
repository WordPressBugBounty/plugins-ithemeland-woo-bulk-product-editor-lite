<?php

namespace wcbel\classes\services\background_process;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\services\background_process\BackgroundProcess;
use wcbel\classes\repositories\Setting;
use wcbel\classes\services\background_process\handlers\HistoryRedoHandler;
use wcbel\classes\services\background_process\handlers\HistoryUndoHandler;
use wcbel\classes\services\background_process\handlers\ProductCreateHandler;
use wcbel\classes\services\background_process\handlers\ProductDeleteHandler;
use wcbel\classes\services\background_process\handlers\ProductDuplicateHandler;
use wcbel\classes\services\background_process\handlers\ProductRestoreHandler;
use wcbel\classes\services\background_process\handlers\ProductUpdateHandler;

class ProductBackgroundProcess extends BackgroundProcess
{
    private static $instance;
    private static $is_enable;

    protected $prefix = 'wcbe';

    public static function init()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        $setting_repository = Setting::get_instance();
        $settings = $setting_repository->get_settings();
        self::$is_enable = (!empty($settings['enable_background_processing']) && $settings['enable_background_processing'] == 'no') ? false : true;
    }

    public static function get_instance()
    {
        return self::$instance;
    }

    public static function is_enable()
    {
        return (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE) ? false : self::$is_enable;
    }

    public function __construct()
    {
        parent::__construct();
    }

    protected function get_handlers()
    {
        return [
            'product_update' => ProductUpdateHandler::class,
            'product_create' => ProductCreateHandler::class,
            'product_delete' => ProductDeleteHandler::class,
            'product_restore' => ProductRestoreHandler::class,
            'product_duplicate' => ProductDuplicateHandler::class,
            'history_undo' => HistoryUndoHandler::class,
            'history_redo' => HistoryRedoHandler::class,
        ];
    }
}
