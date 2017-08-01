<?php

namespace GlaciercreepsMC\morecommands\manager;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\utils\TextFormat;

class FreezeManager implements Listener, CommandExecutor {

    public $plugin;
    public $frozen = [];
    private $permMessage;


    public function __construct(PluginBase $plugin) {
        $this->plugin = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents($this, $this->plugin);
        $this->permMessage = TextFormat::RED."You do not have permission for this!";
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        $count = count($args);
        $cmd = strtolower($command);

        if ($cmd === "freeze"){
            if ($sender->hasPermission("morecommands.freeze")) {

                if ($count == 0) {
                    return false;
                }
                if ($count == 1) {
                    $target = $this->plugin->getServer()->getPlayer($args[0]);

                    if ($target == null) {
                        $sender->sendMessage(TextFormat::YELLOW."Player '".
                            TextFormat::AQUA.$args[0].TextFormat::YELLOW."' was not found!");
                        return true;
                    } else {
                        $this->freezePlayer($target, $sender);
                        return true;
                    }

                }
            } else {
                $sender->sendMessage($this->permMessage);
                return true;
            }
        }


        if ($cmd === "unfreeze"){
            if ($sender->hasPermission("morecommands.unfreeze")){

                if ($count == 0){
                    return false;
                }

                if ($count == 1){
                    $target = $this->plugin->getServer()->getPlayer($args[0]);

                    if ($target == null){
                        $sender->sendMessage(TextFormat::YELLOW."Player '".
                            TextFormat::AQUA.$args[0].TextFormat::YELLOW."' was not found!");
                        return true;
                    } else {
                        $this->unfreezePlayer($target, $sender);
                        return true;
                    }

                }

            } else {
                $sender->sendMessage($this->permMessage);
                return true;
            }
        }
        return true;
    }

    public function freezePlayer(Player $player, CommandSender $sender){
        $id = $player->getUniqueId();
        $name = $player->getName();

        if (in_array($id, $this->frozen)){
            $sender->sendMessage(TextFormat::YELLOW."Player '".
                TextFormat::AQUA.$name.TextFormat::YELLOW."' is already frozen!");
        } else {
            $this->frozen[$name] = $id;
            $sender->sendMessage(TextFormat::GREEN."Player '".
                TextFormat::AQUA.$name.TextFormat::GREEN."' is now frozen.");
            $player->sendMessage(TextFormat::AQUA."You have been frozen.");
        }

    }

    public function unfreezePlayer(Player $player, CommandSender $sender){
        $id = $player->getUniqueId();
        $name = $player->getName();

        if (in_array($id, $this->frozen)){
            $index = array_search($id, $this->frozen);

            if ($index === false){
                $sender->sendMessage(TextFormat::YELLOW."Player '".
                    TextFormat::AQUA.$name.TextFormat::YELLOW."' wasn't frozen!");
            } else {
                unset($this->frozen[$index]);
                $sender->sendMessage(TextFormat::GREEN."Player '".
                    TextFormat::AQUA.$name.TextFormat::GREEN."' has been unfrozen.");
                $player->sendMessage(TextFormat::GREEN."You have been unfrozen.");
            }

        }

    }

    public function getFrozenPlayers(){
        return $this->frozen;
    }

    public function onPlayerMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        foreach ($this->frozen as $name => $id){
            if ($player->getName() === $name && $player->getUniqueId() === $id){
                $event->setTo($event->getFrom());
            }
        }
    }

    public function onBlockBreak(BlockBreakEvent $event){
        $player = $event->getPlayer();
        foreach ($this->frozen as $name => $id){
            if ($player->getName() === $name && $player->getUniqueId() === $id){
                $event->setCancelled();
            }
        }
    }

}
