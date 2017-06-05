<?php
namespace ViperKits\economy;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class PocketMoney extends BaseEconomy{
    public function give($amt, Player $player){
        if(!$this->checkReady()) return false;
        return $this->getAPI()->grantMoney($player->getName(), $amt);
    }
    public function take($amt, Player $player){
        if(!$this->checkReady()) return false;
        return $this->getAPI()->grantMoney($player->getName(), -$amt);
    }
    public function setBal($amt, Player $player){
        if(!$this->checkReady()) return false;
        return $this->getAPI()->setMoney($player->getName(), $amt);
    }

    /**
     * @return \PocketMoney\PocketMoney
     */
    public function getAPI(){
        return $this->getPlugin()->getServer()->getPluginManager()->getPlugin("PocketMoney");
    }
    public function isReady(){
        return ($this->getAPI() instanceof PluginBase);
    }
    public function getName(){
        return "PocketMoney by MinecrafterJPN";
    }
}