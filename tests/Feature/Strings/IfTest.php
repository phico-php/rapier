<?php

namespace Tests\Unit\Blayde;

test('can render @if (single quotes)', function ($expect, $vars) {

    $in = "@if (\$muppet == 'Kermit')
true
@endif
@if (\$muppet != 'Kermit')
false
@endif
";

    $out = blayde()->string($in, $vars);
    expect($out)->toBe($expect);

})->with([
            ["false\n", ['muppet' => 'Fozzy Bear']],
            ["true\n", ['muppet' => 'Kermit']]
        ]);

test('can render if statement with double quotes', function ($expect, $vars) {

    $in = '@if ($muppet == "Kermit")
true
@endif
@if ($muppet != "Kermit")
false
@endif';

    $out = blayde()->string($in, $vars);
    expect($out)->toBe($expect);

})->with([
            ["false\n", ['muppet' => 'Gonzo']],
            ["true\n", ['muppet' => 'Kermit']]
        ]);

