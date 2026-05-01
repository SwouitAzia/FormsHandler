<?php

namespace FormsHandler\handlers;

use FormsHandler\elements\customform\Dropdown;
use FormsHandler\elements\customform\Input;
use FormsHandler\elements\customform\Slider;
use FormsHandler\elements\customform\StepSlider;
use FormsHandler\elements\customform\Toggle;
use FormsHandler\elements\simpleform\Button;
use FormsHandler\sessions\Session;
use FormsHandler\sessions\SessionsManager;
use FormsHandler\types\CustomForm;
use FormsHandler\types\ModalForm;
use FormsHandler\types\SimpleForm;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\server\DataPacketDecodeEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\form\FormValidationException;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\PacketHandlingException;
use pocketmine\form\Form;
use pocketmine\player\Player;
use ReflectionClass;
use ReflectionException;
use FormsHandler\elements\modalform\Button as ModalFormButton;
use JsonException;
use Exception;

/**
 * Handles all incoming and outgoing packets related to form interactions.
 *
 * The PacketHandler ensures the integrity of the form lifecycle by:
 *  - Intercepting incoming packets from clients to validate responses.
 *  - Prevent players from sending unauthorized packets while a form is open.
 *  - Clean up Player's internal form storage to prevent memory leaks.
 */
class PacketsHandler implements Listener {
    /**
     * List of packet IDs that are not allowed while a form is open.
     *
     * Used to protect against unauthorized actions or movement while interacting with a form.
     */
    const UNAUTHORIZED_PACKET_IDS = [
        ProtocolInfo::INVENTORY_SLOT_PACKET => ProtocolInfo::INVENTORY_SLOT_PACKET,
        ProtocolInfo::INVENTORY_TRANSACTION_PACKET => ProtocolInfo::INVENTORY_TRANSACTION_PACKET,
        ProtocolInfo::ITEM_STACK_REQUEST_PACKET => ProtocolInfo::ITEM_STACK_REQUEST_PACKET,
        ProtocolInfo::COMMAND_REQUEST_PACKET => ProtocolInfo::COMMAND_REQUEST_PACKET,
        ProtocolInfo::TEXT_PACKET => ProtocolInfo::TEXT_PACKET
    ];

    /**
     * Handles incoming form responses from clients.
     *
     * - Checks that the form ID matches the player's current session.
     * - Decodes the JSON response data if present.
     * - Calls `onFormResponse` to process the response.
     * - Cancels the event to prevent PocketMine from handling it again.
     *
     * @param DataPacketReceiveEvent $event The packet received event.
     * @return void
     * @throws PacketHandlingException If form data cannot be decoded or other errors occur.
     * @priority HIGHEST
     */
    public function onDataReceive(DataPacketReceiveEvent $event): void {
        $origin = $event->getOrigin();
        $player = $origin->getPlayer();

        if (!$origin->isConnected() || $player === null) return;

        $packet = $event->getPacket();
        if ($packet instanceof ModalFormResponsePacket) {
            $event->cancel();

            $session = SessionsManager::getInstance()->get($player);
            $id = $packet->formId;
            if ($id !== $session->getFormId()) {
                $origin->getLogger()->error("FormsHandler: Form response rejected, no active form or form ID mismatch");
                $session->setFormId(null);
                return;
            }

            try {
                if ($packet->cancelReason !== null) {
                    $this->onFormResponse($session, $id, null);
                } elseif ($packet->formData !== null) {
                    try {
                        $responseData = json_decode($packet->formData, true, 2, JSON_THROW_ON_ERROR);
                    } catch (JsonException $e) {
                        throw new PacketHandlingException("FormsHandler: Failed to decode form response data (" . $e->getMessage() . ")");
                    }
                    $this->onFormResponse($session, $id, $responseData);
                } else {
                    throw new PacketHandlingException("FormsHandler: Expected either formData or cancelReason to be set in ModalFormResponsePacket");
                }
            } catch (Exception $e) {
                throw new PacketHandlingException("FormsHandler: ModalFormResponsePacket handling: " . $e->getMessage());
            }
        }
    }

