<?php

namespace Sunhill\InfoMarket\Exceptions;

use Sunhill\Exceptions\PropertiesException;

/**
 * This exception is raised whenever registerMarketeer is called with a marketeer that has no default name
 * and no name is given
 * @author klaus
 *
 */
class MarketeerHasNoNameException extends PropertiesException
{
    
}