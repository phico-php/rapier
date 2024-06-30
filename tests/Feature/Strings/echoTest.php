<?php

namespace Tests\Unit\Blayde;


test('can render string', function () {
    $b = blayde();
    $str = $b->string('<h1>Hello</h1>');
    expect($str)->toBe('<h1>Hello</h1>');
});

test('can render string with variable', function () {
    $b = blayde();
    $str = $b->string('<h1>Hello {{ $name }}</h1>', ['name' => 'Kermit']);
    expect($str)->toBe('<h1>Hello Kermit</h1>');
});

test('can render string with variables', function () {
    $b = blayde();
    $str = $b->string('<h1>Hello {{ $name }}, my how {{ $colour }} you are</h1>', ['name' => 'Kermit', 'colour' => 'green']);
    expect($str)->toBe('<h1>Hello Kermit, my how green you are</h1>');
});

test('can render string with variable or default', function () {
    $b = blayde();
    $str = $b->string('<h1>Hello {{ $name or "good lookin" }}</h1>');
    expect($str)->toBe('<h1>Hello good lookin</h1>');
});

test('with HTML entities', function ($expect, $xss) {

    $in = "<style>{{ \$xss }}</style>";

    $out = blayde()->string($in, ['xss' => $xss]);
    expect($out)->toBe($expect);

})->with([
            // Test case with safe CSS
            ["<style>body { color: black; }</style>", "body { color: black; }"],

            // Test case with unsafe CSS
            ["<style>&lt;script&gt;alert(&#039;XSS&#039;)&lt;/script&gt;</style>", "<script>alert('XSS')</script>"],

            // Test case with mixed safe and unsafe CSS
            ["<style>body { color: black; } &lt;script&gt;alert(&#039;XSS&#039;)&lt;/script&gt;</style>", "body { color: black; } <script>alert('XSS')</script>"],
        ]);


test('without HTML entities', function ($expect, $var) {

    $in = "Content: {!! \$var !!}";

    $out = blayde()->string($in, ['var' => $var]);
    expect($out)->toBe($expect);

})->with([
            // Test case with plain text
            ["Content: Hello, world!", "Hello, world!"],

            // Test case with HTML content
            ["Content: <strong>Bold text</strong>", "<strong>Bold text</strong>"],

            // Test case with script tag (unsafe content)
            ["Content: <script>alert('XSS')</script>", "<script>alert('XSS')</script>"],

            // Test case with mixed content
            ["Content: Hello, <strong>world</strong>!", "Hello, <strong>world</strong>!"],
        ]);
