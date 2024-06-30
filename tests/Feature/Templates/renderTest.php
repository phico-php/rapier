<?php

namespace Tests\Unit\Blayde;

// test('hello-world.blade.php', function () {

//     $out = blayde()->render("hello-world", [
//         'name' => 'World'
//     ]);
//     expect($out)->toBe("<h1>Hello World</h1>\n");

// });
// test('layouts/child.blade.php', function () {

//     $expect = "<html>
// <head>
//     <title>App Name - Page Title</title>
// </head>
// <body>
//     <p>This replaces the master sidebar.</p>
//     <div class=\"container\">
//         <p>This is my body content.</p>
//     </div>
// </body>
// </html>";

//     $out = blayde()->render("layouts/child", [
//         'name' => 'World'
//     ]);

//     // normalise output as spaces can interfere with checks
//     $out = compactHtml($out);

//     expect($out)->toBe(compactHtml($expect));

// });
test('layouts/child-with-parent.blade.php', function () {

    $expect = "<html>
<head>
    <title>App Name - Page Title</title>
</head>
<body>
    <p>This is the master sidebar.</p>
    <p>This is appended to the master sidebar.</p>
    <div class=\"container\">
        <p>This is my body content.</p>
    </div>
</body>
</html>";

    $out = blayde()->render("layouts/child-with-parent", [
        'name' => 'World'
    ]);

    // normalise output and expect as spaces can interfere with checks
    expect(compactHtml($out))->toBe(compactHtml($expect));

    echo "\n$out\n";

});

