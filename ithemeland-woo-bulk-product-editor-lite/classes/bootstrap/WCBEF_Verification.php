<?php

namespace wcbef\classes\bootstrap;

use wcbef\classes\helpers\Others;

class WCBEF_Verification
{
    public static function is_active()
    {
        if (Others::isAllowedDomain()) {
            return 'yes';
        }

        $is_active = get_option('wcbef_is_active', 'no');
        return ($is_active == 'yes' || $is_active == 'skipped');
    }

    public static function skipped()
    {
        $skipped = get_option('wcbef_is_active', 'no');
        return $skipped == 'skipped';
    }
}
