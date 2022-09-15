<?php

declare(strict_types=1);

namespace Pollen\Asset\Queues;

interface InFooterQueueInterface extends QueueInterface
{
    /**
     * @return bool
     */
    public function inFooter(): bool;
}