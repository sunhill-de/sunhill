<?php
/**
 * @file tests/Unit/Checks/ObjectCheckTest.php
 * Tests the routine in ObjectChecks
 */
namespace Sunhill\ORM\Tests\Unit\Checks;

use Illuminate\Support\Facades\DB;
use Sunhill\Checks\ObjectChecks;

require_once('CheckUtils.php');

test('Process table', function() 
{
    $checker = new ObjectChecks();
    $missing = [];
    $dummy = new \StdClass();
    $dummy->table = 'dummies';
    $dummy->parent = 'object';
    $object = new \StdClass();
    $object->table = 'objects';
    $object->parent = '';
    $matrix = [
        'dummy'=>$dummy,
        'object'=>$object
    ];
    callProtectedMethod($checker, 'processTable', [$matrix, 'dummy', &$missing]);
    expect(empty($missing))->toBe(true);
    DB::table('objects')->where('id',1)->delete();
    callProtectedMethod($checker, 'processTable', [$matrix, 'dummy', &$missing]);
    expect(empty($missing))->toBe(false);    
});

test('', function($check, $destroy_callback)
{
    $checker = new ObjectChecks();
    expect(runCheck($checker, $check, false),'Initial sanity check failed.')->toBe('passed');
    $destroy_callback();
    expect(runCheck($checker, $check, false))->toBe('failed');
    expect(runCheck($checker, $check, true))->toBe('repaired');
    expect(runCheck($checker, $check, false),'Repaired sanity check failed.')->toBe('passed');
})->with([
    ['check_EveryObjectHasAParentEntry', function() { DB::table('dummies')->where('id',5)->delete(); }],
    ['check_ObjectExistance', function() { DB::table('objects')->insert(['id'=>1000,'_uuid'=>'','classname'=>'badclass']); }],
    ]);

