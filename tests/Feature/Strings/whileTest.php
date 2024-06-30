<?php

namespace Tests\Unit\Blayde;

test('@while', function ($expect, $count) {

    $in = <<<'BLADE'
@while($count > 0)
Count is {{ $count }}

@php $count--; @endphp
@endwhile
BLADE;

    $out = blayde()->string($in, ['count' => $count]);
    expect($out)->toBe($expect);

})->with([
            ["Count is 3\nCount is 2\nCount is 1\n", 3],
            ["", 0],
            ["Count is 1\n", 1],
            ["Count is 5\nCount is 4\nCount is 3\nCount is 2\nCount is 1\n", 5],
        ]);
