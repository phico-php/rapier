<?php

namespace Phico\Tests\View\Blayde;

test('Eachs are compiled', function () {
    expect(blayde()->string('@each(\'foo\', \'bar\')'))->toBe('<?php echo $__env->renderEach(\'foo\', \'bar\'); ?>');
    expect(blayde()->string('@each(name(foo))'))->toBe('<?php echo $__env->renderEach(name(foo)); ?>');
});

test('Includes are compiled', function () {
    expect(blayde()->string('@include(\'foo\')'))->toBe('<?php echo $__env->make(\'foo\', \Illuminate\Support\Arr::except(get_defined_vars(), [\'__data\', \'__path\']))->render(); ?>');
    expect(blayde()->string('@include(name(foo))'))->toBe('<?php echo $__env->make(name(foo), \Illuminate\Support\Arr::except(get_defined_vars(), [\'__data\', \'__path\']))->render(); ?>');
});

test('IncludeIfs are compiled', function () {
    expect(blayde()->string('@includeIf(\'foo\')'))->toBe('<?php if ($__env->exists(\'foo\')) echo $__env->make(\'foo\', \Illuminate\Support\Arr::except(get_defined_vars(), [\'__data\', \'__path\']))->render(); ?>');
    expect(blayde()->string('@includeIf(name(foo))'))->toBe('<?php if ($__env->exists(name(foo))) echo $__env->make(name(foo), \Illuminate\Support\Arr::except(get_defined_vars(), [\'__data\', \'__path\']))->render(); ?>');
});

test('IncludeWhens are compiled', function () {
    expect(blayde()->string('@includeWhen(true, \'foo\', ["foo" => "bar"])'))->toBe('<?php echo $__env->renderWhen(true, \'foo\', ["foo" => "bar"], \Illuminate\Support\Arr::except(get_defined_vars(), [\'__data\', \'__path\'])); ?>');
    expect(blayde()->string('@includeWhen(true, \'foo\')'))->toBe('<?php echo $__env->renderWhen(true, \'foo\', \Illuminate\Support\Arr::except(get_defined_vars(), [\'__data\', \'__path\'])); ?>');
});

test('IncludeFirsts are compiled', function () {
    expect(blayde()->string('@includeFirst(["one", "two"])'))->toBe('<?php echo $__env->first(["one", "two"], \Illuminate\Support\Arr::except(get_defined_vars(), [\'__data\', \'__path\']))->render(); ?>');
    expect(blayde()->string('@includeFirst(["one", "two"], ["foo" => "bar"])'))->toBe('<?php echo $__env->first(["one", "two"], ["foo" => "bar"], \Illuminate\Support\Arr::except(get_defined_vars(), [\'__data\', \'__path\']))->render(); ?>');
});
