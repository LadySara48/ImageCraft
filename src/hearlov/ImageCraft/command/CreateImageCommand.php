<?php

namespace hearlov\ImageCraft\command;

use hearlov\ImageCraft\MapProcess;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class CreateImageCommand extends Command{

    public function __construct(){
        parent::__construct("imagecreate", "Create Image Statue", "/imagecreate", ["imagemake", "makeimage"]);
        $this->setPermission("imagecraft.create");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender instanceof Player) return;

        if(!(isset($args[0]) && isset($args[1]) && isset($args[2]) && isset($args[3]))){
            $sender->sendMessage("usage: /imagemake (name) (image url) (width) (height)");
            return;
        }

        if(!filter_var($args[1], FILTER_VALIDATE_URL) || !preg_match('/\.(jpg|jpeg|png)$/i', $args[1])){
            $sender->sendMessage("The link you provided is either invalid or unsupported.");
            return;
        }

        if(!(is_numeric($args[2]) && is_numeric($args[3]))){
            $sender->sendMessage("Height and Width must be numeric values only.");
            return;
        }

        if(strlen($args[0]) > 16){
            $sender->sendMessage("Image name cannot be longer than 16 characters.");
            return;
        }

        MapProcess::createMapInCommand($args[1], $args[0], (int)$args[2], (int)$args[3], $sender);

    }

}