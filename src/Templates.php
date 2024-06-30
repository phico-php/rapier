<?php

declare(strict_types=1);

namespace Phico\View\Rapier;


class Templates
{
    private array $paths = [];
    private array $spaces = [];


    public function get(string $tpl): string
    {
        // swap dots for slashes
        $tpl = str_replace('.', '/', $tpl);
        if ($this->hasNamespace($tpl)) {
            list($name, $file) = $this->splitNamespace($tpl);
            $folder = $this->spaces[$name];
            if (!files(path("$folder/$file.blade.php"))->exists()) {
                throw new RapierException(sprintf("Cannot find template '%s'", $tpl), 5100);
            }
            return files(path("$folder/$file.blade.php"))->read();
        }

        foreach ($this->paths as $path) {
            $file = files(path("$path/$tpl.blade.php"));
            if ($file->exists()) {
                return $file->read();
            }
        }

        throw new RapierException(sprintf("Cannot find template file '%s.blade.php' in '%s'", $tpl, join(', ', $this->paths)), 5101);
    }
    public function has(string $tpl): bool
    {
        if ($this->hasNamespace($tpl)) {
            list($name, $file) = $this->splitNamespace($tpl);
            $folder = $this->spaces[$name];
            return files(path("$folder/$file.blade.php"))->exists();
        }

        $file = "$tpl.blade.php";
        foreach ($this->paths as $path) {
            if (files(path("$path/$file.blade.php"))->exists()) {
                return true;
            }
        }

        return false;
    }
    // ->namespace('admin', 'app/Admin/views')
    public function namespaces(array|string $names, string $path = null): void
    {
        if (is_array($names)) {
            foreach ($names as $name => $path) {
                $this->spaces[$name] = $path;
            }
            return;
        }

        $this->spaces[$names] = $path;
    }
    // ->path('app/views');
    // ->path(['app/views', 'app/errors']);
    public function paths(array|string $paths): void
    {
        $paths = (is_array($paths)) ? $paths : [$paths];
        foreach ($paths as $path) {
            $this->paths[] = $path;
        }
    }


    private function hasNamespace(string $path): bool
    {
        return str_contains($path, '::');
    }
    private function splitNamespace(string $path): array
    {
        list($name, $file) = explode('::', $path, 2);
        if (!isset($this->spaces[$name])) {
            throw new RapierException(sprintf("Cannot process template for unknown namespace '%s'", $name), 5102);
        }

        return [$name, $file];
    }
}
