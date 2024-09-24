<?php

namespace Sunhill\InfoMarket\Exceptions;

use Sunhill\Exceptions\PropertiesException;

/**
 * This exception is raised whenever registerMarketeer is called with a parameter $marketeer that could not
 * be solved to a marketeer object
 * @author klaus
 *
 */
class CantProcessMarketeerException extends InfoMarketException
{
    
}