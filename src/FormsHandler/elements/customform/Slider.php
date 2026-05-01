<?php

namespace FormsHandler\elements\customform;

use FormsHandler\elements\types\CustomFormElement;
use FormsHandler\utils\traits\DefaultValueTrait;

class Slider extends CustomFormElement {
    use DefaultValueTrait;

    /** @var int $min */
    protected int $min;

    /** @var int $max */
    protected int $max;

    /** @var int $step */
    protected int $step;

    /**
     * @param string $text
     * @param int $min
     * @param int $max
     * @param int $step
     * @param int $default
     * @param string|null $label
     */
    public function __construct(
        string $text,
        int $min,
        int $max,
        int $step = -1,
        int $default = -1,
        ?string $label = null
    ) {
        parent::__construct($text, $default, $label);

        $this->min = $min;
        $this->max = $max;

        $this->step = $step;
    }

    /**
     * @return int
     */
    public function getMinValue(): int {
        return $this->min;
    }

    /**
     * @return int
     */
    public function getMaxValue(): int {
        return $this->max;
    }

    /**
     * @return int
     */
    public function getStep(): int {
        return $this->step;
    }

    /**
     * @return int
     */
    public function getDefaultValue(): int {
        return $this->default;
    }

    /**
     * @return array{type: string, text: string, min: int, max: int, step: int, default: int}
     */
    public function jsonSerialize(): array {
        $content = [
            "type" => "slider",
            "text" => $this->getText(),
            "min" => $this->getMinValue(),
            "max" => $this->getMaxValue()
        ];

        $step = $this->getStep();
        if ($step !== 1) {
            $content["step"] = $step;
        }

        $default = $this->getDefaultValue();
        if ($default !== -1) {
            $content["default"] = $default;
        }

        return $content;
    }
}