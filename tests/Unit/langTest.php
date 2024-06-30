<?php

namespace Phico\Tests\View\Blayde;

test('Statement that contains non-consecutive parenthesis are compiled', function () {
    $string = "Foo @lang(function_call('foo(blah)')) bar";
    $expected = "Foo <?php echo app('translator')->getFromJson(function_call('foo(blah)')); ?> bar";

    expect(blayde()->string($string))->toBe($expected);
});

test('Language and choices are compiled', function () {
    expect(blayde()->string("@lang('foo')"))->toBe('<?php echo app(\'translator\')->getFromJson(\'foo\'); ?>');
    expect(blayde()->string("@choice('foo', 1)"))->toBe('<?php echo app(\'translator\')->choice(\'foo\', 1); ?>');
});
