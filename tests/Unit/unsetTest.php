<?php

namespace Phico\Tests\View\Blayde;

test('Unset statements are compiled', function () {
    $string = '@unset ($unset)';
    $expected = '<?php unset($unset); ?>';

    expect(blayde()->string($string))->toBe($expected);
});
