<?php

declare(strict_types=1);

namespace Pollen\Asset\Types;

class InlineCssType extends AbstractInlineType
{
    protected ?string $tag = 'style';

    /**
     * @inheritdoc
     */
    public function render(): string
    {
        $this->htmlAttrs['type'] = 'text/css';

        return parent::render();
    }
}