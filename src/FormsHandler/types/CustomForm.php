<?php

namespace FormsHandler\types;

use FormsHandler\elements\customform\Dropdown;
use FormsHandler\elements\customform\Input;
use FormsHandler\elements\customform\Slider;
use FormsHandler\elements\customform\StepSlider;
use FormsHandler\elements\customform\Toggle;
use FormsHandler\elements\types\CustomElement;
use FormsHandler\elements\visual\FormVisualsTrait;
use FormsHandler\elements\visual\VisualElement;
use FormsHandler\handlers\CustomFormResponseValidation;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;

/**
 * Represents a CustomForm used for user interaction.
 *
 * This form allows adding interactive elements, labels, headers, and dividers.
 */
class CustomForm extends AbstractForm {
    use FormVisualsTrait;

    public const FORM_TYPE = "custom_form";
    public const CUSTOM_CONTENT = "content";

    /** @var CustomElement[] $elements */
    private array $elements = [];

    /** @var array<string, string|int> */
    private array $labelsMap = [];

    public function __construct() {
        parent::__construct();

        $this->data[self::CUSTOM_CONTENT] = [];
    }

    /**
     * @param string $content
     * @return AbstractForm
     * @internal
     */
    public function setContent(string $content): AbstractForm {
        return $this; // just for override
    }

    /**
     * @param CustomElement $element
     * @return $this
     * @deprecated
     * @internal
     */
    public function addElement(CustomElement $element): self {
        $this->data[self::CUSTOM_CONTENT][] = $element->jsonSerialize();

        $this->elements[] = $element;
        $this->labelsMap[] = $element->getLabel() ?? sizeof($this->labelsMap);

        return $this;
    }

    /**
     * @param mixed $data
     */
    public function processData(mixed &$data): void {
        if (!is_null($data) && !is_array($data)) {
            throw new FormValidationException("Expected an array response, got " . gettype($data));
        }
        if (is_array($data)) {
            $mapSize = sizeof($this->labelsMap);
            $new = [];
            for ($i = 0; $i < $mapSize; $i++) {
                if (!isset($this->labelsMap[$i])) {
                    throw new FormValidationException("Invalid element " . $i);
                }

                $element = $this->elements[$i];

                if ($element instanceof VisualElement) {
                    $new[$this->labelsMap[$i]] = null;
                    continue;
                }

                $v = $data[$i] ?? null;
                $isValid = match ($element::class) {
                    Dropdown::class => CustomFormResponseValidation::validDropdown($element, $v),
                    Slider::class => CustomFormResponseValidation::validSlider($element, $v),
                    Toggle::class => CustomFormResponseValidation::validToggle($element, $v),
                    StepSlider::class => CustomFormResponseValidation::validStepSlider($element, $v),
                    Input::class => CustomFormResponseValidation::validInput($element, $v)
                };

                if (!$isValid) {
                    /** @var DefaultValueTrait $element */
                    $new[$this->labelsMap[$i]] = $element->getDefaultValue();
                    continue;
                }
                $new[$this->labelsMap[$i]] = $v;
            }
            $data = $new;
        }
    }

    /**
     * @return callable
     */
    protected function getSubmitCallableSignature(): callable {
        return function(Player $player, array $data) {};
    }

    /**
     * @return string
     */
    public function getFormType(): string {
        return self::FORM_TYPE;
    }
}