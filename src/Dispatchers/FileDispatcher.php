<?php

declare(strict_types=1);

namespace Pollen\Asset\Dispatchers;

class FileDispatcher
{
    /**
     * @var string
     */
    private string $path;

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;

    }

    /**
     * @return array
     */
    public function dispatch(): array
    {
        $pathinfo = pathinfo($this->path);

        return ['ext' => $pathinfo['extension'] ?? null, 'path' => $this->path];
    }
}