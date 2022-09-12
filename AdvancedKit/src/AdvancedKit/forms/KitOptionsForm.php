<?php

namespace AdvancedKit\forms;

use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;

class KitOptionsForm extends MenuForm
{

    public function __construct()
    {
        parent::__construct("Kit Options", "", [
            new MenuOption("Add Kit"),
            new MenuOption("Remove Kit")
        ], function (Player $player, int $option): void {
            switch ($option) {
                case 0:
                    $player->sendForm(new KitAddForm());
                    break;
                case 1:
                    $player->sendForm(new KitRemoveForm());
                    break;
            }
        });
    }
}