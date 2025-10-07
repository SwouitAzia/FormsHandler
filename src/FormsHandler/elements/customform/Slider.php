<?php

namespace FormsHandler\elements\customform;

use FormsHandler\elements\types\CustomFormElement;

class Slider extends CustomFormElement {
    /**
     * @param string $text
     * @param int $min
     * @param int $max
     * @param int $step
     * @param int $default
     * @param string|null $label
     */
    public function __construct(
        protected string $text,
        protected int $min,
        protected int $max,
        protected int $step = -1,
        protected int $default = -1,
        protected ?string $label = null
    ) {}

    /**
     * @return string
     */
    public function getText(): string {
        return $this->text;
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
    public function getDefaultIndex(): int{
        return $this->default;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string {
        return $this->label;
    }

    /**
     * @return array
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

        $default = $this->getDefaultIndex();
        if ($default !== -1) {
            $content["default"] = $default;
        }

        return $content;
    }
}