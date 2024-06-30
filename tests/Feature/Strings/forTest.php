<?php

namespace Tests\Unit\Blayde;

test('@for', function ($expect, $count) {

    $in = <<<'BLADE'
@for ($i = 0; $i < $count; $i++)
Loop iteration {{ $i }}
@endfor
BLADE;

    $out = blayde()->string($in, ['count' => $count]);
    expect($out)->toBe($expect);

})->with([
            ["Loop iteration 0Loop iteration 1Loop iteration 2", 3],
            ["", 0],
            ["Loop iteration 0", 1],
            ["Loop iteration 0Loop iteration 1Loop iteration 2Loop iteration 3Loop iteration 4", 5],
        ]);
