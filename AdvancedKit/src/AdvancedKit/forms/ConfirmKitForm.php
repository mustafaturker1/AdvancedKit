<?php

namespace AdvancedKit\forms;

use AdvancedKit\provider\SQLite;
use dktapps\pmforms\ModalForm;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\player\Player;

class ConfirmKitForm extends ModalForm
{
    public function __construct(string $kitName, string $kitItems, int $kitTime)
    {
        parent::__construct("Confirm Kit: " . $kitName, "\n\nDo you approve to receive the kit?\n\n", function (Player $player, bool $bool) use ($kitName, $kitItems, $kitTime): void {
            switch ($bool) {
                case true:
                    $items = SQLite::getDatabase()->decode($kitName);
                    $inventory = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
                    $inventory->getInventory()->setContents($items);
                    foreach ($inventory->getInventory()->getContents() as $content) {
                        $player->getInventory()->addItem($content);
                    }
                    if (SQLite::getDatabase()->isUsedKitDataControl($player->getName())) {
                        SQLite::getDatabase()->updateUsedKitData($player->getName(), time() + $kitTime);
                    } else {
                        SQLite::getDatabase()->createUsedKitData($player->getName(), time() + $kitTime);
                    }
                    $player->sendMessage("§aThe kit has been added to your inventory!");
                    break;
                default:
                case false:
                    $player->sendMessage("§cTransaction cancelled!");
                    break;
            }
        }, "Confirm", "Refuse");
    }
}