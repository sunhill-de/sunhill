<?php

namespace Sunhill\Tests\TestSupport\Markets;

use Sunhill\InfoMarket\Market;
use Sunhill\Tests\Unit\InfoMarket\TestMarketeer1;
use Sunhill\Tests\Unit\InfoMarket\TestMarketeer2;
use Sunhill\Tests\Unit\InfoMarket\TestMarketeer3;

trait GetMarket
{
    
    protected function getMarket()
    {
        $market = new Market();
        $market->registerMarketeer(new \Sunhill\Tests\TestSupport\Marketeers\TestMarketeer1(), 'marketeer1');
        $market->registerMarketeer(new \Sunhill\Tests\TestSupport\Marketeers\TestMarketeer2(), 'marketeer2');
        $market->registerMarketeer(new \Sunhill\Tests\TestSupport\Marketeers\TestMarketeer3(), 'marketeer3');
        
        return $market;
    }
}