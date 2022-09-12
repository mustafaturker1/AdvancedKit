<?php

namespace AdvancedKit\command;

use AdvancedKit\forms\KitForm;
use AdvancedKit\forms\KitOptionsForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class KitCommand extends Command
{
    public function __construct()
    {
        parent::__construct("kit", "Kit command!");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if (!$sender instanceof Player) return false;
        if (!isset($args[0])) {
            $sender->sendForm(new KitForm($sender));
            return false;
        }
        if ($args[0] === "options" and $sender->getServer()->isOp($sender->getName())) {
            $sender->sendForm(new KitOptionsForm());
        }
        return true;
    }
}