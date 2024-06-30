<?php

namespace Phico\Tests\View\Blayde;

test('While statements are compiled', function () {
    $string = '@while ($foo)
test
@endwhile';
    $expected = '<?php $loop = loop(count($foo), $loop ?? null); while ($foo): ?>
test
<?php $loop->increment(); endwhile; $loop = $loop->parent(); ?>';
    expect(blayde()->string($string))->toBe($expected);
});

test('Nested while statements are compiled', function () {
    $string = '@while ($foo)
@while ($bar)
test
@endwhile
@endwhile';
    $expected = '<?php $loop = loop(count($foo), $loop ?? null); while ($foo): ?>
<?php $loop = loop(count($bar), $loop ?? null); while ($bar): ?>
test
<?php $loop->increment(); endwhile; $loop = $loop->parent(); ?>
<?php $loop->increment(); endwhile; $loop = $loop->parent(); ?>';
    expect(blayde()->string($string))->toBe($expected);
});
