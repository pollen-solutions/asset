<?php

declare(strict_types=1);

namespace Pollen\Asset\Types;

class TagLinkType extends AbstractTagType
{
    protected ?string $tag = 'link';

    /**
     * @see https://www.w3schools.com/tags/tag_link.asp
     */
    protected string $rel;

    /**
     * @param string $rel
     * @param array $htmlAttrs
     */
    public function __construct(string $rel, array $htmlAttrs = [])
    {
        $this->rel = $rel;

        parent::__construct($htmlAttrs);
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->htmlAttrs['rel'] = $this->rel;

        return parent::render();
    }
}
