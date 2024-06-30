<?php

namespace Tests\Unit\Blayde;

test('Comments', function ($expect, $in) {

    $out = blayde()->string($in);
    expect($out)->toBe($expect);

})->with([
            // Single line comment
            ["\n", "{{-- This is a single line comment --}}\n"],

            // Multi-line comment
            ["\n", "{{--\nThis is a multi-line comment\nSpanning multiple lines\n--}}\n"],

            // Comment within HTML
            ["<div>\n\n</div>\n", "<div>\n{{-- This is a comment inside HTML --}}\n</div>\n"],

            // Comment with Blade variables
            ["Hello World ", "Hello {{ 'World' }} {{-- This comment has a Blade variable --}}"],

            // Comment with control structures
            ["Yes\n ", "@if (true)\nYes\n@endif {{-- This comment has a control structure --}}"],

            // Commented out Blade code
            ["This should not be rendered \n", "{{ 'This should not be rendered' }} {{-- This is commented out Blade code --}}\n"],
        ]);
