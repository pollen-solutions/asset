<?php

declare(strict_types=1);

namespace Pollen\Asset\Queues;

use Pollen\Asset\Types\TagCssType;

class CssQueue extends BaseQueue
{
    public const NORMAL = 70;

    /**
     * @param TagCssType $type
     * @param int $priority
     * @param string|null $name
     */
    public function __construct(TagCssType $type, int $priority = self::NORMAL, ?string $name = null)
    {
        parent::__construct($type, $priority, $name);
    }
}