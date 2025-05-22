<?php

use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;

function prepareStorage(string $classname, $test_suite): MysqlObjectStorage
{
    $test = new MysqlObjectStorage();
    $test->setStructure($classname::getExpectedStructure());
    $classname::prepareDatabase($test_suite);
    return $test;
}

function prepareObjectDataset(MysqlObjectStorage &$storage, string $classname)
{
    $storage->setValue('_classname',$classname);
    $storage->setValue('_uuid','11b47be8-05f1-4f7b-8a97-e1e6488dbd44');
    $storage->setValue('_read_cap', null);
    $storage->setValue('_write_cap', null);
    $storage->setValue('_modify_cap', null);
    $storage->setValue('_delete_cap', null);
    $storage->setValue('_created_at', '2024-11-14 20:00:00');
    $storage->setValue('_updated_at', '2024-11-14 20:00:00');
    $storage->setValue('_tags',[]);
    $storage->setValue('_attributes',[]);    
}

function pretendLoaded(MysqlObjectStorage &$storage, int $id, string $class)
{
    setProtectedProperty($storage, 'id', $id);
    foreach ($class::getExpectedData($id) as $key => $value) {
        $storage->setValue($key, $value);
    }
}

function checkResultArray($result, $expected): bool
{
    expect(count($result))->toBe(count($expected));
    foreach ($result as $single) {
        expect($single->id)->toBeIn($expected);
    }
    return true;    
}