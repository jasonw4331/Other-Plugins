<?php
namespace ViperKits\kit;

use ViperKits\ViperKits;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\Player;

class KitHistoryStore implements Listener{
    /** @var  ViperKits */
    private $plugin;
    private $players;
    public function __construct(ViperKits $plugin) {
        $this->plugin = $plugin;
        $this->players = [];
        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());
    }
    public function kitUsed(Player $p, $kit) {
        $this->players[$p->getName()] = $kit;
    }

    /**
     * @param Player $p
     * @return Kit
     */
    public function getKitUsed(Player $p) {
        return $this->players[$p->getName()];
    }
    public function canUse(Player $p) {
        return (!isset($this->players[$p->getName()]) || $this->getPlugin()->getConfig()->get('once-per-life') === false);
    }
    public function onPlayerRespawn(PlayerRespawnEvent $event) {
        if($this->plugin->getConfig()->get("show-kit-info-on-respawn", true)) {
            // TODO List kit info here on respawn
        }
        if(isset($this->players[$event->getPlayer()->getName()])) {
            unset($this->players[$event->getPlayer()->getName()]);
        }
    }
    /**
     * @return \ViperKits\ViperKits
     */
    public function getPlugin() {
        return $this->plugin;
    }
}