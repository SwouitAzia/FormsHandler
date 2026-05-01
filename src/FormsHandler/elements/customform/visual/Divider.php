<?php

namespace FormsHandler\elements\customform\visual;

use FormsHandler\utils\VisualElementType;

class Divider extends VisualElement {
    public function __construct() {
        parent::__construct(VisualElementType::HEADER, "");
    }
}