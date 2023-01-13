<?php

declare(strict_types=1);

namespace Pollen\Asset;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use Pollen\Asset\Events\HandleFooterAfter;
use Pollen\Asset\Events\HandleFooterBefore;
use Pollen\Asset\Events\HandleHeadAfter;
use Pollen\Asset\Events\HandleHeadBefore;
use Pollen\Asset\Queues\BaseQueue;
use Pollen\Asset\Queues\CharsetQueue;
use Pollen\Asset\Queues\CssQueue;
use Pollen\Asset\Queues\HtmlQueue;
use Pollen\Asset\Queues\InFooterQueueInterface;
use Pollen\Asset\Queues\InlineCssQueue;
use Pollen\Asset\Queues\InlineJsQueue;
use Pollen\Asset\Queues\JsQueue;
use Pollen\Asset\Queues\LinkQueue;
use Pollen\Asset\Queues\MetaQueue;
use Pollen\Asset\Queues\QueueInterface;
use Pollen\Asset\Queues\TitleQueue;
use Pollen\Asset\Types\HtmlType;
use Pollen\Asset\Types\InlineCssType;
use Pollen\Asset\Types\InlineJsType;
use Pollen\Asset\Types\InlineTitleType;
use Pollen\Asset\Types\MetaTagType;
use Pollen\Asset\Types\TagCssType;
use Pollen\Asset\Types\TagJsType;
use Pollen\Asset\Types\TagLinkType;
use Pollen\Asset\Types\TypeInterface;
use Pollen\Support\Exception\ManagerRuntimeException;
use Pollen\Support\Filesystem as fs;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;

class AssetManager implements AssetManagerInterface
{
    /**
     * Asset Manager main instance.
     * @var static|null
     */
    private static ?AssetManagerInterface $instance = null;

    protected ?ContainerInterface $container = null;

    protected ?EventDispatcherInterface $eventDispatcher = null;

    protected ?string $basePath = null;

    protected ?string $baseUrl = null;

    /**
     * List of inline JS scripts to include in HTML footer.
     * @var string[]
     */
    protected array $footerInlineJs = [];

    /**
     * List of JS global variables to include in HTML footer.
     * @var array
     */
    protected array $footerGlobalJsVars = [];

    /**
     * List of inline JS scripts to include in HTML head.
     * @var string[]
     */
    protected array $headInlineJs = [];

    /**
     * List of JS global variables to include in HTML head.
     * @var array
     */
    protected array $headGlobalJsVars = [];

    /**
     * List of existing HTML head JS global variables names.
     * @var array
     */
    protected array $headJsVarsNames = [];

    /**
     * List of inline CSS styles to include in HTML head.
     * @var string[]
     */
    protected array $inlineCss = [];

    /**
     * List of registered assets.
     * @var array<string, QueueInterface>|array
     */
    protected array $registered = [];

    /**
     * List of queued assets.
     * @var array<string|int, QueueInterface>|array
     */
    protected array $enqueued = [];

    /**
     * List of asset render in HTML head queue.
     * @var array<string, QueueInterface>|null
     */
    private ?array $headQueue = null;

    /**
     * List of asset render in HTML footer queue.
     * @var array<string, string>|null
     */
    private ?array $footerQueue = null;

    /**
     * @param ContainerInterface|null $container
     * @param EventDispatcherInterface|null $eventDispatcher
     */
    public function __construct(
        ?ContainerInterface $container = null,
        ?EventDispatcherInterface $eventDispatcher = null
    ) {
        $this->container = $container;
        $this->eventDispatcher = $eventDispatcher;

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * Get Asset Manager main instance.
     *
     * @return static
     */
    public static function getInstance(): AssetManagerInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new ManagerRuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * @return ContainerInterface|null
     */
    protected function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }

    /**
     * @return EventDispatcherInterface|null
     */
    protected function getEventDispatcher(): ?EventDispatcherInterface
    {
        if (($this->eventDispatcher === null) && ($container = $this->getContainer())) {
            try {
                $this->eventDispatcher = $container->get(EventDispatcherInterface::class);
            } catch (ContainerExceptionInterface $e) {
                unset($e);
                $this->eventDispatcher = null;
            }
        }

        return $this->eventDispatcher;
    }

    /**
     * @param QueueInterface $queue
     *
     * @return string
     */
    private function mapRenderCallback(QueueInterface $queue): string
    {
        return $queue->render();
    }

