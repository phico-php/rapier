<?php

namespace Tests\Unit\Blayde;

test('@include', function ($expect, $template) {

    $out = blayde()->render($template);
    expect($out)->toBe($expect);

})->with([
            ["This text was included\n\n", 'include/include']
        ]);
