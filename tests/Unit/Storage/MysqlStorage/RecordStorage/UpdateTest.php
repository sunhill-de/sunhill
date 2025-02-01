<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\Exceptions\StorageTableMissingException;
use Illuminate\Support\Facades\Schema;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;

require_once('PrepareStorage.php');

uses(SunhillDatabaseTestCase::class);

/**
 * @todo Why is autoloading necessary?
 */
test('Update a dummy with dummyint modified', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'dummy'));
    setProtectedProperty($test, 'id', 1);
    $test->setValue('dummyint',123);
    $test->setValue('dummyint',1509);
    fillObjectsDataset($test, 'Dummy');
    $test->commit();

    $this->assertDatabaseHas('objects',
        [
            'id'=>1,
            '_classname'=>'Dummy',
            '_uuid'=>'11b47be8-05f1-4f7b-8a97-e1e6488dbd44',            
            '_created_at'=>'2024-11-14 20:00:00',
            '_updated_at'=>'2024-11-14 20:00:00',
        ]);
    $this->assertDatabaseHas('dummies',['id'=>1,'dummyint'=>1509]);
});

test('Update a dummy with nothing modified', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'dummy'));
    setProtectedProperty($test, 'id', 1);
    $test->setValue('dummyint',123);
    fillObjectsDataset($test, 'Dummy');
    
    $test->commit();
    
    $this->assertDatabaseHas('objects',
        [
            'id'=>1,
            '_classname'=>'Dummy',
            '_uuid'=>'11b47be8-05f1-4f7b-8a97-e1e6488dbd44',
            '_created_at'=>'2024-11-14 20:00:00',
            '_updated_at'=>'2024-11-14 20:00:00',
        ]);
    $this->assertDatabaseHas('dummies',['id'=>1,'dummyint'=>123]);
});

test('Update a parentobject with modified array (all entries) and modified simple fields', function()
{
    
    $structure = 
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'parentobject'));
    setProtectedProperty($test, 'id', 7);
    
    $test->setValue('parent_int',111);
    $test->setValue('parent_string','AAA');
    $test->setValue('parent_sarray',[10,11,12]);

    $test->setValue('parent_int',919);
    $test->setValue('parent_string','AZA');
    $test->setValue('parent_sarray',[100,110,120]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>7,'parent_int'=>919,'parent_string'=>'AZA']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>0,'element'=>100]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>1,'element'=>110]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>2,'element'=>120]);    
});

test('Update a parentobject with modified array (added a entry) and unmodified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'parentobject'));
    setProtectedProperty($test, 'id', 7);
    
    $test->setValue('parent_int',111);
    $test->setValue('parent_string','AAA');
    $test->setValue('parent_sarray',[10,11,12]);
    
    $test->setValue('parent_sarray',[10,11,12,99]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>7,'parent_int'=>111,'parent_string'=>'AAA']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>0,'element'=>10]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>1,'element'=>11]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>2,'element'=>12]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>3,'element'=>99]);
});

test('Update a parentobject with modified array (deleted a entry) and unmodified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'parentobject'));
    setProtectedProperty($test, 'id', 7);
    
    $test->setValue('parent_int',111);
    $test->setValue('parent_string','AAA');
    $test->setValue('parent_sarray',[10,11,12]);
    
    $test->setValue('parent_sarray',[10,11]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>7,'parent_int'=>111,'parent_string'=>'AAA']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>0,'element'=>10]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>1,'element'=>11]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>7,'index'=>2]);
});

test('Update a parentobject with modified array (deleted all entries) and unmodified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'parentobject'));
    setProtectedProperty($test, 'id', 7);
    
    $test->setValue('parent_int',111);
    $test->setValue('parent_string','AAA');
    $test->setValue('parent_sarray',[10,11,12]);
    
    $test->setValue('parent_sarray',[]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>7,'parent_int'=>111,'parent_string'=>'AAA']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>7]);
});

