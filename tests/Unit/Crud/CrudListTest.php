<?php

namespace Sunhill\Framework\Tests\Unit\Crud;

use Sunhill\Framework\Tests\TestCase;
use Sunhill\Framework\Crud\AbstractCrudEngine;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Sunhill\Framework\Crud\Exceptions\FieldNotSortableException;
use Sunhill\Framework\Crud\CrudListResponse;

uses(TestCase::class);

function getStdClass($items)
{
    $result = new \StdClass();
    foreach ($items as $key => $value) {
        $result->$key = $value;
    }
    return $result;
}

function getListResponse($entries = 30, $features = null, $offset = 0, $limit = 10, $order = null, $order_dir = 'asc', $filter = null, $data = null, $filters = null, $group_actions = null)
{
    if (is_null($features)) {
        $features = ['show','edit','delete','userfilters','search'];
    }
    if (is_null($data)) {
        $entry = new \StdClass();
        $entry->title = 'C';
        $entry->link = 'http://example.com/C';
        $data = [
            getStdClass(['id'=>1, 'item'=>['A'=>'http://example.com/A'],'payload'=>3]),
            getStdClass(['id'=>2, 'item'=>['title'=>'B','link'=>'http://example.com/B'],'payload'=>5]),
            getStdClass(['id'=>3, 'item'=>$entry,'payload'=>9]),
            getStdClass(['id'=>4, 'item'=>'D','payload'=>3]),
            getStdClass(['id'=>5, 'item'=>'E','payload'=>4]),
            getStdClass(['id'=>6, 'item'=>'F','payload'=>8]),
            getStdClass(['id'=>7, 'item'=>'G','payload'=>1]),
            getStdClass(['id'=>8, 'item'=>'H','payload'=>2]),
            getStdClass(['id'=>9, 'item'=>'I','payload'=>6]),
            getStdClass(['id'=>10, 'item'=>'J','payload'=>7]),
        ];
    }
    if (is_null($group_actions)) {
        $group_actions = [
            'edit'=>'Edit',
            'delete'=>'Delete'
        ];
    }
    App::setLocale('en');
    $engine = \Mockery::mock(AbstractCrudEngine::class);
    $engine->shouldReceive('getElementCount')->atLeast(1)->andReturns($entries);
    $engine->shouldReceive('isSortable')->atLeast(1)->with('id')->andReturn(true);
    $engine->shouldReceive('isSortable')->atLeast(1)->with('item')->andReturns(true);
    $engine->shouldReceive('isSortable')->atLeast(1)->with('payload')->andReturns(false);
    $engine->shouldReceive('getFilters')->once()->andReturns(is_null($filters)?['itemfilter'=>'Item filter','payloadfilter'=>'Payload filter']:$filters);
    $engine->shouldReceive('getListEntries')->once()->with($offset, $limit, (strpos($order,'!')!==false)?substr($order,1):$order, $order_dir, $filter)->andReturns($data);
    $engine->shouldReceive('getColumns')->atLeast(1)->andReturn(['id'=>'id','item'=>'value','payload'=>'value']);
    $engine->shouldReceive('getColumnTitle')->once()->with('id')->andReturn('id');
    $engine->shouldReceive('getColumnTitle')->once()->with('item')->andReturn('item');
    $engine->shouldReceive('getColumnTitle')->once()->with('payload')->andReturn('payload');
    Route::get('/test/list/{offset?}/{limit?}/{order?}/{filter?}', function() { return 'list'; })->name('test.list');
    Route::get('/test/show/{id}', function($id) { return 'show '.$id; })->name('test.show');
    Route::get('/test/edit/{id}', function($id) { return 'edit '.$id; })->name('test.edit');
    Route::get('/test/delete/{id}', function($id) { return 'delete '.$id; })->name('test.delete');
    Route::get('/admin', function() { return 'admin'; })->name('admin.settings');
    Route::get('/test/groupdelete', function() { return 'groupdelete'; })->name('test.groupdelete');
    Route::get('/test/groupedit', function() { return 'groupedit'; })->name('test.groupedit');
    $test = new CrudListResponse($engine);
    $test->setParameters([ // This is required because we call it directly
        'sitename'=>'testsite',
        'hamburger_entries'=>[],
    ]);
    $test->setOffset($offset);
    $test->setLimit($limit);
    if (!is_null($order)) {
        $test->setOrder($order);
    }
    if (!is_null($filter)) {
        $test->setFilter($filter);
    }
    $test->setRouteBase('test');
    foreach ($features as $feature) {
        $method = 'enable'.ucfirst($feature);
        $test->$method();
    }
    if (!is_null($group_actions)) {
        $result = [];
        foreach ($group_actions as $action => $title) {
            $result[$action] = $title;
        }
        $test->setGroupActions($result);
    }
    return $test->getResponse()->render();    
}

