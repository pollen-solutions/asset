<?php

declare(strict_types=1);

namespace Pollen\Asset;

use Pollen\Asset\Types\TypeInterface;

class Asset implements AssetInterface
{
    /**
     * @var string
     */
    private string $handleName;

    /**
     * @var TypeInterface
     */
    private TypeInterface $type;

    /**
     * @var array<string, mixed>|null
     */
    private array $args;

    /**
     * @param string $handleName
     * @param TypeInterface $type
     * @param ...$args
     */
    public function __construct(string $handleName, TypeInterface $type, ...$args)
    {
        $this->handleName = $handleName;
        $this->type = $type;
        $this->args = $args;
    }

    /**
     * @inheritDoc
     */
    public function getHandleName(): string
    {
        return $this->handleName;
    }

    /**
     * @inheritDoc
     */
    public function getType(): TypeInterface
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function getArg(string $arg)
    {
        return $this->args[$arg] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getArgs(): array
    {
        return $this->args ?: [];
    }
}