test('Update a parentobject with modified array (previously empty array) and unmodified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'parentobject'));
    setProtectedProperty($test, 'id', 8);
    
    $test->setValue('parent_int',222);
    $test->setValue('parent_string','BBB');
    $test->setValue('parent_sarray',[]);
    
    $test->setValue('parent_sarray',[110,111]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>8,'parent_int'=>222,'parent_string'=>'BBB']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>8,'index'=>0,'element'=>110]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>8,'index'=>1,'element'=>111]);
});

test('Update a parentobject with unmodified array (empty) and modified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'parentobject'));
    setProtectedProperty($test, 'id', 8);
    
    $test->setValue('parent_int',222);
    $test->setValue('parent_string','BBB');
    $test->setValue('parent_sarray',[]);
    
    $test->setValue('parent_int',2222);
    $test->setValue('parent_string','BBBB');
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>8,'parent_int'=>2222,'parent_string'=>'BBBB']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>8]);
});

test('Update a parentobject with unmodified array (had entries) and modified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'parentobject'));
    setProtectedProperty($test, 'id', 7);
    
    $test->setValue('parent_int',111);
    $test->setValue('parent_string','AAA');
    $test->setValue('parent_sarray',[10,11,12]);
    
    $test->setValue('parent_int',919);
    $test->setValue('parent_string','AZA');
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>7,'parent_int'=>919,'parent_string'=>'AZA']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>0,'element'=>10]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>1,'element'=>11]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>2,'element'=>12]);
});

test('Update a childobject with modified array (all entries) and modified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    setProtectedProperty($test, 'id', 9);
    
    $test->setValue('parent_int',333);
    $test->setValue('parent_string','CCC');
    $test->setValue('parent_sarray',[30,31,32]);
    $test->setValue('child_int',212);
    $test->setValue('child_string','BCD');
    $test->setValue('child_sarray',[200,210,220]);
    
    $test->setValue('parent_int',3331);
    $test->setValue('parent_string','CCCA');
    $test->setValue('parent_sarray',[301,311,321]);
    $test->setValue('child_int',2121);
    $test->setValue('child_string','BCDA');
    $test->setValue('child_sarray',[2001,2101,2201]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>3331,'parent_string'=>'CCCA']);
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>2121,'child_string'=>'BCDA']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>0,'element'=>301]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>311]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>2,'element'=>321]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>0,'element'=>2001]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>2101]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>2,'element'=>2201]);
});

test('Update a childobject with modified both array (all entries) and unmodified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    setProtectedProperty($test, 'id', 9);
    
    $test->setValue('parent_int',333);
    $test->setValue('parent_string','CCC');
    $test->setValue('parent_sarray',[30,31,32]);
    $test->setValue('child_int',212);
    $test->setValue('child_string','BCD');
    $test->setValue('child_sarray',[200,210,220]);
    
    $test->setValue('parent_sarray',[301,311,321]);
    $test->setValue('child_sarray',[2001,2101,2201]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>0,'element'=>301]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>311]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>2,'element'=>321]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>0,'element'=>2001]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>2101]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>2,'element'=>2201]);
});

test('Update a childobject with modified both array (added entries) and unmodified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    setProtectedProperty($test, 'id', 9);
    
    $test->setValue('parent_int',333);
    $test->setValue('parent_string','CCC');
    $test->setValue('parent_sarray',[30,31,32]);
    $test->setValue('child_int',212);
    $test->setValue('child_string','BCD');
    $test->setValue('child_sarray',[200,210,220]);
    
    $test->setValue('parent_sarray',[30,31,32,666]);
    $test->setValue('child_sarray',[200,210,220,6666]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>0,'element'=>30]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>31]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>2,'element'=>32]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>3,'element'=>666]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>0,'element'=>200]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>210]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>2,'element'=>220]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>3,'element'=>6666]);
});

test('Update a childobject with modified both array (removed entries) and unmodified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    setProtectedProperty($test, 'id', 9);
    
    $test->setValue('parent_int',333);
    $test->setValue('parent_string','CCC');
    $test->setValue('parent_sarray',[30,31,32]);
    $test->setValue('child_int',212);
    $test->setValue('child_string','BCD');
    $test->setValue('child_sarray',[200,210,220]);
    
    $test->setValue('parent_sarray',[30,31]);
    $test->setValue('child_sarray',[200,210]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>0,'element'=>30]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>31]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>9,'index'=>2]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>0,'element'=>200]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>210]);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>9,'index'=>2]);
});

