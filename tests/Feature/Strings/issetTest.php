<?php

namespace Tests\Unit\Blayde;

test('@isset', function ($expect, $vars) {

    $in = "@isset(\$muppet)\ntrue\n@endisset";

    $out = blayde()->string($in, $vars);
    expect($out)->toBe($expect);

})->with([
            ["", []],
            ["true\n", ['muppet' => 'Kermit']]
        ]);
