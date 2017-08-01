<?php
namespace sizer;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;

class Resize extends PluginCommand {
    public function __construct(Main $plugin) {
        parent::__construct("resize", $plugin);
        $this->setPermission("resize;resize.use;resize.other");
        $this->setDescription("Allows a player to change their size");
        $this->setUsage("/resize <size> [player]");
        $this->setAliases(["rs"]);
        $this->setPermissionMessage("");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if($sender instanceof Player and count($args) >= 1) {
            if(isset($args[1])) {
                if($sender->hasPermission("resize.other")) {
                    $player = $this->getPlugin()->getServer()->getPlayer($args[1]);
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
                $player = $this->getPlugin()->getServer()->getPlayer($args[1]);
                if($player instanceof Player) {
                    $player->setScale($args[0]);
                    $sender->sendMessage("Scale set to". $player->getScale());
                    return true;
                }else{
                    $sender->sendMessage("The specified player isn't online right now.");
                    return true;
                }
            }else{
                $sender->sendMessage("Usage: /resize <size> [player]");
                return true;
            }
        }
        return true;
    }
}