<?php

namespace Sunhill\Properties\Tests\TestSupport\Markets;

use Sunhill\Properties\InfoMarket\Market;
use Sunhill\Properties\Tests\Unit\InfoMarket\TestMarketeer1;
use Sunhill\Properties\Tests\Unit\InfoMarket\TestMarketeer2;
use Sunhill\Properties\Tests\Unit\InfoMarket\TestMarketeer3;

trait GetMarket
{
    
    protected function getMarket()
    {
        $market = new Market();
        $market->registerMarketeer(new \Sunhill\Properties\Tests\TestSupport\Marketeers\TestMarketeer1(), 'marketeer1');
        $market->registerMarketeer(new \Sunhill\Properties\Tests\TestSupport\Marketeers\TestMarketeer2(), 'marketeer2');
        $market->registerMarketeer(new \Sunhill\Properties\Tests\TestSupport\Marketeers\TestMarketeer3(), 'marketeer3');
        
        return $market;
    }
}