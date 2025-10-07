<?php

namespace FormsHandler\elements\customform;

use FormsHandler\elements\types\CustomFormElement;

class Toggle extends CustomFormElement {
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
    public function getDefaultState(): ?bool {
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

        $default = $this->getDefaultState();
        if ($default !== null) {
            $content["default"] = $default;
        }

        return $content;
    }
}