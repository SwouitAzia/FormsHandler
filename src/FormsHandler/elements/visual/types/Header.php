<?php

namespace FormsHandler\elements\visual\types;

use FormsHandler\elements\visual\VisualElement;
use FormsHandler\elements\visual\VisualElementType;

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