<?php

/**
 * @file InfoMarket.php
 * A facade to the InfoMarket
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2024-03-23
 * Localization: none
 * Documentation: complete
 * Tests: none
 * Coverage: unknown
 */

namespace Sunhill\Properties\Facades;

use Illuminate\Support\Facades\Facade;

class InfoMarket extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'infomarket';
    }
}
