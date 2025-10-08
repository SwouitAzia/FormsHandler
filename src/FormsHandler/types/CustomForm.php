<?php

namespace FormsHandler\types;

use FormsHandler\elements\customform\Divider;
use FormsHandler\elements\customform\Dropdown;
use FormsHandler\elements\customform\Header;
use FormsHandler\elements\customform\Input;
use FormsHandler\elements\customform\Label;
use FormsHandler\elements\customform\Slider;
use FormsHandler\elements\customform\StepSlider;
use FormsHandler\elements\customform\Toggle;
use FormsHandler\elements\customform\VisualElement;
use FormsHandler\elements\types\CustomFormElement;
use FormsHandler\exceptions\FormCreationException;
use FormsHandler\handlers\CustomFormResponseValidation;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;

/**
 * Represents a CustomForm used for user interaction.
 *
 * This form allows adding interactive elements, labels, headers, and dividers.
 */
class CustomForm extends AbstractForm {
    /** @var CustomFormElement[] $elements */
    private array $elements = [];

    /** @var array<string, string|int> */
    private array $labelsMap = [];

    public function __construct() {
        parent::__construct();
        $this->data["type"] = "custom_form";
        $this->data["buttons"] = []; // TODO: useful ?
        $this->data["content"] = [];
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
     * @param CustomFormElement $element
     * @return $this
     */
    public function addElement(CustomFormElement $element): self {
        $this->data["content"][] = $element->jsonSerialize();

        $this->elements[] = $element;
        $this->labelsMap[] = $element->getLabel() ?? sizeof($this->labelsMap);

        return $this;
    }

    /**
     * @param CustomFormElement[] $elements
     * @return $this
     */
    public function setElements(array $elements): self {
        $this->data["content"] = [];
        $this->elements = [];

        foreach ($elements as $element) {
            if (!$element instanceof CustomFormElement) {
                throw new FormCreationException("\$elements must be an array of CustomFormElement");
            }

            $this->addElement($element);
        }

        return $this;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function addLabel(string $text): self {
        return $this->addElement(new Label($text));
    }

    /**
     * @param string $text
     * @return $this
     */
    public function addHeader(string $text): self {
        return $this->addElement(new Header($text));
    }

    /**
     * @return $this
     */
    public function addDivider(): self {
        return $this->addElement(new Divider());
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
            if (count($data) !== $mapSize) {
                throw new FormValidationException("Expected an array response with the size " . $mapSize . ", got " . count($data));
            }
            $new = [];
            foreach ($data as $i => $v) {
                if (!isset($this->labelsMap[$i])) {
                    throw new FormValidationException("Invalid element " . $i);
                }

                $element = $this->elements[$i];

                // Visual elements (headers, dividers, labels) should never have a value.
                if ($element instanceof VisualElement) {
                    $new[$this->labelsMap[$i]] = null;
                    continue;
                }

                $isValid = match ($element::class) {
                    Dropdown::class => CustomFormResponseValidation::validDropdown($element, $v),
                    Slider::class => CustomFormResponseValidation::validSlider($element, $v),
                    Toggle::class => CustomFormResponseValidation::validToggle($element, $v),
                    StepSlider::class => CustomFormResponseValidation::validStepSlider($element, $v),
                    Input::class => CustomFormResponseValidation::validInput($element, $v),
                    default => throw new FormValidationException(
                        "Unsupported element type: " . $element::class
                    ),
                };

                if (!$isValid) {
                    throw new FormValidationException("Invalid value for element at index $i (" . $element::class . ")");
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
}