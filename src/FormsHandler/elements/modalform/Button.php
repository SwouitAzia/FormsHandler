<?php

namespace FormsHandler\elements\modalform;

use FormsHandler\elements\types\ModalFormElement;

class Button extends ModalFormElement {
    /**
     * @param string $text
     */
    public function __construct(
        string $text
    ) {
        parent::__construct($text);
    }

    /**
     * @return string
     */
    public function jsonSerialize(): string {
        return $this->getText();
    }
}