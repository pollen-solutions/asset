<?php

declare(strict_types=1);

namespace Pollen\Asset\Dispatchers;

use Pollen\Asset\Types\StaticType;
use Pollen\Asset\Types\TagCssType;
use Pollen\Asset\Types\TagJsType;
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
     * @return array
     */
    public function dispatch(): array
    {
        $path = $this->file->getFilename();

        if ($this->baseDir) {
            $path = rtrim(
                    preg_replace('#^' . preg_quote($this->baseDir, fs::DS) . '#', '', $this->file->getPath()), '/'
                ) . "/$path";
        }

        if ($this->basePath) {
            $path = rtrim($this->basePath, '/') . "/$path";
        }

        if ($this->baseUrl) {
            $path = rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');
        }

        $ext = $this->file->getExtension();

        $output = compact('ext', 'path');

        switch ($ext) {
            case 'css' :
                $output['type'] = new TagCssType($path);
                break;
            case 'js':
                $output['type'] = new TagJsType($path);
                break;
            default:
                $output['type'] = new StaticType($path);
                break;
        }

        return $output;
    }
}