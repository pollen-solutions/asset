<?php

declare(strict_types=1);

namespace Pollen\Asset\Queues;

use Pollen\Asset\Types\InlineTitleType;

class TitleQueue extends BaseQueue
{
    public const NORMAL = 80;

    /**
     * @param InlineTitleType $type
     * @param int $priority
     * @param string|null $name
     */
    public function __construct(InlineTitleType $type, int $priority = self::NORMAL, ?string $name = null)
    {
        parent::__construct($type, $priority, $name);
    }
}