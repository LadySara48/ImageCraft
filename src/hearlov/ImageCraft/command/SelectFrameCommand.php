<?php

namespace hearlov\ImageCraft\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\player\Player;

class SelectFrameCommand extends Command{

    public static array $playerSelects = [];

    public static array $playerMode = [];

    public function __construct(){
        parent::__construct("frameselect", "Select two frame positions", "/frameselect", []);
        $this->setPermission("imagecraft.select");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender instanceof Player) return;

        self::$playerMode[$sender->getName()] = 1;
        $sender->sendMessage("Select first frame (left up)");
    }

    public static function interactive(PlayerInteractEvent &$event): void{
        $player = $event->getPlayer();
        if(!isset(self::$playerMode[$player->getName()])) return;

        if(self::$playerMode[$player->getName()] === 1){
            self::$playerSelects[$player->getName()][0] = $event->getBlock()->getPosition();
            self::$playerMode[$player->getName()]++;
            $player->sendMessage("Select second frame (right down)");
        }else{
            self::$playerSelects[$player->getName()][1] = $event->getBlock()->getPosition();
            unset(self::$playerMode[$player->getName()]);
            $player->sendMessage("You can now use the /setimage command.");
        }
    }

}