<?php

namespace Tests\Unit\Blayde;

test('@unless', function ($expect, $condition) {

    $in = "@unless(\$condition)\nCondition is false\n@endunless";

    $out = blayde()->string($in, ['condition' => $condition]);
    expect($out)->toBe($expect);

})->with([
            // Test case where the condition is false
            ["Condition is false\n", false],

            // Test case where the condition is true
            ["", true],

            // Test case with a null condition
            ["Condition is false\n", null],

            // Test case with an empty string condition
            ["Condition is false\n", ""],

            // Test case with a non-empty string condition
            ["", "non-empty string"],

            // Test case with a zero condition
            ["Condition is false\n", 0],

            // Test case with a non-zero condition
            ["", 1],
        ]);
