<?php

declare(strict_types=1);

namespace Pollen\Asset;

use Pollen\Asset\Types\TypeInterface;

interface AssetInterface
{
    /**
     * @return string
     */
    public function getHandleName(): string;

    /**
     * @return TypeInterface
     */
    public function getType(): TypeInterface;

    /**
     * @param string $arg
     *
     * @return mixed|null
     */
    public function getArg(string $arg);

    /**
     * @return array
     */
    public function getArgs(): array;
}
