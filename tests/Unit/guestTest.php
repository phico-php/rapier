<?php

namespace Phico\Tests\View\Blayde;

test('guest statements are compiled', function () {
    $string = '@guest("api")
breeze
@endguest';
    $expected = '<?php if (auth("api", "guest")): ?>
breeze
<?php endif; ?>';
    expect(blayde()->string($string))->toBe($expected);
});

