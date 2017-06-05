<?php

namespace Minifixio\onevsone\model;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;


class RoundCheckTask extends PluginTask{
	
	public $arena;
	
	public function onRun($currentTick){
		$this->arena->onRoundEnd();
	}
	
}