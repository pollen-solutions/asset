<?php

declare(strict_types=1);

namespace Pollen\Asset\Queues;

use Pollen\Asset\Types\HtmlType;

class HtmlQueue extends BaseInFooterQueue
{
    public const HIGH = 100;

    public const NORMAL = 0;

    public const LOW = -100;

    protected bool $inFooter = true;

    /**
     * @param HtmlType $type
     * @param bool $inFooter
     * @param int $priority
     * @param string|null $name
     */
    public function __construct(
        HtmlType $type,
        bool $inFooter = false,
        int $priority = self::NORMAL,
        ?string $name = null
    ) {
        parent::__construct($type, $inFooter, $priority, $name);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        if ($this->name === null) {
            $this->name = sha1($this->type->render());
        }
        return $this->name;
    }
}