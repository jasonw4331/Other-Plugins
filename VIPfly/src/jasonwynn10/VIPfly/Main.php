<?php
namespace jasonwynn10\VIPfly;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase {
    /** @var Config $config */
    private $config;
    public function onEnable() {
        @mkdir($this->getDataFolder());
        $this->config = new Config($this->getDataFolder()."config.yml", Config::YAML,[
            "vips" => ["steve","alex"],
            "worlds" => ["world","lobby"]
        ]);
    }

    /**
     * @param Player $p
     * @return bool
     */
    public function isVip(Player $p) : bool{
        return (in_array($p->getName(), $this->config->get("vips",[])) or $p->hasPermission("VIPfly") or $p->isOp());
    }

    /**
     * @param Player $p
     * @return bool
     */
    public function inWorld(Player $p) : bool{
        return in_array($p->getLevel()->getName(), $this->config->get("worlds",[]));
    }

    /**
     * WARNING: MAY INTERFERE WITH OTHER FLIGHT PLUGINS
     * @priority MONITOR
     * @ignoreCancelled true
     * @param EntityLevelChangeEvent $ev
     */
    public function onMove(EntityLevelChangeEvent $ev) {
        /** @var Player $p */
        if(($p = $ev->getEntity()) instanceof Player and $this->isVip($p) and $this->inWorld($p)) {
            $p->setAllowFlight(true);
        }elseif(($p = $ev->getEntity()) instanceof Player) {
            $p->setAllowFlight(false);
        }
    }

    /**
     * @priority LOWEST
     * @ignoreCancelled true
     * @param EntityDamageEvent $ev
     */
    public function onDamage(EntityDamageEvent $ev) {
        /** @var Player $p */
        if(($p = $ev->getEntity()) instanceof Player and $this->isVip($p) and $this->inWorld($p) and $ev->getCause() == $ev::CAUSE_FALL) {
            $ev->setCancelled();
        }
    }
}