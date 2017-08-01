<?php
namespace ViperKits\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use ViperKits\ViperKits;

class BattleKitCommand extends Command implements PluginIdentifiableCommand{
    private $main;
    public function __construct(ViperKits $main){
        parent::__construct("ViperKits", "Get your kits.", "/kit [name]", ["vk", "kit", "kits"]);
        $this->main = $main;
    }
    public function execute(CommandSender $sender, string $label, array $args) : bool {
        if(isset($args[0])){
            if($sender instanceof Player){
                $this->getPlugin()->getKitPaymentController()->grantKit($args[0], $sender);
            }
            else{
                $sender->sendMessage("Please run command in game.");
            }
        }
        else{
            if($sender->hasPermission("ViperKits.listkits")){
                $count = 0;
                foreach($this->getPlugin()->getKitStore()->getKits() as $name => $kit){
                    if($kit->isFree() || $this->getPlugin()->isLinkedToEconomy()){
                        if($sender instanceof Player){
                            if($kit->isActiveIn($sender->getLevel())){
                                $sender->sendMessage(sprintf($this->getPlugin()->getConfig()->get('list-format'), $name, $this->getPlugin()->getConfig()->get('econ-prefix'), ($kit->isFree() ? "0" : $kit->getCost())));
                                $count++;
                            }
                        }
                        else{
                            $sender->sendMessage("$name: " . $this->getPlugin()->getConfig()->get('econ-prefix') . ($kit->isFree() ? "0" : $kit->getCost()));
                            $count++;
                        }
                    }
                }
                $sender->sendMessage("Listed $count of " . count($this->getPlugin()->getKitStore()->getKits()));
            }
        }
	    return true;
    }
	/**
	 * @return ViperKits
	 */
    public function getPlugin() : Plugin {
        return $this->main;
    }
}