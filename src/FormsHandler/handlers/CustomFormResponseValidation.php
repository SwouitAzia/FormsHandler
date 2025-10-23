<?php

namespace FormsHandler\handlers;

use FormsHandler\elements\customform\Dropdown;
use FormsHandler\elements\customform\Input;
use FormsHandler\elements\customform\Slider;
use FormsHandler\elements\customform\StepSlider;
use FormsHandler\elements\customform\Toggle;
use FormsHandler\elements\types\CustomFormElement;

/**
 * Class for validating player-submitted values in custom form elements.
 *
 * Each static method checks whether the received value matches
 * the expected type for a given form element.
 *
 * Static methods are used because validation logic is the same
 * for all custom forms, and storing Closures would waste memory.
 */
final class CustomFormResponseValidation {
    /**
     * @param Toggle $toggle
     * @param mixed $value
     * @return bool
     */
    public static function validToggle(Toggle $toggle, mixed $value): bool {
        return is_bool($value);
    }

    /**
     * @param Input $input
     * @param mixed $value
     * @return bool
     */
    public static function validInput(Input $input, mixed $value): bool {
        return is_string($value);
    }

    /**
     * @param Dropdown $dropdown
     * @param mixed $value
     * @return bool
     */
    public static function validDropdown(Dropdown $dropdown, mixed $value): bool {
        return is_int($value) && isset($dropdown->getOptions()[$value]);
    }

    /**
     * @param Slider $slider
     * @param mixed $value
     * @return bool
     */
    public static function validSlider(Slider $slider, mixed $value): bool {
        return (is_int($value) || is_float($value)) && $value >= $slider->getMinValue() && $value <= $slider->getMaxValue();
    }

    /**
     * @param StepSlider $stepSlider
     * @param mixed $value
     * @return bool
     */
    public static function validStepSlider(StepSlider $stepSlider, mixed $value): bool {
        return is_int($value) && isset($stepSlider->getSteps()[$value]);
    }

    /**
     * @param array $json
     * @param mixed $responseData
     */
    public static function handleCustomFormResponse(array $json, mixed &$responseData): void {
        $new = [];

        foreach ($json["content"] as $i => $b) {
            /** @var Dropdown|Slider|Toggle|StepSlider|Input|null $element */
            $element = self::elementFromJSON($json["content"][$i]);
            if ($element === null) {
                $new[$i] = null;
                continue;
            }
            $v = $responseData[$i] ?? null;
            $isValid = match ($element::class) {
                Dropdown::class => self::validDropdown($element, $v),
                Slider::class => self::validSlider($element, $v),
                Toggle::class => self::validToggle($element, $v),
                StepSlider::class => self::validStepSlider($element, $v),
                Input::class => self::validInput($element, $v),
                default => false
            };

            if (!$isValid) {
                $new[$i] = $element->getDefaultValue();
                continue;
            }

            $new[$i] = $v;
        }

        $responseData = $new;
    }

    /**
     * @param array $data
     * @return CustomFormElement|null
     */
    private static function elementFromJSON(array $data): ?CustomFormElement {
        $element = null;

        switch ($data["type"] ?? "unknown") {
            case "input":
                $element = new Input($data["text"] ?? "", $data["placeholder"] ?? "", $data["default"] ?? "");
                break;
            case "dropdown":
                $element = new Dropdown($data["text"] ?? "", $data["options"] ?? [], $data["default"] ?? null);
                break;
            case "slider":
                $element = new Slider($data["text"] ?? "", $data["min"], $data["max"], $data["step"] ?? -1, $data["default"] ?? -1);
                break;
            case "step_slider":
                $element = new StepSlider($data["text"] ?? "", $data["steps"] ?? [], $data["default"] ?? -1);
                break;
            case "toggle":
                $element = new Toggle($data["text"] ?? "", $data["default"] ?? null);
                break;
        }

        return $element;
    }
}