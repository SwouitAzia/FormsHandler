<?php

namespace FormsHandler\elements\customform;

use FormsHandler\elements\types\CustomFormElement;
use FormsHandler\traits\DefaultValueTrait;

class Input extends CustomFormElement {
    use DefaultValueTrait;
    /**
     * @param string $text
     * @param string $placeholder
     * @param string|null $default
     * @param string|null $label
     */
    public function __construct(
        protected string $text,
        protected string $placeholder = "",
        protected ?string $default = null,
        protected ?string $label = null
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
    public function getPlaceholder(): string {
        return $this->placeholder;
    }

    /**
     * @return string|null
     */
    public function getDefaultValue(): ?string {
        return $this->default;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string {
        return $this->label;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array {
        return [
            "type" => "input",
            "text" => $this->getText(),
            "placeholder" => $this->getPlaceholder(),
            "default" => $this->getDefaultValue()
        ];
    }
}