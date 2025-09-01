<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tags\Tag;
use Sunhill\Tags\Exceptions\TagIDNotFoundException;
use Sunhill\Tags\Exceptions\TagNameAmbiguousException;
use Sunhill\Tags\Exceptions\TagNameNotFoundException;

uses(SunhillDatabaseTestCase::class);

test('commit with nothing to do (no tags or attributes)', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(5);
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>1,'dummyint'=>123]);
    
})->group('update');

test('commit with nothing to do (tags and attributes)', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(1);
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>1,'dummyint'=>123]);
    
})->group('update');

test('modify a dummy (no tags or attributes)', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(5);
    $test->dummyint = 20;
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>5,'dummyint'=>20]);
})->group('update');

test('modify a dummy (tags and attributes)', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(1);
    $test->dummyint = 20;
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>1,'dummyint'=>20]);
})->group('update');

test('modify the object data of a dummy', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(1);
    $test->_read_cap = 'READERS';
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>1,'dummyint'=>123]);
    $this->assertDatabaseHas('objects',['id'=>1,'_read_cap'=>'READERS']);
})->group('update');

test('add a tag by id to a previously untagged dummy', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(6);
    
    $test->_tags[] = 3;

    $test->commit();
    
    $this->assertDatabaseHas('tagobjectassigns',['container_id'=>6,'tag_id'=>3]);
});

it('fails when adding an unknown tag id', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(6);
    
    $test->_tags[] = 999;
})->throws(TagIDNotFoundException::class);

test('add a tag by name to a previously untagged dummy', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(6);
    
    $test->_tags[] = 'TagC';
    
    $test->commit();
    
    $this->assertDatabaseHas('tagobjectassigns',['container_id'=>6,'tag_id'=>3]);
});

it('fails when adding an unknown tag name', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(6);
    
    $test->_tags[] = 'unknown';
})->throws(TagNameNotFoundException::class);

it('fails when adding an amiguous tag name', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(6);
    
    $test->_tags[] = 'TagA';
})->throws(TagNameAmbiguousException::class);

test('add a tag by tagobject to a previously untagged dummy', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(6);
    
    $tag = new Tag(3);
    $test->_tags[] = $tag;
    
    $test->commit();
    
    $this->assertDatabaseHas('tagobjectassigns',['container_id'=>6,'tag_id'=>3]);
});

test('add some tags to a previously untagged dummy', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(6);
    
    $test->_tags[] = 3;
    $test->_tags[] = 4;
    
    $test->commit();
    
    $this->assertDatabaseHas('tagobjectassigns',['container_id'=>6,'tag_id'=>3]);
    $this->assertDatabaseHas('tagobjectassigns',['container_id'=>6,'tag_id'=>4]);
});

test('append a tag to a dummy', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(1);
    
    $test->_tags[] = 3;
    
    $test->commit();
    
    $this->assertDatabaseHas('tagobjectassigns',['container_id'=>1,'tag_id'=>1]);
    $this->assertDatabaseHas('tagobjectassigns',['container_id'=>1,'tag_id'=>3]);    
});

test('append a tag to a dummy using add()', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(1);
    
    $test->_tags->add(3);
    
    $test->commit();
    
    $this->assertDatabaseHas('tagobjectassigns',['container_id'=>1,'tag_id'=>1]);
    $this->assertDatabaseHas('tagobjectassigns',['container_id'=>1,'tag_id'=>3]);
});

test('append a duplicate tag to a dummy', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(1);
    
    $test->_tags[] = 1;
    
    $test->commit();
    
    $this->assertDatabaseHas('tagobjectassigns',['container_id'=>1,'tag_id'=>1]);
});

test('append more tags to a dummy', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(1);
    
    $test->_tags[] = 3;
    $test->_tags[] = 5;
    
    $test->commit();
    
    $this->assertDatabaseHas('tagobjectassigns',['container_id'=>1,'tag_id'=>1]);
    $this->assertDatabaseHas('tagobjectassigns',['container_id'=>1,'tag_id'=>3]);
    $this->assertDatabaseHas('tagobjectassigns',['container_id'=>1,'tag_id'=>5]);
});

test('remove only tag from a dummy', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(1);
    
    $test->_tags->remove(1);
    
    $test->commit();
    
    $this->assertDatabaseMissing('tagobjectassigns',['container_id'=>1]);    
});

test('overwrite one tag from a dummy', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(2);
    
    $test->_tags[0] = 5;
    
    $test->commit();
    
    $this->assertDatabaseMissing('tagobjectassigns',['container_id'=>2,'tag_id'=>1]);
    $this->assertDatabaseHas('tagobjectassigns',['container_id'=>2,'tag_id'=>3]);
    $this->assertDatabaseHas('tagobjectassigns',['container_id'=>2,'tag_id'=>5]);
});

test('remove one tag from a dummy', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(2);
    
    $test->_tags->remove(3);
    
    $test->commit();
    
    $this->assertDatabaseMissing('tagobjectassigns',['container_id'=>2,'tag_id'=>3]);
    $this->assertDatabaseHas('tagobjectassigns',['container_id'=>2,'tag_id'=>1]);
});

test('clear tags from a dummy', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(6);
    
    $test->_tags->clear();
    $this->assertDatabaseMissing('tagobjectassigns',['container_id'=>6]);
});

test('Add a new attribute to a dummy', function()
{
    
})->skip();

test('Overwrite attribute of a dummy', function()
{
    
})->skip();

test('Add an additional attribute to a dummy', function()
{
    
})->skip();

test('Remove attribute from a dummy', function()
{
    
})->skip();

it('Fails when assigning a wrong type to an attribute', function()
{
    
})->skip();

