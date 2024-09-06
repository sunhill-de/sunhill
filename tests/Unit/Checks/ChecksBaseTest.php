<?php
/**
 * @file tests/Unit/Checks/ChecksBaseTest.php
 * Tests the routine in ChecksBase
 */
namespace Sunhill\ORM\Tests\Unit\Checks;

use Illuminate\Support\Facades\DB;

use Sunhill\Checks\ChecksBase;
use Sunhill\Facades\Checks;
use Sunhill\Tests\SunhillTestCase;

uses(SunhillTestCase::class);

test('check for danging pointers', function()
{
    $test = new ChecksBase();
    expect(empty(callProtectedMethod($test,'checkForDanglingPointers',
        ['tags','parent_id','tags','id']
        )))->toBe(true);
    DB::table('tags')->where('id',2)->delete();
    expect(empty(callProtectedMethod($test,'checkForDanglingPointers',
        ['tags','parent_id','tags','id']
        )))->toBe(false);    
});

test('repait dangling pointers', function()
{
    $test = new ChecksBase();
    DB::table('tags')->where('id',2)->delete();
    callProtectedMethod($test,'repairDanglingPointers',
        ['tags','parent_id','tags','id']
        );
    expect(empty(callProtectedMethod($test,'checkForDanglingPointers',
        ['tags','parent_id','tags','id']
        )))->toBe(true);    
});

test('run all tests', function()
{
    Checks::check(false);
    expect(Checks::getTestsFailed())->toBe(0);    
});

