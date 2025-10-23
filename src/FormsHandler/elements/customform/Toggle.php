<?php

namespace FormsHandler\elements\customform;

use FormsHandler\elements\types\CustomFormElement;
use FormsHandler\traits\DefaultValueTrait;

class Toggle extends CustomFormElement {
    use DefaultValueTrait;
    /**
     * @param string $text
     * @param bool|null $default
     * @param string|null $label
     */
    public function __construct(
        protected string $text,
        protected ?bool $default = null,
        protected ?string $label = null
    ) {}

    /**
     * @return string
     */
    public function getText(): string {
        return $this->text;
    }

    /**
     * @return bool|null
     */
    public function getDefaultValue(): ?bool {
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
        $content = [
            "type" => "toggle",
            "text" => $this->getText()
        ];

        $default = $this->getDefaultValue();
        if ($default !== null) {
            $content["default"] = $default;
        }

        return $content;
    }
}