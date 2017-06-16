<?php
namespace jasonwynn10\TagMods;

use _64FF00\PureChat\PureChat;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase {
    
    CONST ADD = 0;
    CONST DEL = 1;
    
    /** @var PureChat $pureChat */
    private $pureChat = null;
    
    public function onEnable() {
        $this->saveDefaultConfig();
        if(!($this->pureChat = $this->getServer()->getPluginManager()->getPlugin("PureChat")) instanceof PureChat) {
            $this->getLogger()->error("PureChat is not installed!");
            $this->setEnabled(false);
            return;
        }
        $this->getLogger()->notice(TF::GREEN."Enabled!");
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        if(strtolower($command) == "addprefix") {
            if(!$sender instanceof Player) return true;
            $prefix = "";
            for($i = 1;$i < count($args); $i++) {
                $prefix .= $args[$i];
            }
            $this->pureChat->setPrefix($prefix, $sender);
        }
        if(strtolower($command) == "addsuffix") {
            if(!$sender instanceof Player) return true;
            $suffix = "";
            for($i = 1;$i < count($args); $i++) {
                $suffix .= $args[$i];
            }
            $this->pureChat->setSuffix($suffix, $sender);
        }
        if(strtolower($command) == "delprefix") {
            if($sender instanceof Player) {
                if(count($args) == 1) {
                    if(($player = $this->getServer()->getPlayer($args[0])) instanceof Player) {
                        $sender->sendMessage(TF::YELLOW."That player may not be online!");
                        return true;
                    }
                    $this->pureChat->setPrefix("", $player);
                }else{
                    $this->pureChat->setPrefix("", $sender);
                }
            }else{
                if(count($args) == 1) {
                    if(($player = $this->getServer()->getPlayer($args[0])) instanceof Player) {
                        $sender->sendMessage(TF::YELLOW."That player may not be online!");
                        return true;
                    }
                    $this->pureChat->setPrefix("", $player);
                }
            }
        }
        if(strtolower($command) == "delsuffix") {
            if($sender instanceof Player) {
                if(count($args) == 1) {
                    if(($player = $this->getServer()->getPlayer($args[0])) instanceof Player) {
                        $sender->sendMessage(TF::YELLOW."That player may not be online!");
                        return true;
                    }
                    $this->pureChat->setSuffix("", $player);
                }else{
                    $this->pureChat->setSuffix("", $sender);
                }
            }else{
                if(count($args) == 1) {
                    if(($player = $this->getServer()->getPlayer($args[0])) instanceof Player) {
                        $sender->sendMessage(TF::YELLOW."That player may not be online!");
                        return true;
                    }
                    $this->pureChat->setSuffix("", $player);
                }
            }
        }
        if(strtolower($command) == "giveprefix") {
            if(count($args) >= 1) {
                if(($player = $this->getServer()->getPlayer($args[0])) instanceof Player) {
                    $sender->sendMessage(TF::YELLOW."That player may not be online!");
                    return true;
                }
                $prefix = "";
                for($i = 1;$i < count($args); $i++) {
                    $prefix .= $args[$i];
                }
                $this->pureChat->setPrefix($prefix, $player);
            }
        }
        if(strtolower($command) == "givesuffix") {
            if(count($args) >= 1) {
                if(($player = $this->getServer()->getPlayer($args[0])) instanceof Player) {
                    $sender->sendMessage(TF::YELLOW."That player may not be online!");
                    return true;
                }
                $suffix = "";
                for($i = 1;$i < count($args); $i++) {
                    $suffix .= $args[$i];
                }
                $this->pureChat->setSuffix($suffix, $player);
            }
        }
        return true;
    }
    public function onDisable() {
        $this->getLogger()->notice(TF::GREEN."Disabled!");
    }
}