<?php

namespace FormsHandler\elements\customform;

use FormsHandler\elements\types\CustomFormElement;

abstract class VisualElement extends CustomFormElement {
    /**
     * @param string $type
     * @param string $text
     */
    public function __construct(
        protected string $type,
        protected string $text
    ) {}

    /**
     * @return string
     */
    public function getText(): string {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }


    /**
     * @return string|null
     */
    public function getLabel(): ?string {
        return null;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array {
        return [
            "type" => $this->getType(),
            "text" => $this->getText()
        ];
    }
}