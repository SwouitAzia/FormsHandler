<?php

namespace FormsHandler\elements\customform;

use FormsHandler\elements\types\CustomFormElement;
use FormsHandler\traits\DefaultValueTrait;

class Dropdown extends CustomFormElement {
    use DefaultValueTrait;
    public function __construct(
        protected string $text,
        protected array $options,
        protected ?int $default = null,
        protected ?string $label = null
    ) {}

    /**
     * @return string
     */
    public function getText(): string {
        return $this->text;
    }

    /**
     * @return array
     */
    public function getOptions(): array {
        return $this->options;
    }

    /**
     * @return int|null
     */
    public function getDefaultValue(): ?int {
        return $this->default;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string {
        return $this->label;
    }

    public function jsonSerialize(): array {
        return [
            "type" => "dropdown",
            "text" => $this->getText(),
            "options" => $this->getOptions(),
            "default" => $this->getDefaultValue()
        ];
    }
}