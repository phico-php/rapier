<?php

namespace Tests\Unit\Blayde;

test('@set', function ($expect, $vars) {

    $in = "@set('muppet', 'Kermit')\n{{ \$muppet }}";

    $out = blayde()->string($in, $vars);
    expect($out)->toBe($expect);

})->with([
            ["Kermit", []],
            ["Kermit", ['muppet' => 'Miss Piggy']],
        ]);

