<?php
namespace LostTeam\task;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat as TF;
use pocketmine\plugin\Plugin;

class GameCountDownTask extends PluginTask{

	const COUNTDOWN_DURATION = 5;

	private $court;
	private $countdownValue;

	public function __construct(Plugin $owner, Court $court) {
		parent::__construct($owner);
		$this->court = $court;
		$this->countdownValue = self::COUNTDOWN_DURATION;
	}
	public function onRun(int $currentTick){
		if(count($this->court->players) < 2){
			$this->court->abortDuel();
		}
		else{
			$player1 = $this->court->players[0];
			$player2 = $this->court->players[1];

			if(!$player1->isOnline() || !$player2->isOnline()) {
				$this->court->abortDuel();
			}
			else{
				$player1->sendTip(TF::GOLD . TF::BOLD . $this->countdownValue . TF::RESET . " sec...");
				$player2->sendTip(TF::GOLD . TF::BOLD . $this->countdownValue . TF::RESET . " sec...");
				$this->countdownValue--;

				if($this->countdownValue == 0){
					$this->court->startDuel();
				}
			}
		}
	}
}