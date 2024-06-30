<?php

namespace Phico\Tests\View\Blayde;

test('ElseIf statements are compiled', function () {
    $string = '@if(name(foo(bar)))
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

test('ElseIf statements with spaces are compiled', function () {
    $string = '@if (name(foo(bar)))
breeze
@elseif (boom(breeze))
boom
@endif';
    $expected = '<?php if (name(foo(bar))): ?>
breeze
<?php elseif (boom(breeze)): ?>
boom
<?php endif; ?>';

    expect(blayde()->string($string))->toBe($expected);
});
