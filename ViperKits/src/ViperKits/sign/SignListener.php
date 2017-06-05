<?php
namespace ViperKits\sign;

use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\tile\Sign;
use ViperKits\ViperKits;

class SignListener implements Listener{
    /** @var ViperKits  */
    private $plugin;
    public function __construct(ViperKits $plugin){
        $this->plugin = $plugin;
        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());
    }
    public function onSignChange(SignChangeEvent $event){
        if($event->getLine(0) === $this->getPlugin()->getConfig()->get('sign-trigger')){
            if($event->getPlayer()->hasPermission("ViperKits.sign.create")){
                if(!empty($event->getLine(1))){
                    $event->getPlayer()->sendMessage("You have successfully created kit sign.");
                }
                else{
                    $event->getPlayer()->sendMessage("You must set a kit.");
                    $event->setCancelled();
                }
            }
            else{
                $event->getPlayer()->sendMessage("You don't have permission to make kit signs.");
                $event->setCancelled();
            }
        }
    }
    public function onInteract(PlayerInteractEvent $event){
        $sign = $event->getPlayer()->getLevel()->getTile($event->getBlock());
        if($sign instanceof Sign){
            $text = $sign->getText();
            if($text[0] === $this->getPlugin()->getConfig()->get('sign-trigger') && !empty($text[1])){
                if($event->getPlayer()->hasPermission("ViperKits.sign.use")){
                    $this->getPlugin()->getKitPaymentController()->grantKit($text[1], $event->getPlayer());
                }
                else{
                    $event->getPlayer()->sendMessage("You don't have permission to use kit signs.");
                }
            }
        }
    }
    /**
     * @return \ViperKits\ViperKits
     */
    public function getPlugin(){
        return $this->plugin;
    }
}
