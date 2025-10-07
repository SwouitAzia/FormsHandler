<?php

namespace FormsHandler\elements\simpleform;

class Header extends VisualElement {
    public function __construct(
        string $text
    ) {
        $this->text = $text;
        parent::__construct("§h§e§a§d§e§r" . "§r$text");
    }
}