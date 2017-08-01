<?php

namespace Minifixio\onevsone\model;

use Minifixio\onevsone\ArenaManager;
use pocketmine\scheduler\PluginTask;

class SignRefreshTask extends PluginTask{
	
	/** @var ArenaManager $arenaManager **/
	public $arenaManager;
	
	public function onRun(int $currentTick){
		$this->arenaManager->refreshSigns();
	}
	
}