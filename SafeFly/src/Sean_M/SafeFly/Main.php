<?php
namespace Sean_M\SafeFly;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class Main extends PluginBase implements Listener {

    public $players = array();

     public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TextFormat::GREEN . "SafeFly by Sean_M enabled!");
     }

     public function onDisable() {
        $this->getLogger()->info(TextFormat::RED . "SafeFly by Sean_M disabled!");
     }
   
     public function onEntityDamage(EntityDamageEvent $event) {
        if($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            $harmed = $event->getEntity();
            if(($damager instanceof Player and $this->isPlayer($damager) and $damager->isFlying() and $harmed instanceof Player) or
                ($harmed instanceof Player and $this->isPlayer($harmed) and $harmed->isFlying() and $damager instanceof Player)) {
                $damager->sendTip(TextFormat::RED . "You cannot damage players while flying!");
                $event->setCancelled(true);
            }elseif($damager instanceof Player and $damager->isCreative() and $damager->isFlying() and $harmed instanceof Player) {
                $damager->sendTip(TextFormat::RED . "You cannot damage players while flying in creative!");
                $event->setCancelled(true);
            }
        }
     }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
        if(strtolower($cmd->getName()) == "fly") {
            if($sender instanceof Player and $sender->hasPermission("safefly.fly")) {
                if(!$sender->isSurvival()) {
                    return true;
                }
                if($this->isPlayer($sender)) {
                    $this->removePlayer($sender);
                    $sender->setFlying(false);
                    $sender->setAllowFlight(false);
                    $sender->sendMessage(TextFormat::RED . "You have disabled fly mode!");
                    return true;
                }
                else{
                    $this->addPlayer($sender);
                    $sender->setAllowFlight(true);
                    $sender->sendMessage(TextFormat::GREEN . "You have enabled fly mode!");
                    return true;
                }
            }
            else{
                $sender->sendMessage(TextFormat::YELLOW . "Please use this command in-game.");
                return true;
            }
        }
        return true;
    }
    public function addPlayer(Player $player) {
        $this->players[$player->getName()] = $player->getName();
    }
    public function isPlayer(Player $player) {
        return in_array($player->getName(), $this->players);
    }
    public function removePlayer(Player $player) {
        unset($this->players[$player->getName()]);
    }
}