// ''''''''''''''''''''''''' Data table '''''''''''''''''''''''''''''''''''''''''''
test('CRUD list unlinked displays entries', function()
{
    expect(getListResponse())->toContain('<td class="id">2</td>');
    expect(getListResponse())->toContain('<td class="value">D</td>');
    expect(getListResponse())->toContain('<td class="value">3</td>');
})->group('crud','data');

test('CRUD list linked displays entries', function()
{
    expect(getListResponse())->toContain('<a href="http://example.com/A">A</a>');
    expect(getListResponse())->toContain('<a href="http://example.com/B">B</a>');
    expect(getListResponse())->toContain('<a href="http://example.com/C">C</a>');
})->group('crud','data');

test('CRUD list displays show link', function()
{
    expect(getListResponse())->toContain('<td class="link show"><a href="'.route('test.show',['id'=>1]).'">show</a>');
})->group('crud','data');

test('CRUD list displays edit link', function()
{
    expect(getListResponse())->toContain('<td class="link edit"><a href="'.route('test.edit',['id'=>1]).'">edit</a>');
})->group('crud','data');

test('CRUD list displays delete link', function()
{
    expect(getListResponse())->toContain('<td class="link delete"><a href="'.route('test.delete',['id'=>1]).'">delete</a>');
})->group('crud','data');

test('CRUD doesnt display links when features disbaled', function()
{
    $response = getListResponse(features: []);
    expect($response)->not->toContain('<a class="show" href="'.route('test.show',['id'=>1]).'">show</a>');
    expect($response)->not->toContain('<a class="edit" href="'.route('test.edit',['id'=>1]).'">edit</a>');
    expect($response)->not->toContain('<a class="delete" href="'.route('test.delete',['id'=>1]).'">delete</a>');
})->group('crud','data');

test('CRUD list handles empty data set', function() {
  $response = getListResponse(data: []);
  expect($response)->toContain('No entries.');
})->group('crud','data');

// ***************************  Paginator *****************************************
test('CRUD list getPageCount', function($entries, $expect)
{
    $engine = \Mockery::mock(AbstractCrudEngine::class);
    $engine->shouldReceive('getElementCount')->andReturn($entries);
    $test = new CrudListResponse($engine);
    expect($test->getPageCount($entries))->toBe($expect);
})->with([
    [0,0],
    [1,1],
    [10,1],
    [11,2],
    [20,2],
    [21,3],
    [125,13]    
]);

test('CRUD list displays right paginator links when first', function()
{
    $response = getListResponse();
    expect($response)->toContain('<a href="'.route('test.list',['offset'=>10,'limit'=>10]).'">2</a>');    
    expect($response)->toContain('<a href="'.route('test.list',['offset'=>10,'limit'=>10]).'">next</a>');
    expect($response)->not->toContain('prev</a>');
})->group('crud','paginator');

