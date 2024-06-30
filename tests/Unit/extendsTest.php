<?php

namespace Phico\Tests\View\Blayde;

test('Extends are compiled', function () {
    $string = '@extends(\'foo\')
test';
    $expected = 'test' . PHP_EOL . '<?php echo $__env->make(\'foo\', \Illuminate\Support\Arr::except(get_defined_vars(), [\'__data\', \'__path\']))->render(); ?>';
    expect(blayde()->string($string))->toBe($expected);

    $string = '@extends(name(foo))' . PHP_EOL . 'test';
    $expected = 'test' . PHP_EOL . '<?php echo $__env->make(name(foo), \Illuminate\Support\Arr::except(get_defined_vars(), [\'__data\', \'__path\']))->render(); ?>';
    expect(blayde()->string($string))->toBe($expected);
});

test('Sequential compile string calls', function () {
    $string = '@extends(\'foo\')
test';
    $expected = 'test' . PHP_EOL . '<?php echo $__env->make(\'foo\', \Illuminate\Support\Arr::except(get_defined_vars(), [\'__data\', \'__path\']))->render(); ?>';
    expect(blayde()->string($string))->toBe($expected);

    // Use the same compiler instance to compile another template with @extends directive
    $string = '@extends(name(foo))' . PHP_EOL . 'test';
    $expected = 'test' . PHP_EOL . '<?php echo $__env->make(name(foo), \Illuminate\Support\Arr::except(get_defined_vars(), [\'__data\', \'__path\']))->render(); ?>';
    expect(blayde()->string($string))->toBe($expected);
});
