<?php

namespace AdvancedKit;

use AdvancedKit\command\KitCommand;
use AdvancedKit\provider\SQLite;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;

class Kit extends PluginBase
{

    /**
     * @var Kit
     */
    private static Kit $instance;

    protected function onEnable(): void
    {
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
        self::$instance = $this;
        new SQLite();
        $this->getServer()->getCommandMap()->register("kit", new KitCommand());
        $this->getLogger()->notice("AdvancedKit Enable - https://github.com/mustafaturker1");
        $data = SQLite::getDatabase()->getKitData();
        if (!empty($data)) {
            foreach ($data as $datum) {
                PermissionManager::getInstance()->addPermission(new Permission($datum["kitPerm"]));
            }
        }
    }

    /**
     * @return Kit
     */
    public static function getInstance(): Kit
    {
        return self::$instance;
    }
}
