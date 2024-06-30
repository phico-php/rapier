<?php

namespace Phico\Tests\View\Blayde;

test('Statement is compiled without options', function () {
    $string = '<div x-data="@js($data)"></div>';
    $expected = '<div x-data="<?php echo js($data); ?>"></div>';

    expect(blayde()->string($string))->toBe($expected);
});

test('Json flags can be set', function () {
    $string = '<div x-data="@js($data, JSON_FORCE_OBJECT)"></div>';
    $expected = '<div x-data="<?php echo js($data, JSON_FORCE_OBJECT); ?>"></div>';

    expect(blayde()->string($string))->toBe($expected);
});

test('Encoding depth can be set', function () {
    $string = '<div x-data="@js($data, JSON_FORCE_OBJECT, 256)"></div>';
    $expected = '<div x-data="<?php echo js($data, JSON_FORCE_OBJECT, 256); ?>"></div>';

    expect(blayde()->string($string))->toBe($expected);
});

