<?php

namespace Phico\Tests\View\Blayde;

test('csrf displays with default var', function () {
    expect(blayde()->string('@csrf'))->toBe('<input type="hidden" name="_csrf_token" value="$csrf">');
});
test('csrf displays with user var', function () {
    expect(blayde()->string('@csrf($foo)'))->toBe('<input type="hidden" name="_csrf_token" value="$foo">');
    expect(blayde()->string('@csrf($bar)'))->toBe('<input type="hidden" name="_csrf_token" value="$bar">');
});
test('csrf displays with user var and custom name', function () {
    expect(blayde()->string('@csrf($var, "custom")'))->toBe('<input type="hidden" name="custom" value="$var">');
    expect(blayde()->string('@csrf($var, \'custom\')'))->toBe('<input type="hidden" name="custom" value="$var">');
});
