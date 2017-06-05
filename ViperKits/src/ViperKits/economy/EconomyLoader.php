<?php
namespace ViperKits\economy;

use ViperKits\ViperKits;

class EconomyLoader{
    public function __construct(ViperKits $plugin){
        if($plugin->getConfig()->get('preferred-economy') !== false){
            $name = $plugin->getConfig()->get('preferred-economy');
            try{
                $econ = new $name($plugin);
                if($econ instanceof BaseEconomy){
                    if($econ->isReady()){
                        $plugin->setEconomy($econ);
                        $plugin->getLogger()->info("Loaded " . $econ->getName());
                    }
                    else{
                        $plugin->getLogger()->critical("The preferred-economy you specified is not loaded.");
                    }
                }
            }
            catch(\ClassNotFoundException $e){
                $plugin->getLogger()->critical("The preferred-economy you specified is not supported.");
            }
        }
        else{
            /*
             * Try loading EconomyS
             */
            $econ = new Economy($plugin);
            if($econ->isReady()){
                $plugin->setEconomy($econ);
                $plugin->getLogger()->info("Loaded " . $econ->getName());
                return;
            }
            /*
             * Try loading PocketMoney
             */
            $econ = new PocketMoney($plugin);
            if($econ->isReady()){
                $plugin->setEconomy($econ);
                $plugin->getLogger()->info("Loaded " . $econ->getName());
                return;
            }
            $plugin->getLogger()->critical("No economy found, an economy is not required but certain features will be unavailable.");
        }
    }
}
