<?php

namespace Phico\Tests\View\Blayde;

test('Shows are compiled', function () {
    expect(blayde()->string('@show'))->toBe('<?php echo $__env->yieldSection(); ?>');
});
