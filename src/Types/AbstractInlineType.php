<?php

declare(strict_types=1);

namespace Pollen\Asset\Types;

abstract class AbstractInlineType extends AbstractType implements InlineTypeInterface
{
    protected ?string $tag;

    protected string $content;

    /**
     * @param string $content
     * @param array $htmlAttrs
     */
    public function __construct(string $content, array $htmlAttrs = [])
    {
        $this->content = $content;

        parent::__construct($htmlAttrs);
    }

    /**
     * @inheritdoc
     */
    public function render(): string
    {
        return <<<HTML
            <$this->tag {$this->htmlAttrs()}>$this->content</$this->tag>
        HTML;
    }
}