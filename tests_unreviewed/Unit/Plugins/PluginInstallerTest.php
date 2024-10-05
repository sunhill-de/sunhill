<?php

namespace Sunhill\Tests\Unit\Plugins;

use Sunhill\Tests\TestCase;
use Sunhill\Plugins\Plugin;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Sunhill\Plugins\Exceptions\PluginRootDirDoesntExistException;
use Sunhill\Plugins\Exceptions\FileNotFoundException;
use Sunhill\Tests\Unit\Plugins\Testplugins\TestPluginInstaller;

uses(TestCase::class);

test('getStorageDir() default', function()
{
    $plugin = \Mockery::mock(Plugin::class);
    $plugin->shouldReceive('getName')->once()->andReturn('testplugin');
    $test = new TestPluginInstaller();
    $test->setOwner($plugin);
    expect($test->getStorageDir())->toBe(storage_path('plugins/testplugin'));
});

test('setStorageDir() overrides default', function()
{
    $test = new TestPluginInstaller();
    $test->setStorageDir('some/dir');
    expect($test->getStorageDir())->toBe('some/dir');
});

test('Directory is created', function() 
{
    clearTemp();   
    $test = new TestPluginInstaller();
    $test->setStorageDir(getTemp());
    callProtectedMethod($test, 'createDir',['test']);
    expect(file_exists(getTemp().'/test'))->toBe(true);
});

test('Directory is renamed', function()
{
    clearTemp();
    $test = new TestPluginInstaller();
    $test->setStorageDir(getTemp());
    mkdir(getTemp().'/test');
    callProtectedMethod($test, 'renameDir',['test','newtest']);
    expect(file_exists(getTemp().'/test'))->toBe(false);
    expect(file_exists(getTemp().'/newtest'))->toBe(true);
});

it("fails when directory doesn't exist", function()
{
    $test = new TestPluginInstaller();
    $test->setStorageDir(getTemp());
    callProtectedMethod($test, 'renameDir',['nonexisting','newtest']);    
})->throws(FileNotFoundException::class);

test('Dirctory is removed', function() 
{
    clearTemp();
    $test = new TestPluginInstaller();
    $test->setStorageDir(getTemp());
    mkdir(getTemp().'/test');
    callProtectedMethod($test, 'deleteDir',['test']);
    expect(file_exists(getTemp().'/test'))->toBe(false);
});

test('File is created', function() 
{
    clearTemp();
    $test = new TestPluginInstaller();
    $test->setStorageDir(getTemp());
    callProtectedMethod($test, 'createFile', ['testfile.txt','Test content']);
    expect(file_exists(getTemp().'/testfile.txt'))->toBe(true);
});
        
test('File is renamed', function()
{
    clearTemp();
    $test = new TestPluginInstaller();
    $test->setStorageDir(getTemp());
    file_put_contents(getTemp().'/testfile.txt','Test');
    callProtectedMethod($test, 'renameFile', ['testfile.txt','newfile.txt']);
    expect(file_exists(getTemp().'/testfile.txt'))->toBe(false);    
    expect(file_exists(getTemp().'/newfile.txt'))->toBe(true);
});

test('File is removed', function()
{
    clearTemp();
    $test = new TestPluginInstaller();
    $test->setStorageDir(getTemp());
    file_put_contents(getTemp().'/testfile.txt','Test');
    callProtectedMethod($test, 'deleteFile', ['testfile.txt']);
    expect(file_exists(getTemp().'/testfile.txt'))->toBe(false);    
});

test('table is created', function()
{
    Schema::dropIfExists('testtable');
    $test = new TestPluginInstaller();
    callProtectedMethod($test,'createTable',['testtable', function(Blueprint $table)
    {
       $table->integer('id');
       $table->string('name',10);
    }]);
    expect(Schema::hasTable('testtable'))->toBe(true);
    expect(Schema::hasColumn('testtable', 'name'))->toBe(true);
});

test('table is modified', function()
{
    Schema::dropIfExists('testtable');
    Schema::create('testtable',function(Blueprint $table) 
    {
        $table->integer('id');
        $table->string('name',10);        
    });
    $test = new TestPluginInstaller();
    callProtectedMethod($test,'modifyTable',['testtable', function(Blueprint $table)
    {
        $table->renameColumn('name','name_id');
    }]);
    expect(Schema::hasColumn('testtable', 'name_id'))->toBe(true);
    expect(Schema::hasColumn('testtable', 'name'))->toBe(false);
});

test('table is removed', function()
{
    Schema::dropIfExists('testtable');
    Schema::create('testtable',function(Blueprint $table)
    {
        $table->integer('id');
        $table->string('name',10);
    });
    $test = new TestPluginInstaller();
    callProtectedMethod($test,'deleteTable',['testtable']);
    expect(Schema::hasTable('testtable'))->toBe(false);    
});

/*
test('Collection is created', function()
{
    
});

test('Collection is modified', function()
{
    
});

test('Collection is deleted', function()
{
    
});

test('Object is created', function()
{
    
});

test('Object is modified', function()
{
    
});

test('Object is deleted', function()
{
    
}); */
