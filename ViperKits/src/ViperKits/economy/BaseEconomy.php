<?php
namespace ViperKits\economy;

use pocketmine\Player;
use ViperKits\ViperKits;

abstract class BaseEconomy{
    private $plugin;
    public function __construct(ViperKits $main){
        $this->plugin = $main;
    }
    public function getPlugin(){
        return $this->plugin;
    }
    public abstract function give($amt, Player $player);
    public abstract function take($amt, Player $player);
    public abstract function setBal($amt, Player $player);
    public abstract function getAPI();
    public abstract function isReady();
    public abstract function getName();
    public function checkReady(){
        if(!$this->isReady()){
            $this->getPlugin()->reportEconomyLinkError();
            return false;
        }
        return true;
    }
}