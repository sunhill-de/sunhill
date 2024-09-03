<?php

uses(\Sunhill\Tests\Unit\Tracer\AbstractTestTracerBackend::class);
use Sunhill\Tests\TestCase;
use Sunhill\Tests\TestSupport\Markets\GetMarket;
use Sunhill\Tracer\Tracer;
use Sunhill\Tracer\Exceptions\PathNotTraceableException;
use Sunhill\Tracer\Backends\FileTracerBackend;
use Sunhill\InfoMarket\Market;


function getBackend()
{
    array_map('unlink', glob(dirname(__FILE__).'/../../temp/*'));

    $test = new FileTracerBackend();
    $test->setTracerDir(dirname(__FILE__).'/../../temp');

    return $test;
}
