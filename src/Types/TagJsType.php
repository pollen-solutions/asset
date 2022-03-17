<?php

declare(strict_types=1);

namespace Pollen\Asset\Types;

class TagJsType extends AbstractTagType implements PathTypeInterface
{
    protected ?string $tag = 'script';

    protected bool $singleton = false;

    protected string $path;

    /**
     * @param string $path
     * @param array $htmlAttrs
     */
    public function __construct(string $path, array $htmlAttrs = [])
    {
        $this->path = $path;

        parent::__construct($htmlAttrs);
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        if (empty($this->htmlAttrs['type'])) {
            $this->htmlAttrs['type'] = 'text/javascript';
        }
        $this->htmlAttrs['src'] = $this->path;

        return parent::render();
    }
}
