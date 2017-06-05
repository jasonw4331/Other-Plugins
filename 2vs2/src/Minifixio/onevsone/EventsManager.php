<?php

namespace Minifixio\onevsone;

use Minifixio\onevsone\utils\PluginUtils;

use pocketmine\tile\Tile;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\tile\Sign;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\utils\TextFormat;


/**
 * Manages PocketMineEvents
 */
class EventsManager implements Listener{

	/** @var ArenaManager */
	private $arenaManager;
		
	public function __construct(ArenaManager $arenaManager){
		$this->arenaManager = $arenaManager;
	}
	
	public function onPlayerQuit(PlayerQuitEvent $event){
		$player = $event->getPlayer();
		$this->arenaManager->removePlayerFromQueueOrArena($player);
	}
	
	public function onPlayerDeath(PlayerDeathEvent $event){
		$deadPlayer = $event->getEntity();
		$arena = $this->arenaManager->getPlayerArena($deadPlayer);
		if($arena != NULL){
                        $event->setDrops([]);
                        $event->setKeepInventory(false);
			$arena->onPlayerDeath($deadPlayer);
		}
	}
	
	public function tileupdate(SignChangeEvent $event){
		if($event->getBlock()->getID() == Item::SIGN_POST || $event->getBlock()->getID() == Block::SIGN_POST || $event->getBlock()->getID() == Block::WALL_SIGN){
			$signTile = $event->getPlayer()->getLevel()->getTile($event->getBlock());
			if(!($signTile instanceof Sign)){
				return true;
			}
			$signLines = $event->getLines();
			if($signLines[0]== OneVsOne::SIGN_TITLE){
				if($event->getPlayer()->isOp()){
					$this->arenaManager->addSign($signTile);
					$event->setLine(1,"-Waiting: "  . $this->arenaManager->getNumberOfPlayersInQueue());
					$event->setLine(2,"-Arenas:" . $this->arenaManager->getNumberOfFreeArenas());
					$event->setLine(3,"-+===+-");
					return true;
				}
			}
		}
	}
}



