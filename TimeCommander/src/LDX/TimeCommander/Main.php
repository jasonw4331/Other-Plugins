<?php
namespace LDX\TimeCommander;

use pocketmine\plugin\PluginBase;

class Main extends PluginBase {
	public function onEnable() {
		$this->saveDefaultConfig();
		$c = $this->getConfig()->getAll();
		foreach ((array)$c["Commands"] as $i) {
			$this->getServer()->getScheduler()->scheduleRepeatingTask(new TimeCommand($this, (string)$i["Command"]), (int)$i["Time"] * 1200);
		}
	}
}