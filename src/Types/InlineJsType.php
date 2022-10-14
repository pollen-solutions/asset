<?php

declare(strict_types=1);

namespace Pollen\Asset\Types;

class InlineJsType extends AbstractInlineType
{
    protected ?string $tag = 'script';

    /**
     * @inheritdoc
     */
    public function render(): string
    {
        $this->htmlAttrs['type'] = 'text/javascript';
        $this->content = "/* <![CDATA[ */$this->content/* ]]> */";

        return parent::render();
    }
}