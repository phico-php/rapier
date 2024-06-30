<?php

namespace Phico\Tests\View\Blayde;

test('Overwrite sections are compiled', function () {
    expect(blayde()->string('@overwrite'))->toBe('<?php $__env->stopSection(true); ?>');
});
