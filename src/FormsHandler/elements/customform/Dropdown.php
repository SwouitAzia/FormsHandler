<?php

namespace FormsHandler\elements\customform;

use FormsHandler\elements\types\CustomFormElement;
use FormsHandler\utils\traits\DefaultValueTrait;

class Dropdown extends CustomFormElement {
    use DefaultValueTrait;

    /** @var array $options */
    protected array $options;

    /**
     * @param string $text
     * @param array $options
     * @param int|null $default
     * @param string|null $label
     */
    public function __construct(
        string $text,
        array $options,
        ?int $default = null,
        ?string $label = null
    ) {
        parent::__construct($text, $default, $label);

        $this->options = $options;
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
     * @return array{type: string, text: string, options: array, default: string|null}
     */
    public function jsonSerialize(): array {
        return [
            "type" => "dropdown",
            "text" => $this->getText(),
            "options" => $this->getOptions(),
            "default" => $this->getDefaultValue()
        ];
    }
}