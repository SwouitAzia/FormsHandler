<?php

namespace FormsHandler\types;

use FormsHandler\elements\modalform\Button;
use FormsHandler\exceptions\FormCreationException;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;

/**
 * Represents a ModalForm used for user interaction.
 *
 * This form provides two buttons for the user to choose from.
 */
class ModalForm extends AbstractForm {
    public function __construct() {
        parent::__construct();
        $this->data["type"] = "modal";

        $this->data["button1"] = "";
        $this->data["button2"] = "";
    }

    /**
     * @param Button $button
     * @param int $index
     * @return self
     */
    private function setButton(Button $button, int $index): self {
        $this->data["button$index"] = $button->jsonSerialize();

        return $this;
    }

    /**
     * @param Button $button
     * @return $this
     */
    public function setTopButton(Button $button): self {
        return $this->setButton($button, 1);
    }

    /**
     * @param Button $button
     * @return $this
     */
    public function setBottomButton(Button $button): self {
        return $this->setButton($button, 2);
    }

    /**
     * @param array $buttons
     * @return $this
     */
    public function setButtons(array $buttons): self {
        $this->data["buttons"] = [];

        if (sizeof($buttons) !== 2) {
            throw new FormCreationException("Buttons should have exactly two buttons");
        }

        foreach ($buttons as $index => $button) {
            if (!$button instanceof Button) {
                throw new FormCreationException("Buttons must be an array of buttons");
            }
            $this->setButton($button, $index + 1);
        }

        return $this;
    }

    /**
     * @param mixed $data
     */
    public function processData(mixed &$data): void {
        if (!is_bool($data) && !is_null($data)) {
            throw new FormValidationException("Expected a boolean response, got " . gettype($data));
        }
    }

    /**
     * @return callable
     */
    protected function getSubmitCallableSignature(): callable {
        return function(Player $player, bool $data) {};
    }
}