test('Update a childobject with modified both array (cleared arrays) and unmodified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    setProtectedProperty($test, 'id', 9);
    
    $test->setValue('parent_int',333);
    $test->setValue('parent_string','CCC');
    $test->setValue('parent_sarray',[30,31,32]);
    $test->setValue('child_int',212);
    $test->setValue('child_string','BCD');
    $test->setValue('child_sarray',[200,210,220]);
    
    $test->setValue('parent_sarray',[]);
    $test->setValue('child_sarray',[]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>9]);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>9]);
});

test('Update a childobject with modified parent array (all entries) and unmodified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    setProtectedProperty($test, 'id', 9);
    
    $test->setValue('parent_int',333);
    $test->setValue('parent_string','CCC');
    $test->setValue('parent_sarray',[30,31,32]);
    $test->setValue('child_int',212);
    $test->setValue('child_string','BCD');
    $test->setValue('child_sarray',[200,210,220]);
    
    $test->setValue('parent_sarray',[301,311,321]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>0,'element'=>301]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>311]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>2,'element'=>321]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>0,'element'=>200]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>210]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>2,'element'=>220]);
});

test('Update a childobject with modified parent array (added entries) and unmodified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    setProtectedProperty($test, 'id', 9);
    
    $test->setValue('parent_int',333);
    $test->setValue('parent_string','CCC');
    $test->setValue('parent_sarray',[30,31,32]);
    $test->setValue('child_int',212);
    $test->setValue('child_string','BCD');
    $test->setValue('child_sarray',[200,210,220]);
    
    $test->setValue('parent_sarray',[30,31,32,666]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>0,'element'=>30]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>31]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>2,'element'=>32]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>3,'element'=>666]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>0,'element'=>200]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>210]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>2,'element'=>220]);
});

test('Update a childobject with modified parent array (removed entries) and unmodified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    setProtectedProperty($test, 'id', 9);
    
    $test->setValue('parent_int',333);
    $test->setValue('parent_string','CCC');
    $test->setValue('parent_sarray',[30,31,32]);
    $test->setValue('child_int',212);
    $test->setValue('child_string','BCD');
    $test->setValue('child_sarray',[200,210,220]);
    
    $test->setValue('parent_sarray',[30,31]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>0,'element'=>30]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>31]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>9,'index'=>2]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>0,'element'=>200]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>210]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>2,'element'=>220]);
});

test('Update a childobject with modified parent array (cleared arrays) and unmodified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    setProtectedProperty($test, 'id', 9);
    
    $test->setValue('parent_int',333);
    $test->setValue('parent_string','CCC');
    $test->setValue('parent_sarray',[30,31,32]);
    $test->setValue('child_int',212);
    $test->setValue('child_string','BCD');
    $test->setValue('child_sarray',[200,210,220]);
    
    $test->setValue('parent_sarray',[]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>9]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9]);
});

// **********************************************************************
test('Update a childobject with modified child array (all entries) and unmodified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    setProtectedProperty($test, 'id', 9);
    
    $test->setValue('parent_int',333);
    $test->setValue('parent_string','CCC');
    $test->setValue('parent_sarray',[30,31,32]);
    $test->setValue('child_int',212);
    $test->setValue('child_string','BCD');
    $test->setValue('child_sarray',[200,210,220]);
    
    $test->setValue('child_sarray',[2001,2101,2201]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>0,'element'=>30]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>31]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>2,'element'=>32]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>0,'element'=>2001]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>2101]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>2,'element'=>2201]);
});

test('Update a childobject with modified child array (added entries) and unmodified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    setProtectedProperty($test, 'id', 9);
    
    $test->setValue('parent_int',333);
    $test->setValue('parent_string','CCC');
    $test->setValue('parent_sarray',[30,31,32]);
    $test->setValue('child_int',212);
    $test->setValue('child_string','BCD');
    $test->setValue('child_sarray',[200,210,220]);
    
    $test->setValue('child_sarray',[200,210,220,6666]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>0,'element'=>30]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>31]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>2,'element'=>32]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>0,'element'=>200]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>210]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>2,'element'=>220]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>3,'element'=>6666]);
});

