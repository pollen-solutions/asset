<?php

declare(strict_types=1);

namespace Pollen\Asset\Queues;

use Pollen\Asset\Types\TypeInterface;

class BaseInFooterQueue extends BaseQueue implements InFooterQueueInterface
{
    /**
     * @var bool
     */
    protected bool $inFooter = false;

    /**
     * @param TypeInterface $type
     * @param bool $inFooter
     * @param int $priority
     * @param string|null $name
     */
    public function __construct(TypeInterface $type, bool $inFooter, int $priority = self::NORMAL, ?string $name = null)
    {
        $this->inFooter = $inFooter;

        parent::__construct($type, $priority, $name);
    }

    /**
     * @inheritDoc
     */
    public function inFooter(): bool
    {
        return $this->inFooter;
    }
}


