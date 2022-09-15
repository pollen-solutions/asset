<?php

declare(strict_types=1);

namespace Pollen\Asset\Queues;

use Pollen\Asset\Types\InlineJsType;

class InlineJsQueue extends BaseInFooterQueue
{
    public const NORMAL = 55;

    /**
     * @param InlineJsType $type
     * @param bool $inFooter
     * @param int $priority
     * @param string|null $name
     */
    public function __construct(
        InlineJsType $type,
        bool $inFooter = false,
        int $priority = self::NORMAL,
        ?string $name = null
    ) {
        parent::__construct($type, $inFooter, $priority, $name);
    }
}