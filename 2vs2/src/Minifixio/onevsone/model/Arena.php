<?php

namespace Minifixio\onevsone\model;

use Minifixio\onevsone\OneVsOne;
use Minifixio\onevsone\ArenaManager;

use pocketmine\Player;
use pocketmine\scheduler\TaskHandler;
use pocketmine\Server;
use pocketmine\level\Position;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;
use pocketmine\block\Block;
use pocketmine\level\particle\DestroyBlockParticle;

class Arena{

	public $active = FALSE;
	
	public $startTime;
	
	public $players = array();
	
	/** @var Position */
	public $position;
	
	/** @var ArenaManager */
	private $manager;
	
	// Roound duration (3min)
	const ROUND_DURATION = 180;
	
	const PLAYER_1_OFFSET_X = 5;
	const PLAYER_2_OFFSET_X = -5;
	
	// Variable for stop the round's timer
	/** @var TaskHandler $taskHandler */
	private $taskHandler;
	/** @var TaskHandler $countdownTaskHandler */
	private $countdownTaskHandler;
	/**
	 * Build a new Arena
	 *
	 * @param Position position Base position of the Arena
	 * @param ArenaManager $manager
	 */
	public function __construct($position, ArenaManager $manager){
		$this->position = $position;
		$this->manager = $manager;
		$this->active = FALSE;
	}
	
	/** 
	 * Demarre un match.
	 * @param Player[] $players
	 */
	public function startRound(array $players){
		
		// Set active to prevent new players
		$this->active = TRUE;
		
		// Set players
		$this->players = $players;
		$player1 = $players[0];
		$player2 = $players[1];
		
		$player1->sendMessage(OneVsOne::getMessage("duel_against") . $player2->getName());
		$player2->sendMessage(OneVsOne::getMessage("duel_against") . $player1->getName());

		// Create a new countdowntask
		$task = new CountDownToDuelTask(OneVsOne::getInstance(), $this);
		$this->countdownTaskHandler = Server::getInstance()->getScheduler()->scheduleDelayedRepeatingTask($task, 20, 20);	
	}
	
	/**
	 * Really starts the duel after countdown
	 */
	public function startDuel(){
		
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
		
		// Give kit
		foreach ($this->players as $player){
			$this->giveKit($player);
		}
		
		// Fix start time
		$this->startTime = new \DateTime('now');
		
		$player1->sendTip(OneVsOne::getMessage("duel_tip"));
		$player1->sendMessage(OneVsOne::getMessage("duel_start"));
		
		$player2->sendTip(OneVsOne::getMessage("duel_tip"));
		$player2->sendMessage(OneVsOne::getMessage("duel_start"));
		
		// Launch the end round task
		$task = new RoundCheckTask(OneVsOne::getInstance());
		$task->arena = $this;
		$this->taskHandler = Server::getInstance()->getScheduler()->scheduleDelayedTask($task, self::ROUND_DURATION * 20);
	}
	
	/**
	 * Abort duel during countdown if one of the players has quit
	 */
	public function abortDuel(){
		Server::getInstance()->getScheduler()->cancelTask($this->countdownTaskHandler->getTaskId());
	}
	
	private function giveKit(Player $player){
		// Clear inventory
		$player->getInventory()->clearAll();
		
		// Give sword, food and armor
		$player->getInventory()->addItem(Item::get(ITEM::IRON_SWORD));
		$player->getInventory()->addItem(Item::get(ITEM::BREAD));
		$player->getInventory()->setItemInHand(Item::get(ITEM::IRON_SWORD));
		
		// Pur the armor on the player
		$player->getInventory()->setHelmet(Item::get(302, 0, 1));
		$player->getInventory()->setChestplate(Item::get(303, 0, 1));
		$player->getInventory()->setLeggings(Item::get(304, 0, 1));
		$player->getInventory()->setBoots(Item::get(305, 0, 1));
		$player->getInventory()->sendArmorContents($player);
		
		// Set his life to 20
		$player->setHealth(20);
		$player->removeAllEffects();

   }
   
   /**
    * When a player was killed
    * @param Player $loser
    */
   public function onPlayerDeath(Player $loser){
   	
		// Finish the duel and teleport the winner at spawn
   		if($loser == $this->players[0]){
   			$winner = $this->players[1];
   		}
   		else{
   			$winner = $this->players[0];
   		}  		
   		$loser->sendMessage(OneVsOne::getMessage("duel_loser") . $winner->getName());
   		$loser->removeAllEffects();
   		
   		$winner->sendMessage( OneVsOne::getMessage("duel_winner") . $loser->getName());
   		$winner->removeAllEffects();
   		
   		// Teleport the winner at spawn
   		$winner->teleport($winner->getSpawn());

   		// Set his life to 20
   		$winner->setHealth(20);
   		Server::getInstance()->broadcastMessage(TextFormat::GREEN . TextFormat::BOLD . "» " . TextFormat::GOLD . $winner->getName() . TextFormat::WHITE . OneVsOne::getMessage("duel_broadcast") . TextFormat::RED . $loser->getName() . TextFormat::WHITE . " !");
   		
   		// Reset arena
   		$this->reset();
   }

   /**
    * Reset the Arena to current state
    */
   private function reset(){
   		// Put active a rena after the duel
   		$this->active = FALSE;
   		foreach ($this->players as $player){
   			$player->getInventory()->setItemInHand(new Item(Item::AIR,0,0));
   			$player->getInventory()->clearAll();
   			$player->getInventory()->sendArmorContents($player);
   			$player->getInventory()->sendContents($player);
   			$player->getInventory()->sendHeldItem($player);
   		}
   		$this->players = array();
   		$this->startTime = NULL;
   		if($this->taskHandler != NULL){
   			Server::getInstance()->getScheduler()->cancelTask($this->taskHandler->getTaskId());
   			$this->manager->notifyEndOfRound($this);
   		}
   }
   
   /**
    * When a player quit the game
    * @param Player $loser
    */
   public function onPlayerQuit(Player $loser){
   		// Finish the duel when a player quit
   		// With onPlayerDeath() function
   		$this->onPlayerDeath($loser);
   }
   
   /**
    * When maximum round time is reached
    */
   public function onRoundEnd(){
   		foreach ($this->players as $player){
   			$player->teleport($player->getSpawn());
   			$player->sendMessage(TextFormat::BOLD . "++++++++=++++++++");
   			$player->sendMessage(OneVsOne::getMessage("duel_timeover"));
   			$player->sendMessage(TextFormat::BOLD . "++++++++=++++++++");
   			$player->removeAllEffects();
   		}
   		
   		// Reset arena
   		$this->reset();   		
	 }
	 
	 public function isPlayerInArena(Player $player){
	 	return in_array($player, $this->players);
	 }
	 
	 public function sparyParticle(Player $player){
		$particle = new DestroyBlockParticle(new Vector3($player->getX(), $player->getY(), $player->getZ()), Block::get(8));
	 	$player->getLevel()->addParticle($particle);
    }
}



