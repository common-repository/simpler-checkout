<?php

namespace Simpler\Services;

class BladeFactory
{
    /**
     * @var BladeService
     */
    private static $bladeAdminService;

    public static function forAdmin()
    {
        if (is_null(self::$bladeAdminService)) {
            self::$bladeAdminService = new BladeService(
                SIMPLERWC_PATH.'includes/Admin/assets/views',
                SIMPLERWC_PATH.'includes/Admin/assets/views/compiles',
                BladeService::MODE_AUTO
            );
        }

        return self::$bladeAdminService;
    }
}