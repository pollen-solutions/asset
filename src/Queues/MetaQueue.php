<?php

declare(strict_types=1);

namespace Pollen\Asset\Queues;

use Pollen\Asset\Types\MetaTagType;

class MetaQueue extends BaseQueue
{
    public const NORMAL = 75;

    /**
     * @param MetaTagType $type
     * @param int $priority
     * @param string|null $name
     */
    public function __construct(MetaTagType $type, int $priority = self::NORMAL, ?string $name = null)
    {
        parent::__construct($type, $priority, $name);
    }
}