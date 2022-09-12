<?php

namespace AdvancedKit\forms;

use AdvancedKit\provider\SQLite;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class KitRemoveForm extends MenuForm
{

    public function __construct()
    {
        $options = [];
        $data = SQLite::getDatabase()->getKitData();
        if (!empty($data)) {
            foreach ($data as $datum) {
                $options[] = new MenuOption(TextFormat::clean($datum["kitName"]));
            }
        }
        parent::__construct("Kit Remove", "", $options, function (Player $player, int $option): void {
            if (!$player->getServer()->isOp($player->getName())) return;
            $selected = $this->getOption($option)->getText();
            SQLite::getDatabase()->removeKitData($selected);
            $player->sendMessage("§aSuccessfully deleted kit!");
        });
    }
}