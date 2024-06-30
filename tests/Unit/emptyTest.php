<?php

namespace Phico\Tests\View\Blayde;

test('empty statements are compiled', function () {
    $string = '@empty($test)
breeze
@endempty';
    $expected = '<?php if(empty($test)): ?>
breeze
<?php endif; ?>';

    expect(blayde()->string($string))->toBe($expected);
});
test('empty statements with spaces are compiled', function () {
    $string = '@empty ($test)
breeze
@endempty';
    $expected = '<?php if(empty($test)): ?>
breeze
<?php endif; ?>';

    expect(blayde()->string($string))->toBe($expected);
});
