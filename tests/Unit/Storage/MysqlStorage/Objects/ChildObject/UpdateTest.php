<?php
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\ChildObject;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Update a ChildObject with nothing modified (both arrays filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 9, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>0,'element'=>30]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>31]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>2,'element'=>32]);

    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>0,'element'=>200]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>210]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>2,'element'=>220]);
})->group('update');

test('Update a ChildObject only parent simple fields (both arrays filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 9, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    $test->setValue('parent_int',999);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>999,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>0,'element'=>30]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>31]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>2,'element'=>32]);
    
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>0,'element'=>200]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>210]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>2,'element'=>220]);
})->group('update');

test('Update a ChildObject only child simple field (both arrays filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 9, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    $test->setValue('child_int',999);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>0,'element'=>30]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>31]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>2,'element'=>32]);
    
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>999,'child_string'=>'BCD']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>0,'element'=>200]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>210]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>2,'element'=>220]);
})->group('update');

test('Update a ChildObject only both simple field (both arrays filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 9, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    $test->setValue('parent_int',888);
    $test->setValue('child_int',999);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>888,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>0,'element'=>30]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>31]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>2,'element'=>32]);
    
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>999,'child_string'=>'BCD']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>0,'element'=>200]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>210]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>2,'element'=>220]);
})->group('update');

test('Update a ChildObject with add element to parent array (both arrays filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 9, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    $test->setValue('parent_sarray',[30,31,32,33]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>0,'element'=>30]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>31]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>2,'element'=>32]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>3,'element'=>33]);
    
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>0,'element'=>200]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>210]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>2,'element'=>220]);
})->group('update');

test('Update a ChildObject with delete element to parent array (both arrays filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 9, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    $test->setValue('parent_sarray',[30,31]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>0,'element'=>30]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>31]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>9,'index'=>2,'element'=>32]);
    
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>0,'element'=>200]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>210]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>2,'element'=>220]);
})->group('update');

test('Update a ChildObject with change element to parent array (both arrays filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 9, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    $test->setValue('parent_sarray',[30,55,32]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>0,'element'=>30]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>55]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>2,'element'=>32]);
    
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>0,'element'=>200]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>210]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>2,'element'=>220]);
})->group('update');

test('Update a ChildObject with clear parent array (both arrays filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 9, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    $test->setValue('parent_sarray',[]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>9]);
    
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>0,'element'=>200]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>210]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>2,'element'=>220]);
})->group('update');

test('Update a ChildObject with add element to child array (both arrays filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 9, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    $test->setValue('child_sarray',[200,210,220,33]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>0,'element'=>30]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>31]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>2,'element'=>32]);
    
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>0,'element'=>200]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>210]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>2,'element'=>220]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>3,'element'=>33]);
})->group('update');

test('Update a ChildObject with remove element to child array (both arrays filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 9, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    $test->setValue('child_sarray',[200,210]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>0,'element'=>30]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>31]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>2,'element'=>32]);
    
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>0,'element'=>200]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>210]);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>9,'index'=>2,'element'=>220]);
})->group('update');

test('Update a ChildObject with change element to child array (both arrays filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 9, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    $test->setValue('child_sarray',[200,333,220]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>0,'element'=>30]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>31]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>2,'element'=>32]);
    
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>0,'element'=>200]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>333]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>2,'element'=>220]);
})->group('update');

test('Update a ChildObject with clear child array (both arrays filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 9, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    $test->setValue('child_sarray',[]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>0,'element'=>30]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>31]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>2,'element'=>32]);
    
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>9]);
})->group('update');

test('Update a ChildObject with nothing modified (only parent array filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 10, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>10,'parent_int'=>444,'parent_string'=>'DDD']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>0,'element'=>40]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>1,'element'=>41]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>2,'element'=>42]);
    
    $this->assertDatabaseHas('childobjects',['id'=>10,'child_int'=>222,'child_string'=>'CDE']);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>10]);
})->group('update');

test('Update a ChildObject modify simple fields of parent (only parent array filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 10, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    $test->setValue('parent_string','WOW');
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>10,'parent_int'=>444,'parent_string'=>'WOW']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>0,'element'=>40]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>1,'element'=>41]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>2,'element'=>42]);
    
    $this->assertDatabaseHas('childobjects',['id'=>10,'child_int'=>222,'child_string'=>'CDE']);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>10]);
})->group('update');

test('Update a ChildObject modify simple field of child (only parent array filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 10, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    $test->setValue('child_string','WOW');
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>10,'parent_int'=>444,'parent_string'=>'DDD']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>0,'element'=>40]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>1,'element'=>41]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>2,'element'=>42]);
    
    $this->assertDatabaseHas('childobjects',['id'=>10,'child_int'=>222,'child_string'=>'WOW']);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>10]);
})->group('update');

