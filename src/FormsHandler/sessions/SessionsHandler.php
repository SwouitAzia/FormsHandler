<?php

namespace FormsHandler\sessions;

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use WeakMap;

final class SessionsHandler {
    use SingletonTrait;

    /** @var WeakMap $sessions */
    private WeakMap $sessions;

    /**
     * @param Player $player
     * @return Session
     */
    public function get(Player $player): Session {
        $this->sessions ??= new WeakMap();
        if (!isset($this->sessions[$player])) {
            $this->sessions[$player] = new Session($player);
            return $this->sessions[$player];
        }
        return $this->sessions[$player];
    }
}