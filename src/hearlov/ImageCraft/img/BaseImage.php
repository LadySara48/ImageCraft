<?php

namespace hearlov\ImageCraft\img;

use hearlov\ImageCraft\item\ExtraVanillaItems;
use hearlov\ImageCraft\item\FilledMap;
use hearlov\ImageCraft\math\ImageMapSerializer;
use pocketmine\item\Item;

use function gzcompress;
use function gzuncompress;

class BaseImage{

    /**
     * @var string
     */
    public readonly string $map_name;
    /**
     * @var int
     */
    public readonly int $map_id;
    /**
     * @var Frame[]
     */
    public readonly array $classes;
    /**
     * @var array
     */
    public readonly array $lora;


    /**
     * @param string $name
     * @param int $map_id
     * @param array $classes
     * @param string $lora
     */
    public function __construct(string $name, int $map_id, array $classes, string $lora){
        $this->map_name = $name;
        $this->map_id = $map_id;
        $this->classes = $classes;
        $this->lora = explode(":", $lora);
    }

    /**
     * @return FilledMap
     */
    public function getMapItem(): Item{
        $item = ExtraVanillaItems::FILLED_MAP();
        $item->setId($this->map_id);
        $item->setLore(["Pos: (".$this->lora[0].", ".$this->lora[1].")", "Name:".$this->map_name." #".$this->map_id]);
        return $item;
    }

    public static function serialize(string $classes){
        return gzcompress($classes);
    }

    public static function unserialize(int $id, string $data, string $name, string $lora){
        $args = gzuncompress($data);
        $alldata[0] = new Frame(ImageMapSerializer::unserializeString($args));
        return new self($name, $id, $alldata, $lora);
    }

}