<?php

namespace Phico\Tests\View\Blayde;

test('Extends are compiled', function () {

    $expected = '<html><head><title>App Name - Child Page Title</title></head><body><div class="sidebar"><p>This is the master sidebar.</p><p>This is appended to the master sidebar.</p></div><div class="container"><p>This is my body content.</p></div></body></html>';

    $out = rapier()->render('child');

    expect(compactHtml($out))->toBe(compactHtml($expected));

});

test('Nested extends are compiled', function () {

    $expected = '<html><head><title>App Name - Grandchild Page Title</title></head><body><div class="sidebar"><p>This is the master sidebar.</p><p>This is appended to the master sidebar.</p></div><div class="container"><p>This is my body content.</p></div></body></html>';

    $out = rapier()->render('grandchild');

    expect(compactHtml($out))->toBe(compactHtml($expected));

});

