<?php

declare(strict_types=1);

namespace Phico\View\Rapier;

use Phico\View\Rapier\Directive;
use Phico\View\ViewInterface;


class Rapier implements ViewInterface
{
    private Cache $cache;
    private Templates $templates;
    private array $directives;
    private bool $use_cache;


    public function __construct()
    {
        $this->cache = new Cache;
        $this->templates = new Templates;
        $this->directives = [];
        $this->use_cache = true;
    }

    public function useCache(bool $use_cache = true): self
    {
        $this->use_cache = $use_cache;
        return $this;
    }

    public function directive(string $tag, callable $handler): self
    {
        $this->directives[$tag] = new Directive($tag, $handler);
        return $this;
    }
    public function namespaces(array|string $names, string $path = null): self
    {
        $this->templates->namespaces($names, $path);
        return $this;
    }
    public function paths(string $type, array|string $paths): self
    {
        match (strtolower($type)) {
            'cache' => $this->cache->path($paths),
            'template', 'templates', 'view', 'views' => $this->templates->paths($paths),
            default => throw new RapierException(sprintf("Unknown type '%s' in path", $type), 5000),
        };
        return $this;
    }
    public function render(string $template, array $data = [], bool $is_string = false): string
    {
        try {

            $renderer = new Renderer($this->cache, $this->templates, $this->directives, $this->use_cache);

            return $renderer->render($template, $data, $is_string);

        } catch (\Throwable $th) {

            throw new RapierException(sprintf('%s in file %s line %d', $th->getMessage(), $th->getFile(), $th->getLine()), 5050, $th);

        }
    }
    public function string(string $blade, array $data = []): ?string
    {
        return $this->render($blade, $data, true);
    }
    public function template(string $template, array $data): string
    {
        return $this->render($template, $data, false);
    }

}
