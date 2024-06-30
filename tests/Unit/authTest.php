<?php

namespace Phico\Tests\View\Blayde;

test('auth statements are compiled', function () {
    $string = '@auth("api")
breeze
@endauth';
    $expected = '<?php if (auth("api")): ?>
breeze
<?php endif; ?>';
    expect(blayde()->string($string))->toBe($expected);
});

test('Plain auth statements are compiled', function () {
    $string = '@auth
breeze
@endauth';
    $expected = '<?php if (auth()): ?>
breeze
<?php endif; ?>';
    expect(blayde()->string($string))->toBe($expected);
});
