<?php
namespace Primus\CrateKeys;

use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\inventory\DoubleChestInventory;
use pocketmine\item\Item;
use pocketmine\tile\Chest;
use Primus\CrateKeys\event\PlayerCrateKeyRecieveEvent;
use Primus\CrateKeys\event\PlayerCrateKeyUseEvent;

class EventListener implements Listener {
    protected $plugin;
    private $sourceBlocks = [];

    public function __construct (Main $plugin) {
        $this->plugin = $plugin;
        $this->sourceBlocks = $plugin->getConfig()->get('source-blocks');
    }

    public function onChestOpen(InventoryOpenEvent $e) {
        if($e->isCancelled())
            return null;

        $inv = $e->getInventory();
        $p = $e->getPlayer();

        if($p->getInventory()->getItemInHand()->getId() !== $this->plugin->getConfig()->get("key-id"))
            return null;
        // CHECK Permission
        if(!$p->hasPermission("cratekeys.use")) {
            $p->sendMessage($this->plugin->getLang('no-permission-for-use'));
            $e->setCancelled(true);
            return null;
        }

        // Disable DoubleChest inventory open with key
        if($inv instanceof DoubleChestInventory and $this->plugin->getConfig()->get("allow-use-keys-on-double-chests") === false) {
            $e->setCancelled(true);
            $p->sendMessage($this->plugin->getLang('cant-open-double-chest'));
            return null;
        }

        $b = $inv->getHolder();

        if($b->getLevel()->getBlock($b->subtract(0, 1))->getId() === $this->plugin->getConfig()->get("pattern-block-id") or $this->plugin->getConfig()->get('allow-keys-on-all-chests')) {
            if(empty($inv->getContents()) and $this->plugin->getConfig()->get('allow-occupied-chests') === false) {
                $this->plugin->getServer()->getPluginManager()->callEvent(new PlayerCrateKeyUseEvent($e->getPlayer(), $e->getPlayer()->getInventory()->getItemInHand(), $b->getBlock()));
                return null;
            }else{
                $p->sendMessage($this->plugin->getLang('occupied-chest'));
                $e->setCancelled(true);
            }
        }else{
            $p->sendMessage($this->plugin->getLang("incorrect-pattern"));
            $e->setCancelled(true);
        }
    }

    public function onMine(BlockBreakEvent $e) {
        if($e->isCancelled())
            return;
        $p = $e->getPlayer();
        $b = $e->getBlock();
        if(array_key_exists($b->getId(), $this->sourceBlocks)) {
            if($this->plugin->getChance($this->plugin->getConfig()->get('chance'))) {
                if(!$p->hasPermission('cratekeys.receive')) {
                    $p->sendMessage($this->plugin->getLang('no-permission-for-receive'));
                    return;
                }
                $this->plugin->getServer()->getPluginManager()->callEvent(new PlayerCrateKeyRecieveEvent($p, $b));
            }
        }
    }

    public function onKeyUse(PlayerCrateKeyUseEvent $e) {
        if($e->isCancelled())
            return;
        $p = $e->getPlayer();
        $key = $e->getKey();
        // Key--
        $pInv = $p->getInventory();
        $pInv->removeItem($key);
        $rKey = new Item($key->getId(), $key->getDamage(), $key->getCount() - 1);
        $pInv->addItem($rKey);
        // Load chest
        $chestTile = $p->getLevel()->getTile($e->getTarget());
        if($chestTile instanceof Chest) {
            $this->plugin->putRandomContent($chestTile->getInventory());
            $p->sendMessage($this->plugin->getLang("key-use-message", $p));
        }
    }

    public function onKeyRecieve(PlayerCrateKeyRecieveEvent $e) {
        if($e->isCancelled())
            return;
        $p = $e->getPlayer();
        #$b = $e->getBlock();
        // DONT ALLOW GIVE KEYS TO CREATIVE PLAYERS
        if($p->getGamemode() == 1)
            return;
        $p->sendMessage($this->plugin->getLang('key-receive-message'));
        if($this->plugin->getConfig()->get('broadcast-message-on-key-recieve'))
            $this->plugin->getServer()->broadcastMessage($this->plugin->getLang('key-receive-broadcast-message'));
        // GIVE PLAYER KEY
        $key = Item::get($this->plugin->getConfig()->get('key-id'), 0 ,1);
        $e->getPlayer()->getInventory()->addItem($key);
    }

    public function onKeyHold(PlayerItemHeldEvent $e) {
        if($e->isCancelled())
            return;
        $i = $e->getItem();
        if($i->getId() === $this->plugin->getConfig()->get('key-id')) {
            if($this->plugin->getConfig()->get('enable-custom-key-name'))$e->getPlayer()->sendPopup($this->plugin->getConfig()->get('key-popup'));
        }
    }

    public function onPlace(BlockPlaceEvent $e) {
        if($e->isCancelled())
            return;
        $p = $e->getPlayer();
        $b = $e->getBlock();
        // TRYING TO CREATE CRATE CHEST
        if($b->getLevel()->getBlockIdAt($b->getFloorX(), $b->getFloorY() -1, $b->getFloorZ()) === $this->plugin->getConfig()->get('pattern-block-id') && $b->getId() === Block::CHEST) {
            // CHECK PERMISSION
            if($p->hasPermission('cratekeys.chest.crate')) {
                $p->sendMessage($this->plugin->getLang("crate-chest-created"));
            }else{
                $e->setCancelled();
                $p->sendMessage($this->plugin->getLang('no-permission-for-crate-chest-create'));
            }
        }
    }
}