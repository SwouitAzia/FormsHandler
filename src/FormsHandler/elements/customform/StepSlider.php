<?php

namespace FormsHandler\elements\customform;

use FormsHandler\elements\types\CustomFormElement;
use FormsHandler\utils\traits\DefaultValueTrait;

class StepSlider extends CustomFormElement {
    use DefaultValueTrait;

    /** @var array $steps */
    protected array $steps;

    /**
     * @param string $text
     * @param array $steps
     * @param int $default
     * @param string|null $label
     */
    public function __construct(
        string $text,
        array $steps,
        int $default = -1,
        ?string $label = null
    ) {
        parent::__construct($text, $default, $label);

        $this->steps = $steps;
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
     * @return array{type: string, text: string, steps: array, default: int}
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