<?php

namespace FormsHandler\elements\modalform;

use FormsHandler\elements\types\ModalFormElement;

class Button extends ModalFormElement {
    public function __construct(
        protected string $text
    ) {}

    /**
     * @return string
     */
    public function getText(): string {
        return $this->text;
    }

    /**
     * @return string
     */
    public function jsonSerialize(): string {
        return $this->getText();
    }
}