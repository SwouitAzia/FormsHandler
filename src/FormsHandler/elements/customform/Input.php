<?php

namespace FormsHandler\elements\customform;

use FormsHandler\elements\FormElement;
use FormsHandler\elements\types\CustomElement;
use FormsHandler\elements\types\CustomElementTrait;
use FormsHandler\utils\label\LabelInterface;

class Input extends FormElement implements CustomElement, LabelInterface {
    use CustomElementTrait;

    /** @var string $placeholder */
    protected string $placeholder;

    /**
     * @param string $text
     * @param string $placeholder
     * @param string $default
     * @param string|null $label
     */
    public function __construct(
        string $text,
        string $placeholder = "",
        string $default = "",
        ?string $label = null
    ) {
        parent::__construct($text);

        $this->default = $default;
        $this->label = $label;

        $this->placeholder = $placeholder;
    }

    /**
     * @return string
     */
    public function getPlaceholder(): string {
        return $this->placeholder;
    }

    /**
     * @return string
     */
    public function getDefaultValue(): string {
        return $this->default;
    }

    /**
     * @return array{type: string, text: string, placeholder: string, default: string}
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