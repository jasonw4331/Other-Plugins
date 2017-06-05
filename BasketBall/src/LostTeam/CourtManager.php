<?php
namespace LostTeam;

use LostTeam\task\Court;
use LostTeam\task\SignRefreshTask;
use pocketmine\Player;
use pocketmine\level\Location;
use pocketmine\level\Position;
use pocketmine\scheduler\TaskHandler;
use pocketmine\utils\Config;
use pocketmine\tile\Sign;
use pocketmine\utils\TextFormat as TF;

class CourtManager {

    const SIGN_REFRESH_DELAY = 5;

	/** @var Court[] $courts */
	private $courts = [];
	/** @var Player[] $queue */
	private $queue = [];
	/** @var Config $config */
	private $config;
	/** @var BBall $plugin */
    private $plugin;
    /** @var Sign[] $signTiles */
	private $signTiles = [];
	/** @var TaskHandler $signRefreshTaskHandler */
	private $signRefreshTaskHandler;

	public function __construct(BBall $plugin, Config $config) {
		$this->config = $config;
		  $this->plugin = $plugin;

		if(!$this->config->get('courts')) {
			$this->config->set('courts', []);
			$courtPositions = [];
		}else{
			$courtPositions = $this->config->get('courts');
		}

		if(!$this->config->get('signs')) {
			$this->config->set('signs', []);
			$signPositions = [];
		}else{
			$signPositions = $this->config->get('signs');
		}

		$this->parseCourtPositions($courtPositions);
		$this->parseSignPositions($signPositions);

		$task = new SignRefreshTask($this->plugin, $this);
		$this->signRefreshTaskHandler = $this->plugin->getServer()->getScheduler()->scheduleRepeatingTask($task, self::SIGN_REFRESH_DELAY * 20);
	}
	public function parseCourtPositions(array $courtPositions) {
		foreach ($courtPositions as $n => $courtPosition) {
			$this->plugin->getServer()->loadLevel($courtPosition[3]);
			if(($level = $this->plugin->getServer()->getLevelByName($courtPosition[3])) === null) {
				$this->plugin->getLogger()->error("[BBall] - " . $courtPosition[3] . " is not loaded. Court " . $n . " is now disabled.");
			}
			else{
				$newCourtPosition = new Position($courtPosition[0], $courtPosition[1], $courtPosition[2], $level);
				$newCourt = new Court($newCourtPosition, $this);
				array_push($this->courts, $newCourt);
                $this->plugin->getLogger()->debug("[BBall] - Court " . $n . " loaded at position " . (string)$newCourtPosition);
			}
		}
	}
	public function parseSignPositions(array $signPositions) {
		$this->plugin->getLogger()->info(TF::GREEN . "Load signs... " . TF::RED . count($signPositions) . " signs");
		foreach ($signPositions as $n => $signPosition) {
			$this->plugin->getServer()->loadLevel($signPosition[3]);
			if (($level = $this->plugin->getServer()->getLevelByName($signPosition[3])) !== null) {
				$newSignPosition = new Position($signPosition[0], $signPosition[1], $signPosition[2], $level);
				/** @var Sign $tile */
				$tile = $level->getTile($newSignPosition);
				if ($tile != null) {
					$cleanTileTitle = TF::clean($tile->getText()[0]);
					$cleanBBallTitle = TF::clean(BBall::SIGN_TITLE);

					if ($tile !== null && $tile instanceof Sign && $cleanTileTitle === $cleanBBallTitle) {
						array_push($this->signTiles, $tile);
						continue;
					}
				}
			} else {
				$this->plugin->getLogger()->info(TF::RED . "Level " . $signPosition[3] . " does not exists. Please check configuration.");
			}
		}
	}
	public function addNewPlayerToQueue(Player $newPlayer) {

		if(in_array($newPlayer, $this->queue)) {
            $newPlayer->sendMessage(TF::GOLD . TF::BOLD . "[BBall] " . TF::WHITE . BBall::getMessage("queue_alreadyinqueue"));
			return;
		}

		$currentCourt = $this->getPlayerCourt($newPlayer);
		if($currentCourt != null) {
			$newPlayer->sendMessage(TF::GOLD . TF::BOLD . "[BBall] " . TF::WHITE . BBall::getMessage("court_alreadyincourt"));
			return;
		}

		array_push($this->queue, $newPlayer);

		$this->plugin->getLogger()->info("[BBall] - There is actually " . count($this->queue) . " players in the queue");
		$newPlayer->sendMessage(TF::GOLD . TF::BOLD . "[BBall] " . TF::WHITE . BBall::getMessage("queue_join"));
		$newPlayer->sendMessage(TF::GOLD . TF::BOLD . "[BBall] " . TF::WHITE . BBall::getMessage("queue_playersinqueue"). count($this->queue));
		$newPlayer->sendTip(BBall::getMessage("queue_popup"));

		$this->launchNewRounds();
		$this->refreshSigns();
	}
	private function launchNewRounds() {

		if(count($this->queue) < 2) {
			$this->plugin->getLogger()->debug("There is not enough players to start a duel : " . count($this->queue));
			return;
		}

		$this->plugin->getLogger()->debug("Check ".  count($this->courts) . " courts");

		$freeCourt = null;
		foreach ($this->courts as $court) {
			if(!$court->active) {
				$freeCourt = $court;
				break;
			}
		}

		if($freeCourt == null) {
			$this->plugin->getLogger()->debug("[BBall] - No free court found");
			return;
		}

		$roundPlayers = [];
		array_push($roundPlayers, array_shift($this->queue), array_shift($this->queue));
		$this->plugin->getLogger()->debug("[BBall] - Starting duel : " . $roundPlayers[0]->getName() . " vs " . $roundPlayers[1]->getName());
		$freeCourt->startRound($roundPlayers);
	}
	public function notifyEndOfRound() {
		$this->launchNewRounds();
	}
	public function getPlayerCourt(Player $player) {
		foreach ($this->courts as $court) {
			if ($court->isPlayerInCourt($player)) {
				return $court;
			}
		}
		return null;
	}
	public function referenceNewCourt(Location $location) {
		$newCourt = new Court($location, $this);
		array_push($this->courts, $newCourt);

		$courts = $this->config->get('courts');
		array_push($courts, [$newCourt->position->getX(), $newCourt->position->getY(), $newCourt->position->getZ(), $newCourt->position->getLevel()->getName()]);
		$this->config->set("courts", $courts);
		$this->config->save();
	}
	public function removePlayerFromQueueOrCourt(Player $player) {
		$currentCourt = $this->getPlayerCourt($player);
		if($currentCourt != null) {
			$currentCourt->onPlayerDeath($player);
			return;
		}

		$index = array_search($player, $this->queue);
		if($index != false) {
			unset($this->queue[$index]);
		}
		$this->refreshSigns();
	}
	public function getNumberOfCourts() {
		return count($this->courts);
	}
	public function getNumberOfFreeCourts() {
		$numberOfFreeCourts = count($this->courts);
		foreach ($this->courts as $court) {
			if($court->active) {
				$numberOfFreeCourts--;
			}
		}
		return $numberOfFreeCourts;
	}
	public function getNumberOfPlayersInQueue() {
		return count($this->queue);
	}
	public function addSign(Sign $signTile) {
		$signs = $this->config->get('signs');
		$signs[count($this->signTiles)] = [$signTile->getX(), $signTile->getY(), $signTile->getZ(), $signTile->getLevel()->getName()];
		$this->config->set("signs", $signs);
		$this->config->save();
		array_push($this->signTiles, $signTile);
	}
	public function refreshSigns() {
		foreach ($this->signTiles as $signTile) {
			if($signTile->level != null) {
				$signTile->setText("§b§l[BBall]", "§aWaiting: " . $this->getNumberOfPlayersInQueue(), "§dCourts: " . $this->getNumberOfFreeCourts(), "§e-+===+-");
			}
		}
	}
}