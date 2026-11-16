<?php

namespace KamaThumb\Infrastructure\Storage;

use KamaThumb\Domain\Contracts\StorageInterface;

final class FileSystemStorage implements StorageInterface
{
    public function __construct(
        protected string $basePath,
        protected string $baseUrl,
    ) {
        $this->basePath = mb_rtrim($basePath, '/\\');
        $this->baseUrl = mb_rtrim($baseUrl, '/');
    }

    public function exists(string $path): bool
    {
        return file_exists($this->path($path));
    }

    public function put(string $path, string $contents): bool
    {
        $fullPath = $this->path($path);
        $dir = dirname($fullPath);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return file_put_contents($fullPath, $contents) !== false;
    }

    public function get(string $path): string
    {
        $fullPath = $this->path($path);

        if (! $this->exists($path)) {
            return '';
        }

        return file_get_contents($fullPath) ?: '';
    }

    public function delete(string $path): bool
    {
        $fullPath = $this->path($path);

        if (! $this->exists($path)) {
            return false;
        }

        return unlink($fullPath);
    }

    public function url(string $path): string
    {
        return $this->baseUrl . '/' . mb_ltrim($path, '/');
    }

    public function path(string $relativePath): string
    {
        return $this->basePath . '/' . mb_ltrim($relativePath, '/');
    }

    public function clearCache(): int
    {
        if (! is_dir($this->basePath)) {
            return 0;
        }

        $count = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->basePath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                unlink($file->getPathname());
                $count++;
            } elseif ($file->isDir()) {
                rmdir($file->getPathname());
            }
        }

        return $count;
    }
}
