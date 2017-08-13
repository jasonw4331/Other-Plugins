<?php
namespace hidden\unhidden;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\item\Item;

class Main extends PluginBase implements Listener
{
	private $hidden = [];
	public function onJoin(PlayerJoinEvent $ev) {
		$ev->getPlayer()->getInventory()->setItemInHand(Item::get(Item::CLOCK)); //TODO make item customizable
		foreach($this->getServer()->getOnlinePlayers() as $player) {
			if(in_array($player->getName(), $this->hidden) and $player->canSee($ev->getPlayer())) {
				$player->hidePlayer($ev->getPlayer());
			}
			if(in_array($ev->getPlayer()->getName(), $this->hidden) and $ev->getPlayer()->canSee($player)) {
				$ev->getPlayer->hidePlayer($player);
			}
		}
	}
	public function onTap(PlayerInteractEvent $ev) {
		if($ev->getItem()->getId() == Item::CLOCK) { //TODO make item customisable
			if(!in_array($ev->getPlayer->getName(), $this->hidden)) {
				$this->hidden[] = $ev->getPlayer()->getName();
				foreach($ev->getPlayer()->getLevel()->getPlayers() as $player) { //hide players
					$ev->getPlayer()->hidePlayer($player);
				}
			}else{
				$key = array_search($ev->getPlayer()->getName());
				unset($this->hidden[$key]);
				foreach($ev->getPlayer()->getLevel()->getPlayers() as $player) { //show players
					$ev->getPlayer()->showPlayer($player);
				}
			}
		}
	}
}
