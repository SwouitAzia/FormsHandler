<?php

namespace FormsHandler\types;

use FormsHandler\elements\simpleform\Button;
use FormsHandler\elements\types\SimpleElement;
use FormsHandler\elements\visual\FormVisualsTrait;
use FormsHandler\elements\visual\VisualElement;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;

/**
 * Represents a SimpleForm used for user interaction.
 *
 * This form allows adding interactive buttons, labels, headers, and dividers.
 */
class SimpleForm extends AbstractForm {
    use FormVisualsTrait;

    public const FORM_TYPE = "form";
    public const SIMPLE_ELEMENTS = "elements";

    /** @var SimpleElement[] $elements */
    private array $elements = [];

    /** @var array<string, string|int> */
    private array $labelsMap = [];

    public function __construct() {
        parent::__construct();

        $this->data[self::SIMPLE_ELEMENTS] = [];
    }

    /**
     * @param SimpleElement $element
     * @return $this
     * @internal
     */
    public function addElement(SimpleElement $element): self {
        $this->data[self::SIMPLE_ELEMENTS][] = $element->jsonSerialize();

        $this->elements[] = $element;
        $this->labelsMap[] = $element->getLabel() ?? sizeof($this->labelsMap);

        return $this;
    }

    /**
     * @param Button $button
     * @return $this
     */
    public function addButton(Button $button): self {
        return $this->addElement($button);
    }

    /**
     * @param mixed $data
     */
    public function processData(mixed &$data): void {
        if (!is_null($data)) {
            if (!is_int($data)) {
                throw new FormValidationException("Expected an integer response, got " . gettype($data));
            }
            $count = count($this->data[self::SIMPLE_ELEMENTS]);
            if ($data >= $count || $data < 0) {
                throw new FormValidationException("Button at $data does not exist");
            }
            if (!$this->elements[$data] instanceof VisualElement) {
                throw new FormValidationException("Button at index $data is not a button");
            }
            $data = $this->labelsMap[$data] ?? null;
        }
    }

    /**
     * @return callable
     */
    protected function getSubmitCallableSignature(): callable {
        return function(Player $player, mixed $data) {};
    }

    /**
     * @return string
     */
    public function getFormType(): string {
        return self::FORM_TYPE;
    }
}