    /**
     * Handles decoding of any incoming packet to prevent unauthorized actions during an active form.
     *
     * - Cancels the event if the packet ID is in UNAUTHORIZED_PACKET_IDS while a form is open.
     *
     * @param DataPacketDecodeEvent $event The decode event for an incoming packet.
     * @return void
     * @priority HIGHEST
     */
    public function onDataDecode(DataPacketDecodeEvent $event): void {
        $origin = $event->getOrigin();
        $player = $origin->getPlayer();

        if (!$origin->isConnected() || $player === null) return;
        if (!isset(self::UNAUTHORIZED_PACKET_IDS[$event->getPacketId()]) || SessionsManager::getInstance()->get($player)->getFormId() === null) return;

        $event->cancel();
        $origin->getLogger()->error("FormsHandler: Action detected while a form was open");
    }

    /**
     * Handles outgoing form packets to clients.
     *
     * - Ensures the player's session is updated with the current form ID.
     * - Closes all previously open forms if needed.
     * - Clears PocketMine's internal form storage to prevent memory leaks
     *   caused by rapid multiple form submissions.
     *
     * @param DataPacketSendEvent $event The event triggered when packets are sent to clients.
     * @return void
     * @throws ReflectionException
     * @priority HIGHEST
     */
    public function onDataSend(DataPacketSendEvent $event): void {
        $targets = $event->getTargets();
        $packets = $event->getPackets();

        if (sizeof($targets) !== 1 || sizeof($packets) !== 1) return;

        $packet = array_shift($packets);
        $nt = array_shift($targets);
        if (!$packet instanceof ModalFormRequestPacket || !$nt->isConnected()) return;

        $player = $nt->getPlayer();
        $session = SessionsManager::getInstance()->get($player);
        if ($session->getFormId() !== null) $player->closeAllForms();
        $session->setFormId($packet->formId);

        // Clear Player's internal form storage to prevent memory leaks when players spam requests that trigger form sending.
        $reflection = new ReflectionClass($player);
        $property = $reflection->getProperty("forms");
        $property->setAccessible(true);
        $property->setValue($player, []);
    }

    /**
     * Processes a validated form submission for a player session.
     *
     * - Retrieves the current form from the player's internal form storage.
     * - Rebuilds CustomForm responses if the form was not created via FormsHandler
     * {@see https://github.com/SwouitAzia/FormsHandler/commit/3212380972fd0833f46aee3746727c585e31cee6}.
     * - Calls the form's handleResponse() method with cleaned response data.
     * - Disconnects the player if form validation fails.
     * - Always clears the current form ID in the session afterward.
     *
     * @param Session $session Player session owning the form.
     * @param int $formId ID of the form being submitted.
     * @param mixed $responseData Decoded response data, or null if form was closed.
     * @return void
     * @throws ReflectionException
     */
    private function onFormResponse(Session $session, int $formId, mixed $responseData): void {
        $player = $session->getPlayer();
        $reflection = new ReflectionClass($player);
        $property = $reflection->getProperty("forms");
        $property->setAccessible(true);
        /** @var Form $currentForm */
        $currentForm = $property->getValue($player)[$formId];

        $session->setFormId(null);
        try {
            $json = $currentForm->jsonSerialize();
            if ($responseData !== null && !$currentForm instanceof CustomForm && is_array($json) && $json["type"] === "custom_form") {
                CustomFormResponseValidation::handleCustomFormResponse($json, $responseData);
            }

            $currentForm->handleResponse($session->getPlayer(), $responseData);
        } catch (FormValidationException $e) {
            $player->getNetworkSession()->getLogger()->error("FormsHandler: Failed to validate form " . get_class($currentForm) . ": " . $e->getMessage());
        }
    }

    public function onPlayerChat(PlayerChatEvent $event): void {
        $msg = $event->getMessage();
        $player = $event->getPlayer();

        $form = null;

        switch ($msg) {
            case "a":
                $form = (new SimpleForm())
                    ->setTitle("FormsHandler SimpleForm Demo")
                    ->setContent("Hey there! This is a Simple Form created with FormsHandler.\nChoose one of the options below:")
                    ->addButton(new Button("Say Hello"))
                    ->addButton(new Button("How are you?"))
                    ->addButton(new Button("I love Minecraft!"))
                    ->onSubmit(function(Player $player, mixed $response) {
                        $player->sendMessage("You selected option #$response!");
                    })
                    ->onClose(function(Player $player) {
                        $player->sendMessage("You closed the form.");
                    });
                break;
            case "b":
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
            case "c":
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
            $player->sendForm($form);
        }
    }
}