<?php

declare(strict_types=1);

namespace Pollen\Asset\Types;

use Pollen\Support\Html;

abstract class AbstractType implements HtmlAttrsTypeInterface, TypeInterface
{
    protected array $htmlAttrs = [];

    /**
     * @param array $htmlAttrs
     */
    public function __construct(array $htmlAttrs = [])
    {
        $this->htmlAttrs = $htmlAttrs;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function htmlAttrs(): string
    {
        return Html::attr($this->htmlAttrs);
    }

    /**
     * @inheritDoc
     */
    public function addHtmlAttr(string $key, ?string $value = null): void
    {
        if ($value !== null) {
            $this->htmlAttrs[$key] = $value;
        } else {
            $this->htmlAttrs[] = $key;
        }
    }

    /**
     * @inheritDoc
     */
    public function setHtmlAttrs(array $htmlAttrs): void
    {
        $this->htmlAttrs = $htmlAttrs;
    }

    /**
     * @inheritDoc
     */
    abstract public function render(): string;
}
