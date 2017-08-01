<?php
namespace jasonwynn10;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\format\Chunk;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase {
	/** @var Chunk[] $chunks */
	public $chunks = [];

	public function onEnable() {
		$this->getLogger()->notice(TF::GREEN."Enabled!");
	}
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
		if(!$sender instanceof Player) {
			$sender->sendMessage(TF::YELLOW."You need to be in-game to use this command!");
			return true;
		}
		if(strtolower($command) == "copy") {
			$chunk = $sender->getLevel()->getChunk($sender->getX() >> 4, $sender->getZ() >> 4);
			if($chunk instanceof Chunk) {
				$this->chunks[$sender->getName()] = $chunk;
			}
		}
		if(strtolower($command) == "paste") {
			$this->chunks[$sender->getName()]->setX($sender->getX() >> 4);
			$this->chunks[$sender->getName()]->setZ($sender->getZ() >> 4);
			$this->chunks[$sender->getName()]->setChanged();
			$sender->getLevel()->setChunk($sender->getX() >> 4, $sender->getZ() >> 4, $this->chunks[$sender->getName()]);
		}
		return true;
	}
	public function onDisable() {
		$this->getLogger()->notice(TF::GREEN."Disabled!");
	}
}