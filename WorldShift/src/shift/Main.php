<?php
namespace shift;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase {
	public function onEnable() {
		$this->getLogger()->notice(TF::GREEN."Enabled!");
	}
	public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
		if(strtolower($command) == "world") {
			if($sender instanceof Player) {
				if(count($args) >= 1) {
					if($this->getServer()->loadLevel($args[0])) {
						if(($l = $this->getServer()->getLevelByName($args[0])) != null and isset($args[1])) {
							if(($p = $this->getServer()->getPlayer($args[1])) instanceof Player) {
								if($p->hasPermission("world.".strtolower($args[0]))) {
									$p->teleport($l->getSpawnLocation());
									return true;
								}
							}else{
								$this->getLogger()->error("Player isn't a player");
								return true;
							}
						}elseif(($l = $this->getServer()->getLevelByName($args[0])) != null) {
							$sender->teleport($l->getSpawnLocation());
							return true;
						}
					}
					$this->getLogger()->error("Level isn't a level");
					return true;
				}else{
					return false;
				}
			}else{
				if(count($args) >= 2) {
					if($this->getServer()->loadLevel($args[0])) {
						if(($l = $this->getServer()->getLevelByName($args[0])) != null and isset($args[1])) {
							if(($p = $this->getServer()->getPlayer($args[1])) instanceof Player) {
								if($p->hasPermission("world.".strtolower($args[0]))) {
									$p->teleport($l->getSpawnLocation());
									return true;
								}
							}else{
								$this->getLogger()->error("Player isn't a player");
								return true;
							}
						}
					}
                    $this->getLogger()->error("Level isn't a level");
                    return true;
				}else{
					return false;
				}
			}
		}
		if(strtolower($command) == "worlds") {
			$sender->sendMessage(TF::YELLOW."---+---Worlds---+---");
			foreach ($this->getServer()->getLevels() as $level) {
				if(!$level->isClosed()) {
					$sender->sendMessage(TF::YELLOW.$level->getName());
				}
			}
			return true;
		}
		return true;
	}
}