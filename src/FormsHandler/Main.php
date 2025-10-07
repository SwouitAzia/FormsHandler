<?php

namespace FormsHandler;

use FormsHandler\handlers\EventsHandler;
use FormsHandler\handlers\PacketsHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase {
    use SingletonTrait;

    /** @var bool $enhancedUi */
    private bool $enhancedUi = true;
    /** @var string $packName */
    private string $packName = "FormsHandlerUI";

    public function onEnable(): void {
        $this->loadConfig();

        $pluginManager = $this->getServer()->getPluginManager();
        $pluginManager->registerEvents(new PacketsHandler(), $this);
        $pluginManager->registerEvents(new EventsHandler(), $this);

        if ($this->enhancedUi) $this->copyResourcePack();
    }

    private function loadConfig(): void {
        $this->saveDefaultConfig();
        $config = $this->getConfig();

        $this->enhancedUi = $config->getNested("enhanced-ui", $this->enhancedUi);
        $this->packName = $config->getNested("packName", $this->packName);
    }

    private function copyResourcePack(): void {
        // TODO copy resource pack automatically
    }
}