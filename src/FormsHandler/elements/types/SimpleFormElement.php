<?php

namespace FormsHandler\elements\types;

use FormsHandler\utils\interfaces\LabelInterface;
use FormsHandler\utils\traits\LabelTrait;

abstract class SimpleFormElement extends FormElement implements LabelInterface {
    use LabelTrait;

    /**
     * @param string $text
     * @param string|null $label
     */
    public function __construct(
        string $text,
        ?string $label = null
    ) {
        parent::__construct($text);

        $this->label = $label;
    }

    /**
     * TODO: useful?
     * @return bool
     */
    public function isButton(): bool {
        return true;
    }
}