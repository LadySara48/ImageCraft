<?php

namespace hearlov\ImageCraft\form;

use hearlov\ImageCraft\cache\ImageCache;
use hearlov\ImageCraft\img\BaseImage;
use pocketmine\form\Form;
use pocketmine\player\Player;

class ListOfNamespaceForm implements Form{

    private array $list;

    public function __construct(public string $namespace){
        $this->list = array_values(ImageCache::getMapsInNamespace($namespace));
    }

    public function jsonSerialize(): array{
        /**
         * @var BaseImage $val
         */
        $args = array_map(fn($val) => ["text" => $val->map_name ." #".$val->map_id], $this->list);
        return [
            "type" => "form",
            "title" => "All images of ". $this->namespace,
            "content" => "",
            "buttons" => $args
        ];
    }

    public function handleResponse(Player $player, $data): void{
        if(is_null($data)) return;

        $baseimage = $this->list[$data] ?? null;
        if($baseimage === null) return;

        $player->sendForm(new ShowImageInIdForm($baseimage->map_id));

    }

}