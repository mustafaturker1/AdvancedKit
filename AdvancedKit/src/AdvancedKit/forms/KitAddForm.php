<?php

namespace AdvancedKit\forms;

use AdvancedKit\provider\SQLite;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class KitAddForm extends CustomForm
{

    /**
     * @var array|string[]
     */
    private array $time = ["3", "7", "12"];

    public function __construct()
    {
        parent::__construct("Kit Add", [
            new Input("kitName", "Kit Name:", "eg. Premium"),
            new Label("label1", "\n"),
            new Dropdown("kitTime", "Kit Time (Day)", $this->time),
            new Label("label2", "\n")
        ], function (Player $player, CustomFormResponse $response): void {
            $kitName = TextFormat::clean($response->getString("kitName"));
            $kitTime = $this->time[$response->getInt("kitTime")];
            $data = SQLite::getDatabase();
            if ($data->isKitDataControl($kitName)) {
                $player->sendMessage("§cThere is a kit with the same name!");
                return;
            }
            $kitPerm = strtolower($kitName . ".perm");
            $this->addKitItems($player, $kitName, $kitPerm, $kitTime * 86400);
        });
    }

    public function addKitItems(Player $player, string $kitName, string $kitPerm, int $kitTime)
    {
        $inventory = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $inventory->setName($kitName . " Add Items");
        $inventory->setInventoryCloseListener(function () use ($player, $inventory, $kitName, $kitPerm, $kitTime): void {
            if (!empty($inventory->getInventory()->getContents())) {
                $items = SQLite::getDatabase()->encode($inventory->getInventory()->getContents());
                SQLite::getDatabase()->createKitData($kitName, $kitPerm, $kitTime, $items);
                $player->sendMessage("§aSuccessfully created the kit!");
                $player->sendMessage("§eKit Perm: §6" . $kitPerm);
                PermissionManager::getInstance()->addPermission(new Permission($kitPerm));
            } else {
                $player->sendMessage("§cOperation failed because kit items were not determined!");
            }
        });
        $inventory->send($player);
    }
}