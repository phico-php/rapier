<?php

namespace Phico\Tests\View\Blayde;

test('Comments are compiled', function () {
    $string = '{{--this is a comment--}}';
    expect(blayde()->string($string))->toBeEmpty();

    $string = '{{--
this is a comment
--}}';
    expect(blayde()->string($string))->toBeEmpty();

    $string = sprintf('{{-- this is an %s long comment --}}', str_repeat('extremely ', 1000));
    expect(blayde()->string($string))->toBeEmpty();
});

test('Blade code inside comments is not compiled', function () {
    $string = '{{-- @foreach() --}}';
    expect(blayde()->string($string))->toBeEmpty();
});
