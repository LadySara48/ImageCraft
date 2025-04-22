<?php
declare(strict_types=1);

namespace hearlov\ImageCraft;

use hearlov\ImageCraft\cache\ImageCache;
use hearlov\ImageCraft\command\CreateImageCommand;
use hearlov\ImageCraft\command\DeleteImageCommand;
use hearlov\ImageCraft\command\GiveMapCommand;
use hearlov\ImageCraft\command\MainMenuCommand;
use hearlov\ImageCraft\command\SelectFrameCommand;
use hearlov\ImageCraft\command\SetImageCommand;
use hearlov\ImageCraft\img\Frame;
use hearlov\ImageCraft\item\ExtraVanillaItems;
use hearlov\ImageCraft\math\ImageMapSerializer;
use hearlov\ImageCraft\math\ImageSplitter;
use hearlov\ImageCraft\provider\MapProvider;
use pocketmine\block\ItemFrame;
use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\StringToItemParser;
use pocketmine\network\mcpe\protocol\ClientboundMapItemDataPacket;
use pocketmine\network\mcpe\protocol\MapInfoRequestPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\plugin\PluginBase;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use pocketmine\network\mcpe\protocol\types\MapImage;

/*
 *
 * ImageCraft HearMap => PNG, JPG, WEBP and GIF files load in map data
 *
 */

class HearMap extends PluginBase implements Listener{

    /**
     * @var MapProvider
     */
    public MapProvider $provider;

    /**
     * @var HearMap
     */
    private static self $instance;
    public static function getInstance(): self{ return self::$instance; }
    public function onLoad(): void{ self::$instance = $this; }

    public function onEnable(): void{
        $this->registerItems();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->registerAll("hearmap", [
            new GiveMapCommand(), new SelectFrameCommand(), new SetImageCommand(), new CreateImageCommand(),
            new MainMenuCommand(), new DeleteImageCommand()
        ]);
        $this->provider = new MapProvider();
        ImageCache::registerAllMap($this->provider);

        //MapProcess::createMapInFile($this->getDataFolder()."tifa", "tifa3", "jpg", 16, 9);

    }

    public function registerItems(): void{
        $item = ExtraVanillaItems::FILLED_MAP();

        GlobalItemDataHandlers::getDeserializer()->map(ItemTypeNames::FILLED_MAP, fn() => clone $item);
        GlobalItemDataHandlers::getSerializer()->map($item, fn() => new SavedItemData(ItemTypeNames::FILLED_MAP));

        StringToItemParser::getInstance()->register("filled_map", fn() => clone $item);
        StringToItemParser::getInstance()->register("image", fn() => clone $item);
    }

    public function onRequest(DataPacketReceiveEvent $event){
        $pack = $event->getPacket();
        if(!$pack instanceof MapInfoRequestPacket) return;
        $data = ImageCache::getMap($pack->mapId);
        if($data === null) return;

        $packet = new ClientboundMapItemDataPacket();
        $packet->mapId = $pack->mapId;
        $packet->dimensionId = DimensionIds::OVERWORLD;
        $packet->isLocked = false;
        $packet->scale = 1;
        $packet->xOffset = $packet->yOffset = 0;
        $packet->colors = new MapImage($data->classes[0]->getData());
        $packet->origin = new BlockPosition(0,0, 0);
        $packet->parentMapIds[] = $pack->mapId;
        $event->getOrigin()->sendDataPacket($packet);
    }

    public function playerInteract(PlayerInteractEvent $event){
        if($event->getBlock() instanceof ItemFrame) SelectFrameCommand::interactive($event);
    }

}