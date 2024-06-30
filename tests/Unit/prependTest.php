<?php

namespace Phico\Tests\View\Blayde;

test('Prepend is compiled', function () {
    $string = '@prepend(\'foo\')
bar
@endprepend';
    $expected = '<?php $__env->startPrepend(\'foo\'); ?>
bar
<?php $__env->stopPrepend(); ?>';

    expect(blayde()->string($string))->toBe($expected);
});
