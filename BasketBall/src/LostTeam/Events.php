<?php
namespace LostTeam;

use pocketmine\block\Block;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\tile\Sign;

class Events implements Listener {
    private $courtManager;
    public function __construct(CourtManager $courtManager) {
        $this->courtManager = $courtManager;
    }
    public function onPlayerQuit(PlayerQuitEvent $event) {
        $player = $event->getPlayer();
        $this->courtManager->removePlayerFromQueueOrCourt($player);
    }
    public function onPlayerDeath(PlayerDeathEvent $event) {
        $deadPlayer = $event->getEntity();
        $court = $this->courtManager->getPlayerCourt($deadPlayer);
        $event->setDrops([]);
        $event->setKeepInventory(false);
        if($court != null) {
            $court->onPlayerDeath($deadPlayer);
        }
    }
    public function tileupdate(SignChangeEvent $event) {
        if($event->getBlock()->getId() == Block::SIGN_POST || $event->getBlock()->getId() == Block::WALL_SIGN) {
            /** @var Sign $signTile */
            $signTile = $event->getPlayer()->getLevel()->getTile($event->getBlock());
            $signLines = $event->getLines();
            if(strtolower($signLines[0]) == BBall::SIGN_TITLE or strtolower($signLines[0]) == "bball") {
                if($event->getPlayer()->hasPermission("sign.make")) {
                    $this->courtManager->addSign($signTile);
                    $event->setLine(0,"§b§l[BBall]");
                    $event->setLine(1,"§aWaiting: "  . $this->courtManager->getNumberOfPlayersInQueue());
                    $event->setLine(2,"§dCourts:" . $this->courtManager->getNumberOfFreeCourts());
                    $event->setLine(3,"§e-+===+-");
                    return;
                }
            }
        }
    }
}