<?php

namespace FormsHandler\elements\visual\types;

use FormsHandler\elements\visual\VisualElement;
use FormsHandler\elements\visual\VisualElementType;

class Divider extends VisualElement {
    public function __construct() {
        parent::__construct(VisualElementType::DIVIDER, "");
    }
}