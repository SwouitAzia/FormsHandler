<?php

namespace FormsHandler\handlers;

use FormsHandler\elements\customform\Dropdown;
use FormsHandler\elements\customform\Input;
use FormsHandler\elements\customform\Slider;
use FormsHandler\elements\customform\StepSlider;
use FormsHandler\elements\customform\Toggle;

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
}