<?php

namespace Phico\Tests\View\Blayde;

test('isset statements are compiled', function () {
    $string = '@isset ($test)
breeze
@endisset';
    $expected = '<?php if(isset($test)): ?>
breeze
<?php endif; ?>';

    expect(blayde()->string($string))->toBe($expected);
});
