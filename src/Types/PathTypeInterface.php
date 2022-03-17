<?php

declare(strict_types=1);

namespace Pollen\Asset\Types;

interface PathTypeInterface extends TypeInterface
{
    /**
     * @return string
     */
    public function getPath(): string;
}