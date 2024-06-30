<?php

namespace Phico\Tests\View\Blayde;

test('Unless statements without space are compiled', function () {
    $string = '@unless(name(foo(bar)))
breeze
@endunless';
    $expected = '<?php if(!(name(foo(bar)))): ?>
breeze
<?php endif; ?>';

    expect(blayde()->string($string))->toBe($expected);
});

test('Unless statements with space are compiled', function () {
    $string = '@unless (name(foo(bar)))
breeze
@endunless';
    $expected = '<?php if(!(name(foo(bar)))): ?>
breeze
<?php endif; ?>';

    expect(blayde()->string($string))->toBe($expected);
});
