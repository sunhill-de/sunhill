<?php
/**
 * @file MigrateTest.php
 * tests: /src/Storage/MysqlStorage/MysqlObjectStorage.php
 * free of dependent units: only from AbstractObjectStorage
 */
use Sunhill\Tests\SunhillDatabaseTestCase;
use Illuminate\Support\Facades\Schema;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Illuminate\Support\Facades\DB;

uses(SunhillDatabaseTestCase::class);

function makeStructure($fields): \stdClass
{
    $result = new \stdClass();
    $result->name = "testtable";
    $result->type = "record";
    $result->elements = $fields;
    $result->skipping_members = [];
    $result->options = [];
    return $result;
}

test('Migrate fresh table', function(array $infos, bool $type_check = true)
{
    $infos = array_merge($infos, ['name'=>'field','storage_subid'=>'testtable']);
    Schema::dropIfExists('testtable');
    $storage = new MysqlObjectStorage();
    $storage->setStructure(makeStructure(['field'=>makeStdClass($infos)]));
    
    $storage->migrate();
    $this->assertDatabaseHasTable('testtable');
    $this->assertDatabaseTableHasColumn('testtable','field');
    if ($type_check) {
        $this->assertDatabaseTableColumnIsType('testtable', 'field', $infos['type']);
    }
})->with([
    'integer'=>[['type'=>'integer']],
    'string'=>[['type'=>'string','max_length'=>10]],    
    'float'=>[['type'=>'float']],
    'boolean'=>[['type'=>'boolean'],false],
    'date'=>[['type'=>'date']],
    'time'=>[['type'=>'time']],
    'datetime'=>[['type'=>'datetime']],
    'text'=>[['type'=>'text']],
]);

test('Migrate fresh table with reference', function()
{
    $infos = ['name'=>'field','storage_subid'=>'testtable','type'=>'record'];
    Schema::dropIfExists('testtable');
    $storage = new MysqlObjectStorage();
    $storage->setStructure(makeStructure(['field'=>makeStdClass($infos)]));
    
    $storage->migrate();
    $this->assertDatabaseHasTable('testtable');
    $this->assertDatabaseTableHasColumn('testtable','field');
    $this->assertDatabaseTableColumnIsType('testtable', 'field', 'integer');
});

test('Migrate with a default value', function()
{
    Schema::dropIfExists('testtable');
    $storage = new MysqlObjectStorage();
    $storage->setStructure(makeStructure([
        'field1'=>makeStdClass(['name'=>'field1','storage_subid'=>'testtable','type'=>'integer']),
        'field2'=>makeStdClass(['name'=>'field2','storage_subid'=>'testtable','type'=>'integer','default'=>10]),
    ]));
    $storage->migrate();
    DB::table('testtable')->insert([['field1'=>11,'field2'=>22]]);
    DB::table('testtable')->insert([['field1'=>33]]);
    $this->assertDatabaseHas('testtable',['field1'=>11,'field2'=>22]);
    $this->assertDatabaseHas('testtable',['field2'=>33,'field2'=>10]);
});

test('Migrate with nullable value', function()
{
    Schema::dropIfExists('testtable');
    $storage = new MysqlObjectStorage();
    $storage->setStructure(makeStructure([
        'field1'=>makeStdClass(['name'=>'field1','storage_subid'=>'testtable','type'=>'integer']),
        'field2'=>makeStdClass(['name'=>'field2','storage_subid'=>'testtable','type'=>'integer','nullable'=>true]),
    ]));
    $storage->migrate();
    DB::table('testtable')->insert([['field1'=>11,'field2'=>22]]);
    DB::table('testtable')->insert([['field1'=>33]]);
    $this->assertDatabaseHas('testtable',['field1'=>11,'field2'=>22]);
    $this->assertDatabaseHas('testtable',['field2'=>33,'field2'=>null]);
});

