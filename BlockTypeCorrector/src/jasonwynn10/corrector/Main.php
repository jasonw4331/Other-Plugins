<?php
namespace jasonwynn10\corrector;

use pocketmine\block\Block;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\plugin\PluginBase;

class Main extends pluginBase implements Listener {
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        foreach ($this->getServer()->getLevels() as $level) {
            $this->getServer()->getScheduler()->scheduleTask(new Corrector($this, $level));
        }
    }
    public function onTap(PlayerInteractEvent $ev) {
        $this->getLogger()->info("TAPPED");
        $block = $ev->getBlock();
        $this->getLogger()->info("ID: ".$block->getId().":".$block->getDamage());
        if(array_key_exists("{$block->getId()}:{$block->getDamage()}", $this->getConfig()->get("blocks", []))) {
            $arr = explode(":", $this->getConfig()->get("blocks", [])["{$block->getId()}:{$block->getDamage()}"]);
            $newBlock = Block::get((int)$arr[0], (int)$arr[1]);
            $ev->getPlayer()->getLevel()->setBlock($block, $newBlock,false, true);
            $this->getLogger()->info("FIXED");
            $this->getLogger()->info("ID: ".$newBlock->getId().":".$newBlock->getDamage());
        }else{
            $this->getLogger()->info("KEY doesn't exist!");
        }
    }
}