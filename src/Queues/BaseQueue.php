<?php

declare(strict_types=1);

namespace Pollen\Asset\Queues;

use Pollen\Asset\Types\TypeInterface;

class BaseQueue implements QueueInterface
{
    /**
     * High priority.
     * @const int
     */
    public const HIGH = 100;

    /**
     * Normal priority.
     * @const int
     */
    public const NORMAL = 0;

    /**
     * Low priority.
     * @const int
     */
    public const LOW = -100;

    protected ?string $name;

    protected TypeInterface $type;

    protected bool $inFooter = false;

    protected int $priority;

    /**
     * @param TypeInterface $type
     * @param int $priority
     * @param string|null $name
     */
    public function __construct(TypeInterface $type, int $priority = self::NORMAL, ?string $name = null)
    {
        $this->type = $type;
        $this->priority = $priority;
        $this->name = $name;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        if ($this->name === null) {
            $this->name = sha1((string)$this->type);
        }
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @inheritDoc
     */
    public function inFooter(): bool
    {
        return $this->inFooter;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return $this->type->render();
    }
}