test('Migrate with nullable and default value', function()
{
    Schema::dropIfExists('testtable');
    $storage = new MysqlObjectStorage();
    $storage->setStructure(makeStructure([
        'field1'=>makeStdClass(['name'=>'field1','storage_subid'=>'testtable','type'=>'integer']),
        'field2'=>makeStdClass(['name'=>'field2','storage_subid'=>'testtable','type'=>'integer','default'=>10,'nullable'=>true]),
    ]));
    $storage->migrate();
    DB::table('testtable')->insert([['field1'=>11,'field2'=>22]]);
    DB::table('testtable')->insert([['field1'=>33]]);
    $this->assertDatabaseHas('testtable',['field1'=>11,'field2'=>22]);
    $this->assertDatabaseHas('testtable',['field2'=>33,'field2'=>10]);
});

test('Migrate with defaultsNull value', function()
{
    Schema::dropIfExists('testtable');
    $storage = new MysqlObjectStorage();
    $storage->setStructure(makeStructure([
        'field1'=>makeStdClass(['name'=>'field1','storage_subid'=>'testtable','type'=>'integer']),
        'field2'=>makeStdClass(['name'=>'field2','storage_subid'=>'testtable','type'=>'integer','default'=>null,'nullable'=>true]),
    ]));
    $storage->migrate();
    DB::table('testtable')->insert([['field1'=>11,'field2'=>22]]);
    DB::table('testtable')->insert([['field1'=>33]]);
    $this->assertDatabaseHas('testtable',['field1'=>11,'field2'=>22]);
    $this->assertDatabaseHas('testtable',['field2'=>33,'field2'=>null]);
});

test('Migrate an array', function($element_type, $check_type = true)
{
    Schema::dropIfExists('testtable');
    $storage = new MysqlObjectStorage();
    $storage->setStructure(makeStructure([
        'field'=>makeStdClass(['name'=>'field','storage_subid'=>'testtable','type'=>'array','element_type'=>$element_type,'index_type'=>'integer']),
    ]));
    $storage->migrate();
    $this->assertDatabaseTableColumnIsType('testtable_field', 'container_id', 'integer');
    $this->assertDatabaseTableColumnIsType('testtable_field', 'index', 'integer');
    if ($check_type) {
        $this->assertDatabaseTableColumnIsType('testtable_field', 'element', $element_type);
    }
})->with([
    'integer'=>['integer'],
    'string'=>['string'],
    'float'=>['float'],
    'boolean'=>['boolean', false],
    'date'=>['date'],
    'time'=>['time'],
    'datetime'=>['datetime'],
    'text'=>['text'],
]);

test('Migrate an array of references', function()
{
    Schema::dropIfExists('testtable');
    $storage = new MysqlObjectStorage();
    $storage->setStructure(makeStructure([
        'field'=>makeStdClass(['name'=>'field','storage_subid'=>'testtable','type'=>'array','element_type'=>'record','index_type'=>'integer']),
    ]));
    $storage->migrate();
    $this->assertDatabaseTableColumnIsType('testtable_field', 'container_id', 'integer');
    $this->assertDatabaseTableColumnIsType('testtable_field', 'index', 'integer');
    $this->assertDatabaseTableColumnIsType('testtable_field', 'element', 'integer');
});

test('Migrate a map', function($element_type, $check_type = true)
{
    Schema::dropIfExists('testtable');
    $storage = new MysqlObjectStorage();
    $storage->setStructure(makeStructure([
        'field'=>makeStdClass(['name'=>'field','storage_subid'=>'testtable','type'=>'array','element_type'=>$element_type,'index_type'=>'string']),
    ]));
    $storage->migrate();
    $this->assertDatabaseTableColumnIsType('testtable_field', 'container_id', 'integer');
    $this->assertDatabaseTableColumnIsType('testtable_field', 'index', 'string');
    if ($check_type) {
        $this->assertDatabaseTableColumnIsType('testtable_field', 'element', $element_type);
    }   
})->with([
    'integer'=>['integer'],
    'string'=>['string'],
    'float'=>['float'],
    'boolean'=>['boolean', false],
    'date'=>['date'],
    'time'=>['time'],
    'datetime'=>['datetime'],
    'text'=>['text'],
]);