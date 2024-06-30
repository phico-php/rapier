<?php

namespace Phico\Tests\View\Blayde;

test('Statement is compiled with safe default encoding options', function () {
    $string = 'var foo = @json($var);';
    $expected = 'var foo = <?php echo json_encode($var, 15, 512); ?>;';

    expect(blayde()->string($string))->toBe($expected);
});

test('Encoding options can be overwritten', function () {
    $string = 'var foo = @json($var, JSON_HEX_TAG);';
    $expected = 'var foo = <?php echo json_encode($var, JSON_HEX_TAG, 512); ?>;';

    expect(blayde()->string($string))->toBe($expected);
});

test('Depath can be overwritten', function () {
    $string = 'var foo = @json($var, JSON_HEX_TAG, 16);';
    $expected = 'var foo = <?php echo json_encode($var, JSON_HEX_TAG, 16); ?>;';

    expect(blayde()->string($string))->toBe($expected);
});
