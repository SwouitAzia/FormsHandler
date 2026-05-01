<?php

namespace FormsHandler\elements\modalform;

use FormsHandler\elements\FormElement;
use FormsHandler\elements\types\ModalElement;

class Button extends FormElement implements ModalElement {
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