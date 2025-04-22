<?php

namespace hearlov\ImageCraft\form;

use pocketmine\form\Form;
use pocketmine\player\Player;

class MainMenuForm implements Form{

    public function jsonSerialize(): array{
        return [
            "type" => "form",
            "title" => "ImageCraft Main Menu",
            "content" => "",
            "buttons" => [
                ["text" => "Create New Image"],
                ["text" => "List all Images"]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void{
        switch($data){
            case 0:
                $player->sendForm(new CreateImageForm());
                break;
            case 1:
                $player->sendForm(new ListAllImageNamespacesForm());
                break;
        }
    }

}