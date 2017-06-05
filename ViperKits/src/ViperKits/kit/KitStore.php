<?php
namespace ViperKits\kit;

use ViperKits\ViperKits;
use pocketmine\event\Listener;
use pocketmine\permission\Permission;

class KitStore implements Listener{
    /** @var ViperKits*/
    private $plugin;
    /** @var  Kit[] */
    private $kits;
    public function __construct(ViperKits $plugin){
        $this->plugin = $plugin;
        foreach($this->getPlugin()->getConfig()->get('kits') as $name => $kitData){
            $perm = new Permission("ViperKits.use.$name", "Apply $name kit");
            $this->getPlugin()->getServer()->getPluginManager()->addPermission($perm);
            $perm->addParent("ViperKits.use", true);
            $this->kits[$name] = new Kit($name, $kitData, $this->getPlugin());
        }
        $this->getPlugin()->getLogger()->info("Loaded " . count($this->kits) . " kits.");
    }
    /**
     * @return \ViperKits\kit\Kit[]
     */
    public function getKits(){
        return $this->kits;
    }
    /**
     * @return \ViperKits\ViperKits
     */
    public function getPlugin(){
        return $this->plugin;
    }
    /**
     * @param $name
     * @return bool
     */
    public function kitExists($name){
        return isset($this->kits[$name]);
    }

    /**
     * @param $name
     * @return Kit|bool
     */
    public function getKit($name){
        if($this->kitExists($name)){
            return $this->kits[$name];
        }
        else{
            return false;
        }
    }
}