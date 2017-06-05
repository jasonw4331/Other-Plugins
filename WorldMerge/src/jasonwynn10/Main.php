<?php
namespace jasonwynn10;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\format\Chunk;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase {
	/** @var  Chunk[][] */
	public $copies = [];
	/** @var array  */
	public $chunksave1 = [];
	/** @var array  */
	public $chunksave2 = [];

	public function onEnable() {
		$this->getLogger()->notice(TF::GREEN."Enabled!");
	}
	public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
		if(!$sender instanceof Player) {
			$sender->sendMessage(TF::YELLOW."You need to be in-game to use this command!");
			return true;
		}
		if(strtolower($command) == "chunk1") {
			# TODO
		}
		if(strtolower($command) == "chunk2") {
			# TODO
		}
		if(strtolower($command) == "copy") {
			$this->copy($sender);
		}
		if(strtolower($command) == "paste") {
			$this->paste($sender);
		}
		return true;
	}
	public function copy(Player $player) {
		# TODO
	}
	public function paste(Player $player) {
		# TODO
	}
	public function onDisable() {
		$this->getLogger()->notice(TF::GREEN."Disabled!");
	}
}