test('Update a childobject with modified child array (removed entries) and unmodified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    setProtectedProperty($test, 'id', 9);
    
    $test->setValue('parent_int',333);
    $test->setValue('parent_string','CCC');
    $test->setValue('parent_sarray',[30,31,32]);
    $test->setValue('child_int',212);
    $test->setValue('child_string','BCD');
    $test->setValue('child_sarray',[200,210,220]);
    
    $test->setValue('child_sarray',[200,210]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>0,'element'=>30]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>1,'element'=>31]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9,'index'=>2]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>0,'element'=>200]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>9,'index'=>1,'element'=>210]);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>9,'index'=>2]);
});

test('Update a childobject with modified child array (cleared arrays) and unmodified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    setProtectedProperty($test, 'id', 9);
    
    $test->setValue('parent_int',333);
    $test->setValue('parent_string','CCC');
    $test->setValue('parent_sarray',[30,31,32]);
    $test->setValue('child_int',212);
    $test->setValue('child_string','BCD');
    $test->setValue('child_sarray',[200,210,220]);
    
    $test->setValue('child_sarray',[]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>9,'parent_int'=>333,'parent_string'=>'CCC']);
    $this->assertDatabaseHas('childobjects',['id'=>9,'child_int'=>212,'child_string'=>'BCD']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>9]);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>9]);
});

test('Update a childobject with modified parent array (previously empty) and unmodified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    setProtectedProperty($test, 'id', 11);
    
    $test->setValue('parent_int',555);
    $test->setValue('parent_string','EEE');
    $test->setValue('parent_sarray',[]);
    $test->setValue('child_int',232);
    $test->setValue('child_string','DEF');
    $test->setValue('child_sarray',[400,410,420]);
    
    $test->setValue('parent_sarray',[1234,2345,3456]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>11,'parent_int'=>555,'parent_string'=>'EEE']);
    $this->assertDatabaseHas('childobjects',['id'=>11,'child_int'=>232,'child_string'=>'DEF']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>11]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11]);
});

test('Update a childobject with modified child array (parent previously empty) and unmodified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    setProtectedProperty($test, 'id', 11);
    
    $test->setValue('parent_int',555);
    $test->setValue('parent_string','EEE');
    $test->setValue('parent_sarray',[]);
    $test->setValue('child_int',232);
    $test->setValue('child_string','DEF');
    $test->setValue('child_sarray',[400,410,420]);
    
    $test->setValue('child_sarray',[1234,2345,3456]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>11,'parent_int'=>555,'parent_string'=>'EEE']);
    $this->assertDatabaseHas('childobjects',['id'=>11,'child_int'=>232,'child_string'=>'DEF']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>11]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>11,'index'=>1,'element'=>2345]);
});

test('Update a childobject with modified child array (previously empty) and unmodified simple fields', function()
{
    
    $structure =
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    setProtectedProperty($test, 'id', 10);
    
    $test->setValue('parent_int',444);
    $test->setValue('parent_string','DDD');
    $test->setValue('parent_sarray',[40,41,42]);
    $test->setValue('child_int',222);
    $test->setValue('child_string','CDE');
    $test->setValue('child_sarray',[]);
    
    $test->setValue('child_sarray',[1234,2345,3456]);
    
    fillObjectsDataset($test);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>10,'parent_int'=>444,'parent_string'=>'DDD']);
    $this->assertDatabaseHas('childobjects',['id'=>10,'child_int'=>222,'child_string'=>'CDE']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>10]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>10]);
});


it('update fails when a table is missing', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    Schema::drop('parentobjects_parent_sarray');
    
    $test->setValue('parent_int',1509);
    $test->setValue('parent_string','ACDC');
    $test->setValue('parent_sarray',[12,34,56]);
    $test->setValue('child_int',5678);
    $test->setValue('child_string','ACAC');
    $test->setValue('child_sarray',[78,90,12]);
    
    fillObjectsDataset($test);
    
    $test->commit();
})->throws(StorageTableMissingException::class);

