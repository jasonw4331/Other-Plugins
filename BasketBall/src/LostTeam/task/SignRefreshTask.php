<?php
namespace LostTeam\task;

use LostTeam\BBall;
use LostTeam\CourtManager;
use pocketmine\scheduler\PluginTask;


class SignRefreshTask extends PluginTask{

    /** @var CourtManager $courtManager */
    public $courtManager;

    public function __construct(BBall $owner, CourtManager $manager) {
        parent::__construct($owner);
        $this->courtManager = $manager;
    }

	public function onRun(int $currentTick) {
		$this->courtManager->refreshSigns();
	}
}