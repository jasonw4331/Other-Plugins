<?php
namespace jasonwynn10\l8cmds;

use pocketmine\plugin\PluginBase;

class Main extends PluginBase {
	public function onEnable() {
		$this->getServer()->getCommandMap()->register(self::class, new AfterCommand($this));
	}
}