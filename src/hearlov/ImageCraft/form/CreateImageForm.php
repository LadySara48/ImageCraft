<?php

namespace hearlov\ImageCraft\form;

use pocketmine\form\Form;
use pocketmine\player\Player;
use pocketmine\Server;

class CreateImageForm implements Form{

    CONST MAX_WH = 24;

    public function jsonSerialize(): array{
        return [
            "type" => "custom_form",
            "title" => "",
            "content" => [
                ["type" => "input", "text" => "Your Image Name (Namespace)", "placeholder" => "Ex: anime_girl"],
                ["type" => "input", "text" => "Image URL", "placeholder" => "URL ends with .jpg, .jpeg or .png image"],
                ["type" => "slider", "text" => "Width (x,z)", "min" => 1, "max" => self::MAX_WH],
                ["type" => "slider", "text" => "Height (Y)", "min" => 1, "max" => self::MAX_WH],
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void{
        if(is_null($data)) return;

        $name = '"'.$data[0].'"';
        $url = '"'.$data[1].'"';
        $width = '"'.$data[2].'"';
        $height = '"'.$data[3].'"';

        Server::getInstance()->dispatchCommand($player, "imagecreate $name $url $width $height");
    }

}