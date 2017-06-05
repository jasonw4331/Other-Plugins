<?php
namespace lifetime;

use pocketmine\entity\Entity;
use pocketmine\scheduler\PluginTask;

class GrowPlayer extends PluginTask {
    public function __construct(Main $plugin) {
        parent::__construct($plugin);
    }
    public function onRun($tick) {
        foreach($this->getOwner()->getServer()->getOnlinePlayers() as $player) {
            $val = $player->getDataProperty(Entity::DATA_SCALE);
            $player->setScale($val + 0.2);
        }
    }
}
