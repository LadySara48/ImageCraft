<?php

namespace hearlov\ImageCraft\command;

use hearlov\ImageCraft\cache\ImageCache;
use hearlov\ImageCraft\HearMap;
use hearlov\ImageCraft\img\BaseImage;
use hearlov\ImageCraft\MapProcess;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class DeleteImageCommand extends Command{

    public function __construct(){
        parent::__construct("imagedelete", "Delete for Images", "/imagedelete (name or map_id)", ["deleteimage", "removeimage"]);
        $this->setPermission("imagecraft.delete");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender instanceof Player) return;

        if(!isset($args[0])){
            $sender->sendMessage("usage: /imagedelete (name or map_id)");
            return;
        }

        if(is_numeric($args[0])){

            $map = ImageCache::getMap((int)$args[0]);
            if(!$map instanceof BaseImage){
                $sender->sendMessage("There is no image with this ID.");
                return;
            }

            ImageCache::deleteMap($map);
            $sender->sendMessage("The image has been successfully removed.");

        }elseif(strlen($args[0] > 0)){

            if(!ImageCache::isNamespaceAvailable($args[0])){
                $sender->sendMessage("There is no image with this Namespace.");
                return;
            }

            $val = ImageCache::deleteMapsInNamespace($args[0]);
            $sender->sendMessage("A total of $val images have been successfully removed.");

        }



    }

    public function getOwningPlugin(): HearMap{
        return HearMap::getInstance();
    }

}