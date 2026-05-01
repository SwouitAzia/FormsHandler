<?php

namespace FormsHandler\elements\customform\visual;

use FormsHandler\utils\VisualElementType;

class Label extends VisualElement {
    /**
     * @param string $text
     */
    public function __construct(
        string $text
    ) {
        parent::__construct(VisualElementType::LABEL, $text);
    }
}