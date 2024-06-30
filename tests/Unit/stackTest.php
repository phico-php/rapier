<?php

namespace Phico\Tests\View\Blayde;

test('Stack is compiled', function () {
    $string = '@stack(\'foo\')';
    $expected = '<?php echo $__env->yieldPushContent(\'foo\'); ?>';

    expect(blayde()->string($string))->toBe($expected);
});
