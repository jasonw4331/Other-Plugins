<?php
namespace jasonwynn10\l8cmds;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class AfterCommand extends PluginCommand {
	/**
	 * AfterCommand constructor.
	 * @param Main $plugin
	 */
	public function __construct(Main $plugin) {
		parent::__construct("after", $plugin);
		$this->setUsage("/after <seconds: int> <command: string>");
		$this->setDescription("Runs a set command after the set amount of seconds");
		$this->setPermission("LateCmds");
	}

	/**
	 * @param CommandSender $sender
	 * @param string $commandLabel
	 * @param array $args
	 *
	 * @return bool
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		if(!$this->testPermission($sender)) {
			return true;
		}
		if(empty($args) or !isset($args[2])) {
			return false;
		}
		$ticks = (int) array_shift($args);
		$command = implode(" ", $args);
		$this->getPlugin()->getServer()->getScheduler()->scheduleDelayedTask(new LateCommandTask($this->getPlugin(), $sender, $command), $ticks * 20);
		return true;
	}

	/**
	 * @return Main
	 */
	public function getPlugin(): Plugin {
		return parent::getPlugin();
	}

	/**
	 * @param Player $player
	 *
	 * @return array
	 */
	public function generateCustomCommandData(Player $player): array {
		$commands = [];
		foreach ($this->getPlugin()->getServer()->getCommandMap()->getCommands() as $command) {
			if($command->testPermissionSilent($player)) {
				$commands[] = $command->getName();
			}
		}
		if($key = array_search("after", $commands)) {
			unset($commands[$key]);
		}
		sort($commands, SORT_FLAG_CASE);
		$arr = parent::generateCustomCommandData($player);
		$arr["overloads"]["default"]["input"]["parameters"] = [
			[
				"name" => "seconds",
				"type" => "int",
				"optional" => false
			],
			[
				"name" => "command",
				"type" => "stringenum",
				"optional" => false,
				"enum_values" => $commands
			]
		];
		return $arr;
	}
}