<?php

use Sunhill\Framework\Tests\Responses\SampleAssembleResponse;
use Sunhill\Framework\Plugins\Exceptions\FileNotFoundException;

uses(\Sunhill\Framework\Tests\TestCase::class);

test('Response assembles two files', function() 
{
    clearTemp();
    file_put_contents(getTemp().'/fileA', 'AAA');
    file_put_contents(getTemp().'/fileB', 'BBB');
    $test = new SampleAssembleResponse();
    $test->addFile(getTemp().'/fileA');
    $test->addFile(getTemp().'/fileB');
    expect($test->getResponse())->toBe('AAABBB');
});

it('Throws exception when file does not exist', function()
{
    $test = new SampleAssembleResponse();
    $test->addFile('doesnotexists');
})->throws(FileNotFoundException::class);

it('Throws exception when dir does not exist', function()
{
    $test = new SampleAssembleResponse();
    $test->addDir('doesnotexists');
})->throws(FileNotFoundException::class);

test('Response assembles two files with line break', function()
{
    clearTemp();
    file_put_contents(getTemp().'/fileA', "AAA\n");
    file_put_contents(getTemp().'/fileB', "BBB\n");
    $test = new SampleAssembleResponse();
    $test->addFile(getTemp().'/fileA');
    $test->addFile(getTemp().'/fileB');
    expect($test->getResponse())->toBe("AAA\nBBB\n");
});

test('Response assembles a directory', function()
{
    clearTemp();
    file_put_contents(getTemp().'/fileB', 'BBB');
    file_put_contents(getTemp().'/fileA', 'AAA');
    $test = new SampleAssembleResponse();
    $test->addDir(getTemp());
    expect($test->getResponse())->toBe("AAABBB");
});
