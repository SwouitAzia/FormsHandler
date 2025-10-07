<?php

namespace FormsHandler\handlers;

use Exception;
use FormsHandler\sessions\Session;
use FormsHandler\sessions\SessionsHandler;
use FormsHandler\types\AbstractForm;
use JsonException;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\form\FormValidationException;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\ServerboundDiagnosticsPacket;
use pocketmine\network\PacketHandlingException;
use ReflectionClass;
use ReflectionException;

/**
 * Handles all incoming and outgoing packets related to form interactions.
 *
 * The PacketHandler ensures the integrity of the form lifecycle by:
 *  - Intercepting incoming packets from clients to validate responses.
 *  - Verifying that players cannot interact, move, or respond to closed forms.
 *  - Tracking outgoing forms to maintain session consistency and prevent spoofed responses.
 */
class PacketsHandler implements Listener {
    private const AUTHORIZED_PACKETS = [
        // TODO
        PlayerAuthInputPacket::class => PlayerAuthInputPacket::class,
        ServerboundDiagnosticsPacket::class => ServerboundDiagnosticsPacket::class
    ];

    /**
     * Intercepts and validates all packets received from the client.
     *
     * - If the packet is a ModalFormResponsePacket, the method checks
     *   whether the form ID matches the currently active one in the player’s session.
     * - If the IDs don’t match, or if the form was already closed, the player
     *   is disconnected with an error message.
     * - Handles both valid form responses and cancelled (closed) forms.
     * - Also closes the form if a restricted packet (like movement) is received
     *   while a form is open.
     *
     * @param DataPacketReceiveEvent $event The event triggered when a packet is received from a client.
     * @return void
     * @throws PacketHandlingException
     */
    public function onDataReceive(DataPacketReceiveEvent $event): void {
        $origin = $event->getOrigin();
        $player = $origin->getPlayer();

        if (!$origin->isConnected() || $player === null) return;

        $packet = $event->getPacket();
        if ($packet instanceof ModalFormResponsePacket) {
            $event->cancel();
            if (!$player->isConnected()) return;

            $session = SessionsHandler::getInstance()->get($player);
            $id = $packet->formId;
            if ($id !== $session->getCurrentFormId()) {
                $origin->disconnectWithError("Form response rejected: no active form or form ID mismatch (form may have been closed before responding or the player performed an action while the form was open).");
                $session->setCurrentFormId(null);
                return;
            }

            try {
                if ($packet->cancelReason !== null) {
                    $this->onFormResponse($session, $id, null);
                } elseif ($packet->formData !== null) {
                    try {
                        $responseData = json_decode($packet->formData, true, 2, JSON_THROW_ON_ERROR);
                    } catch(JsonException $e) {
                        throw PacketHandlingException::wrap($e, "Failed to decode form response data");
                    }
                    $this->onFormResponse($session, $id, $responseData);
                } else {
                    throw new PacketHandlingException("Expected either formData or cancelReason to be set in ModalFormResponsePacket");
                }
            } catch (Exception $e) {
                throw PacketHandlingException::wrap($e, "ModalFormResponsePacket handling: ");
            }

        } else if (!isset(self::AUTHORIZED_PACKETS[$packet::class]) && SessionsHandler::getInstance()->get($player)->getCurrentFormId() !== null) {
            $origin->disconnectWithError( "Unexpected client action detected while a form was open.");
            SessionsHandler::getInstance()->get($player)->setCurrentFormId(null);
        }
    }

    /**
     * Processes a validated form submission for the given player session.
     *
     * This replaces the default PocketMine Player::onFormSubmit() behavior.
     * It handles both successful form responses and cancellations (form closed by player).
     *
     * - Calls the associated form's handleResponse() method.
     * - Wraps and logs any validation or packet handling exceptions.
     * - Ensures the current form is reset afterward, regardless of success or failure.
     *
     * @param Session $session The player’s session that owns the form.
     * @param int $formId The form ID being submitted.
     * @param mixed $responseData The decoded form response data, or null if the form was closed.
     *
     * @return void
     * @throws ReflectionException
     */
    private function onFormResponse(Session $session, int $formId, mixed $responseData): void {
        $player = $session->getPlayer();
        $reflection = new ReflectionClass($player);
        $property = $reflection->getProperty("forms");
        $property->setAccessible(true);
        /** @var AbstractForm $currentForm */
        $currentForm = $property->getValue($player)[$formId];

        try {
            $currentForm->handleResponse($session->getPlayer(), $responseData);
        } catch (FormValidationException $e) {
            $player->getNetworkSession()->disconnectWithError("Failed to validate form " . get_class($currentForm) . ": " . $e->getMessage());
        } finally {
            $session->setCurrentFormId(null);
        }
    }

    /**
     * Intercepts all outgoing packets sent to the client.
     *
     * This method ensure that each sent form packet matches
     * the player’s current session state and to prevent potential memory leaks
     * caused by PocketMine’s internal form storage.
     *
     * By default, PocketMine stores every sent form in the Player object’s
     * `$forms` array until a response is received. However, if a player triggers
     * a large number of forms (for example, by spamming interactions or sending
     * multiple form requests in rapid succession), this array can grow indefinitely
     * (credits : Zwuiix-cmd, Nya-Enzo).
     *
     * To prevent memory leaks and maintain stable performance, we
     * clear this array whenever a form is sent and registered in the player’s session.
     *
     * @param DataPacketSendEvent $event The event triggered when packets are sent to the client.
     * @return void
     * @throws ReflectionException
     */
    public function onDataSend(DataPacketSendEvent $event): void {
        $targets = $event->getTargets();
        $packets = $event->getPackets();

        if (sizeof($targets) !== 1 || sizeof($packets) !== 1) return;

        $packet = array_shift($packets);
        $nt = array_shift($targets);
        if (!$packet instanceof ModalFormRequestPacket || !$nt->isConnected()) return;

        $player = $nt->getPlayer();
        $session = SessionsHandler::getInstance()->get($player);
        if ($session->getCurrentFormId() !== null) $player->closeAllForms();
        $session->setCurrentFormId($packet->formId);

        // Clear PocketMine's internal form registry to prevent memory leaks
        // when players spam requests that trigger form sending.
        $reflection = new ReflectionClass($player);
        $property = $reflection->getProperty("forms");
        $property->setAccessible(true);
        $property->setValue($player, []);
    }
}