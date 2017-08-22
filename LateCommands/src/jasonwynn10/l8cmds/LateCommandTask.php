<?php

namespace jasonwynn10\l8cmds;

use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;

class LateCommandTask extends PluginTask {
	/** @var string $command */
	private $command;
	/** @var CommandSender|Player $sender */
	private $sender;
	public function __construct(Main $plugin, CommandSender $sender, string $command) {
		parent::__construct($plugin);
		$this->command = $command;
		$this->sender = $sender;
	}
	public function onRun(int $currentTick) {
		if($this->sender instanceof Player and !$this->sender->isClosed()) {
			$this->getOwner()->getServer()->dispatchCommand($this->sender, $this->command);
		}else{
			$this->getOwner()->getServer()->dispatchCommand(new ConsoleCommandSender(), $this->command);
		}
	}
}