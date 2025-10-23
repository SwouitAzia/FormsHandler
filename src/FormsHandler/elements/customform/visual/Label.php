<?php

namespace FormsHandler\elements\customform\visual;

class Label extends VisualElement {
    /**
     * @param string $text
     */
    public function __construct(
        string $text
    ) {
        $this->text = $text;
        parent::__construct("label", $this->getText());
    }
}