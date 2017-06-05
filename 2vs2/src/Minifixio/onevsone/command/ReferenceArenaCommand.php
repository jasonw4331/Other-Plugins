<?php

namespace Minifixio\onevsone\command;

use pocketmine\command\Command;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\command\CommandSender;
use pocketmine\level\Location;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;


use Minifixio\onevsone\OneVsOne;
use Minifixio\onevsone\ArenaManager;

/**
 * Command to reference a new Arena in the pool 
 * @author Minifixio
 */
class ReferenceArenaCommand extends Command {

	private $plugin;
	private $arenaManager;
	public $commandName = "refarena";

	public function __construct(OneVsOne $plugin, ArenaManager $arenaManager){
		parent::__construct($this->commandName, "Reference a new arena");
		$this->setUsage("/$this->commandName");
		$this->command = $this->commandName;
		
		$this->plugin = $plugin;
		$this->arenaManager = $arenaManager;
	}

	public function execute(CommandSender $sender, $label, array $params){
		if(!$this->plugin->isEnabled()){
			return false;
		}

		if(!$sender instanceof Player){
			$sender->sendMessage("Please use the command in-game");
			return true;
		}
		
		if($sender->hasPermission("onevsone.refarena")){
		
		// Get current op location
		$playerLocation = $sender->getLocation();
		
		$this->plugin->getLogger()->info("location" . $sender->getLocation());
		
		// Add the arena
		$this->arenaManager->referenceNewArena($playerLocation);
		
		// Notify the op
		$sender->sendMessage("[1vs1] A new arena has been created at your position ! There are " . $this->arenaManager->getNumberOfArenas() ." arenas.");
		
		return true;
		}
		
		else{
			$sender->sendMessage(TextFormat::RED . "You must be op for use this command !");
		}
	}
}