<?php

declare(strict_types=1);

namespace Pollen\Asset\Types;

interface TypeInterface
{
    /**
     * @return string
     */
    public function render(): string;
}