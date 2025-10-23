<?php

namespace FormsHandler\elements\customform\visual;

class Header extends VisualElement {
    /**
     * @param string $text
     */
    public function __construct(
        string $text
    ) {
        $this->text = $text;
        parent::__construct("header", $this->getText());
    }
}