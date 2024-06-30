<?php

declare(strict_types=1);

namespace Phico\View\Rapier;

function e(null|string $str = null): string
{
    return htmlentities((string) $str, ENT_QUOTES);
}
function js(null|string $str = null): string
{
    return (string) $str;
}
function loop(int $count, Loop $parent = null): Loop
{
    return new Loop($count, $parent);
}
