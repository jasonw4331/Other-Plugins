<?php
namespace LostTeam\task;

use LostTeam\BBall;
use pocketmine\scheduler\PluginTask;

class RoundCheckTask extends PluginTask{

    /** @var Court $court */
	public $court;

	public function __construct(BBall $owner, Court $court){
        parent::__construct($owner);
        $this->court= $court;
    }

    public function onRun(int $currentTick) {
		$this->court->onRoundEnd();
	}
}