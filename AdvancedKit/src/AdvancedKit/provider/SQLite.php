<?php

namespace AdvancedKit\provider;

use AdvancedKit\Kit;
use pocketmine\item\Item;
use SQLite3;

class SQLite
{

    /**
     * @var SQLite
     */
    private static SQLite $database;

    private SQLite3 $kit;

    public function __construct()
    {
        self::$database = $this;
        $this->kit = new SQLite3(Kit::getInstance()->getDataFolder() . "Kits.db");
        $this->kit->exec("CREATE TABLE IF NOT EXISTS Kits(kitName VARCHAR(20), kitPerm VARCHAR(20), kitTime INT, kitItems TEXT)");
        $this->kit->exec("CREATE TABLE IF NOT EXISTS UsedKits(playerName VARCHAR(20), kitTime INT)");
    }

    /**
     * @param string $playerName
     * @param int $time
     * @return void
     */
    public function createUsedKitData(string $playerName, int $time): void
    {
        $data = $this->kit->prepare("INSERT INTO UsedKits(playerName, kitTime) VALUES(:playerName, :kitTime)");
        $data->bindValue(":playerName", $playerName);
        $data->bindValue(":kitTime", $time);
        $data->execute();
    }

    /**
     * @param string $playerName
     * @param int $time
     * @return void
     */
    public function updateUsedKitData(string $playerName, int $time): void
    {
        $data = $this->kit->prepare("UPDATE UsedKits SET kitTime = :kitTime WHERE playerName = :playerName");
        $data->bindValue(":kitTime", $time);
        $data->bindValue(":playerName", $playerName);
        $data->execute();
    }

    /**
     * @param string $playerName
     * @return bool
     */
    public function isUsedKitDataControl(string $playerName): bool
    {
        $data = $this->kit->prepare("SELECT * FROM UsedKits WHERE playerName = :playerName");
        $data->bindValue(":playerName", $playerName);
        $control = $data->execute();
        if (empty($control->fetchArray(SQLITE3_ASSOC))) return false; else {
            return true;
        }
    }

    /**
     * @param string $kitName
     * @return bool
     */
    public function isKitDataControl(string $kitName): bool
    {
        $data = $this->kit->prepare("SELECT * FROM Kits WHERE kitName = :kitName");
        $data->bindValue(":kitName", $kitName);
        $control = $data->execute();
        if (empty($control->fetchArray(SQLITE3_ASSOC))) return false; else {
            return true;
        }
    }

    /**
     * @param string $kitName
     * @param string $kitPerm
     * @param int $kitTime
     * @param string $kitItems
     * @return void
     */
    public function createKitData(string $kitName, string $kitPerm, int $kitTime, string $kitItems): void
    {
        $data = $this->kit->prepare("INSERT INTO Kits(kitName, kitPerm, kitTime, kitItems) VALUES(:kitName, :kitPerm, :kitTime, :kitItems)");
        $data->bindValue(":kitName", $kitName);
        $data->bindValue(":kitPerm", $kitPerm);
        $data->bindValue(":kitTime", $kitTime);
        $data->bindValue(":kitItems", $kitItems);
        $data->execute();
    }

    /**
     * @param string $kitName
     * @return void
     */
    public function removeKitData(string $kitName): void
    {
        if ($this->isKitDataControl($kitName)) {
            $data = $this->kit->prepare("DELETE FROM Kits WHERE kitName = :kitName");
            $data->bindValue(":kitName", $kitName);
            $data->execute();
        }
    }

    /**
     * @param string $kitName
     * @return bool|array
     */
    public function decode(string $kitName): bool|array
    {
        foreach ($this->getKitData() as $datum) if ($datum["kitName"] == $kitName) {
            return array_map(fn($i) => Item::jsonDeserialize($i), json_decode($datum["kitItems"], true));

        }
        return true;
    }

    /**
     * @param array $array
     * @return bool|string
     */
    public function encode(array $array): bool|string
    {
        return json_encode(array_map(fn($i) => $i->jsonSerialize(), $array));
    }

    /**
     * @return array
     */
    public function getUsedKitData(): array
    {
        $data = $this->kit->prepare("SELECT * FROM UsedKits");
        $control = $data->execute();
        $array = [];

        while ($rows = $control->fetchArray(SQLITE3_ASSOC)) {
            $array[] = $rows;
        }
        return $array;
    }

    /**
     * @return array
     */
    public function getKitData(): array
    {
        $data = $this->kit->prepare("SELECT * FROM Kits");
        $control = $data->execute();
        $array = [];

        while ($rows = $control->fetchArray(SQLITE3_ASSOC)) {
            $array[] = $rows;
        }
        return $array;
    }

    /**
     * @return SQLite
     */
    public static function getDatabase(): SQLite
    {
        return self::$database;
    }
}