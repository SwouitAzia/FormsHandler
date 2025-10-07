<?php

namespace FormsHandler\sessions;

use pocketmine\player\Player;

class Session {
    /** @var int|null $currentFormId */
    private ?int $currentFormId = null;

    /**
     * @param Player $player
     */
    public function __construct(
        private Player $player
    ) {}

    /**
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }

    /**
     * @return int|null
     */
    public function getCurrentFormId(): ?int {
        return $this->currentFormId;
    }

    /**
     * @param int|null $currentFormId
     */
    public function setCurrentFormId(?int $currentFormId): void {
        $this->currentFormId = $currentFormId;
    }
}