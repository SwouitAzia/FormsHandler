<?php

namespace FormsHandler\elements\customform;

use FormsHandler\elements\types\CustomFormElement;
use FormsHandler\utils\traits\DefaultValueTrait;

class Toggle extends CustomFormElement {
    use DefaultValueTrait;

    /**
     * @param string $text
     * @param bool|null $default
     * @param string|null $label
     */
    public function __construct(
        string $text,
        ?bool $default = null,
        ?string $label = null
    ) {
        parent::__construct($text, $default, $label);
    }

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
     * @return array{type: string, text: string, default: bool}
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