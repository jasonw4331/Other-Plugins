<?php

namespace GlaciercreepsMC\morecommands;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\command\PluginCommand;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

use GlaciercreepsMC\morecommands\manager\MuteManager;
use GlaciercreepsMC\morecommands\manager\FreezeManager;

class MoreCommands extends PluginBase {

    public $freezeManager;

    /** @var LastCommandListener $lastCommandListener */
    private $lastCommandListener;

    private $permMessage = TextFormat::RED."You do not have permission for this!";
    private $consoleMsg = TextFormat::RED."Only players may use this command!";

    public function onEnable(){
        //I use objects because I need to use them in this file
        $muteManager = new MuteManager($this);
        $this->registerCommand("mute", "/mute <player>", "Mutes a player", "morecommands.mute", "Allows you to mute players", "op", $muteManager);
        $this->registerCommand("unmute", "/unmute <player>", "Unmutes a player", "morecommands.unmute", "Allows you to unmute players", "op", $muteManager);

        $freezeManager = new FreezeManager($this);
        $this->registerCommand("freeze", "/freeze <player>", "Freezes a player", "morecommands.freeze", "Allows you to freeze players", "op", $freezeManager);
        $this->registerCommand("unfreeze", "/unfreeze <player>", "Unfreezes a player", "morecommands.unfreeze", "Allows you to unfreeze players", "op", $freezeManager);
        $this->lastCommandListener = new LastCommandListener($this);
    }

