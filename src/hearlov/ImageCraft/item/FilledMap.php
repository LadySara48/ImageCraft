<?php

namespace hearlov\ImageCraft\item;

use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;

class FilledMap extends Item{
    private int $map_id;

    public function setId(int $id): self{
        $this->map_id = $id;
        return $this;
    }

    public function getMapId(): int{
        return $this->map_id ?? 0;
    }

    protected function serializeCompoundTag(CompoundTag $tag): void{
        parent::serializeCompoundTag($tag);
        $tag->setLong("map_uuid", $this->map_id ?? 0);
    }

    protected function deserializeCompoundTag(CompoundTag $tag): void{
        parent::deserializeCompoundTag($tag);
        $this->map_id = $tag->getLong("map_uuid", 0);
    }

}