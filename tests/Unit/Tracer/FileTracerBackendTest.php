<?php

uses(\Sunhill\Properties\Tests\Unit\Tracer\AbstractTestTracerBackend::class);
use Sunhill\Properties\Tests\TestCase;
use Sunhill\Properties\Tests\TestSupport\Markets\GetMarket;
use Sunhill\Properties\Tracer\Tracer;
use Sunhill\Properties\Tracer\Exceptions\PathNotTraceableException;
use Sunhill\Properties\Tracer\Backends\FileTracerBackend;
use Sunhill\Properties\InfoMarket\Market;


function getBackend()
{
    array_map('unlink', glob(dirname(__FILE__).'/../../temp/*'));

    $test = new FileTracerBackend();
    $test->setTracerDir(dirname(__FILE__).'/../../temp');

    return $test;
}