test('CRUD list displays right paginator links when order set', function()
{
    $response = getListResponse(order: 'item');
    expect($response)->toContain('<a href="'.route('test.list',['offset'=>10,'limit'=>10,'order'=>'item']).'">2</a>');
    expect($response)->toContain('<a href="'.route('test.list',['offset'=>10,'limit'=>10,'order'=>'item']).'">next</a>');
})->group('crud','paginator');

test('CRUD list displays right paginator links when filter set', function()
{
    $response = getListResponse(filter: 'itemfilter');
    expect($response)->toContain('<a href="'.route('test.list',['offset'=>10,'limit'=>10,'filter'=>'itemfilter']).'">2</a>');
    expect($response)->toContain('<a href="'.route('test.list',['offset'=>10,'limit'=>10,'filter'=>'itemfilter']).'">next</a>');
})->group('crud','paginator');

test('CRUD list displays right paginator links when order and filter set', function()
{
    $response = getListResponse(order: 'item', filter: 'itemfilter');
    expect($response)->toContain('<a href="'.route('test.list',['offset'=>10,'limit'=>10,'order'=>'item','filter'=>'itemfilter']).'">2</a>');
    expect($response)->toContain('<a href="'.route('test.list',['offset'=>10,'limit'=>10,'order'=>'item','filter'=>'itemfilter']).'">next</a>');
})->group('crud','paginator');

test('CRUD list displays right paginator links when last', function()
{
    $response = getListResponse(30,null,20);
    expect($response)->not->toContain('next</a>');
    expect($response)->toContain('<a href="'.route('test.list',['offset'=>10,'limit'=>10]).'">prev</a>');
})->group('crud','paginator');

test('CRUD list displays right paginator links when middle', function()
{
    $response = getListResponse(30,null,10);
    expect($response)->toContain('<a href="'.route('test.list',['offset'=>20,'limit'=>10]).'">next</a>');
    expect($response)->toContain('<a href="'.route('test.list',['offset'=>0,'limit'=>10]).'">prev</a>');
})->group('crud','paginator');

test('CRUD list displays right paginator count', function()
{
    $response = getListResponse();
    expect($response)->toContain('<nav role="paginator">');
    expect($response)->not->toContain('<a href="'.route('test.list',['offset'=>0,'limit'=>10]).'">1</a');
    expect($response)->toContain('<div class="active-page">1</div>');
    expect($response)->toContain('<a href="'.route('test.list',['offset'=>10,'limit'=>10]).'">2</a');
    expect($response)->toContain('<a href="'.route('test.list',['offset'=>20,'limit'=>10]).'">3</a');
    expect($response)->not->toContain('<a href="'.route('test.list',['offset'=>30,'limit'=>10]).'">1</a');
})->group('crud','paginator');

test('CRUD list displays no paginator when too few entries', function()
{
    $response = getListResponse(5);
    expect($response)->not->toContain('<div class="paginator">');
})->group('crud','paginator');

test('CRUD list displays paginator for one additional entry', function()
{
    $response = getListResponse(31);
    expect($response)->toContain(route('test.list',['offset'=>30,'limit'=>10]));
})->group('crud','paginator');

test('CRUD list displays 11 pages-paginator without ellipse with offset 0', function()
{
    $response = getListResponse(105,null,0);
    expect($response)->not->toContain('<a href="'.route('test.list',['offset'=>0,'limit'=>10]).">1</a>");
    expect($response)->toContain('<a href="'.route('test.list',['offset'=>100,'limit'=>10]).'">11</a>');
})->group('crud','paginator');

test('CRUD list displays 12 pages-paginator with ellipse with offset 0', function()
{
    $response = getListResponse(125,null,0);
    expect($response)->toContain('<div class="active-page">1</div>');
    expect($response)->toContain(route('test.list',['offset'=>90,'limit'=>10]));
    expect($response)->not->toContain(route('test.list',['offset'=>100,'limit'=>10]));
    expect($response)->toContain('<div class="ellipse">...</div>');
    expect($response)->toContain(route('test.list',['offset'=>120,'limit'=>10]));
})->group('crud','paginator');

