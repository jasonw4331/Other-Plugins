<?php
declare(strict_types=1);
namespace jasonwynn10\pce;

use _64FF00\PureChat\PureChat;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class PureChatExtras extends PluginBase {
	/** @var PureChat $pureChat */
	private $pureChat;

	public function onEnable() {
		$this->pureChat = $pureChat = $this->getServer()->getPluginManager()->getPlugin("PureChat");
		/** @var PluginCommand $suffix */
		$suffix = $this->getServer()->getPluginCommand("setsuffix");
		$suffix->setUsage("/setsuffix <player> <suffix>");
		$suffix->setExecutor($this);
		/** @var PluginCommand $prefix */
		$prefix = $this->getServer()->getPluginCommand("setprefix");
		$prefix->setUsage("/setprefix <player> <prefix>");
		$prefix->setExecutor($this);
	}
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
		switch(strtolower($command->getName())) {
			case "setprefix":
				if(!isset($args[0]))
					return false;
				$playerName = $args[0];
				$player = $this->getServer()->getPlayer($playerName);
				if($player === null) {
					return $this->pureChat->onCommand($sender, $command, $label, $args);
				}
				array_shift($args);

				$prefix = str_replace("{BLANK}", ' ', implode(' ', $args));

				$this->pureChat->setPrefix($prefix, $player, $this->pureChat->getConfig()->get("enable-multiworld-chat") ? $player->getLevel()->getName() : null);

				$sender->sendMessage(TextFormat::GREEN . PureChat::MAIN_PREFIX . " You set your prefix to " . $prefix . ".");
				return true;
				break;
			case "setsuffix":
				if(!isset($args[0]))
					return false;
				$playerName = $args[0];
				$player = $this->getServer()->getPlayer($playerName);
				if($player === null) {
					return $this->pureChat->onCommand($sender, $command, $label, $args);
				}
				array_shift($args);

				$suffix = str_replace("{BLANK}", ' ', implode(' ', $args));

				$this->pureChat->setSuffix($suffix, $player, $this->pureChat->getConfig()->get("enable-multiworld-chat") ? $player->getLevel()->getName() : null);

				$sender->sendMessage(TextFormat::GREEN . PureChat::MAIN_PREFIX . " You set your suffix to " . $suffix . ".");
				return true;
				break;
			default:
				return true;
		}
	}
}