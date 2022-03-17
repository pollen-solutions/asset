<?php

declare(strict_types=1);

namespace Pollen\Asset\Queues;

interface QueueInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return int
     */
    public function getPriority(): int;

    /**
     * @return bool
     */
    public function inFooter(): bool;

    /**
     * @return string
     */
    public function render(): string;
}