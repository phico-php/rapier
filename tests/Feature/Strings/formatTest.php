<?php

namespace Tests\Unit\Blayde;

use Phico\View\ViewException;

test('can render @format', function ($expect, $in) {
    $out = blayde()->string($in);
    expect($out)->toBe($out);
})->with([
            ["This String", "@format ('This %s', 'String')"],
            ["99 red balloons", '@format("%d %s %s", 99, "red", "balloons")']
        ]);
