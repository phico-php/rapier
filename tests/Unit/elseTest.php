<?php

namespace Phico\Tests\View\Blayde;

test('Else statements are compiled', function () {
    $string = '@if (name(foo(bar)))
breeze
@else
boom
@endif';
    $expected = '<?php if (name(foo(bar))): ?>
breeze
<?php else: ?>
boom
<?php endif; ?>';

    expect(blayde()->string($string))->toBe($expected);
});

test('ElseIf statements are compiled', function () {
    $string = '@if (name(foo(bar)))
breeze
@elseif(boom(breeze))
boom
@endif';
    $expected = '<?php if (name(foo(bar))): ?>
breeze
<?php elseif (boom(breeze)): ?>
boom
<?php endif; ?>';

    expect(blayde()->string($string))->toBe($expected);
});
