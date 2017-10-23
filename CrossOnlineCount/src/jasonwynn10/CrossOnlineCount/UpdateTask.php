<?php
namespace jasonwynn10\CrossOnlineCount;

use pocketmine\scheduler\PluginTask;

class UpdateTask extends PluginTask {
	public function onRun(int $currentTick) {
		$this->getOwner()->update();
	}
}