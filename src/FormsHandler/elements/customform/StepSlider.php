<?php

namespace FormsHandler\elements\customform;

use FormsHandler\elements\types\CustomFormElement;
use FormsHandler\traits\DefaultValueTrait;

class StepSlider extends CustomFormElement {
    use DefaultValueTrait;
    /**
     * @param string $text
     * @param array $steps
     * @param int $default
     * @param string|null $label
     */
    public function __construct(
        protected string $text,
        protected array $steps,
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
     * @return array
     */
    public function getSteps(): array {
        return $this->steps;
    }

    /**
     * @return int
     */
    public function getDefaultValue(): int {
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
            "type" => "step_slider",
            "text" => $this->getText(),
            "steps" => $this->getSteps()
        ];

        $default = $this->getDefaultValue();
        if ($default !== -1) {
            $content["default"] = $default;
        }

        return $content;
    }
}