<?php

declare(strict_types=1);

namespace Pollen\Asset\Types;

class TagCssType extends AbstractTagType implements PathTypeInterface
{
    protected ?string $tag = 'link';

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
     * @inheritdoc
     */
    public function render(): string
    {
        $this->htmlAttrs['rel'] = 'stylesheet';
        $this->htmlAttrs['type'] = 'text/css';
        $this->htmlAttrs['href'] = $this->path;

        return parent::render();
    }
}
