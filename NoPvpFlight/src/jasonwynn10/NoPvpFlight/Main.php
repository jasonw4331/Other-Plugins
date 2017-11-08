<?php
namespace jasonwynn10\NoPvpFlight;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {
	/** @var string[] $players */
    private $players = [];
	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	public function onEntityDamage(EntityDamageEvent $event) {
		if($event instanceof EntityDamageByEntityEvent) {
			$damager = $event->getDamager();
     		$damaged = $event->getEntity();
            if($damaged instanceof Player and !$damaged->hasPermission("pvp.fly") and $damager instanceof Player) {
            	$damaged->setFlying(false);
            	$damaged->setAllowFlight(false);
			}
		}
	}
	public function addPlayer(Player $player) {
		$this->players[$player->getName()] = $player->getName();
	}
	public function isPlayer(Player $player) {
		return in_array($player->getName(), $this->players);
	}
	public function removePlayer(Player $player) {
		unset($this->players[$player->getName()]);
	}
}