<?php
namespace sizer;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase{
    public function onEnable() {
        $this->getLogger()->notice(TF::GREEN."Enabled!");
    }
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
	    if($sender instanceof Player) {
		    if(isset($args[1])) {
			    if($sender->hasPermission("resize.other")) {
				    $player = $this->getServer()->getPlayer($args[1]);
				    if($player instanceof Player) {
					    $player->setScale($args[0]);
					    $sender->sendMessage("Scale set to". $player->getScale());
					    return true;
				    }
				    $sender->sendMessage("The specified player isn't online right now.");
				    return true;
			    }
		    }else{
			    if($sender->hasPermission("resize.use")) {
				    if(is_float($args[0])) {
					    $sender->setScale($args[0]);
					    $sender->sendMessage("Scale set to". $sender->getScale());
					    return true;
				    }
				    return false;
			    }
		    }
	    }else{
		    if(isset($args[1])) {
			    $player = $this->getServer()->getPlayer($args[1]);
			    if($player instanceof Player) {
				    $player->setScale($args[0]);
				    $sender->sendMessage("Scale set to". $player->getScale());
				    return true;
			    }else{
				    $sender->sendMessage("The specified player isn't online right now.");
				    return true;
			    }
		    }else{
			    return false;
		    }
	    }
	    return true;
    }
	public function onDisable() {
        $this->getLogger()->notice(TF::GREEN."Disabled!");
    }
}