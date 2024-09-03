<?php

use Sunhill\Framework\Tests\TestCase;

uses(TestCase::class);

test('entry with link is rendered', function()
{
    expect(view('framework::components.optional_link',['entry'=>makeStdClass(['link'=>'http://example.com','title'=>'example'])])->render())
    ->toContain('<a href="http://example.com">example</a>');
});

test('entry without link is rendered', function()
{
    expect(view('framework::components.optional_link',['entry'=>makeStdClass(['title'=>'example'])])->render())
    ->toContain('example');    
});

test('template with optional-link tag is rendered', function()
{
    expect(view('framework::test.optionallink', [
        'link'=>makeStdClass(['link'=>'http://example.com','title'=>'example']), 
        'sitename'=>'test'
    ])->render())->toContain('<a href="http://example.com">example</a>');
});

test('entry with link as array is rendered', function()
{
    expect(view('framework::test.optionallink',[
        'link'=>['link'=>'http://example.com','title'=>'example'],
        'sitename'=>'test'
    ])->render())
    ->toContain('<a href="http://example.com">example</a>');
});

test('entry with link single assoc array is rendered', function()
{
    expect(view('framework::test.optionallink',[
        'link'=>['example'=>'http://example.com'],
        'sitename'=>'test'        
    ])->render())
    ->toContain('<a href="http://example.com">example</a>');
});


test('template without optional-link tag is rendered', function()
{
    expect(view('framework::test.optionallink',[
        'link'=>makeStdClass(['title'=>'example']),
        'sitename'=>'test'        
    ])->render())->toContain('example');
    
});

test('entry without link (only title) is rendered', function()
{
    expect(view('framework::test.optionallink',[
        'link'=>'example',
        'sitename'=>'test'
    ])->render())
    ->toContain('example');
});

