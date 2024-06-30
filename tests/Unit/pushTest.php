<?php

namespace Phico\Tests\View\Blayde;

test('Push is compiled', function () {
    $string = '@push(\'foo\')
test
@endpush';
    $expected = '<?php $__env->startPush(\'foo\'); ?>
test
<?php $__env->stopPush(); ?>';

    expect(blayde()->string($string))->toBe($expected);
});
