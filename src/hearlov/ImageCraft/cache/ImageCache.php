<?php

namespace hearlov\ImageCraft\cache;

use hearlov\ImageCraft\HearMap;
use hearlov\ImageCraft\img\BaseImage;
use hearlov\ImageCraft\provider\MapProvider;

class ImageCache{

    /**
     * @var BaseImage[]
     */
    private static array $images = [];

    /**
     * @var array
     */
    private static array $namespaces = [];

    public static function registerAllMap(MapProvider $provider): void{
        $all = $provider->getMapDatas();
        foreach($all as $args){
            $class = BaseImage::unserialize((int)$args["id"], $args["data"], $args["name"], $args["lora"]);
            self::registerMap($args["id"], $args["name"], $class);
        }
    }

    public static function registerMap(int $id, string $name, BaseImage $baseImage): void{
        self::$images[$id] = $baseImage;
        self::$namespaces[$name][$id] = $baseImage;
    }

    public static function getMap(int $id): ?BaseImage{
        return self::$images[$id] ?? null;
    }

    public static function getAllMap(): array{
        return self::$namespaces;
    }

    public static function isNamespaceAvailable(string $str){
        return in_array($str, array_keys(self::$namespaces));
    }

    /**
     * @return BaseImage[]
     */
    public static function getMapsInNamespace(string $str): array{
        return self::$namespaces[$str] ?? [];
    }

    public static function deleteMapsInNamespace(string $namespace): int{
        $all = self::getMapsInNamespace($namespace);
        foreach($all as $baseImage){
            self::deleteMap($baseImage);
        }
        self::deleteNamespaceControl($namespace);
        return count($all);
    }

    public static function deleteMap(BaseImage $baseImage): void{
        HearMap::getInstance()->provider->deleteMapData($baseImage->map_id);
        if(isset(self::$namespaces[$baseImage->map_name][$baseImage->map_id]))
            unset(self::$namespaces[$baseImage->map_name][$baseImage->map_id]);

        if(isset(self::$images[$baseImage->map_id]))
            unset(self::$images[$baseImage->map_id]);

        self::deleteNamespaceControl($baseImage->map_name);
    }

    public static function deleteNamespaceControl(string $namespace): void{
        if(!self::isNamespaceAvailable($namespace)) return;
        if(count(self::$namespaces[$namespace]) !== 0) return;
        unset(self::$namespaces[$namespace]);
    }

}