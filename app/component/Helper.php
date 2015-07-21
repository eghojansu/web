<?php

namespace app\component;

class Helper
{
    static $months = [1=>'January','February','March','April','May','June','July',
        'August', 'September', 'October', 'November', 'December'];

    public static function monthNumber($month)
    {
        return array_search($month, self::$months);
    }
}
