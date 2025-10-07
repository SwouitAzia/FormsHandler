<?php

namespace FormsHandler\types;

use pocketmine\form\Form as PMForm;
use pocketmine\player\Player;
use pocketmine\utils\Utils;

abstract class AbstractForm implements PMForm {
    /** @var array $data */
    protected array $data = [];

    /** @var callable|null $submit */
    private $submit = null;
    /** @var callable|null $close */
    private $close = null;

    public function __construct() {
        $this->data["title"] = "";
        $this->data["content"] = "";
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self {
        $this->data["title"] = $title;

        return $this;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): self {
        $this->data["content"] = $content;

        return $this;
    }

    /**
     * @param callable $onSubmit
     * @return $this
     */
    public function onSubmit(callable $onSubmit): self {
        Utils::validateCallableSignature($this->getSubmitCallableSignature(), $onSubmit);
        $this->submit = $onSubmit;

        return $this;
    }

    /**
     * @param callable $onClose
     * @return $this
     */
    public function onClose(callable $onClose): self {
        Utils::validateCallableSignature(function(Player $player) {}, $onClose);
        $this->close = $onClose;

        return $this;
    }

    /**
     * @param Player $player
     * @param mixed $data
     */
    public function handleResponse(Player $player, mixed $data): void {
        $this->processData($data);

        if ($data === null && $this->close !== null) {
            $closure = $this->close;
            $closure($player);
        } else if ($data !== null && $this->submit !== null) {
            $closure = $this->submit;
            $closure($player, $data);
        }
    }

    /**
     * @param mixed $data
     */
    abstract protected function processData(mixed &$data): void;

    /**
     * @return callable
     */
    abstract protected function getSubmitCallableSignature(): callable;

    /**
     * @return array
     */
    public function jsonSerialize(): array {
        return $this->data;
    }
}