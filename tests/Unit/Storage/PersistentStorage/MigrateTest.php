<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Types\TypeInteger;
use Sunhill\Tests\TestSupport\Storages\DummyPersistentStorage;

uses(SimpleTestCase::class);

test('Nothing to do', function()
{
   $structure = [
       'str_field'=>makeStdclass(['name'=>'str_field','type'=>'string','max_length'=>100]),
       'int_field'=>makeStdClass(['name'=>'int_field','type'=>'integer']),
       'float_field'=>makeStdClass(['name'=>'float_field','type'=>'float']),
       'array_field'=>makeStdClass(['name'=>'array_field','type'=>'array','element_type'=>TypeInteger::class])];
   $test = new DummyPersistentStorage();
   $test->setStructure($structure);
   $test->migrate();
   
   expect($test::$persistent_data[1]['str_field'])->toBe('DEF');   
});

test('Unmigrated', function()
{
    $structure = [
        'str_field'=>makeStdclass(['name'=>'str_field','type'=>'string','max_length'=>100]),
        'int_field'=>makeStdClass(['name'=>'int_field','type'=>'integer']),
        'float_field'=>makeStdClass(['name'=>'float_field','type'=>'float']),
        'array_field'=>makeStdClass(['name'=>'array_field','type'=>'array','element_type'=>TypeInteger::class])];
    $test = new DummyPersistentStorage();
    $test::$persistent_data = null;
    $test->setStructure($structure);
    $test->migrate();
    
    expect($test::$persistent_data)->toBe('migrated new');
});

test('Structure chan', function()
{
    $structure = [
        'str_field'=>makeStdclass(['name'=>'str_field','type'=>'string','max_length'=>100]),
        'int_field'=>makeStdClass(['name'=>'int_field','type'=>'integer']),
        'float_field'=>makeStdClass(['name'=>'float_field','type'=>'float']),
        'array_field'=>makeStdClass(['name'=>'array_field','type'=>'array','element_type'=>TypeInteger::class])];
    $test = new DummyPersistentStorage();
    unset($test::$persistent_data[1]['str_field']);
    $test->setStructure($structure);
    $test->migrate();
    
    expect($test::$persistent_data)->toBe('migration changed');
});