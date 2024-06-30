<?php

namespace Phico\Tests\View\Blayde;

test('Switch statements are compiled', function () {
    $string = '@switch(true)
@case(1)
foo

@case(2)
bar
@endswitch

foo

@switch(true)
@case(1)
foo

@case(2)
bar
@endswitch';
    $expected = '<?php switch(true):
case (1): ?>
foo

<?php case (2): ?>
bar
<?php endswitch; ?>

foo

<?php switch(true):
case (1): ?>
foo

<?php case (2): ?>
bar
<?php endswitch; ?>';

    expect(blayde()->string(compactHtml($string)))->toBe(compactHtml($expected));
});
