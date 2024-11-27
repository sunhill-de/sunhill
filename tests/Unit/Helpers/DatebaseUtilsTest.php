<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Illuminate\Support\Facades\DB;

uses(SunhillDatabaseTestCase::class);

test('DBTableExists() passes', function()
{
    expect(DBTableExists('objects'))->toBe(true);
});

test('DBTableExists() fails', function()
{
    expect(DBTableExists('notexisting'))->toBe(false);
});

test('DBTableHasColumn() passws', function()
{
    expect(DBTableHasColumn('objects', '_uuid'))->toBe(true); 
});

test('DBTableHasColumn() fails', function()
{
    expect(DBTableHasColumn('objects', 'notexisting'))->toBe(false);
});

test('DBTableColumnType', function()
{
    expect(DBTableColumnType('objects','_uuid'))->toBe('string'); 
});

test('DBTableColumnAdditional()', function()
{    
    $help = DBTableColumnAdditional('objects','_read_cap');
    expect($help)->toBe('string');
});