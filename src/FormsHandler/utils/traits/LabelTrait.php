<?php

namespace FormsHandler\utils\traits;

use FormsHandler\utils\interfaces\LabelInterface;

/**
 * @see LabelInterface
 */
trait LabelTrait {
    /** @var string|null $label */
    protected ?string $label;

    /**
     * @return string|null
     */
    public function getLabel(): ?string {
        return $this->label;
    }
}