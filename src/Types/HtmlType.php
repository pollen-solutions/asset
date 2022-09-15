<?php

declare(strict_types=1);

namespace Pollen\Asset\Types;

class HtmlType extends AbstractInlineType
{
    /**
     * @inheritdoc
     */
    public function render(): string
    {
        return <<<HTML
            $this->content
        HTML;
    }
}
