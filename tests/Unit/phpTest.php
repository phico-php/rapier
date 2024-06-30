<?php

namespace Phico\Tests\View\Blayde;

test('Php statements with expression are compiled', function () {
    $string = '@php($set = true)';
    $expected = '<?php ($set = true); ?>';
    expect(blayde()->string($string))->toBe($expected);
});

test('Php statements without expression are ignored', function () {
    $string = '@php';
    $expected = '@php';
    expect(blayde()->string($string))->toBe($expected);

    $string = '{{ "Ignore: @php" }}';
    $expected = '<?php echo e("Ignore: @php"); ?>';
    expect(blayde()->string($string))->toBe($expected);
});

test('Php statements do not parse Blade code', function () {
    $string = '@php echo "{{ This is a blade tag }}" @endphp';
    $expected = '<?php echo "{{ This is a blade tag }}" ?>';
    expect(blayde()->string($string))->toBe($expected);
});

test('Verbatim and Php statements do not get mixed up', function () {
    $string = "@verbatim {{ Hello, I'm not blade! }}"
        . "\n@php echo 'And I'm not PHP!' @endphp"
        . "\n@endverbatim {{ 'I am Blade' }}"
        . "\n@php echo 'I am PHP {{ not Blade }}' @endphp";

    $expected = " {{ Hello, I'm not blade! }}"
        . "\n@php echo 'And I'm not PHP!' @endphp"
        . "\n <?php echo e('I am Blade'); ?>"
        . "\n\n<?php echo 'I am PHP {{ not Blade }}' ?>";

    expect(blayde()->string($string))->toBe($expected);
});
