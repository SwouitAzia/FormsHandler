<?php

namespace FormsHandler\elements\customform;

use FormsHandler\elements\FormElement;
use FormsHandler\elements\types\CustomElement;
use FormsHandler\elements\types\CustomElementTrait;
use FormsHandler\utils\label\LabelInterface;

class Slider extends FormElement implements CustomElement, LabelInterface {
    use CustomElementTrait;

    /** @var float $min */
    protected float $min;

    /** @var float $max */
    protected float $max;

    /** @var int $step */
    protected int $step;

    /**
     * @param string $text
     * @param float $min
     * @param float $max
     * @param int $step
     * @param float $default
     * @param string|null $label
     */
    public function __construct(
        string $text,
        float $min,
        float $max,
        int $step = 1,
        float $default = 1,
        ?string $label = null
    ) {
        parent::__construct($text);

        $this->default = $default;
        $this->label = $label;

        $this->min = $min;
        $this->max = $max;

        $this->step = $step;
    }

    /**
     * @return float
     */
    public function getMinValue(): float {
        return $this->min;
    }

    /**
     * @return float
     */
    public function getMaxValue(): float {
        return $this->max;
    }

    /**
     * @return int
     */
    public function getStep(): int {
        return $this->step;
    }

    /**
     * @return float
     */
    public function getDefaultValue(): float {
        return $this->default;
    }

    /**
     * @return array{type: string, text: string, min: float, max: float, step: int, default: float}
     */
    public function jsonSerialize(): array {
        return [
            "type" => "slider",
            "text" => $this->getText(),
            "min" => $this->getMinValue(),
            "max" => $this->getMaxValue(),
            "step" => $this->getStep(),
            "default" => $this->getDefaultValue()
        ];
    }
}