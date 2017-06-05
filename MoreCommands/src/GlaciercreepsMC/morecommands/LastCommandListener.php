<?php

namespace GlaciercreepsMC\morecommands;

use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\ServerCommandEvent;
use pocketmine\utils\TextFormat;
use \pocketmine\command\CommandSender;

class LastCommandListener implements Listener {
    /** @var string[][] $lastCommands */
    private $lastCommands = [];
    /** @var MoreCommands $plugin */
    private $plugin;
    /** @var array $errorMessages */
    private $errorMessages = [
        "empty" => TextFormat::YELLOW."Command history empty!",
        "notbetween" => TextFormat::YELLOW."Invalid count. Must provide a # from 1-10",
        "invalidcount1" => TextFormat::YELLOW."Invalid. So far, you have run ",
        "invalidcount2" => TextFormat::YELLOW." commands."
    ];
    /** @var string $msg */
    private $msg;


    public function __construct(MoreCommands $plugin) {
        $this->plugin = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function onPlayerCommandPreprocessEvent(PlayerCommandPreprocessEvent $event){
        $id = $event->getPlayer()->getName();
        $cmd = strtolower($event->getMessage());

        if (!array_key_exists($id, $this->lastCommands)){
            $this->lastCommands[$id] = [];
            if (strpos($cmd, "last") !== false){
                return;
            } else {
                $this->addCommandToHistory($id, $cmd);
            }

        } else {
            if (strpos($cmd, "last") !== false){
                return;
            } else {
                $this->addCommandToHistory($id, $cmd);
            }
        }

    }

    public function onServerCommandEvent(ServerCommandEvent $event){
        $cmd = strtolower($event->getCommand());
        $id = $event->getSender()->getName();

        if (!array_key_exists($id, $this->lastCommands)){
            $this->lastCommands[$id] = [];
            if (strpos($cmd, "last") !== false){
                return;
            } else {
                $this->addCommandToHistory($id, $cmd);
            }

        } else {
            if (strpos($cmd, "last") !== false){
                return;
            } else {
                $this->addCommandToHistory($id, $cmd);
            }
        }

    }

    public function onPlayerQuit(PlayerQuitEvent $event){
        unset($this->lastCommands[$event->getPlayer()->getName()]); //save memory
    }

    public function getLastCommand(CommandSender $sender, $backCount = 1){

        if (count($this->lastCommands[$sender->getName()]) < 1){
            $this->setLastCommandErrorMsg($this->errorMessages["empty"]);
            return null;
        }

        if ($backCount <= 0 || $backCount > 10){
            //$this(TextFormat::YELLOW."Invalid count. Must provide a # from 1-10");
            $this->setLastCommandErrorMsg($this->errorMessages["notbetween"]);
            return null;
        }

        $id = $sender->getName();

        $count = count($this->lastCommands[$id]);
        if ($backCount > $count){
            $this->setLastCommandErrorMsg($this->errorMessages["invalidcount1"].TextFormat::AQUA.$count.$this->errorMessages["invalidcount2"]);
            return null;
        }

        if ($backCount === 1){
            $index = $this->getLastElementIndex($this->lastCommands[$id]);
            return ($sender instanceof Player) ? (substr($this->lastCommands[$id][$index], 1)) : $this->lastCommands[$id][$index];
        } else if ($backCount >= 2) {
            $index = $count - $backCount;
            return ($sender instanceof Player) ? (substr($this->lastCommands[$id][$index], 1)) : $this->lastCommands[$id][$index];
        }
        return null;
    }

    public function showHistory(CommandSender $sender){
        $id = $sender->getName();
        $brack1 = TextFormat::GOLD."[";
        $brack2 = TextFormat::GOLD."]";
        $a = TextFormat::AQUA;
        $count = count($this->lastCommands[$id]);

        if ($count === 0){
            $sender->sendMessage($this->errorMessages["empty"]);
            return;
        }

        for ($i = $count-1, $j = 0; $i >= 0; $i--, $j++){
            $sender->sendMessage($brack1.$a.($i+1).$brack2.": ".$a.$this->lastCommands[$id][$j]);
        }
    }

    private function addCommandToHistory(string $id, string $cmd){
        if (count($this->lastCommands[$id]) >= 10) array_pop($this->lastCommands[$id]);
        $this->lastCommands[$id][] = $cmd;
    }

    private function setLastCommandErrorMsg(string $msg){
        $this->msg = $msg;
    }

    public function getLastCommandErrorMsg(){
        return $this->msg;
    }

    private function getLastElementIndex(array &$array){
        $arr = $array;
        end($array);
        $index = key($array);
        reset($array);
        $array = $arr;
        return $index;
    }

}


