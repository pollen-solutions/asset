<?php

declare(strict_types=1);

namespace Pollen\Asset;

use Pollen\Container\ServiceProvider;

class AssetServiceProvider extends ServiceProvider
{
    protected array $services = [
        AssetManagerInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->add(AssetManagerInterface::class, AssetManager::class);
    }
}