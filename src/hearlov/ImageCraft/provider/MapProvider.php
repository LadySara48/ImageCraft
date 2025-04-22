<?php

namespace hearlov\ImageCraft\provider;

use hearlov\ImageCraft\HearMap;
use SQLite3;

class MapProvider extends SQLite3{

        public function __construct(){
            $dir = HearMap::getInstance()->getDataFolder();
            parent::__construct($dir."Data.db");
            $this->exec("CREATE TABLE IF NOT EXISTS mapData(id INTEGER PRIMARY KEY, name TEXT, data TEXT, lora TEXT)");
        }

        public function setMapData(string $name, string $data, string $lora){
            $WrteHandle = $this->prepare("INSERT INTO mapData(name, data, lora) VALUES (:name, :data, :lora)");
            $WrteHandle->bindParam(":name", $name);
            $WrteHandle->bindParam(":data", $data, SQLITE3_BLOB);
            $WrteHandle->bindParam(":lora", $lora);
            $WrteHandle->bindParam(":delay", $delay);
            $WrteHandle->bindParam(":type", $type);
            $WrteHandle->execute();
        }

        public function deleteMapData(int $map_id){
            $DeleteHandle = $this->prepare("DELETE FROM mapData WHERE id = :id");
            $DeleteHandle->bindParam(":id", $map_id);
            $DeleteHandle->execute();
        }

        public function getMapData(string $name): array{
            $ReadHandle = $this->prepare("SELECT * FROM mapData WHERE name = :name");
            $ReadHandle->bindParam(":name", $name);
            $exec = $ReadHandle->execute();
            while($datas = $exec->fetchArray(SQLITE3_ASSOC)) $args[] = $datas;
            return $args ?? [];
        }

        public function getMapDatas(): array{
            $ReadHandle = $this->prepare("SELECT * FROM mapData");
            $exec = $ReadHandle->execute();
            while($datas = $exec->fetchArray(SQLITE3_ASSOC)) $args[] = $datas;
            return $args ?? [];
        }

}