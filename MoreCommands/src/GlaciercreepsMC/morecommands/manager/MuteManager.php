<?php

namespace GlaciercreepsMC\morecommands\manager;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor ;
use pocketmine\utils\TextFormat;

class MuteManager implements Listener, CommandExecutor{

    private $plugin;
    private $muted = [];
    private $permMessage;

    public function __construct(PluginBase $plugin) {
        $this->plugin = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents($this, $this->plugin);
        $this->permMessage = TextFormat::RED."You do not have permission for this!";
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        $count = count($args);
        $cmd = strtolower($command->getName());

        if ($cmd === "mute"){
            if ($sender->hasPermission("morecommands.mute")){

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
                        $this->mutePlayer($target, $sender);
                        return true;
                    }

                }

            } else {
                $sender->sendMessage($this->permMessage);
                return true;
            }
        }

        if ($cmd === "unmute"){
            if ($sender->hasPermission("morecommands.unmute")){

                if ($count === 0){
                    return false;
                }
                if ($count === 1){
                    $target = $this->plugin->getServer()->getPlayer($args[0]);

                    if ($target == null){
                        $sender->sendMessage(TextFormat::YELLOW."Player '".
                            TextFormat::AQUA.$args[0].TextFormat::YELLOW."' was not found!");
                        return true;
                    } else {
                        $this->unmutePlayer($target, $sender);
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

    public function mutePlayer(Player $player, CommandSender $sender){
        $id = $player->getUniqueId();
        $name = $player->getName();

        if (in_array($id, $this->muted)){
            $sender->sendMessage(TextFormat::YELLOW."Player '".
                TextFormat::AQUA.$name.TextFormat::YELLOW."' is already muted!");
        } else {
            $this->muted[$name] = $id;
            $sender->sendMessage(TextFormat::GREEN."Player '".
                TextFormat::AQUA.$name.TextFormat::GREEN."' has been muted.");
            $player->sendMessage(TextFormat::RED."You have been muted.");
        }

    }

    public function unmutePlayer(Player $player, CommandSender $sender){
        $id = $player->getUniqueId();
        $name = $player->getName();

        if (in_array($id, $this->muted)){
            $index = array_search($id, $this->muted);

            if ($index === false){
                $sender->sendMessage(TextFormat::YELLOW."Player '".
                    TextFormat::AQUA.$name.TextFormat::YELLOW."' wasn't muted!");
            } else {
                unset($this->muted[$index]);
                $sender->sendMessage(TextFormat::GREEN."Player '".
                    TextFormat::AQUA.$name.TextFormat::GREEN."' has been unmuted.");
                $player->sendMessage(TextFormat::GREEN."You have been unmuted.");
            }

        }

    }

    public function onPlayerChat(PlayerChatEvent $event)
    {
        $player = $event->getPlayer();
        foreach ($this->muted as $name => $id) {
            if ($player->getName() === $name && $player->getUniqueId() === $id){
                $event->setCancelled();
            } else {
                return;
            }
        }
    }
}
