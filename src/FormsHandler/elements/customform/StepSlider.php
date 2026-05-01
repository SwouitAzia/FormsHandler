<?php

namespace FormsHandler\elements\customform;

use FormsHandler\elements\FormElement;
use FormsHandler\elements\types\CustomElement;
use FormsHandler\elements\types\CustomElementTrait;
use FormsHandler\utils\label\LabelInterface;

class StepSlider extends FormElement implements CustomElement, LabelInterface {
    use CustomElementTrait;

    /** @var string[] $steps */
    protected array $steps;

    /**
     * @param string $text
     * @param string[] $steps
     * @param int $default
     * @param string|null $label
     */
    public function __construct(
        string $text,
        array $steps,
        int $default = 0,
        ?string $label = null
    ) {
        parent::__construct($text);

        $this->default = $default;
        $this->label = $label;

        $this->steps = $steps;
    }

    /**
     * @return string[]
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
        return [
            "type" => "step_slider",
            "text" => $this->getText(),
            "steps" => $this->getSteps(),
            "default" => $this->getDefaultValue()
        ];
    }
}