<?php

declare(strict_types=1);

namespace Pollen\Asset\Types;

class StaticType implements PathTypeInterface
{
    protected string $path;

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @inheritdoc
     */
    public function render(): string
    {
       return '';
    }
}
