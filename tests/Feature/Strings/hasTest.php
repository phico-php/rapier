<?php

namespace Tests\Unit\Blayde;

test('@has', function ($expect, $vars) {

    $in = "@has(\$muppet)\n{{ \$muppet }}\n@else\n{{ 'no muppet' }}\n@endhas";

    $out = blayde()->string($in, $vars);
    expect($out)->toBe($expect);

})->with([
            ["no muppet", []],
            ["no muppet", ['muppet' => '']],
            ["Kermit", ['muppet' => 'Kermit']],
            ["Miss Piggy", ['muppet' => 'Miss Piggy']],
        ]);
