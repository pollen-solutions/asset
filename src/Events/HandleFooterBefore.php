<?php

declare(strict_types=1);

namespace Pollen\Asset\Events;

use League\Event\HasEventName;
use Pollen\Asset\AssetManagerInterface;

class HandleFooterBefore implements HasEventName
{
    protected AssetManagerInterface $assetManager;

    /**
     * @param AssetManagerInterface $assetManager
     */
    public function __construct(AssetManagerInterface $assetManager)
    {
        $this->assetManager = $assetManager;
    }

    public function eventName(): string
    {
        return 'asset.handle.footer.before';
    }

    /**
     * @return AssetManagerInterface
     */
    public function getAssetManager(): AssetManagerInterface
    {
        return $this->assetManager;
    }
}