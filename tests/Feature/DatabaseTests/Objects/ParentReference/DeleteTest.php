<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\ParentReference;

uses(SunhillDatabaseTestCase::class);

test('delete a ParentReference', function()
{
    ParentReference::prepareDatabase($this);
    $write = new ParentReference();
    
    $write->delete(17);
    
    $this->assertDatabaseMissing('parentreferences',['id'=>17]);
    $this->assertDatabaseMissing('parentreferences_parent_rarray',['container_id'=>17]);
});

