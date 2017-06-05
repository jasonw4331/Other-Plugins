<?php

namespace Minifixio\onevsone\command;

use pocketmine\command\Command;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

use Minifixio\onevsone\OneVsOne;
use Minifixio\onevsone\ArenaManager;

class JoinCommand extends Command implements PluginIdentifiableCommand{

	private $plugin;
	private $arenaManager;
	public $commandName = "match";

	public function __construct(OneVsOne $plugin, ArenaManager $arenaManager){
		parent::__construct($this->commandName, "Join 1vs1 queue !");
		$this->setUsage("/$this->commandName");
		
		$this->plugin = $plugin;
		$this->arenaManager = $arenaManager;
	}

	public function getPlugin(){
		return $this->plugin;
	}

	public function execute(CommandSender $sender, $label, array $params){
		if(!$this->plugin->isEnabled()){
			return false;
		}

		if(!$sender instanceof Player){
			$sender->sendMessage("Please use the command in-game");
			return true;
		}
		
		$this->arenaManager->addNewPlayerToQueue($sender);
	 	
		return true;
	}
}