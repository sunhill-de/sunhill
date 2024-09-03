<?php

namespace Sunhill\Properties\Tests\Unit\Tracer;

use Sunhill\Properties\Tests\TestCase;
use Sunhill\Properties\Tests\TestSupport\Markets\GetMarket;
use Sunhill\Properties\Tracer\Tracer;
use Sunhill\Properties\Tracer\Exceptions\PathNotTraceableException;
use Sunhill\Properties\Tracer\Backends\FileTracerBackend;
use Sunhill\Properties\Facades\InfoMarket;

abstract class AbstractTestTracerBackend extends TestCase
{
    
    use GetMarket;
    
    abstract protected function getBackend();
    
    public function testTraceUntraceIsTraced()
    {
        $test = $this->getBackend();
        $data = new \StdClass();
        $data->value = 'ABC';
        
        InfoMarket::shouldReceive('pathExists')->with('marketeer1.element1')->andReturn(true);
        InfoMarket::shouldReceive('requestData')->with('marketeer1.element1')->andReturn($data);
        
        $this->assertFalse($test->isTraced('marketeer1.element1'));
        $test->trace('marketeer1.element1');
        $this->assertTrue($test->isTraced('marketeer1.element1'));
        $test->untrace('marketeer1.element1');
        $this->assertFalse($test->isTraced('marketeer1.element1'));
    }
    
    public function testGetTracedElements()
    {
        $test = $this->getBackend();
        $data1 = new \StdClass();
        $data1->value = 'ABC';
        $data2 = new \StdClass();
        $data2->value = 'DEF';
        
        InfoMarket::shouldReceive('pathExists')->with('marketeer1.element1')->andReturn(true);
        InfoMarket::shouldReceive('pathExists')->with('marketeer2.element2')->andReturn(true);
        InfoMarket::shouldReceive('requestData')->with('marketeer1.element1')->andReturn($data1);
        InfoMarket::shouldReceive('requestData')->with('marketeer2.element2')->andReturn($data2);
        
        $test->trace('marketeer1.element1');
        $test->trace('marketeer2.element2');
        $this->assertEquals(['marketeer1.element1','marketeer2.element2'], $test->getTracedElements());
    }
    
    public function testGetLastPair()
    {
        $test = $this->getBackend();
        $data = new \StdClass();
        $data->value = 'ABC';
        
        InfoMarket::shouldReceive('pathExists')->with('marketeer1.element1')->andReturn(true);
        InfoMarket::shouldReceive('requestData')->with('marketeer1.element1')->andReturn($data);
        
        $test->trace('marketeer1.element1', 1000);
        
        $this->assertEquals(1000, $test->getLastChange('marketeer1.element1'));
        $this->assertEquals('ABC', $test->getLastValue('marketeer1.element1'));
    }
    
    protected function getUpdateBackend()
    {
        $test = $this->getBackend();
        $data = new \StdClass();
        $data->value = 'ABC';
        
        InfoMarket::shouldReceive('pathExists')->with('marketeer1.element1')->andReturn(true);
        InfoMarket::shouldReceive('requestData')->once()->with('marketeer1.element1')->andReturn($data);
        
        $test->trace('marketeer1.element1', 1000);
        
        $data->value = 'DEF';
        InfoMarket::shouldReceive('requestData')->once()->with('marketeer1.element1')->andReturn($data);
        
        $test->updateTraces(2000);
        
        return $test;
    }
    
    public function testUpdateFirst()
    {
        $test = $this->getUpdateBackend();
        $this->assertEquals(1000, $test->getFirstChange('marketeer1.element1'));
        $this->assertEquals('ABC', $test->getFirstValue('marketeer1.element1'));
    }

    public function testUpdateLast()
    {
        $test = $this->getUpdateBackend();
        $this->assertEquals(2000, $test->getLastChange('marketeer1.element1'));
        $this->assertEquals('DEF', $test->getLastValue('marketeer1.element1'));
    }
    
    protected function getRangeBackend()
    {
        $values = [1000=>10,2000=>20,4000=>30,5000=>40,6000=>50];        
        
        $test = $this->getBackend();
        $data = new \StdClass();
        
        InfoMarket::shouldReceive('pathExists')->with('marketeer1.element1')->andReturn(true);
        
        $first = true;
        
        foreach ($values as $stamp => $value) {
            $data->value = $value;
            InfoMarket::shouldReceive('requestData')->once()->with('marketeer1.element1')->andReturn($data);
            if ($first) {
                $test->trace('marketeer1.element1', $stamp);
                $first = false;
            } else {
                $test->updateTraces($stamp);                
            }
        }
                
        return $test;
    }
    
    /**
     * @dataProvider GetValueAtProvider
     * @param unknown $stamp
     * @param unknown $expect
     */
    public function testGetValueAt($stamp, $expect)
    {
        $test = $this->getRangeBackend();
        $this->assertEquals($expect, $test->getValueAt('marketeer1.element1', $stamp));
    }
    
    public static function GetValueAtProvider()
    {
        return [
            [1000,10],
            [1500,10],
            [1999,10],
            [2000,20],
            [3000,20],
            [4000,30],
            [4500,30],
            [9000,50]
        ];    
    }
    
    public function testRangeStatisticsOverall()
    {
        $test = $this->getRangeBackend();
        $statistics = $test->getRangeStatistics('marketeer1.element1',0,6000);
        
        $this->assertEquals(5000, $statistics->range);
        $this->assertEquals(10, $statistics->min);
        $this->assertEquals(50, $statistics->max);
        $this->assertEquals(24, $statistics->avg);
    }
    
    public function testRangeStatistics()
    {
        $test = $this->getRangeBackend();
        $statistics = $test->getRangeStatistics('marketeer1.element1',1500,5500);
        
        $this->assertEquals(4000, $statistics->range);
        $this->assertEquals(10, $statistics->min);
        $this->assertEquals(40, $statistics->max);
        $this->assertEquals(23.75, $statistics->avg);
    }
    
    public function testRangeValues()
    {
        $test = $this->getRangeBackend();
        $values = $test->getRangeValues('marketeer1.element1',1500,5500,500);
        $this->assertEquals([
            1500=>10,
            2000=>20,
            2500=>20,
            3000=>20,
            3500=>20,
            4000=>30,
            4500=>30,
            5000=>40,
            5500=>40
        ],$values);
    }
}
