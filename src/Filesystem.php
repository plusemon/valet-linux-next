<?php

namespace ValetLinuxNext;

use Traversable;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class Filesystem extends SymfonyFilesystem
{
    public function get(string $path)
    {
        return file_get_contents($path);
    }

    public function put(string $path, string $contents)
    {
        return file_put_contents($path, $contents);
    }

    public function ensureDirExists(string $path, int $mode = 0755)
    {
        if (!is_dir($path)) {
            $this->mkdir($path, $mode);
        }
    }

    public function exists(Traversable|array|string $path): bool
    {
        return parent::exists($path);
    }

    public function isLink(string $path): bool
    {
        return is_link($path);
    }
}
