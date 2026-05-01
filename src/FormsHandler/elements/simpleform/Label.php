<?php

namespace FormsHandler\elements\simpleform;

class Label extends VisualElement {
    /**
     * @param string $text
     */
    public function __construct(
        string $text
    ) {
        $this->text = $text;
        parent::__construct("§l§a§b§e§l" . "§r$text");
    }
}