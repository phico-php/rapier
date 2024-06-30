<?php

namespace Tests\Unit\Blayde;

test('@foreach', function ($expect, $items) {

    $in = <<<'BLADE'
@foreach($items as $item)
Item: {{ $item }}
@endforeach
BLADE;

    $out = blayde()->string($in, ['items' => $items]);
    expect($out)->toBe($expect);

})->with([
            ["Item: AItem: BItem: C", ['A', 'B', 'C']],
            ["", []],
            ["Item: 1Item: 2", [1, 2]],
            ["Item: appleItem: banana", ['apple', 'banana']],
        ]);
