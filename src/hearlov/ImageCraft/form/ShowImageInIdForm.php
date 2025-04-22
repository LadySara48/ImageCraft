<?php

namespace hearlov\ImageCraft\form;

use hearlov\ImageCraft\cache\ImageCache;
use hearlov\ImageCraft\img\BaseImage;
use pocketmine\form\Form;
use pocketmine\player\Player;
use pocketmine\Server;

class ShowImageInIdForm implements Form{

    private ?BaseImage $image;

    public function __construct(int $id){
        $this->image = ImageCache::getMap($id);
    }

    public function jsonSerialize(): array{
        if($this->image instanceof BaseImage) {
            return [
                "type" => "form",
                "title" => "Show Image #" . $this->image->map_id,
                "content" => "Image Lore: " . implode(", ", $this->image->lora),
                "buttons" => [
                    ["text" => "Give self Map"],
                    ["text" => "Give self Namespace Maps"],
                    ["text" => "Set Namespaces selected frames"],
                    ["text" => "Delete self Map"],
                    ["text" => "Delete self Namespace Maps"],
                ]
            ];
        }else{
            return [
                "type" => "form",
                "title" => "Image Not Found",
                "content" => "Not found this image",
                "buttons" => []
            ];
        }
    }

    public function handleResponse(Player $player, $data): void{
        if(is_null($data)) return;
        if(!$this->image instanceof BaseImage) return;

        switch($data){
            case 0:
                Server::getInstance()->dispatchCommand($player, "giveimage ".$this->image->map_id);
                break;
            case 1:
                Server::getInstance()->dispatchCommand($player, "giveimage ".$this->image->map_name);
                break;
            case 2:
                Server::getInstance()->dispatchCommand($player, "setimage ".$this->image->map_name);
                break;
            case 3:
                Server::getInstance()->dispatchCommand($player, "imagedelete ".$this->image->map_id);
                break;
            case 4:
                Server::getInstance()->dispatchCommand($player, "imagedelete ".$this->image->map_name);
        }

    }

}