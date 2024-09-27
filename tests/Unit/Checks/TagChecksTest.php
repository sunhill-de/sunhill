<?php
/**
 * @file tests/Unit/Checks/TagCheckTest.php
 * Tests the routine in TagChecks
 */
namespace Sunhill\ORM\Tests\Unit\Checks;

use Illuminate\Support\Facades\DB;
use Sunhill\Tests\SunhillTestCase;
use Sunhill\Checks\TagChecks;
use Sunhill\Tests\SunhillDatabaseTestCase;

require_once('CheckUtils.php');

function getMatrix()
{
    $tag1 = new \StdClass();
    $tag1->id = 1;
    $tag1->name ='TagA';
    $tag1->parent_id = 0;
    $tag2 = new \StdClass();
    $tag2->id = 2;
    $tag2->name ='TagB';
    $tag2->parent_id = 1;
    $tag3 = new \StdClass();
    $tag3->id = 3;
    $tag3->name ='TagC';
    $tag3->parent_id = 2;
    $tag4 = new \StdClass();
    $tag4->id = 4;
    $tag4->name ='TagD';
    $tag4->parent_id = 0;
    return [1=>$tag1,2=>$tag2,3=>$tag3,4=>$tag4];
}

uses(SunhillDatabaseTestCase::class);

    
test('Build Tag Matrix', function()
{
    $test = new TagChecks();
    $result = callProtectedMethod($test, 'buildTagMatrix');
    
    expect($result[8]->name)->toBe('TagE');
});

    
test('GetFullpath',function() 
{
        $test = new TagChecks();
        $matrix = getMatrix();
        expect(callProtectedMethod($test, 'getFullpath', [1, $matrix]))->toBe('TagA');
        expect(callProtectedMethod($test, 'getFullpath', [3, $matrix]))->toBe('TagA.TagB.TagC');
});
    
test('Add Permutations', function()
{
    $test = new TagChecks();
    $matrix = getMatrix();
    $result = [];
    lProtectedMethod($test, 'addPermutations', [&$result, $matrix, 3, 3]);
    usort($result,function($a,$b) { return ($a->path_name < $b->path_name)?-1:1;});
    expect(count($result))->toBe(3);
    expect($result[0]->path_name)->toBe('TagA.TagB.TagC');
    expect($result[0]->id)->toBe(3);
});

test('BuildExpectedTagCache', function()
    {
        $test = new TagChecks();
        $matrix = $this->getMatrix();
        $result = $this->callProtectedMethod($test, 'buildExpectedTagCache',[&$matrix]);
        usort($result,function($a,$b) { return ($a->path_name < $b->path_name)?-1:1;});
        
        $this->assertEquals(7,count($result));
        $this->assertEquals('TagD',$result[6]->path_name);
        $this->assertEquals(4,$result[6]->id);
    });
    
    /**
     * @dataProvider repairableProblemProvider
     * @param unknown $check
     * @param unknown $destroy_callback
     */
test('RepairableProblems', function($check, $destroy_callback)
    {
        $checker = new TagChecks();
        expect(runCheck($checker, $check, false))->toBe('passed');
        $destroy_callback();
        expect(runCheck($checker, $check, false))->toBe('failed');
        expect(runCheck($checker, $check, true))->toBe('repaired');
        exeect(runCheck($checker, $check, false))->toBe('passed');
    })->with([
            ['check_TagsWithNotExistingParents',function() {DB::table('tags')->where('id',7)->delete();} ],            
            ['check_TagCacheWithNotExistingTags',function() {DB::table('tags')->where('id',7)->delete();} ],            
            ['check_TagObjectAssignsTagsExist',function() {DB::table('tags')->where('id',4)->delete();} ],
            ['check_TagObjectAssignsObjectsExist',function() {DB::table('objects')->where('id',10)->delete();} ],
            ['check_ExpectedTagcacheEntries',function() {DB::table('tagcache')->where('path_name','TagD')->delete();} ],
            ['check_UnexpectedTagcacheEntries',function() {DB::table('tagcache')->insert(['path_name'=>'wrongtag','tag_id'=>4]);} ]
            ]);
    
    
