<?php

namespace Phico\Tests\View\Blayde;

test('For statements are compiled', function () {
    $string = '@for ($i = 0; $i < 10; $i++)
test
@endfor';
    $expected = '<?php $loop = loop(0, $loop ?? null); for($i = 0; $i < 10; $i++): ?>
test
<?php $loop->increment(); endfor; $loop = $loop->parent(); ?>';

    expect(blayde()->string($string))->toBe($expected);
});

test('Nested For Statements Are Compiled', function () {
    $string = '@for ($i = 0; $i < 10; $i++)
@for ($j = 0; $j < 20; $j++)
test
@endfor
@endfor';
    $expected = '<?php $loop = loop(0, $loop ?? null); for($i = 0; $i < 10; $i++): ?>
<?php $loop = loop(0, $loop ?? null); for($j = 0; $j < 20; $j++): ?>
test
<?php $loop->increment(); endfor; $loop = $loop->parent(); ?>
<?php $loop->increment(); endfor; $loop = $loop->parent(); ?>';
    expect(blayde()->string($string))->toBe($expected);
});