test('CRUD list displays 12 pages-paginator with ellipse with offset 120', function()
{
    $response = getListResponse(125,null,120);
    expect($response)->toContain(route('test.list',['offset'=>0,'limit'=>10]));
    expect($response)->not->toContain(route('test.list',['offset'=>10,'limit'=>10]));
    expect($response)->toContain(route('test.list',['offset'=>100,'limit'=>10]));
    expect($response)->toContain(route('test.list',['offset'=>110,'limit'=>10]));
    expect($response)->toContain('<div class="ellipse">...</div>');
    expect($response)->not->toContain(route('test.list',['offset'=>120,'limit'=>10]));
})->group('crud','paginator');


test('CRUD list displays paginator with ellipse with offset 0', function() 
{
   $response = getListResponse(entries: 1000,offset: 0);
   expect($response)->toContain('<div class="active-page">1</div>');
   expect($response)->toContain(route('test.list',['offset'=>90,'limit'=>10]));
   expect($response)->not->toContain(route('test.list',['offset'=>100,'limit'=>10]));
   expect($response)->toContain('<div class="ellipse">...</div>');
   expect($response)->toContain(route('test.list',['offset'=>990,'limit'=>10]));
})->group('crud','paginator');

test('CRUD list displays paginator with ellipse with offset 50', function()
{
    $response = getListResponse(entries: 1000, offset: 50);
    expect($response)->toContain(route('test.list',['offset'=>0,'limit'=>10]));
    expect($response)->toContain(route('test.list',['offset'=>100,'limit'=>10]));
    expect($response)->not->toContain(route('test.list',['offset'=>110,'limit'=>10]));
    expect($response)->toContain('<div class="ellipse">...</div>');
    expect($response)->toContain(route('test.list',['offset'=>990,'limit'=>10]));
})->group('crud','paginator');

test('CRUD list displays paginator with ellipse with offset 500', function()
{
    $response = getListResponse(entries: 1000, offset: 500);
    expect($response)->toContain(route('test.list',['offset'=>0,'limit'=>10]));
    expect($response)->not->toContain(route('test.list',['offset'=>440,'limit'=>10]));
    expect($response)->toContain(route('test.list',['offset'=>450,'limit'=>10]));
    expect($response)->toContain(route('test.list',['offset'=>550,'limit'=>10]));
    expect($response)->toContain('<div class="ellipse">...</div>');
    expect($response)->not->toContain(route('test.list',['offset'=>560,'limit'=>10]));
    expect($response)->toContain(route('test.list',['offset'=>990,'limit'=>10]));
})->group('crud','paginator');

// ============================ sorting ================================
test('CRUD list displays order columns', function()
{
    $response = getListResponse();
    expect($response)->toContain('<td class="id active_asc"><a href="'.route('test.list',['offset'=>0,'limit'=>10,'order'=>'!id']).'">id</a></td>');
    expect($response)->toContain('<td class="value"><a href="'.route('test.list',['offset'=>0,'limit'=>10,'order'=>'item']).'">item</a></td>');
    expect($response)->toContain('<td class="value">payload</td>');
})->group('crud','sorting');

test('CRUD list displays order columns with offset', function()
{
    // "<td class="id active_asc"><a href="http://localhost/test/list/0/10/!id">id</a></td>"
    $response = getListResponse(entries: 30,offset: 20);
    expect($response)->toContain('<td class="id active_asc"><a href="'.route('test.list',['offset'=>0,'limit'=>10,'order'=>'!id']).'">id</a></td>');
    expect($response)->toContain('<td class="value"><a href="'.route('test.list',['offset'=>0,'limit'=>10,'order'=>'item']).'">item</a></td>');
    expect($response)->toContain('<td class="value">payload</td>');
})->group('crud','sorting');

