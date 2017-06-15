<?php
namespace corrector;

use pocketmine\plugin\PluginBase;

class Main extends pluginBase {
    public function onEnable() {
        $this->saveDefaultConfig();
        $this->getServer()->getScheduler()->scheduleTask(new Corrector($this));
    }
}
