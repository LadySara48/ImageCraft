<?php
declare(strict_types=1);

namespace hearlov\ImageCraft\item;

use pocketmine\block\utils\RecordType;
use pocketmine\block\VanillaBlocks as Blocks;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\entity\Squid;
use pocketmine\entity\Villager;
use pocketmine\entity\Zombie;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\enchantment\ItemEnchantmentTags as EnchantmentTags;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIdentifier as IID;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\VanillaArmorMaterials as ArmorMaterials;
use pocketmine\math\Vector3;
use pocketmine\utils\CloningRegistryTrait;
use function is_int;
use function mb_strtoupper;
use function strtolower;

/**
 * @method static FilledMap FILLED_MAP()
 */

final class ExtraVanillaItems{
    use CloningRegistryTrait;

    private function __construct(){
        //NOOP
    }

    /**
     * @phpstan-template TItem of Item
     * @phpstan-param \Closure(IID) : TItem $createItem
     * @phpstan-return TItem
     */
    protected static function register(string $name, Item $item){
        self::_registryRegister($name, $item);
    }

    /**
     * @return Item[]
     * @phpstan-return array<string, Item>
     */
    public static function getAll() : array{
        /** @var Item[] $result */
        $result = self::_registryGetAll();
        return $result;
    }

    protected static function setup() : void{
        self::register("filled_map", new FilledMap(new ItemIdentifier(ItemTypeIds::newId()), "Filled Map"));
    }

}