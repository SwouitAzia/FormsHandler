<?php

namespace FormsHandler\sessions;

use pocketmine\player\Player;

class Session {
    /** @var Player $player */
    private Player $player;

    /** @var int|null $formId */
    private ?int $formId = null;

    /**
     * @param Player $player
     */
    public function __construct(
        Player $player
    ) {
        $this->player = $player;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }

    /**
     * @return int|null
     */
    public function getFormId(): ?int {
        return $this->formId;
    }

    /**
     * @param int|null $id
     */
    public function setFormId(?int $id): void {
        $this->formId = $id;
    }
}