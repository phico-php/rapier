<?php

namespace Phico\Tests\View\Blayde;

test('HasSection statements are compiled', function () {
    $string = '@hasSection("section")
breeze
@endif';
    $expected = '<?php if (! empty(trim($__env->yieldContent("section")))): ?>
breeze
<?php endif; ?>';

    expect(blayde()->string($string))->toBe($expected);
});
