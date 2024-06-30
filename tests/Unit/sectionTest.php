<?php

namespace Phico\Tests\View\Blayde;

test('Section starts are compiled', function () {
    expect(blayde()->string('@section(\'foo\')'))->toBe('<?php $__env->startSection(\'foo\'); ?>');
    expect(blayde()->string('@section(name(foo))'))->toBe('<?php $__env->startSection(name(foo)); ?>');
});
