<?php

namespace AdvancedKit\forms;

use AdvancedKit\provider\SQLite;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class KitForm extends MenuForm
{

    public function __construct(Player $player)
    {
        $options = [];
        $data = SQLite::getDatabase()->getKitData();
        if (!empty($data)) {
            foreach ($data as $datum) {
                if ($player->hasPermission($datum["kitPerm"])) {
                    $options[] = new MenuOption("§a" . $datum["kitName"]);
                } else {
                    $options[] = new MenuOption("§c" . $datum["kitName"]);
                }
            }
        }
        parent::__construct("Select Kit", "",
            $options,
            function (Player $player, int $option): void {
                $selectedKit = TextFormat::clean($this->getOption($option)->getText());
                foreach (SQLite::getDatabase()->getKitData() as $datum) {
                    if ($selectedKit == $datum["kitName"]) {
                        if ($player->hasPermission($datum["kitPerm"])) {
                            if (!empty(SQLite::getDatabase()->getUsedKitData())) {
                                foreach (SQLite::getDatabase()->getUsedKitData() as $item) {
                                    if ($player->getName() == $item["playerName"]) {
                                        if (time() > $item["kitTime"]) {
                                            $player->sendForm(new ConfirmKitForm($selectedKit, $datum["kitItems"], $datum["kitTime"]));
                                        } else {
                                            $player->sendMessage("§cTo get the kit again, you have to wait as long as: §f" . date("d.m.Y H:i:s", $item["kitTime"]));
                                        }
                                    } else {
                                        $player->sendForm(new ConfirmKitForm($selectedKit, $datum["kitItems"], $datum["kitTime"]));
                                    }
                                }
                            } else {
                                $player->sendForm(new ConfirmKitForm($selectedKit, $datum["kitItems"], $datum["kitTime"]));
                            }
                        } else {
                            $player->sendMessage("§cYou are not authorized for this kit!");
                        }
                    }
                }
            }
        );
    }
}