<?php
namespace jasonwynn10\Bancid;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {
    /** @var Config $config */
    private $config;
    public function onEnable() {
        @mkdir($this->getDataFolder());
        $this->saveResource("banned.yml");
        $this->config = new Config($this->getDataFolder()."banned.yml", Config::YAML, [
            "Banned" => [],
            "Times" => []
        ]);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new TimeTask($this), 20*60); //run every minute
        $this->getLogger()->notice("Enabled!");
    }
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        if(strtolower($command) == "bancid") {
            if (isset($args[0])) {
                if(($player = $this->getServer()->getPlayer($args[0])) instanceof Player) {
                    $name = $player->getName();
                    $id = $player->getClientId();
                    $banned = $this->getConfig()->get("Banned", []);
                    $times = $this->getConfig()->get("Times", []);
                    $banned[$name] = $id;
                    $times[$id] = -1;
                    $this->getConfig()->set("Banned", $banned);
                    $this->getConfig()->set("Times", $times);
                    $this->getConfig()->save();
                    $player->setBanned(true); //easy way to kick the player while giving a ban message
                    $sender->sendMessage($name." is now banned with the cid: ".$id);
                }else{
                    $sender->sendMessage("That player isn't online right now!");
                }
            }else{
                return false;
            }
        }elseif(strtolower($command) == "unbancid") {
            if (isset($args[0])) {
                $banned = $this->getConfig()->get("Banned", []);
                $times = $this->getConfig()->get("Times", []);
                foreach($banned as $name => $id) {
                    if($args[0] == $name) {
                        unset($banned[$name]);
                        unset($times[$id]);
                        $this->getServer()->getNameBans()->remove($name);
                        break;
                    }
                    if($args[0] == $id) {
                        $key = array_search($id, $banned, true);
                        unset($banned[$key]);
                        unset($times[$id]);
                        $this->getServer()->getNameBans()->remove($key);
                        break;
                    }
                }
                $this->getConfig()->set("Banned", $banned);
                $this->getConfig()->set("Times", $times);
                $this->getConfig()->save();
            }
        }elseif(strtolower($command) == "timebancid") {
            if(isset($args[0]) and isset($args[1])) {
                if(($player = $this->getServer()->getPlayer($args[0])) instanceof Player and is_int($args[1])) {
                    $name = $player->getName();
                    $id = $player->getClientId();
                    $banned = $this->getConfig()->get("Banned", []);
                    $times = $this->getConfig()->get("Times", []);
                    $banned[$name] = $id;
                    $times[$id] = $args[1];
                    $this->getConfig()->set("Banned", $banned);
                    $this->getConfig()->set("Times", $times);
                    $this->getConfig()->save();
                    $player->setBanned(true); //easy way to kick the player while giving a ban message
                    $sender->sendMessage($name." is now banned with the cid: ".$id);
                }else{
                    $sender->sendMessage("That player isn't online right now!");
                }
            }else{
                return false;
            }
        }
        return true;
    }
    public function onPreLogin(PlayerPreLoginEvent $ev) {
        $p = $ev->getPlayer();
        foreach($this->getConfig()->get("Banned", []) as $name => $id) {
            foreach ($this->getConfig()->get("Times", []) as $key => $time) {
                if($key === $id) {
                    if($p->getClientId() === $id) {
                        $ev->setKickMessage("You are banned for {$time} more minutes!");
                        $ev->setCancelled();
                    }
                }else{
                    if($p->getClientId() === $id) {
                        $ev->setKickMessage("You have been banned!");
                        $ev->setCancelled();
                    }
                }
            }
        }
    }
    public function unBanPlayer($id) {
        $banned = $this->getConfig()->get("Banned", []);
        $times = $this->getConfig()->get("Times", []);
        foreach($banned as $name => $cid) {
            if($cid === $id) {
                $name = array_search($id, $banned, true);
                unset($banned[$name]);
                unset($times[$id]);
                $this->getServer()->getNameBans()->remove($name);
                break;
            }
        }
        $this->getConfig()->set("Banned", $banned);
        $this->getConfig()->set("Times", $times);
        $this->getConfig()->save();
    }
    public function getConfig() : Config {
        return $this->config;
    }
}