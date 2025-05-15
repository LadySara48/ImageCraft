<?php

namespace hearlov\ImageCraft\command;

use hearlov\ImageCraft\cache\ImageCache;
use hearlov\ImageCraft\HearMap;
use hearlov\ImageCraft\img\BaseImage;
use pocketmine\block\ItemFrame;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\Position;

class SetImageCommand extends Command{

    public function __construct(){
        parent::__construct("setimage", "Set images to selected positions", "/setimage", []);
        $this->setPermission("imagecraft.setimage");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender instanceof Player) return;
        if(!(isset(SelectFrameCommand::$playerSelects[$sender->getName()][0]) && isset(SelectFrameCommand::$playerSelects[$sender->getName()][1]))) return;
        if(!ImageCache::isNamespaceAvailable(($args[0] ?? ""))){
            $sender->sendMessage("ImageMap is not found");
            return;
        }
        /**
         * @var Position $pos1
         * @var Position $pos2
         */
        $pos1 = SelectFrameCommand::$playerSelects[$sender->getName()][0];
        $pos2 = SelectFrameCommand::$playerSelects[$sender->getName()][1];

        if(!($sender->getWorld()->getFolderName() === $pos1->getWorld()->getFolderName() && $pos1->getWorld()->getFolderName() === $pos2->getWorld()->getFolderName())) return;

        $x = (max($pos1->getFloorX(), $pos2->getFloorX()) - min($pos1->getFloorX(), $pos2->getFloorX())) + 1;
        $y = (max($pos1->getFloorY(), $pos2->getFloorY()) - min($pos1->getFloorY(), $pos2->getFloorY())) + 1;
        $z = (max($pos1->getFloorZ(), $pos2->getFloorZ()) - min($pos1->getFloorZ(), $pos2->getFloorZ())) + 1;

        $origin = min($x, $z);
        $width = max($x, $z);
        $height = $y;

        if($origin !== 1){
            $sender->sendMessage("The selected origin can only be 1 block thick.");
            return;
        }

        $images = array_values(ImageCache::getMapsInNamespace($args[0] ?? ""));
        if(($width*$height) !== count($images)){
            $sender->sendMessage("The selected area is not compatible with the image set.");
            return;
        }

        if(($pos1->getFloorY() - $pos2->getFloorY()) <= 0){
            $sender->sendMessage("Selected area is invalid. First select the top-left corner, then the bottom-right corner.");
            return;
        }

        if($x >= 2){
            $widthpos1 = $pos1->getFloorX();
            $widthpos2 = $pos2->getFloorX();
            $widthpos3 = $pos1->getFloorZ();
            $position_type = "x";
        }elseif($z >= 2){
            $widthpos1 = $pos1->getFloorZ();
            $widthpos2 = $pos2->getFloorZ();
            $widthpos3 = $pos1->getFloorX();
            $position_type = "z";
        }else{
            $sender->sendMessage("Selected area is invalid. First select the top-left corner, then the bottom-right corner.");
            return;
        }

        print_r("$widthpos1,$widthpos2,$widthpos3");

        $id = 0;
        foreach(range($widthpos1, $widthpos2) as $posx){
            foreach(range(max($pos1->getFloorY(), $pos2->getFloorY()), min($pos1->getFloorY(), $pos2->getFloorY())) as $posy){
                $vector = new Vector3($position_type === "x" ? $posx : $widthpos3, $posy, $position_type === "z" ? $posx : $widthpos3);
                $block = $pos1->getWorld()->getBlock($vector);
                if(!$block instanceof ItemFrame) return;
                /**
                 * @var BaseImage $image
                 */
                $image = $images[$id];
                $block->setFramedItem($image->getMapItem());
                $pos1->getWorld()->setBlock($vector, $block);

                ++$id;
            }
        }
    }

    public function getOwningPlugin(): HearMap{
        return HearMap::getInstance();
    }

}