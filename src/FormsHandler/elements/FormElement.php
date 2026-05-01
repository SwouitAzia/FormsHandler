<?php

namespace FormsHandler\elements;

use JsonSerializable;

abstract class FormElement implements JsonSerializable {
    /** @var string $text */
    protected string $text;

    /**
     * @param string $text
     */
    public function __construct(
        string $text
    ) {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText(): string {
        return $this->text;
    }
}