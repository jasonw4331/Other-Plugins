<?php
namespace MysteryCrates;

use pocketmine\block\Chest;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\scheduler\PluginTask;

class CloseChest extends PluginTask {
    private $chest;
    private $player;

    public function __construct(Main $plugin, string $player, Chest $chest) {
        parent::__construct($plugin);
        $this->player = $player;
        $this->chest = $chest;
    }

    public function onRun(int $tick) {
        $pk = new BlockEventPacket();
        $pk->x = $this->chest->getX();
        $pk->y = $this->chest->getY();
        $pk->z = $this->chest->getZ();
        $pk->case1 = 1;
        $pk->case2 = 0;
        $pl = $this->getOwner()->getServer()->getPlayer($this->player);
        $pl->dataPacket($pk);
    }
}
