<?php
namespace sleep;

use pocketmine\block\Bed;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBedEnterEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase implements Listener {
    /** @var bool $visual */
    public $visual = false;

    public function onEnable() {
        $this->getServer()->getLogger()->notice(TF::GREEN."Enabled!");
        $this->saveDefaultConfig();
        if(strtolower($this->getConfig()->get("Visual", "off")) === "on") {
            $this->visual = true;
        }
    }

    /**
     * @param PlayerBedEnterEvent $e
     *
     * @priority MONITOR
     */
    public function BedTap(PlayerBedEnterEvent $e) {
        $player = $e->getPlayer();
        $name = $player->getName();
        $block = $e->getBed();
        if($block instanceof Bed) {
            if($player->hasPermission("sleep.timechange")) {
                $this->getServer()->broadcastMessage(TF::YELLOW.$name." is now sleeping!");
                $this->getServer()->getScheduler()->scheduleRepeatingTask(new SleepTask($this, $player->getLevel(), $this->visual), 4);
            }
        }
    }

    public function onDisabled() {
        $this->getServer()->getLogger()->notice(TF::GREEN."Disabled!");
    }
}
