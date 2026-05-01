<?php

namespace FormsHandler\elements\customform;

use FormsHandler\elements\types\CustomFormElement;
use FormsHandler\utils\traits\DefaultValueTrait;

class Input extends CustomFormElement {
    use DefaultValueTrait;

    /** @var string $placeholder */
    protected string $placeholder;

    /**
     * @param string $text
     * @param string $placeholder
     * @param string|null $default
     * @param string|null $label
     */
    public function __construct(
        string $text,
        string $placeholder = "",
        ?string $default = "",
        ?string $label = null
    ) {
        parent::__construct($text, $default, $label);

        $this->placeholder = $placeholder;
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
     * @return array{type: string, text: string, placeholder: string, default: mixed}
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