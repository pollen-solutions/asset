<?php

declare(strict_types=1);

namespace Pollen\Asset\Types;

class MetaTagType extends AbstractTagType
{
    protected ?string $tag = 'meta';

    /**
     * @inheritdoc
     */
    public function render(): string
    {
        return "<$this->tag {$this->htmlAttrs()}>";
    }
}