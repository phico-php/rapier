<?php

namespace Phico\Tests\View\Blayde;

test('If statements are compiled', function () {
    $string = '@if (name(foo(bar)))
breeze
@endif';
    $expected = '<?php if(name(foo(bar))): ?>
breeze
<?php endif; ?>';

    expect(blayde()->string($string))->toBe($expected);
});
