<?php

namespace Phico\Tests\View\Blayde;

test('Break statements are compiled', function () {
    $string = '@for ($i = 0; $i < 10; $i++)
test
@break
@endfor';
    $expected = '<?php $loop = \Phico\Blayde\loop(0, $loop ?? null); for($i = 0; $i < 10; $i++): ?>
test
<?php break; ?>
<?php $loop->increment(); endfor; $loop = $loop->parent(); ?>';

    expect(blayde()->string($string))->toBe($expected);
});

test('Break statements with expression are compiled', function () {
    $string = '@for ($i = 0; $i < 10; $i++)
test
@break(TRUE)
@endfor';
    $expected = '<?php $loop = \Phico\Blayde\loop(0, $loop ?? null); for($i = 0; $i < 10; $i++): ?>
test
<?php if(TRUE) break; ?>
<?php $loop->increment(); endfor; $loop = $loop->parent(); ?>';

    expect(blayde()->string($string))->toBe($expected);
});

test('Break statements with argument are compiled', function () {
    $string = '@for ($i = 0; $i < 10; $i++)
test
@break(2)
@endfor';
    $expected = '<?php $loop = \Phico\Blayde\loop(0, $loop ?? null); for($i = 0; $i < 10; $i++): ?>
test
<?php break 2; ?>
<?php $loop->increment(); endfor; $loop = $loop->parent(); ?>';

    expect(blayde()->string($string))->toBe($expected);
});

test('Break statements with spaced argument are compiled', function () {
    $string = '@for ($i = 0; $i < 10; $i++)
test
@break( 2 )
@endfor';
    $expected = '<?php $loop = \Phico\Blayde\loop(0, $loop ?? null); for($i = 0; $i < 10; $i++): ?>
test
<?php break 2; ?>
<?php $loop->increment(); endfor; $loop = $loop->parent(); ?>';

    expect(blayde()->string($string))->toBe($expected);
});

test('Break statements with faulty argument are compiled', function () {
    $string = '@for ($i = 0; $i < 10; $i++)
test
@break(-2)
@endfor';
    $expected = '<?php $loop = \Phico\Blayde\loop(0, $loop ?? null); for($i = 0; $i < 10; $i++): ?>
test
<?php break 1; ?>
<?php $loop->increment(); endfor; $loop = $loop->parent(); ?>';

    expect(blayde()->string($string))->toBe($expected);
});
