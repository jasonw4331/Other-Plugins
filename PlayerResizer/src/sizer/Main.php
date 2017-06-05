<?php
namespace sizer;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase{
    public function onEnable() {
        $this->getServer()->getCommandMap()->register("resizer", new Resize($this));
        $this->getLogger()->notice(TF::GREEN."Enabled!");
    }
    public function onDisable() {
        $this->getLogger()->notice(TF::GREEN."Disabled!");
    }
}