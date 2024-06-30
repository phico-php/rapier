<?php

namespace Tests\Unit\Blayde;

test('@forelse', function ($expect, $items) {

    $in = <<<'BLADE'
@forelse($items as $item)
Item: {{ $item }}

@empty
No items found
@endforelse
BLADE;

    $out = blayde()->string($in, ['items' => $items]);
    expect($out)->toBe($expect);

})->with([
            ["Item: A\nItem: B\nItem: C\n", ['A', 'B', 'C']],
            ["No items found\n", []],
            ["Item: 1\nItem: 2\n", [1, 2]],
            ["Item: apple\nItem: banana\n", ['apple', 'banana']],
        ]);
