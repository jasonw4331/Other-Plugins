<?php

namespace LostTeam\task;

use LostTeam\BBall;
use LostTeam\CourtManager;

use pocketmine\Player;
use pocketmine\scheduler\TaskHandler;
use pocketmine\Server;
use pocketmine\level\Position;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;
use pocketmine\block\Block;
use pocketmine\level\particle\DestroyBlockParticle;

class Court{

	public $active = FALSE;

	public $startTime;

	/** @var player[] $players */
	public $players = [];

	/** @var Position $position */
	public $position;

	private $manager;

	const ROUND_DURATION = 180;

	const PLAYER_1_OFFSET_X = 5;
	const PLAYER_2_OFFSET_X = -5;

	/** @var TaskHandler $taskHandler */
	private $taskHandler;
	/** @var TaskHandler $countdownTaskHandler */
	private $countdownTaskHandler;

	public function __construct($position, CourtManager $manager) {
		$this->position = $position;
		$this->manager = $manager;
		$this->active = FALSE;
	}

	public function startRound(array $players) {

		$this->active = TRUE;

		$this->players = $players;
		$player1 = $players[0];
		$player2 = $players[1];

		$player1->sendMessage(BBall::getMessage("game_against") . $player2->getName());
		$player2->sendMessage(BBall::getMessage("game_against") . $player1->getName());

		$this->countdownTaskHandler = Server::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new GameCountDownTask(BBall::$instance, $this), 20, 20);
	}

	public function startDuel() {

		Server::getInstance()->getScheduler()->cancelTask($this->countdownTaskHandler->getTaskId());

		$player1 = $this->players[0];
		$player2 = $this->players[1];

		$pos_player1 = Position::fromObject($this->position, $this->position->getLevel());
		$pos_player1->x += self::PLAYER_1_OFFSET_X;

		$pos_player2 = Position::fromObject($this->position, $this->position->getLevel());
		$pos_player2->x += self::PLAYER_2_OFFSET_X;
		$player1->teleport($pos_player1, 90, 0);
		$player2->teleport($pos_player2, -90, 0);
		$this->sparyParticle($player1);
		$this->sparyParticle($player2);
		$player1->setGamemode(0);
		$player2->setGamemode(0);

		foreach ($this->players as $player) {
			$this->giveKit($player);
		}

		$this->startTime = new \DateTime('now');

		$player1->sendTip(BBall::getMessage("game_tip"));
		$player1->sendMessage(BBall::getMessage("game_start"));

		$player2->sendTip(BBall::getMessage("game_tip"));
		$player2->sendMessage(BBall::getMessage("game_start"));

		$this->taskHandler = Server::getInstance()->getScheduler()->scheduleDelayedTask(new RoundCheckTask(BBall::$instance, $this), self::ROUND_DURATION * 20);
	}

	public function abortDuel() {
		if($this->countdownTaskHandler instanceof TaskHandler);
		Server::getInstance()->getScheduler()->cancelTask($this->countdownTaskHandler->getTaskId());
	}

	private function giveKit(Player $player) {
		$player->getInventory()->clearAll();

		$player->setHealth(20);
		$player->removeAllEffects();
	}

	public function onPlayerDeath(Player $loser) {

		if($loser == $this->players[0]) {
			$winner = $this->players[1];
		}
		else{
			$winner = $this->players[0];
		}
		$loser->sendMessage(BBall::getMessage("game_loser") . $winner->getName());
		$loser->removeAllEffects();

		$winner->sendMessage( BBall::getMessage("game_winner") . $loser->getName());
		$winner->removeAllEffects();

		$winner->teleport($winner->getSpawn());

		$winner->setHealth(20);
		Server::getInstance()->broadcastMessage(TextFormat::GREEN . TextFormat::BOLD . "» " . TextFormat::GOLD . $winner->getName() . TextFormat::WHITE . BBall::getMessage("game_broadcast") . TextFormat::RED . $loser->getName() . TextFormat::WHITE . " !");

		$this->reset();
	}

	private function reset() {
		$this->active = FALSE;
		foreach ($this->players as $player) {
			if($player instanceof Player);
			$player->getInventory()->setItemInHand(new Item(Item::AIR,0,0));
			$player->getInventory()->clearAll();
			$player->getInventory()->sendArmorContents($player);
			$player->getInventory()->sendContents($player);
			$player->getInventory()->sendHeldItem($player);
		}
		$this->players = array();
		$this->startTime = NULL;
		if($this->taskHandler != NULL) {
			Server::getInstance()->getScheduler()->cancelTask($this->taskHandler->getTaskId());
			$this->manager->notifyEndOfRound();
		}
	}

	public function onPlayerQuit(Player $loser) {
		$this->onPlayerDeath($loser);
	}

	public function onRoundEnd() {
		foreach ($this->players as $player) {
			$player->teleport($player->getSpawn());
			$player->sendMessage(TextFormat::BOLD . "++++++++=++++++++");
			$player->sendMessage(BBall::getMessage("game_timeover"));
			$player->sendMessage(TextFormat::BOLD . "++++++++=++++++++");
			$player->removeAllEffects();
		}

		$this->reset();
	}

	public function isPlayerInCourt(Player $player) {
		return in_array($player, $this->players);
	}

	public function sparyParticle(Player $player) {
		$particle = new DestroyBlockParticle(new Vector3($player->getX(), $player->getY(), $player->getZ()), Block::get(8));
		$player->getLevel()->addParticle($particle);
	}
}