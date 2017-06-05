<?php

namespace Minifixio\onevsone\model;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;


class SignRefreshTask extends PluginTask{
	
	/** var ArenaManager **/
	public $arenaManager;
	
	public function onRun($currentTick){
		$this->arenaManager->refreshSigns();
	}
	
}