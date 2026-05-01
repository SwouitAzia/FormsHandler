<?php

namespace FormsHandler\elements\types;

use FormsHandler\utils\interfaces\LabelInterface;
use FormsHandler\utils\traits\DefaultValueTrait;
use FormsHandler\utils\traits\LabelTrait;

abstract class CustomFormElement extends FormElement implements LabelInterface {
    use DefaultValueTrait;
    use LabelTrait;

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
}