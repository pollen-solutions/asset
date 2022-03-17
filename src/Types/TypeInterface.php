<?php

declare(strict_types=1);

namespace Pollen\Asset\Types;

interface TypeInterface
{
    /**
     * @return string
     */
    public function htmlAttrs(): string;

    /**
     * @param string $key
     * @param string|null $value
     *
     * @return void
     */
    public function addHtmlAttr(string $key, ?string $value = null): void;

    /**
     * @param array $htmlAttrs
     *
     * @return void
     */
    public function setHtmlAttrs(array $htmlAttrs): void;

    /**
     * @return string
     */
    public function render(): string;
}