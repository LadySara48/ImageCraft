<?php

namespace hearlov\ImageCraft;

use hearlov\ImageCraft\asyncpool\DownloadTask;
use hearlov\ImageCraft\cache\ImageCache;
use hearlov\ImageCraft\img\BaseImage;
use hearlov\ImageCraft\math\ImageMapSerializer;
use hearlov\ImageCraft\math\ImageSplitter;
use pocketmine\player\Player;

use pocketmine\math\Vector2;
use pocketmine\Server;

class MapProcess{

    /**
     * @const Array IMAGE_EXTENSIONS
     * Supported image extensions.
     * I originally planned to support WebP when developing this plugin, but unfortunately,
     * the GD library in PHP-Binaries behaves like an outdated version and does not support WebP.
     */
    CONST IMAGE_EXTENSIONS = [
        "jpg", "jpeg", "png"
    ];

    /**
     * @param string $str
     * @param string $name
     * @param int $width
     * @param int $height
     * @param Player $player
     * @return void
     */
    public static function createMapInCommand(string $str, string $name, int $width, int $height, Player $player): void{
        $extension = pathinfo($str, PATHINFO_EXTENSION);
        if(!in_array($extension, self::IMAGE_EXTENSIONS)) return;

        $args = [
            "fullpath" => HearMap::getInstance()->getDataFolder()."$name", "url" => $str,
            "name" => $name, "width" => $width, "height" => $height, "player" => $player->getName(),
            "extension" => $extension
        ];

        $async = new DownloadTask(serialize($args));
        Server::getInstance()->getAsyncPool()->submitTask($async);
        $player->sendMessage("The image you requested to process has been added to the download queue. Please wait.");

    }

    /**
     * @param string $file
     * @param string $name
     * @param string $extension
     * @param int $width
     * @param int $height
     * @param string $player
     * @return void
     *
     * Download task Returned Event
     *
     */
    public static function commandRequestConfirmed(string $file, string $name, string $extension, int $width, int $height, string $player): void{
        self::createMapInFile($file, $name, $extension, $width, $height, $player);
    }

    public static function commandRequestUncorfirmed(string $player, string $error): void{
        if(($p = Server::getInstance()->getPlayerExact($player)) instanceof Player){
            $p->sendMessage("The file from the provided link could not be downloaded. Reason: $error");
        }
    }


    /**
     * @param string $file
     * @param string $name
     * @param string $extension
     * @param int $width
     * @param int $height
     * @param string|null $player
     * @return void
     */
    public static function createMapInFile(string $file, string $name, string $extension, int $width, int $height, ?string $player = null): void{
        if(!in_array($extension, self::IMAGE_EXTENSIONS)) return;
        if(ImageCache::isNamespaceAvailable($name)) return;

        self::makeImage($file, $name, $extension, $width, $height, $player);

    }

    /**
     * @param string $file
     * @param string $name
     * @param string $extension
     * @param int $width
     * @param int $height
     * @param string|null $player
     * @return void
     *
     * Make Image in Server Querry ($file = file no extension name, $extension "png", "jpg" like in not dot extension)
     *
     */
    private static function makeImage(string $file, string $name, string $extension, int $width, int $height, ?string $player = null): void{
        $split = ImageSplitter::splitImage($file, $width, $height, $extension);
        $counter = self::getLine2D($width, $height);

        $loras = []; //Optimized Classes (128x128 was added to avoid opening the new Color class)
        foreach($split as $index => $args){
            $classes = ImageMapSerializer::serializeArray($args);
            $lora2d = $counter[$index];
            $lora = $lora2d->getFloorX().":".$lora2d->getFloorY();

            $serialized = BaseImage::serialize($classes);
            HearMap::getInstance()->provider->setMapData($name, $serialized, $lora);
            $loras[$lora] = $classes;
        }

        $data = HearMap::getInstance()->provider->getMapData($name);
        foreach($data as $args){
            $classes[0] = $loras[$args["lora"]];
            $class = new BaseImage($args["id"], $classes, $args["name"], $args["lora"]);
            ImageCache::registerMap($args["id"], $args["name"], $class);
        }

        if(($p = Server::getInstance()->getPlayerExact($player)) instanceof Player) $p->sendMessage("Image completed.\nPlacement Command: /setimage $name\nGet Maps Command: /giveimage $name");
    }


    /**
     * @param int $width
     * @param int $height
     * @return Vector2[]
     */
    protected static function getLine2D(int $width, int $height): array{
        $args = [];
        for($x = 0; $x < $width; ++$x){
            for($y = 0; $y < $height; ++$y){
                $args[] = new Vector2($x, $y);
            }
        }
        return $args;
    }

}