test('CRUD list respects ordering asc and marks column', function()
{
   $response = getListResponse(order: 'item');
   expect($response)->toContain('<td class="value active_asc"><a href="'.route('test.list',['offset'=>0,'limit'=>10,'order'=>'!item']).'">item</a>');
})->group('crud','sorting');

test('CRUD list respects ordering desc and marks column', function()
{
    $response = getListResponse(order: '!item', order_dir:'desc');
    expect($response)->toContain('<td class="value active_desc"><a href="'.route('test.list',['offset'=>0,'limit'=>10,'order'=>'item']).'">item</a>');
})->group('crud','sorting');

test('CRUD list fails when sort field is not sortable', function()
{
    $engine = \Mockery::mock(AbstractCrudEngine::class);
    $engine->shouldReceive('isSortable')->once()->with('payload')->andReturns(false);
    $test = new CrudListResponse($engine);    
    $test->setOrder('payload');
})->group('crud','sorting')->throws(FieldNotSortableException::class);

// ============================ filter =================================
test('CRUD list offsers filters', function()
{
    $response = getListResponse();
    expect($response)
        ->toContain('<select class="filter" name="filter" id="filter">')
        ->toContain('<option value="none">(no filter)</option>');
})->group('crud','filter');

test('CRUD list offsers fixed filters', function()
{
    $response = getListResponse();
    expect($response)
    ->toContain('<option value="itemfilter">Item filter</option>');
})->group('crud','filter');

test('CRUD list offsers user filters', function()
{
    $response = getListResponse();
    expect($response)
    ->toContain('<option value="userfilter">User defined filter...</option>');
})->group('crud','filter');

test('CRUD list doesnt offser user filters when disabled', function()
{
    $response = getListResponse(features: []);    
    expect($response)
    ->not->toContain('<option value="userfilter">User defined filter...</option>');
})->group('crud','filter');

test('CRUD list doesnt offers filters when all empty', function() 
{
    $response = getListResponse(features: [], filters:[]);
    expect($response)
    ->not->toContain('<select class="filter" name="filter" id="filter">');    
})->group('crud','filter');

test('CRUD list does offer at least user filters when fixed are empty', function()
{
    $response = getListResponse(filters:[]);
    expect($response)
    ->toContain('<select class="filter" name="filter" id="filter">')
    ->toContain('<option value="userfilter">User defined filter...</option>');
})->group('crud','filter');

// ================================== Group actions =======================================
test('CRUD list provides group action select fields', function() {
    $response = getListResponse();
    expect($response)->toContain('<td class="group"><input type="checkbox" name="group[]" value="1"></td>');
})->group('crud','group');

test('CRUD list provides group action buttons', function() {
    $response = getListResponse();
    expect($response)->toContain('<button id="edit" class="group">Edit</button>');
})->group('crud','group');

test('CRUD list hides group action select field when no group actions', function()
{
    $response = getListResponse(group_actions: []);
    expect($response)->not->toContain('<td class="group"><input type="checkbox" name="group[]" value="1"></td>');
})->group('crud','group');

test('CRUD list hides group action buttons when no group actions', function()
{
    $response = getListResponse(group_actions: []);
    expect($response)->not->toContain('<button id="edit" class="group">Edit</button>');    
})->group('crud','group');

// =============================== Search Field ==============================================
test('CRUD list provides a search field when search is on', function()
{
   $response = getListResponse();
   expect($response)->toContain('<input name="search_str" id="search_str">');
   expect($response)->toContain('<button id="submit_search" class="search">');
})->group('crud','search');

test('CRUD list doesnt provide a search field when search is off', function()
{
    $response = getListResponse(features:[]);
    expect($response)->not->toContain('<input name="search_str" id="search_str">');
    expect($response)->not->toContain('<button id="submit_search" class="search">');    
})->group('crud','search');;