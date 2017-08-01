<?php

namespace Minifixio\onevsone\model;

use pocketmine\scheduler\PluginTask;

class RoundCheckTask extends PluginTask{
	/** @var Arena $arena */
	public $arena;
	
	public function onRun(int $currentTick){
		$this->arena->onRoundEnd();
	}
	
}