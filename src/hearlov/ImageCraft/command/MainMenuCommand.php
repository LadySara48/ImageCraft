<?php

namespace hearlov\ImageCraft\command;

use hearlov\ImageCraft\form\MainMenuForm;
use hearlov\ImageCraft\HearMap;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class MainMenuCommand extends Command{

    public function __construct(){
        parent::__construct("imagemenu", "Show ImageCraft Menu", "/imagemenu", []);
        $this->setPermission("imagecraft.menu");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender instanceof Player) return;

        $sender->sendForm(new MainMenuForm());
    }

    public function getOwningPlugin(): HearMap{
        return HearMap::getInstance();
    }

}