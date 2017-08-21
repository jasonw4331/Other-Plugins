<?php
namespace WarpPads\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

use WarpPads\MainClass;

class DelWP extends PluginCommand {
	/**
	 * DelWP constructor.
	 * @param MainClass $plugin
	 */
	public function __construct(MainClass $plugin) {
		parent::__construct("delwp", $plugin);
		$this->setPermission("warppads.op");
		$this->setDescription("Delete a Warp Pad");
		$this->setUsage("/delwp <name: string>");
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
		$loc = $this->getPlugin()->getConfig()->get("Warp Pads");
		$selection = $loc[strtolower($args[0])];
		if($selection == null) {
			$sender->sendMessage(TextFormat::RED."Warp Pad doesn't exist!");
			return true;
		}
		$ga = $this->getPlugin()->getConfig()->getAll();
		unset($ga["Warp Pads"][strtolower($args[0])]);
		$this->getPlugin()->getConfig()->setAll($ga);
		$this->getPlugin()->getConfig()->save();
		$sender->sendMessage(TextFormat::RED."Warp Pad deleted!");
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
	public function generateCustomCommandData(Player $player): array {
		$arr = parent::generateCustomCommandData($player);
		$pads = $this->getPlugin()->getConfig()->get("Warp Pads", []);
		$pads = array_keys($pads);
		sort($pads, SORT_FLAG_CASE);
		$arr["overloads"]["default"]["input"]["parameters"] = [
			[
				"name" => "name",
				"type" => "stringenum",
				"optional" => false,
				"enum_values" => $pads
			]
		];
		return $arr;
	}
}