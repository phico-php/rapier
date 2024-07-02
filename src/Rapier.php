<?php

declare(strict_types=1);

namespace Phico\View\Rapier;

use Phico\View\{Cache, Templates, ViewException, ViewInterface};


class Rapier implements ViewInterface
{
    private Renderer $renderer;
    private array $options = [
        'use_cache' => false,
        'cache_path' => 'storage/views',
        'view_paths' => [],
        'namespaces' => [],
        'directives' => [],
    ];


    public function __construct(array $config = [])
    {
        // apply default options, overriding with user config
        foreach ($this->options as $k => $v) {
            $this->options[$k] = (isset($config[$k])) ? $config[$k] : $v;
        }

        // init renderer instance
        $this->renderer = new Renderer(
            new Cache($this->options['cache_path'], $this->options['use_cache']),
            new Templates($this->options['view_paths'], $this->options['namespaces'], '%s.blade.php'),
            $this->options['directives'],
            $this->options['use_cache']
        );

    }

    // public function directive(string $tag, callable $handler): self
    // {
    //     $this->directives[$tag] = new Directive($tag, $handler);
    //     return $this;
    // }
    // public function namespaces(array|string $names, string $path = null): self
    // {
    //     $this->templates->namespaces($names, $path);
    //     return $this;
    // }
    // public function paths(string $type, array|string $paths): self
    // {
    //     match (strtolower($type)) {
    //         'cache' => $this->cache->path($paths),
    //         'template', 'templates', 'view', 'views' => $this->templates->paths($paths),
    //         default => throw new RapierException(sprintf("Unknown type '%s' in path", $type), 5000),
    //     };
    //     return $this;
    // }
    public function render(string $template, array $data = [], bool $is_string = false): string
    {
        try {

            return $this->renderer->render($template, $data, $is_string);

        } catch (\Throwable $th) {

            throw new ViewException(sprintf('%s in file %s line %d', $th->getMessage(), $th->getFile(), $th->getLine()), 5050, $th);

        }
    }
    public function string(string $blade, array $data = []): ?string
    {
        return $this->render($blade, $data, true);
    }
    public function template(string $template, array $data = []): string
    {
        return $this->render($template, $data, false);
    }

}
