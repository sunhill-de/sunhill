<?php

/**
 * @file Plugins.php
 * A facade to the PluginManager
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2024-03-04
 * Localization: none
 * Documentation: complete
 * Tests: none
 * Coverage: unknown
 */

namespace Sunhill\Facades;

use Illuminate\Support\Facades\Facade;

class Plugins extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'plugins';
    }
}
