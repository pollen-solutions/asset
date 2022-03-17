<?php

declare(strict_types=1);

namespace Pollen\Asset\Types;

abstract class AbstractTagType extends AbstractType implements TagTypeInterface
{
    protected ?string $tag;

    protected bool $singleton = true;

    /**
     * @inheritdoc
     */
    public function render(): string
    {
        return $this->singleton
            ? <<<HTML
                <$this->tag {$this->htmlAttrs()}> 
                HTML
            : <<<HTML
                <$this->tag {$this->htmlAttrs()}></$this->tag>
                HTML;
    }
}