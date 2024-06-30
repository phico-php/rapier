<?php

declare(strict_types=1);

namespace Phico\View\Rapier;

use Phico\Filesystem\FilesystemException;


class Cache
{
    private string $path = '';


    public function delete(string $template): void
    {
        try {
            $file = files($this->filepath($template));
            if ($file->exists()) {
                $file->delete();
            }
        } catch (FilesystemException $e) {
            throw new RapierException(sprintf("Cannot delete cached template '%s'", $template), 5200, $e);
        }
    }
    public function filepath(string $template): string
    {
        return path(sprintf('%s/%s', $this->path, sha1($template)));
    }
    public function get(string $template): ?string
    {
        $file = files($this->filepath($template));
        if ($file->exists()) {
            return $file->read();
        }

        return null;
    }
    public function has(string $template): bool
    {
        return files($this->filepath($template))->exists();
    }
    public function path(string $path): void
    {
        $folder = folders(path($path));
        if (!$folder->exists()) {
            $folder->create(0777);
        }
        $this->path = $path;
    }
    public function put(string $template, string $content): void
    {
        try {
            files($this->filepath($template))->write($content);
        } catch (FilesystemException $e) {
            throw new RapierException(sprintf("Cannot put template '%s' in cache", $template), 5201, $e);
        }
    }
}
