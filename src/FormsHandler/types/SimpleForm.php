<?php

namespace FormsHandler\types;

use FormsHandler\elements\simpleform\Button;
use FormsHandler\elements\simpleform\Divider;
use FormsHandler\elements\simpleform\Header;
use FormsHandler\elements\simpleform\Label;
use FormsHandler\exceptions\FormCreationException;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;

/**
 * Represents a SimpleForm used for user interaction.
 *
 * This form allows adding interactive buttons, labels, headers, and dividers.
 */
class SimpleForm extends AbstractForm {
    /** @var Button[] $buttons */
    private array $buttons = [];

    /** @var array<string, string|int> */
    private array $labelsMap = [];

    public function __construct() {
        parent::__construct();
        $this->data["type"] = "form";
        $this->data["buttons"] = [];
    }

    /**
     * @param Button $button
     * @return $this
     */
    public function addButton(Button $button): self {
        $this->data["buttons"][] = $button->jsonSerialize();

        $this->buttons[] = $button;
        $this->labelsMap[] = $button->getLabel() ?? sizeof($this->labelsMap);

        return $this;
    }

    /**
     * @param array $buttons
     * @return $this
     */
    public function setButtons(array $buttons): self {
        $this->data["buttons"] = [];
        $this->buttons = [];
        $this->labelsMap = [];

        foreach ($buttons as $button) {
            if (!$button instanceof Button) {
                throw new FormCreationException("Buttons must be an array of buttons");
            }

            $this->addButton($button);
        }

        return $this;
    }

    /**
     * @param Label $label
     * @return $this
     */
    public function addLabel(Label $label): self {
        return $this->addButton($label);
    }

    /**
     * @param Header $header
     * @return $this
     */
    public function addHeader(Header $header): self {
        return $this->addButton($header);
    }

    /**
     * @param Divider $divider
     * @return $this
     */
    public function addDivider(Divider $divider): self {
        return $this->addButton($divider);
    }

    /**
     * @param mixed $data
     */
    public function processData(mixed &$data): void {
        if (!is_null($data)) {
            if (!is_int($data)) {
                throw new FormValidationException("Expected an integer response, got " . gettype($data));
            }
            $count = count($this->data["buttons"]);
            if ($data >= $count || $data < 0) {
                throw new FormValidationException("Button at $data does not exist");
            }
            if (!$this->buttons[$data]->isButton()) {
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
}