<?php
namespace lifetime;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase implements Listener {
    public function onEnable() {
        $this->getLogger()->notice(TF::GREEN."Enabled!");
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new GrowPlayer($this), 20 * 60);
    }
    public function onJoin(PlayerJoinEvent $ev) {
        $p = $ev->getPlayer();
        $p->setScale(0.2);
    }
    public function onDeath(PlayerDeathEvent $ev) {
        $p = $ev->getPlayer();
        $p->setScale(0.2);
    }
    public function onDisable() {
        $this->getLogger()->notice(TF::GREEN."Disabled!");
    }
}
