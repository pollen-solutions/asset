<?php

declare(strict_types=1);

namespace Pollen\Asset\Loaders;

class FileLoader extends Loader
{
    protected ?string $baseDir = null;

    protected ?string $basePath = null;

    protected ?string $baseUrl = null;

    protected ?string $filename = null;

    /**
     * @param string|null $filename
     * @param string|null $basePath
     * @param string|null $baseUrl
     * @param string|null $baseDir
     * @param callable|null $loadCallback
     */
    public function __construct(
        ?string $filename = null,
        ?string $basePath = null,
        ?string $baseUrl = null,
        ?string $baseDir = null,
        ?callable $loadCallback = null
    ) {
        $this->filename = $filename;
        $this->basePath = $basePath;
        $this->baseDir = $baseDir;
        $this->baseUrl = $baseUrl;

        parent::__construct($loadCallback);
    }

    /**
     * @return string|null
     */
    public function getBaseDir(): ?string
    {
        if ($this->baseDir === null && file_exists($this->filename)) {
            $this->baseDir = dirname($this->filename);
        }

        return $this->baseDir;
    }

    /**
     * @param string $baseDir
     *
     * @return void
     */
    public function setBaseDir(string $baseDir): void
    {
        $this->baseDir = $baseDir;
    }

    /**
     * @return string|null
     */
    public function getBasePath(): ?string
    {
        return $this->basePath;
    }

    /**
     * @param string $basePath
     *
     * @return void
     */
    public function setBasePath(string $basePath): void
    {
        $this->basePath = $basePath;
    }

    /**
     * @return string|null
     */
    public function getBaseUrl(): ?string
    {
        return $this->baseUrl;
    }

    /**
     * @param string $baseUrl
     *
     * @return void
     */
    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }
}