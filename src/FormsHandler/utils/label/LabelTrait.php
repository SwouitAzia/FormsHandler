<?php

namespace FormsHandler\utils\label;

/**
 * @see LabelInterface
 */
trait LabelTrait {
    /** @var string|null $label */
    protected ?string $label = null;

    /**
     * @return string|null
     */
    public function getLabel(): ?string {
        return $this->label;
    }
}