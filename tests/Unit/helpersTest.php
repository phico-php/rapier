<?php

namespace Phico\Tests\View\Blayde;

test('Echos are compiled', function () {
    expect(blayde()->string('@csrf'))->toBe('<?php echo csrf_field(); ?>');
    expect(blayde()->string("@method('patch')"))->toBe('<?php echo method_field(\'patch\'); ?>');
    expect(blayde()->string('@dd($var1)'))->toBe('<?php dd($var1); ?>');
    expect(blayde()->string('@dd($var1, $var2)'))->toBe('<?php dd($var1, $var2); ?>');
    expect(blayde()->string('@dump($var1, $var2)'))->toBe('<?php dump($var1, $var2); ?>');
});
