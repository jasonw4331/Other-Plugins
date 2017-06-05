<?php
namespace ViperKits\kit;

use ViperKits\ViperKits;
use pocketmine\Player;

class KitPaymentController{
    /** @var  ViperKits */
    private $plugin;
    public function __construct(ViperKits $plugin){
        $this->plugin = $plugin;
    }
    public function grantKit($name, Player $player){
        $kit = $this->getPlugin()->getKitStore()->getKit($name);
        if($kit !== false){
            if($player->hasPermission("ViperKits.use.$name")){
                if($kit->isActiveIn($player->getLevel())){
                    if($this->getPlugin()->getKitHistoryStore()->canUse($player)){
                        if(!$kit->isFree()){
                            if($this->getPlugin()->isLinkedToEconomy()){
                                if($this->getPlugin()->getEconomy()->take($kit->getCost(), $player)){
                                    $kit->applyTo($player);
                                    $this->getPlugin()->getKitHistoryStore()->kitUsed($player, $kit);
                                    return true;
                                }
                                else{
                                    $player->sendMessage("$name can't be purchased.");
                                    return false;
                                }
                            }
                            else{
                                $player->sendMessage("$name can't be purchased at this time.");
                                return false;
                            }
                        }
                        else{
                            $kit->applyTo($player);
                            $this->getPlugin()->getKitHistoryStore()->kitUsed($player, $kit);
                            return true;
                        }
                    }
                    else{
                        $player->sendMessage("You can only use one kit per life.");
                        return false;
                    }
                }
                else{
                    $player->sendMessage("$name is not available in this world.");
                    return false;
                }
            }
            else{
                $player->sendMessage("You don't have permission to use $name.");
                return false;
            }
        }
        else{
            $player->sendMessage("$name doesn't exist.");
            return false;
        }
    }
    /**
     * @return \ViperKits\ViperKits
     */
    public function getPlugin(){
        return $this->plugin;
    }
}
