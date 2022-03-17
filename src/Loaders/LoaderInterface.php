<?php

declare(strict_types=1);

namespace Pollen\Asset\Loaders;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Pollen\Asset\Types\TypeInterface;

interface LoaderInterface extends ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @param TypeInterface $loaded
     *
     * @return void
     */
    public function addLoaded(TypeInterface $loaded): void;

    /**
     * @return array<TypeInterface>
     */
    public function load(): array;
}