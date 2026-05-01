<?php

namespace FormsHandler\elements\customform\visual;

use FormsHandler\utils\VisualElementType;

class Header extends VisualElement {
    /**
     * @param string $text
     */
    public function __construct(
        string $text
    ) {
        parent::__construct(VisualElementType::HEADER, $text);
    }
}