test('Update a ChildObject modify both simple fields (only parent array filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 10, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    $test->setValue('parent_string','WOW');
    $test->setValue('child_string','DOH');
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>10,'parent_int'=>444,'parent_string'=>'WOW']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>0,'element'=>40]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>1,'element'=>41]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>2,'element'=>42]);
    
    $this->assertDatabaseHas('childobjects',['id'=>10,'child_int'=>222,'child_string'=>'DOH']);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>10]);
})->group('update');

test('Update a ChildObject add element to parent array  (only parent array filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 10, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    $test->setValue('parent_sarray',[40,41,42,43]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>10,'parent_int'=>444,'parent_string'=>'DDD']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>0,'element'=>40]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>1,'element'=>41]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>2,'element'=>42]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>3,'element'=>43]);
    
    $this->assertDatabaseHas('childobjects',['id'=>10,'child_int'=>222,'child_string'=>'CDE']);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>10]);
})->group('update');

test('Update a ChildObject delete element to parent array  (only parent array filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 10, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    $test->setValue('parent_sarray',[40,41]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>10,'parent_int'=>444,'parent_string'=>'DDD']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>0,'element'=>40]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>1,'element'=>41]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>10,'index'=>2,'element'=>42]);
    
    $this->assertDatabaseHas('childobjects',['id'=>10,'child_int'=>222,'child_string'=>'CDE']);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>10]);
})->group('update');

test('Update a ChildObject change element to parent array  (only parent array filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 10, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    $test->setValue('parent_sarray',[40,99,42]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>10,'parent_int'=>444,'parent_string'=>'DDD']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>0,'element'=>40]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>1,'element'=>99]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>2,'element'=>42]);
    
    $this->assertDatabaseHas('childobjects',['id'=>10,'child_int'=>222,'child_string'=>'CDE']);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>10]);
})->group('update');

test('Update a ChildObject clear parent array  (only parent array filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 10, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    $test->setValue('parent_sarray',[]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>10,'parent_int'=>444,'parent_string'=>'DDD']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>10]);
    
    $this->assertDatabaseHas('childobjects',['id'=>10,'child_int'=>222,'child_string'=>'CDE']);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>10]);
})->group('update');

test('Update a ChildObject add element to child array  (only parent array filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 10, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    $test->setValue('child_sarray',[400,410]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>10,'parent_int'=>444,'parent_string'=>'DDD']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>0,'element'=>40]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>1,'element'=>41]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>2,'element'=>42]);
    
    $this->assertDatabaseHas('childobjects',['id'=>10,'child_int'=>222,'child_string'=>'CDE']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>10,'index'=>0,'element'=>400]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>10,'index'=>1,'element'=>410]);
})->group('update');

test('Update a ChildObject change everything (only parent array filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 10, ChildObject::class);
    prepareObjectDataset($test, 'ChildObject');
    $test->setValue('parent_int',919);
    $test->setValue('child_string','TRA');
    $test->setValue('parent_sarray',[40,888,42]);
    $test->setValue('child_sarray',[400,410]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>10,'parent_int'=>919,'parent_string'=>'DDD']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>0,'element'=>40]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>1,'element'=>888]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10,'index'=>2,'element'=>42]);
    
    $this->assertDatabaseHas('childobjects',['id'=>10,'child_int'=>222,'child_string'=>'TRA']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>10,'index'=>0,'element'=>400]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>10,'index'=>1,'element'=>410]);
})->group('update');

test('Update a ChildObject with nothing modified (only child array filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 11, ChildObject::class);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>11,'parent_int'=>555,'parent_string'=>'EEE']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>11]);
    
    $this->assertDatabaseHas('childobjects',['id'=>11,'child_int'=>232,'child_string'=>'DEF']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>0,'element'=>400]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>1,'element'=>410]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>2,'element'=>420]);
})->group('update');

test('Update a ChildObject modify simple fields of parent (only child array filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 11, ChildObject::class);
    $test->setValue('parent_int',999);    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>11,'parent_int'=>999,'parent_string'=>'EEE']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>11]);
    
    $this->assertDatabaseHas('childobjects',['id'=>11,'child_int'=>232,'child_string'=>'DEF']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>0,'element'=>400]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>1,'element'=>410]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>2,'element'=>420]);
})->group('update');

test('Update a ChildObject mpdify simple fields of child (only child array filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 11, ChildObject::class);
    $test->setValue('child_int',999);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>11,'parent_int'=>555,'parent_string'=>'EEE']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>11]);
    
    $this->assertDatabaseHas('childobjects',['id'=>11,'child_int'=>999,'child_string'=>'DEF']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>0,'element'=>400]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>1,'element'=>410]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>2,'element'=>420]);
})->group('update');

test('Update a ChildObject add element to child array (only child array filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 11, ChildObject::class);
    $test->setValue('child_sarray',[400,410,420,430]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>11,'parent_int'=>555,'parent_string'=>'EEE']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>11]);
    
    $this->assertDatabaseHas('childobjects',['id'=>11,'child_int'=>232,'child_string'=>'DEF']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>0,'element'=>400]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>1,'element'=>410]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>2,'element'=>420]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>3,'element'=>430]);
})->group('update');