    /**
     * String normalization.
     *
     * @param string $str
     *
     * @return string
     */
    private function normalizeStr(string $str): string
    {
        return html_entity_decode(rtrim(trim($str), ';'), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Variables normalization.
     *
     * @param array|bool|int|string $vars
     *
     * @return string
     */
    private function normalizeVars($vars): string
    {
        if (is_array($vars)) {
            foreach ($vars as &$v) {
                if (is_scalar($v)) {
                    $v = (is_bool($v) || is_int($v)) ? $v : $this->normalizeStr((string)$v);
                }
            }
            unset($v);

            try {
                $vars = json_encode($vars, JSON_THROW_ON_ERROR);
            } catch (Throwable $e) {
                unset($e);
                $vars = '';
            }
        } elseif (is_scalar($vars)) {
            $vars = (is_bool($vars) || is_int($vars)) ? $vars : "'" . $this->normalizeStr((string)$vars) . "'";
        } else {
            throw new InvalidArgumentException(
                'Type of asset vars are invalid. Only scalar or array of scalar allowed.'
            );
        }

        return (string)$vars;
    }

    /**
     * HTML head inline CSS concatenation.
     *
     * @return string
     */
    protected function concatHeadInlineCss(): string
    {
        $concatCss = '';

        foreach ($this->inlineCss as $inlineCss) {
            $concatCss .= $this->normalizeStr($inlineCss);
        }

        return $concatCss;
    }

    /**
     * HTML head global variables and inline JS concatenation.
     *
     * @return string
     */
    protected function concatHeadInlineJs(): string
    {
        $concatJs = '';

        foreach ($this->headGlobalJsVars as $key => $vars) {
            $this->headJsVarsNames[] = $key;
            $concatJs .= "var $key=" . $this->normalizeVars($vars) . ";";
        }

        foreach ($this->headInlineJs as $inlineJs) {
            $concatJs .= $this->normalizeStr($inlineJs) . ";";
        }

        return $concatJs;
    }

    /**
     * HTML footer global variables and inline JS concatenation.
     *
     * @return string
     */
    protected function concatFooterInlineJs(): string
    {
        $concatJs = '';

        foreach ($this->footerGlobalJsVars as $key => $vars) {
            if (is_array($vars) && in_array($key, $this->headJsVarsNames, true)) {
                foreach ($vars as $k => $v) {
                    $concatJs .= $key . "['$k']=" . $this->normalizeVars($v) . ";";
                }
            } else {
                $concatJs .= "let $key=" . $this->normalizeVars($vars) . ";";
            }
        }

        foreach ($this->footerInlineJs as $inlineJs) {
            $concatJs .= $this->normalizeStr($inlineJs) . ";";
        }

        return $concatJs;
    }

    /**
     * HTML head queue handling.
     *
     * @return void
     */
    protected function handleHeadQueue(): void
    {
        if ($this->headQueue === null) {
            $this->headQueue = [];

            if ($event = $this->getEventDispatcher()) {
                $event->dispatch(new HandleHeadBefore($this));
            }

            if ($inlineCss = $this->concatHeadInlineCss()) {
                $this->enqueueInlineCss($inlineCss, [], InlineCssQueue::NORMAL + 1, '_head-inline-css');
            }

            if ($inlineJs = $this->concatHeadInlineJs()) {
                $this->enqueueInlineJs($inlineJs, [], false, InlineJsQueue::NORMAL + 1, '_head-inline-js');
            }

            $queueCollection = (new Collection($this->enqueued))
                ->filter(
                    function (QueueInterface $queue) {
                        return !$queue instanceof InFooterQueueInterface || !$queue->inFooter();
                    }
                )
                ->sortByDesc(
                    function (QueueInterface $queue) {
                        return $queue->getPriority();
                    }
                );

            /** @var QueueInterface $queue */
            foreach ($queueCollection as $queue) {
                $this->headQueue[$queue->getName()] = $queue;
            }

            if ($event = $this->getEventDispatcher()) {
                $event->dispatch(new HandleHeadAfter($this));
            }
        }
    }

    /**
     * HTML footer queue handling.
     *
     * @return void
     */
    protected function handleFooterQueue(): void
    {
        if ($this->footerQueue === null) {
            $this->footerQueue = [];

            if ($event = $this->getEventDispatcher()) {
                $event->dispatch(new HandleFooterBefore($this));
            }

            if ($inlineJs = $this->concatFooterInlineJs()) {
                $this->enqueueInlineJs($inlineJs, [], true, JsQueue::NORMAL + 1, '_footer-inline-js');
            }

            $queueCollection = (new Collection($this->enqueued))
                ->filter(
                    function (QueueInterface $queue) {
                        return $queue instanceof InFooterQueueInterface ? $queue->inFooter() : false;
                    }
                )
                ->sortByDesc(
                    function (QueueInterface $queue) {
                        return $queue->getPriority();
                    }
                );

            /**
             * @var string $name
             * @var QueueInterface $queue
             */
            foreach ($queueCollection as /*$name =>*/ $queue) {
                $this->footerQueue[] = $queue;
            }

            if ($event = $this->getEventDispatcher()) {
                $event->dispatch(new HandleFooterAfter($this));
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function addGlobalJsVar(
        string $key,
        $value,
        bool $inFooter = false,
        ?string $namespace = 'app'
    ): AssetManagerInterface {
        $argKey = $inFooter ? 'footerGlobalJsVars' : 'headGlobalJsVars';

        if ($namespace === null) {
            $this->{$argKey}[$key] = $value;
        } else {
            $this->{$argKey}[$namespace] = $this->{$argKey}[$namespace] ?? [];
            $this->{$argKey}[$namespace][$key] = $value;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
       return $this->registered;
    }

    /**
     * @inheritDoc
     */
    public function get(string $handleName): ?AssetInterface
    {
        if (!$registered = $this->registered[$handleName] ?? null) {
            return null;
        }
        return $registered;
    }

    /**
     * @inheritDoc
     */
    public function enqueue(QueueInterface $queue): string
    {
        $this->enqueued[$queue->getName()] = $queue;

        return $queue->getName();
    }

    /**
     * @inheritDoc
     */
    public function enqueueType(TypeInterface $type, int $priority = BaseQueue::NORMAL, ?string $name = null): string
    {
        $this->enqueue($queue = new BaseQueue($type, $priority, $name));

        return $queue->getName();
    }

    /**
     * @inheritDoc
     */
    public function enqueueCharset(
        string $charset = 'UTF-8',
        array $htmlAttrs = [],
        int $priority = CharsetQueue::NORMAL,
        ?string $handleName = null
    ): string {
        return $this->enqueue(
            new CharsetQueue(
                new MetaTagType(array_merge($htmlAttrs + ['charset' => $charset])), $priority,
                $handleName ?? '_charset'
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function enqueueTitle(
        string $title,
        array $htmlAttrs = [],
        int $priority = TitleQueue::NORMAL,
        ?string $handleName = null
    ): string {
        return $this->enqueue(
            new TitleQueue(new InlineTitleType($title, $htmlAttrs), $priority, $handleName ?? '_title')
        );
    }

    /**
     * @inheritDoc
     */
    public function enqueueLink(
        string $rel,
        ?string $href = null,
        array $htmlAttrs = [],
        int $priority = LinkQueue::NORMAL,
        ?string $handleName = null
    ): string {
        if ($href !== null) {
            $htmlAttrs['href'] = $href;
        }
        return $this->enqueue(new LinkQueue(new TagLinkType($rel, $htmlAttrs), $priority, $handleName));
    }

    /**
     * @inheritDoc
     */
    public function enqueueMeta(
        ?string $name = null,
        ?string $content = null,
        array $htmlAttrs = [],
        int $priority = MetaQueue::NORMAL,
        ?string $handleName = null
    ): string {
        if ($name !== null) {
            $htmlAttrs['name'] = $name;
        }
        if ($content !== null) {
            $htmlAttrs['content'] = $content;
        }
        return $this->enqueue(new MetaQueue(new MetaTagType($htmlAttrs), $priority, $handleName));
    }

    /**
     * @inheritDoc
     */
    public function enqueueCss(
        string $path,
        array $htmlAttrs = [],
        int $priority = CssQueue::NORMAL,
        ?string $handleName = null
    ): string {
        return $this->enqueue(new CssQueue(new TagCssType($path, $htmlAttrs), $priority, $handleName));
    }

    /**
     * @inheritDoc
     */
    public function enqueueInlineCss(
        string $css,
        array $htmlAttrs = [],
        int $priority = InlineCssQueue::NORMAL,
        ?string $handleName = null
    ): string {
        return $this->enqueue(new InlineCssQueue(new InlineCssType($css, $htmlAttrs), $priority, $handleName));
    }

    /**
     * @inheritDoc
     */
    public function enqueueJs(
        string $path,
        array $htmlAttrs = [],
        bool $inFooter = false,
        int $priority = JsQueue::NORMAL,
        ?string $handleName = null
    ): string {
        return $this->enqueue(new JsQueue(new TagJsType($path, $htmlAttrs), $inFooter, $priority, $handleName));
    }

    /**
     * @inheritDoc
     */
    public function enqueueInlineJs(
        string $js,
        array $htmlAttrs = [],
        bool $inFooter = false,
        int $priority = InlineJsQueue::NORMAL,
        ?string $handleName = null
    ): string {
        return $this->enqueue(new InlineJsQueue(new InlineJsType($js, $htmlAttrs), $inFooter, $priority, $handleName));
    }

    /**
     * @inheritDoc
     */
    public function enqueueHtml(
        string $html,
        bool $inFooter = false,
        int $priority = HtmlQueue::NORMAL,
        ?string $handleName = null
    ): string {
        return $this->enqueue(new HtmlQueue(new HtmlType($html), $inFooter, $priority, $handleName));
    }

    /**
     * @inheritDoc
     */
    public function enqueueRegistered(string $handleName): string
    {
        if (!$registered = $this->registered[$handleName] ?? null) {
            throw new InvalidArgumentException(
                sprintf(
                    'Registered assets with handle name [%s] do not exists. Please register it before.',
                    $handleName
                )
            );
        }

        $type = $registered->getType();

        switch (true) {
            case $type instanceof HtmlType:
                $queue = new HtmlQueue($type, ...$registered->getArgs());
                break;
            case $type instanceof InlineCssType:
                $queue = new InlineCssQueue($type, ...$registered->getArgs());
                break;
            case $type instanceof InlineJsType:
                $queue = new InlineJsQueue($type, ...$registered->getArgs());
                break;
            case $type instanceof InlineTitleType:
                $queue = new TitleQueue($type, ...$registered->getArgs());
                break;
            case $type instanceof MetaTagType:
                $queue = new MetaQueue($type, ...$registered->getArgs());
                break;
            case $type instanceof TagCssType:
                $queue = new CssQueue($type, ...$registered->getArgs());
                break;
            case $type instanceof TagJsType:
                $queue = new JsQueue($type, ...$registered->getArgs());
                break;
            case $type instanceof TagLinkType:
                $queue = new LinkQueue($type, ...$registered->getArgs());
                break;
        }

        if (!isset($queue)) {
            $queue = new BaseQueue($type, ...$registered->getArgs());
        }

        return $this->enqueue($queue);
    }

    /**
     * @inheritDoc
     */
    public function register(string $handleName, TypeInterface $type, ...$args): AssetInterface
    {
        if (isset($this->registered[$handleName])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Another registered assets with handle name [%s] already exists. Please deregister it before.',
                    $handleName
                )
            );
        }

        return $this->registered[$handleName] = new Asset($handleName, $type, ...$args);
    }

    /**
     * @inheritDoc
     */
    public function registerCss(
        string $handleName,
        string $path,
        array $htmlAttrs = [],
        int $priority = CssQueue::NORMAL
    ): AssetInterface {
        return $this->register($handleName, new TagCssType($path, $htmlAttrs), $priority);
    }

    /**
     * @inheritDoc
     */
    public function registerJs(
        string $handleName,
        string $path,
        array $htmlAttrs = [],
        bool $inFooter = false,
        int $priority = JsQueue::NORMAL
    ): AssetInterface {
        return $this->register($handleName, new TagJsType($path, $htmlAttrs), $inFooter, $priority);
    }

    /**
     * @inheritDoc
     */
    public function deregister(string $handleName): AssetInterface
    {
        $registered = $this->registered[$handleName];

        if ($registered) {
            unset($this->registered[$handleName]);
        }

        return $registered;
    }

    /**
     * @inheritDoc
     */
    public function getHead(): string
    {
        $this->handleHeadQueue();

        return $this->headQueue ? implode("\n", array_map([$this, 'mapRenderCallback'], $this->headQueue)) : '';
    }

    /**
     * @inheritDoc
     */
    public function getFooter(): string
    {
        $this->handleFooterQueue();

        return $this->footerQueue ? implode("\n", array_map([$this, 'mapRenderCallback'], $this->footerQueue)) : '';
    }

    /**
     * @inheritDoc
     */
    public function getBasePath(): ?string
    {
        return $this->basePath;
    }

    /**
     * @inheritDoc
     */
    public function setBasePath(string $basePath): AssetManagerInterface
    {
        $this->basePath = fs::normalizePath($basePath);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBaseUrl(): ?string
    {
        return $this->baseUrl;
    }

    /**
     * @inheritDoc
     */
    public function setBaseUrl(string $baseUrl): AssetManagerInterface
    {
        $this->baseUrl = fs::normalizePath($baseUrl);

        return $this;
    }
}
