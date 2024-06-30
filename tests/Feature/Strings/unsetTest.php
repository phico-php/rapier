<?php

namespace Tests\Unit\Blayde;

test('@unset', function ($expect, $vars) {

    $in = "@isset(\$muppet)set\n@endisset\n@unset(\$muppet)\n@isset(\$muppet)still set\n@endisset\n";

    $out = blayde()->string($in, $vars);
    expect($out)->toBe($expect);

})->with([
            ["", []],
            ["set\n", ['muppet' => 'Kermit']],
        ]);