test('Update a ChildObject delete element from child array (only child array filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 11, ChildObject::class);
    $test->setValue('child_sarray',[400,410]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>11,'parent_int'=>555,'parent_string'=>'EEE']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>11]);
    
    $this->assertDatabaseHas('childobjects',['id'=>11,'child_int'=>232,'child_string'=>'DEF']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>0,'element'=>400]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>1,'element'=>410]);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>11,'index'=>2,'element'=>420]);
})->group('update');

test('Update a ChildObject change element to child array (only child array filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 11, ChildObject::class);
    $test->setValue('child_sarray',[400,999,420]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>11,'parent_int'=>555,'parent_string'=>'EEE']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>11]);
    
    $this->assertDatabaseHas('childobjects',['id'=>11,'child_int'=>232,'child_string'=>'DEF']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>0,'element'=>400]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>1,'element'=>999]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>2,'element'=>420]);
})->group('update');

test('Update a ChildObject clear child array (only child array filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 11, ChildObject::class);
    $test->setValue('child_sarray',[]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>11,'parent_int'=>555,'parent_string'=>'EEE']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>11]);
    
    $this->assertDatabaseHas('childobjects',['id'=>11,'child_int'=>232,'child_string'=>'DEF']);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>11]);
})->group('update');

test('Update a ChildObject add element to parent array (only child array filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 11, ChildObject::class);
    $test->setValue('parent_sarray',[400,410]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>11,'parent_int'=>555,'parent_string'=>'EEE']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>11,'index'=>0,'element'=>400]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>11,'index'=>1,'element'=>410]);
    
    $this->assertDatabaseHas('childobjects',['id'=>11,'child_int'=>232,'child_string'=>'DEF']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>0,'element'=>400]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>1,'element'=>410]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>2,'element'=>420]);
})->group('update');

test('Update a ChildObject modify everything (only child array filled)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 11, ChildObject::class);
    $test->setValue('parent_int',999);
    $test->setValue('parent_sarray',[400,410]);
    $test->setValue('child_string','LOL');
    $test->setValue('child_sarray',[400,777,420]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>11,'parent_int'=>999,'parent_string'=>'EEE']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>11,'index'=>0,'element'=>400]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>11,'index'=>1,'element'=>410]);
    
    $this->assertDatabaseHas('childobjects',['id'=>11,'child_int'=>232,'child_string'=>'LOL']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>0,'element'=>400]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>1,'element'=>777]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>2,'element'=>420]);
})->group('update');

test('Update a ChildObject with nothing modified (both arrays empty)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 12, ChildObject::class);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>12,'parent_int'=>666,'parent_string'=>'FFF']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>12]);
    
    $this->assertDatabaseHas('childobjects',['id'=>12,'child_int'=>242,'child_string'=>'EFG']);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>12]);
})->group('update');

test('Update a ChildObject add elements to parent (both arrays empty)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 12, ChildObject::class);

    $test->setValue('parent_sarray',[10,20]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>12,'parent_int'=>666,'parent_string'=>'FFF']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>12,'index'=>0,'element'=>10]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>12,'index'=>1,'element'=>20]);
    
    $this->assertDatabaseHas('childobjects',['id'=>12,'child_int'=>242,'child_string'=>'EFG']);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>12]);
})->group('update');

test('Update a ChildObject add elements to child (both arrays empty)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 12, ChildObject::class);
    
    $test->setValue('child_sarray',[10,20]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>12,'parent_int'=>666,'parent_string'=>'FFF']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>12]);
    
    $this->assertDatabaseHas('childobjects',['id'=>12,'child_int'=>242,'child_string'=>'EFG']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>12,'index'=>0,'element'=>10]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>12,'index'=>1,'element'=>20]);
})->group('update');

test('Update a ChildObject add elements to both (both arrays empty)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 12, ChildObject::class);
    
    $test->setValue('parent_sarray',[210,220]);
    $test->setValue('child_sarray',[10,20]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>12,'parent_int'=>666,'parent_string'=>'FFF']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>12,'index'=>0,'element'=>210]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>12,'index'=>1,'element'=>220]);
    
    $this->assertDatabaseHas('childobjects',['id'=>12,'child_int'=>242,'child_string'=>'EFG']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>12,'index'=>0,'element'=>10]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>12,'index'=>1,'element'=>20]);
})->group('update');

test('Update a ChildObject change everything (both arrays empty)', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    pretendLoaded($test, 12, ChildObject::class);
    
    $test->setValue('parent_int',999);
    $test->setValue('child_string','DDD');
    $test->setValue('parent_sarray',[210,220]);
    $test->setValue('child_sarray',[10,20]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>12,'parent_int'=>999,'parent_string'=>'FFF']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>12,'index'=>0,'element'=>210]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>12,'index'=>1,'element'=>220]);
    
    $this->assertDatabaseHas('childobjects',['id'=>12,'child_int'=>242,'child_string'=>'DDD']);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>12,'index'=>0,'element'=>10]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>12,'index'=>1,'element'=>20]);
})->group('update');

