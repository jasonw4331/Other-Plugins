<?php
namespace MysteryCrates;

use pocketmine\network\mcpe\protocol\RemoveEntityPacket;
use pocketmine\scheduler\PluginTask;

class DespawnItem extends PluginTask {

    private $eid;

    public function __construct(Main $plugin, int $eid) {
        parent::__construct($plugin);
        $this->eid = $eid;
    }

    public function onRun(int $tick) {
        $players = $this->getOwner()->getServer()->getOnlinePlayers();
        $pk = new RemoveEntityPacket();
        $pk->entityUniqueId = $this->eid;
        foreach ($players as $pl) {
            $pl->directDataPacket($pk);
        }
    }
}
