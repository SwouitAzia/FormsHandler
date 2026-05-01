<?php

namespace FormsHandler\elements\customform;

use FormsHandler\elements\FormElement;
use FormsHandler\elements\types\CustomElement;
use FormsHandler\elements\types\CustomElementTrait;
use FormsHandler\utils\label\LabelInterface;

class Toggle extends FormElement implements CustomElement, LabelInterface {
    use CustomElementTrait;

    /**
     * @param string $text
     * @param bool|null $default
     * @param string|null $label
     */
    public function __construct(
        string $text,
        bool $default = false,
        ?string $label = null
    ) {
        parent::__construct($text);

        $this->default = $default;
        $this->label = $label;
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
    public function getDefaultValue(): bool {
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
        return [
            "type" => "toggle",
            "text" => $this->getText(),
            "default" => $this->getDefaultValue()
        ];
    }
}