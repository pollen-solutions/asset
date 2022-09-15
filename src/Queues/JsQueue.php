<?php

declare(strict_types=1);

namespace Pollen\Asset\Queues;

use Pollen\Asset\Types\TagJsType;

class JsQueue extends BaseInFooterQueue
{
    public const NORMAL = 60;

    /**
     * @param TagJsType $type
     * @param bool $inFooter
     * @param int $priority
     * @param string|null $name
     */
    public function __construct(
        TagJsType $type,
        bool $inFooter = false,
        int $priority = self::NORMAL,
        ?string $name = null
    ) {
        parent::__construct($type, $inFooter, $priority, $name);
    }
}