    private function registerCommand($cmd, $usage, $desc, $permName, $permDesc, $permDefault, CommandExecutor $executor){
        $command = new PluginCommand($cmd, $this);
        $command->setUsage($usage);
        $command->setDescription($desc);

        $perm = new Permission($permName);
        $perm->setDescription($permDesc);
        $perm->setDefault($permDefault);
        $this->getServer()->getPluginManager()->addPermission($perm);

        $command->setExecutor($executor);
        $this->getServer()->getCommandMap()->register("morecommands", $command);
    }


    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {

        $cmd = strtolower($command->getName());
        $count = count($args);
        switch($cmd){
            case "gms":
                if(!($sender instanceof Player)){
                    $sender->sendMessage($this->consoleMsg);
                    return true;
                }
                $player = $this->getServer()->getPlayer($sender->getName());

                if($player->hasPermission("morecommands.gms")){
                    if($player->getGamemode() == 0){
                        $player->sendMessage(TextFormat::RED."You are already in survival mode!");
                    } else {
                        $player->setGamemode(0);
                        $player->sendMessage(TextFormat::GREEN."You are now in survival mode!");
                    }
                    return true;

                } else {
                    $player->sendMessage($this->permMessage);
                    return true;
                }
                break;
            case "gmc":
                if(!($sender instanceof Player)){
                    $sender->sendMessage($this->consoleMsg);
                    return true;
                }
                $player = $this->getServer()->getPlayer($sender->getName());

                if($player->hasPermission("morecommands.gmc")){
                    if($player->getGamemode() == 1){
                        $player->sendMessage(TextFormat::RED."You are already in creative mode!");
                    } else {
                        $player->setGamemode(1);
                        $player->sendMessage(TextFormat::GREEN."You are now in creative mode!");
                    }
                    return true;

                } else {
                    $player->sendMessage($this->permMessage);
                    return true;
                }
                break;
            case "gma":
                if(!($sender instanceof Player)){
                    $sender->sendMessage($this->consoleMsg);
                    return true;
                }
                $player = $this->getServer()->getPlayer($sender->getName());

                if($player->hasPermission("morecommands.gma")){
                    if($player->getGamemode() == 2){
                        $player->sendMessage(TextFormat::RED."You are already in adventure mode!");
                    } else {
                        $player->setGamemode(2);
                        $player->sendMessage(TextFormat::GREEN."You are now in adventure mode!");
                    }
                    return true;

                } else {
                    $player->sendMessage($this->permMessage);
                    return true;
                }
                break;
            case "gmspc":
                if(!($sender instanceof Player)){
                    $sender->sendMessage($this->consoleMsg);
                    return true;
                }
                $player = $this->getServer()->getPlayer($sender->getName());

                if($player->hasPermission("morecommands.gmspc")){
                    if($player->getGamemode() == 3){
                        $player->sendMessage(TextFormat::RED."You are already in spectator mode!");
                    } else {
                        $player->setGamemode(3);
                        $player->sendMessage(TextFormat::GREEN."You are now in spectator mode!");
                    }
                    return true;

                } else {
                    $player->sendMessage($this->permMessage);
                    return true;
                }
                break;
            case "slay":
                if($sender->hasPermission("morecommands.slay")){
                    if($count == 0){
                        return false;
                    }
                    if($count == 1){
                        $target = $this->getServer()->getPlayer($args[0]);

                        if($target == null){
                            $sender->sendMessage(TextFormat::YELLOW."Player '"
                                .TextFormat::AQUA.$args[0].TextFormat::YELLOW."' was not found!");
                            return true;
                        } else {
                            $target->setHealth(0);
                            $sender->sendMessage(TextFormat::YELLOW."Player '".
                                TextFormat::AQUA.$args[0].TextFormat::YELLOW."' has been slain.");
                            return true;
                        }

                    }
                } else {
                    $sender->sendMessage($this->permMessage);
                    return true;
                }
                break;
            case "heal":
                if($sender->hasPermission("morecommands.heal")){
                    if($count == 0){

                        if(!($sender instanceof Player)){
                            $sender->sendMessage(TextFormat::RED."Silly console, /heal is for players!");
                            return true;
                        } else {
                            $sender->setHealth(20);
                            $sender->sendMessage(TextFormat::GREEN."You have been healed.");
                            return true;
                        }

                    }
                    if($count == 1){
                        $target = $this->getServer()->getPlayer($args[0]);

                        if($target == null){
                            $sender->sendMessage(TextFormat::YELLOW."Player '".
                                TextFormat::AQUA.$args[0].TextFormat::YELLOW."' was not found!");
                            return true;
                        } else {
                            $target->setHealth(20);
                            $target->sendMessage(TextFormat::GREEN."You were healed.");
                            $sender->sendMessage(TextFormat::YELLOW."Player '".
                                TextFormat::AQUA.$args[0].TextFormat::YELLOW."' was healed.");
                            return true;
                        }

                    }
                } else {
                    $sender->sendMessage($this->permMessage);
                    return true;
                }
                break;
            case "last":
                if($count === 0){
                    $cmd = $this->lastCommandListener->getLastCommand($sender);
                    if($cmd !== null){
                        $this->getServer()->dispatchCommand($sender, $cmd);
                        return true;
                    } else {
                        //then an error message was set
                        $sender->sendMessage($this->lastCommandListener->getLastCommandErrorMsg());
                        return true;
                    }
                } else if($count === 1){

                    if($args[0] === "history"){
                        $this->lastCommandListener->showHistory($sender);
                        return true;
                    } else {

                        $num = $args[0];
                        if(!is_int($num)){
                            return false;
                        }
                        $cmd = $this->lastCommandListener->getLastCommand($sender, $num);

                        if($cmd !== null){
                            $this->getServer()->dispatchCommand($sender, $cmd);
                            return true;
                        } else {
                            $sender->sendMessage($this->lastCommandListener->getLastCommandErrorMsg());
                            return true;
                        }

                    }
                }

                else { return false; }

                break;
            case "uuid":
                if($sender->hasPermission("morecommands.uuid")){

                    if(!($sender instanceof Player)){
                        $sender->sendMessage($this->consoleMsg);
                        return true;
                    }

                    $sender->sendMessage(TextFormat::YELLOW."Your UUID is: ".TextFormat::AQUA.$sender->getUniqueId());
                    $sender->sendMessage(TextFormat::YELLOW."Your EntityID is: ".TextFormat::AQUA.$sender->getId());
                    return true;
                } else {
                    $sender->sendMessage($this->permMessage);
                    return true;
                }
                break;
        }

        return true;
    }

}