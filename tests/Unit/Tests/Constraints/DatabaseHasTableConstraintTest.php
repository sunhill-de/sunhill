<?php
/**
 * @file DescriptorHasLinkConstraintTest.php
 * tests the DescriptorHasLinkConstraint
 */
namespace Sunhill\Test\Unit\Constraints;

use Sunhill\Test\Constraints\DatabaseHasTableConstraint;
use Illuminate\Support\Facades\DB;
use Sunhill\Tests\TestCase;

uses(TestCase::class);

test('DatabaseHasTable() fails', function()
{
    DB::statement('drop table if exists testtable');
    $constraint = new DatabaseHasTableConstraint();
    expect($constraint->matches('testtable'))->toBe(false);
});

test('DatabaseHasTable() passes', function()
{
    DB::statement('create table testtable (id int)');
    $constraint = new DatabaseHasTableConstraint();
    expect($constraint->matches('testtable'))->toBe(true);
    DB::statement('drop table if exists testtable');
});
