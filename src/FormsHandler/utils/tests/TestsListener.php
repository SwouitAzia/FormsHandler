<?php

namespace FormsHandler\utils\tests;

use FormsHandler\elements\customform\Dropdown;
use FormsHandler\elements\customform\Input;
use FormsHandler\elements\customform\Slider;
use FormsHandler\elements\customform\StepSlider;
use FormsHandler\elements\customform\Toggle;
use FormsHandler\elements\modalform\Button as ModalFormButton;
use FormsHandler\elements\simpleform\Button;
use FormsHandler\Main;
use FormsHandler\types\CustomForm;
use FormsHandler\types\ModalForm;
use FormsHandler\types\SimpleForm;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;

class TestsListener implements Listener {
    /**
     * @param PlayerChatEvent $event
     */
    public function onPlayerChat(PlayerChatEvent $event): void {
        $msg = $event->getMessage();
        $player = $event->getPlayer();

        $form = null;

        switch ($msg) {
            case "simple":
                $form = (new SimpleForm())
                    ->setTitle("FormsHandler SimpleForm Demo")
                    ->setContent("Hey there! This is a Simple Form created with FormsHandler.\nChoose one of the options below:")
                    ->addButton(new Button("Say Hello"))
                    ->addHeader("Header")
                    ->addDivider()
                    ->addButton(new Button("How are you?"))
                    ->addLabel("Label")
                    ->addButton(new Button("I love Minecraft!"))
                    ->onSubmit(function(Player $player, mixed $response) {
                        $player->sendMessage("You selected option #$response!");
                    })
                    ->onClose(function(Player $player) {
                        $player->sendMessage("You closed the form.");
                    });
                break;
            case "custom":
                $form = (new CustomForm())
                    ->setTitle("FormsHandler CustomForm Demo")
                    ->addHeader("Welcome to the CustomForm Demo")
                    ->addLabel("Hey there! This is a Custom Form created with FormsHandler.\nThis form demonstrates multiple element types provided by FormsHandler.")
                    ->addDivider()

                    ->addElement(new Input("Your nickname", "Enter your name...", "Steve"))
                    ->addElement(new Toggle("Enable notifications"))
                    ->addDivider()

                    ->addHeader("Preferences")
                    ->addElement(new Dropdown("Choose your favorite block", ["Grass Block", "Diamond Block", "TNT", "Crafting Table"]))
                    ->addElement(new Slider("Select your skill level", 1, 10, 1, 5))
                    ->addElement(new StepSlider("Pick a difficulty", ["Peaceful","Easy","Normal","Hard"]))
                    ->addDivider()
                    ->addLabel("End of form")

                    ->onSubmit(function(Player $player, array $response) {
                        // ...
                    })
                    ->onClose(function(Player $player) {
                        $player->sendMessage("You closed the form.");
                    });
                break;
            case "modal":
                $form = (new ModalForm())
                    ->setTitle("FormsHandler ModalForm Demo")
                    ->setContent("Hey there! This is a Modal Form created with FormsHandler.\nChoose one of the options below:")
                    ->setTopButton(new ModalFormButton("Top button"))
                    ->setBottomButton(new ModalFormButton("Bottom button"))
                    ->onSubmit(function(Player $player, bool $response) {
                        $player->sendMessage("You selected option #" . (int) $response . "!");
                    })
                    ->onClose(function(Player $player) {
                        $player->sendMessage("You closed the form.");
                    });
        }

        if ($form !== null) {
            Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player, $form): void {
                if (!$player->isConnected()) {
                    return;
                }

                $player->sendForm($form);
            }), 20);
        }
    }
}