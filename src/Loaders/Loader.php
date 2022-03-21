<?php

declare(strict_types=1);

namespace Pollen\Asset\Loaders;

use ArrayIterator;
use Pollen\Asset\Types\TypeInterface;

class Loader implements LoaderInterface
{
    /**
     * @var callable|null
     */
    protected $loadCallback;

    /**
     * @var array<TypeInterface>
     */
    protected array $preloaded = [];

    /**
     * @var array<TypeInterface>
     */
    protected array $loaded = [];

    /**
     * @param callable|null $loadCallback
     */
    public function __construct(?callable $loadCallback = null)
    {
        $this->loadCallback = $loadCallback;

        $this->preload();
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->loaded);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->loaded);
    }

    /**
     * @param mixed $offset
     *
     * @return TypeInterface|null
     */
    public function offsetGet($offset): ?TypeInterface
    {
        return $this->loaded[$offset] ?? null;
    }

    /**
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset): void {}

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->loaded[$offset]);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value): void {}

    /**
     * @return void
     */
    protected function preload(): void
    {
        $this->preloaded = [];
    }

    /**
     * @inheritDoc
     */
    public function addLoaded(TypeInterface $loaded): void
    {
        $this->loaded[] = $loaded;
    }

    /**
     * @inheritDoc
     */
    public function load(): array
    {
        if ($callback = $this->loadCallback) {
            $callback($this->preloaded, $this);
        } else {
            $this->loaded = $this->preloaded;
        }

        return $this->loaded;
    }
}