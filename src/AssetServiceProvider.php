<?php

declare(strict_types=1);

namespace Pollen\Asset;

use Pollen\Container\BootableServiceProvider;
use Pollen\Event\EventDispatcherInterface;
use Pollen\Kernel\Events\ConfigLoadEvent;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class AssetServiceProvider extends BootableServiceProvider
{
    protected $provides = [
        AssetManagerInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        try {
            /** @var EventDispatcherInterface $event */
            if ($event = $this->getContainer()->get(EventDispatcherInterface::class)) {
                $event->subscribeTo('config.load', static function (ConfigLoadEvent $event) {
                    if (is_callable($config = $event->getConfig('asset'))) {
                        $config($event->getApp()->get(AssetManager::class), $event->getApp());
                    }
                });
            }
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            unset($e);
        }
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(AssetManagerInterface::class, AssetManager::class);
    }
}