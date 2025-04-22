<?php

namespace hearlov\ImageCraft\form;

use ClassyHD\SkyblockMain\Forms;
use hearlov\ImageCraft\cache\ImageCache;
use hearlov\ImageCraft\img\BaseImage;
use pocketmine\form\Form;
use pocketmine\player\Player;

class ListAllImageNamespacesForm implements Form{

    private array $list;

    public function __construct(){
        $this->list = array_keys(ImageCache::getAllMap());
    }

    public function jsonSerialize(): array{
        $args = array_map(fn($val) => ["text" => $val], $this->list);
        return [
            "type" => "form",
            "title" => "All image Namespaces",
            "content" => "",
            "buttons" => $args
        ];
    }

    public function handleResponse(Player $player, $data): void{
        if(is_null($data)) return;

        $namespace = $this->list[$data] ?? null;
        if($namespace === null) return;
        if(!ImageCache::isNamespaceAvailable($namespace)) return;

        $player->sendForm(new ListOfNamespaceForm($namespace));
    }

}