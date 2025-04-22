<?php

namespace hearlov\ImageCraft\command;

use hearlov\ImageCraft\cache\ImageCache;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class GiveMapCommand extends Command{

    public function __construct(){
        parent::__construct("givemap", "Give an ImageCraft Map", "/giveimage", ["giveimage", "imagegive"]);
        $this->setPermission("imagecraft.give");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender instanceof Player) return;
        if(!isset($args[0])){
            $sender->sendMessage("usage: /giveimage <map_name|map_id>");
            return;
        }

        if(isset($args[0]) && is_numeric($args[0])){
            $image = ImageCache::getMap((int)$args[0]);
            if($image === null){
                $sender->sendMessage("ImageMap is not found");
                return;
            }

            $item = $image->getMapItem();
            if($sender->getInventory()->canAddItem($item)) $sender->getInventory()->addItem($item);
            else{
                $sender->getWorld()->dropItem($sender->getPosition()->add(0,1,0), $item, new Vector3((mt_rand(0,6)/4),0.2, (mt_rand(0,6)/4)), 20);
            }
        }else{
            if(!ImageCache::isNamespaceAvailable($args[0])){
                $sender->sendMessage("ImageMap is not found");
                return;
            }

            $images = ImageCache::getMapsInNamespace($args[0]);
            $allitems = array_map(function($b) use ($sender){
                $item = $b->getMapItem();
                if($sender->getInventory()->canAddItem($item)) $sender->getInventory()->addItem($item);
                else{
                    $sender->getWorld()->dropItem($sender->getPosition()->add(0,1,0), $item, new Vector3((mt_rand(0,6)/4),0.2, (mt_rand(0,6)/4)), 20);
                }
                return $b->getMapItem();
            }, $images);
        }
    }

}