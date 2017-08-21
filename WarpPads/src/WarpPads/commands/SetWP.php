<?php
namespace WarpPads\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

use WarpPads\MainClass;

class SetWP extends PluginCommand {
	/**
	 * SetWP constructor.
	 * @param MainClass $plugin
	 */
	public function __construct(MainClass $plugin) {
		parent::__construct("setwp", $plugin);
		$this->setPermission("warppads.op");
		$this->setDescription("Create a Warp Pad");
		$this->setUsage("/setwp <name: string>");
	}

	/**
	 * @param CommandSender $sender
	 * @param string $label
	 * @param array $args
	 *
	 * @return bool
	 */
	public function execute(CommandSender $sender, string $label, array $args) {
		if(!$this->testPermission($sender)) {
			return true;
		}
		if(empty($args)) {
			return false;
		}
		$this->getPlugin()->wpStep1[$sender->getName()] = strtolower($args[0]);
		$sender->sendMessage(TextFormat::GREEN."Please tap the Starting Warp Pad");
		return true;
	}

	/**
	 * @return MainClass
	 */
	public function getPlugin() : Plugin {
		return parent::getPlugin();
	}

	/**
	 * @param Player $player
	 *
	 * @return array
	 */
	public function generateCustomCommandData(Player $player) : array {
		$arr = parent::generateCustomCommandData($player);
		$arr["overloads"]["default"]["input"]["parameters"] = [
			[
				"name" => "name",
				"type" => "rawtext",
				"optional" => false
			]
		];
		return $arr;
	}
}