<?php
namespace ViperKits;

use pocketmine\plugin\PluginBase;
use ViperKits\command\BattleKitCommand;
use ViperKits\economy\Economy;
use ViperKits\economy\EconomyLoader;
use ViperKits\economy\BaseEconomy;
use ViperKits\economy\PocketMoney;
use ViperKits\kit\KitHistoryStore;
use ViperKits\kit\KitPaymentController;
use ViperKits\kit\KitStore;
use ViperKits\sign\SignListener;

class ViperKits extends PluginBase{
    /** @var  EconomyLoader */
    private $economyLoader;
    /** @var  BaseEconomy */
    private $economy = false;
    /** @var  KitStore */
    private $kitStore;
    /** @var  KitPaymentController */
    private $kitPaymentController;
    /** @var  BattleKitCommand */
    private $mainCommand;
    /** @var  SignListener */
    private $signListener;
    /** @var  KitHistoryStore */
    private $kitHistoryStore;
    public function onEnable(){
        $this->saveDefaultConfig();

        $this->economyLoader = $this->economyLoader();//new EconomyLoader($this);
        $this->kitStore = new KitStore($this);
        $this->kitHistoryStore = new KitHistoryStore($this);
        $this->kitPaymentController = new KitPaymentController($this);
        
        $this->mainCommand = new BattleKitCommand($this);
        $this->getServer()->getCommandMap()->register("ViperKits", $this->mainCommand);
        
        $this->signListener = new SignListener($this);

    }

    public function economyLoader() {
        if($this->getConfig()->get('preferred-economy') !== false){
            $name = $this->getConfig()->get('preferred-economy');
            try{
                $econ = new $name($this);
                if($econ instanceof BaseEconomy){
                    if($econ->isReady()){
                        $this->setEconomy($econ);
                        $this->getLogger()->info("Loaded " . $econ->getName());
                    }
                    else{
                        $this->getLogger()->critical("The preferred-economy you specified is not loaded.");
                    }
                }
            }
            catch(\ClassNotFoundException $e){
                $this->getLogger()->critical("The preferred-economy you specified is not supported.");
            }
        }
        else{
            /*
             * Try loading EconomyS
             */
            $econ = new Economy($this);
            if($econ->isReady()){
                $this->setEconomy($econ);
                $this->getLogger()->info("Loaded " . $econ->getName());
                return;
            }
            /*
             * Try loading PocketMoney
             */
            $econ = new PocketMoney($this);
            if($econ->isReady()){
                $this->setEconomy($econ);
                $this->getLogger()->info("Loaded " . $econ->getName());
                return;
            }
            $this->getLogger()->critical("No economy found, an economy is not required but certain features will be unavailable.");
        }
    }

    /**
     * @param \ViperKits\economy\BaseEconomy $economy
     */
    public function setEconomy(BaseEconomy $economy){
        $this->economy = $economy;
    }

    /**
     * @return \ViperKits\economy\BaseEconomy
     */
    public function getEconomy(){
        return $this->economy;
    }
    /**
     * @return bool
     */
    public function isLinkedToEconomy(){
        return $this->economy instanceof BaseEconomy;
    }
    /**
     * @return \ViperKits\kit\KitStore
     */
    public function getKitStore(){
        return $this->kitStore;
    }
    /**
     * @return \ViperKits\command\BattleKitCommand
     */
    public function getMainCommand(){
        return $this->mainCommand;
    }
    /**
     * @return \ViperKits\economy\EconomyLoader
     */
    public function getEconomyLoader(){
        return $this->economyLoader;
    }
    /**
     * @return \ViperKits\sign\SignListener
     */
    public function getSignListener(){
        return $this->signListener;
    }

    /**
     * @return \ViperKits\kit\KitPaymentController
     */
    public function getKitPaymentController(){
        return $this->kitPaymentController;
    }

    /**
     * @return \ViperKits\kit\KitHistoryStore
     */
    public function getKitHistoryStore(){
        return $this->kitHistoryStore;
    }
    public function reportEconomyLinkError(){
        $this->getLogger()->critical("The link to " . $this->economy->getName() . " has been lost. Paid kits are no longer available.");
        $this->economy = false;
    }

}
