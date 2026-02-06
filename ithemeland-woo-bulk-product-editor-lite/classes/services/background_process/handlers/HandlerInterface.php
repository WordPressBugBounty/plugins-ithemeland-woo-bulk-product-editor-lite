<?php

namespace wcbel\classes\services\background_process\handlers;

defined('ABSPATH') || exit(); // Exit if accessed directly

interface HandlerInterface
{
    public function handle($item);
}
