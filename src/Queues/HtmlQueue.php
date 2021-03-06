<?php

declare(strict_types=1);

namespace Pollen\Asset\Queues;

class HtmlQueue implements QueueInterface
{
    public const HIGH = 100;

    public const NORMAL = 0;

    public const LOW = -100;

    private ?string $name;

    protected string $html;

    protected bool $inFooter = true;

    protected int $priority;

    /**
     * @param string $html
     * @param bool $inFooter
     * @param int $priority
     * @param string|null $name
     */
    public function __construct(
        string $html,
        bool $inFooter = false,
        int $priority = self::NORMAL,
        ?string $name = null
    ) {
        $this->html = $html;
        $this->inFooter = $inFooter;
        $this->priority = $priority;
        $this->name = $name;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        if ($this->name === null) {
            $this->name = sha1($this->html);
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
     * @return bool
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
        return $this->html;
    }
}