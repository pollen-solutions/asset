<?php

declare(strict_types=1);

namespace Pollen\Asset\Queues;

use Pollen\Asset\Types\InlineCssType;

class InlineCssQueue extends BaseQueue
{
    public const NORMAL = 65;

    /**
     * @param InlineCssType $type
     * @param int $priority
     * @param string|null $name
     */
    public function __construct(InlineCssType $type, int $priority = self::NORMAL, ?string $name = null)
    {
        parent::__construct($type, $priority, $name);
    }
}