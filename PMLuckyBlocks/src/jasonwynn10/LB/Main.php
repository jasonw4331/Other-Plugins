<?php
namespace jasonwynn10\LB;

use pocketmine\block\Gold;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase implements Listener {

    public function onEnable() {
        $this->getLogger()->notice(TF::GREEN."Enabled!");
    }

    public function onDisable() {
        $this->getLogger()->notice(TF::GREEN."Disabled!");
    }

    public function onBlockBreak(BlockBreakEvent $e) {
        $b = $e->getBlock();
        if($b instanceof Gold) {
            $e->setDrops([
            	Item::get(Item::COCOA_BLOCK,0,mt_rand(0,6))
                         ]);
        }
    }
}