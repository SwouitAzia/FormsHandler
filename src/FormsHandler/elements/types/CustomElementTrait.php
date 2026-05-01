<?php

namespace FormsHandler\elements\types;

use FormsHandler\utils\label\LabelTrait;

trait CustomElementTrait {
    use LabelTrait;

    /** @var mixed $default */
    protected mixed $default;

    /**
     * @param string $text
     * @param mixed|null $default
     * @param string|null $label
     */
    public function __construct(
        string $text,
        mixed $default = null,
        ?string $label = null
    ) {
        parent::__construct($text);

        $this->default = $default;
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue(): mixed {
        return $this->default;
    }
}