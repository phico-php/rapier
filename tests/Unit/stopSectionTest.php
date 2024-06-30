<?php

namespace Phico\Tests\View\Blayde;

test('Stop sections are compiled', function () {
    expect(blayde()->string('@stop'))->toBe('<?php $__env->stopSection(); ?>');
});
