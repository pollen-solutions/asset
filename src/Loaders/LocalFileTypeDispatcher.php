<?php

declare(strict_types=1);

namespace Pollen\Asset\Loaders;

use Pollen\Asset\Types\TagCssType;
use Pollen\Asset\Types\TagJsType;
use Pollen\Asset\Types\TypeInterface;
use Pollen\Support\Filesystem as fs;
use SplFileInfo;

class LocalFileTypeDispatcher
{
    protected SplFileInfo $file;

    protected ?string $basePath = null;

    protected ?string $baseUrl = null;

    protected ?string $baseDir = null;

    /**
     * @param SplFileInfo $file
     * @param string|null $baseDir
     * @param string|null $basePath
     * @param string|null $baseUrl
     */
    public function __construct(
        SplFileInfo $file,
        ?string $basePath = null,
        ?string $baseDir = null,
        ?string $baseUrl = null
    ) {
        $this->file = $file;
        $this->basePath = $basePath;
        $this->baseDir = $baseDir;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return TypeInterface|null
     */
    public function dispatch(): ?TypeInterface
    {
        $path = $this->file->getFilename();

        if ($this->baseDir) {
            $path = preg_replace('#^' . preg_quote($this->baseDir, fs::DS) . '#', '', $this->file->getPath()) . $path;
        }
        if ($this->basePath) {
            $path = rtrim($this->basePath, '/') . "/$path";
        }
        if ($this->baseUrl) {
            $path = rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');
        }

        switch ($this->file->getExtension()) {
            case 'css' :
                return new TagCssType($path);
            case 'js':
                return new TagJsType($path);
            default:
                return null;
        }
    }
}