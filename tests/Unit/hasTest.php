<?php

namespace Phico\Tests\View\Blayde;

test('has statements are compiled', function () {
    $string = '@has($test)
breeze
@endisset';
    $expected = '<?php if (isset($test) && ! empty($test)): ?>
breeze
<?php endif; ?>';

    expect(blayde()->string($string))->toBe($expected);
});
test('has statements with spaces are compiled', function () {
    $string = '@has ($test)
breeze
@endisset';
    $expected = '<?php if (isset($test) && ! empty($test)): ?>
breeze
<?php endif; ?>';

    expect(blayde()->string($string))->toBe($expected);
});
