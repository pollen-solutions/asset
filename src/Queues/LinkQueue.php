<?php

declare(strict_types=1);

namespace Pollen\Asset\Queues;

use Pollen\Asset\Types\TagLinkType;

class LinkQueue extends BaseQueue
{
    public const NORMAL = 65;

    /**
     * @param TagLinkType $type
     * @param int $priority
     * @param string|null $name
     */
    public function __construct(TagLinkType $type, int $priority = self::NORMAL, ?string $name = null)
    {
        parent::__construct($type, $priority, $name);
    }
}