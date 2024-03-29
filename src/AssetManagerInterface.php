<?php

declare(strict_types=1);

namespace Pollen\Asset;

use Pollen\Asset\Queues\BaseQueue;
use Pollen\Asset\Queues\CharsetQueue;
use Pollen\Asset\Queues\CssQueue;
use Pollen\Asset\Queues\HtmlQueue;
use Pollen\Asset\Queues\InlineCssQueue;
use Pollen\Asset\Queues\InlineJsQueue;
use Pollen\Asset\Queues\JsQueue;
use Pollen\Asset\Queues\LinkQueue;
use Pollen\Asset\Queues\MetaQueue;
use Pollen\Asset\Queues\QueueInterface;
use Pollen\Asset\Queues\TitleQueue;
use Pollen\Asset\Types\TypeInterface;

interface AssetManagerInterface
{
    /**
     * @param string $key
     * @param $value
     * @param bool $inFooter
     * @param string|null $namespace
     *
     * @return AssetManagerInterface
     */
    public function addGlobalJsVar(
        string $key,
        $value,
        bool $inFooter = false,
        ?string $namespace = 'app'
    ): AssetManagerInterface;

    /**
     * @return array<string, AssetInterface>|array
     */
    public function all(): array;

    /**
     * @param string $handleName
     *
     * @return AssetInterface|null
     */
    public function get(string $handleName): ?AssetInterface;

    /**
     * @param QueueInterface $queue
     *
     * @return string
     */
    public function enqueue(QueueInterface $queue): string;

    /**
     * @param TypeInterface $type
     * @param int $priority
     * @param string|null $name
     *
     * @return string
     */
    public function enqueueType(TypeInterface $type, int $priority = BaseQueue::NORMAL, ?string $name = null): string;

    /**
     * @param string $charset
     * @param array $htmlAttrs
     * @param int $priority
     * @param string|null $handleName
     *
     * @return string
     */
    public function enqueueCharset(
        string $charset = 'UTF-8',
        array $htmlAttrs = [],
        int $priority = CharsetQueue::NORMAL,
        ?string $handleName = null
    ): string;

    /**
     * @param string $title
     * @param array $htmlAttrs
     * @param int $priority
     * @param string|null $handleName
     *
     * @return string
     */
    public function enqueueTitle(
        string $title,
        array $htmlAttrs = [],
        int $priority = TitleQueue::NORMAL,
        ?string $handleName = null
    ): string;

    /**
     * @param string $rel
     * @param string|null $href
     * @param array $htmlAttrs
     * @param int $priority
     * @param string|null $handleName
     *
     * @return string
     */
    public function enqueueLink(
        string $rel,
        ?string $href = null,
        array $htmlAttrs = [],
        int $priority = LinkQueue::NORMAL,
        ?string $handleName = null
    ): string;

    /**
     * @param string|null $name
     * @param string|null $content
     * @param array $htmlAttrs
     * @param int $priority
     * @param string|null $handleName
     *
     * @return string
     */
    public function enqueueMeta(
        ?string $name = null,
        ?string $content = null,
        array $htmlAttrs = [],
        int $priority = MetaQueue::NORMAL,
        ?string $handleName = null
    ): string;

    /**
     * @param string $path
     * @param array $htmlAttrs
     * @param int $priority
     * @param string|null $handleName
     *
     * @return string
     */
    public function enqueueCss(
        string $path,
        array $htmlAttrs = [],
        int $priority = CssQueue::NORMAL,
        ?string $handleName = null
    ): string;

    /**
     * @param string $css
     * @param array $htmlAttrs
     * @param int $priority
     * @param string|null $handleName
     *
     * @return string
     */
    public function enqueueInlineCss(
        string $css,
        array $htmlAttrs = [],
        int $priority = InlineCssQueue::NORMAL,
        ?string $handleName = null
    ): string;

    /**
     * @param string $path
     * @param array $htmlAttrs
     * @param bool $inFooter
     * @param int $priority
     * @param string|null $handleName
     *
     * @return string
     */
    public function enqueueJs(
        string $path,
        array $htmlAttrs = [],
        bool $inFooter = false,
        int $priority = JsQueue::NORMAL,
        ?string $handleName = null
    ): string;

    /**
     * @param string $js
     * @param array $htmlAttrs
     * @param bool $inFooter
     * @param int $priority
     * @param string|null $handleName
     *
     * @return string
     */
    public function enqueueInlineJs(
        string $js,
        array $htmlAttrs = [],
        bool $inFooter = false,
        int $priority = InlineJsQueue::NORMAL,
        ?string $handleName = null
    ): string;

    /**
     * @param string $html
     * @param bool $inFooter
     * @param int $priority
     * @param string|null $handleName
     *
     * @return string
     */
    public function enqueueHtml(
        string $html,
        bool $inFooter = false,
        int $priority = HtmlQueue::NORMAL,
        ?string $handleName = null
    ): string;

    /**
     * @param string $handleName
     *
     * @return string
     */
    public function enqueueRegistered(string $handleName): string;

    /**
     * @param string $handleName
     * @param TypeInterface $type
     * @param ...$args
     *
     * @return AssetInterface
     */
    public function register(string $handleName, TypeInterface $type, ...$args): AssetInterface;

    /**
     * @param string $handleName
     *
     * @return AssetInterface
     */
    public function deregister(string $handleName): AssetInterface;

    /**
     * @param string $handleName
     * @param string $path
     * @param array $htmlAttrs
     * @param int $priority
     *
     * @return AssetInterface
     */
    public function registerCss(
        string $handleName,
        string $path,
        array $htmlAttrs = [],
        int $priority = CssQueue::NORMAL
    ): AssetInterface;

    /**
     * @param string $handleName
     * @param string $path
     * @param array $htmlAttrs
     * @param bool $inFooter
     * @param int $priority
     *
     * @return AssetInterface
     */
    public function registerJs(
        string $handleName,
        string $path,
        array $htmlAttrs = [],
        bool $inFooter = false,
        int $priority = JsQueue::NORMAL
    ): AssetInterface;

    /**
     * @return string
     */
    public function getHead(): string;

    /**
     * @return string
     */
    public function getFooter(): string;

    /**
     * @return string|null
     */
    public function getBasePath(): ?string;

    /**
     * @param string $basePath
     *
     * @return AssetManagerInterface
     */
    public function setBasePath(string $basePath): AssetManagerInterface;

    /**
     * @return string|null
     */
    public function getBaseUrl(): ?string;

    /**
     * @param string $baseUrl
     *
     * @return AssetManagerInterface
     */
    public function setBaseUrl(string $baseUrl): AssetManagerInterface;
}