<?php
namespace jasonwynn10\BZ;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use pocketmine\event\player\PlayerInteractEvent;

class Main extends PluginBase implements Listener {

    /** @var Config $a */
    public $a;
    /** @var Area[] $areas */
    public $areas = array(), $levels, $sel1, $sel2, $pos1, $pos2;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
        $this->a = new Config($this->getDataFolder()."areas.yml",Config::YAML,["zones"=>array()]);
        if(file_exists($this->getDataFolder()."areas.yml")) {
            $this->getAreas();
        }
        $this->getLogger()->notice(TF::GREEN."Enabled!");
    }

    /**
     * @param CommandSender $player
     * @param Command $command
     * @param string $label
     * @param array $args
     * @return bool
     */
    public function onCommand(CommandSender $player, Command $command, string $label, array $args) : bool {
        if(strtolower($command) === "bz" or strtolower($command) === "buildzone") {
            if(!($player instanceof Player)) {
                $player->sendMessage(TF::RED . "Command must be used in-game.");
                return true;
            }
            if(!isset($args[0])) {
                return false;
            }
            if($player->hasPermission("area") || $player->hasPermission("area.cmd")) {
                $n = $player->getName();
                $action = strtolower($args[0]);
                $o = null;
                switch($action) {
                    case "pos1":
                        if(isset($this->sel1[$n]) || isset($this->sel2[$n])) {
                            $o = "You're already selecting a position!";
                        } else {
                            $this->sel1[$n] = true;
                            $o = "Please tap the block of the first position.";
                        }
                        break;
                    case "pos2":
                        if(isset($this->sel1[$n]) || isset($this->sel2[$n])) {
                            $o = "You're already selecting a position!";
                        } else {
                            $this->sel2[$n] = true;
                            $o = "Please tap the block of the second position.";
                        }
                        break;
                    case "create":
                        if(isset($args[1])) {
                            if (isset($this->pos1) and isset($this->pos2)) {
                                if (!isset($this->areas[$args[1]])) {
                                    new Area($args[1], $this->pos1, $this->pos2, $this);
                                    $this->saveAreas();
                                    unset($this->pos1);
                                    unset($this->pos2);
                                    $o = "Area created!";
                                } else {
                                    $o = "An area with that name already exists.";
                                }
                            } else {
                                $o = "Please select both positions first.";
                            }
                        } else {
                            $o = "Please specify a name for this area.";
                        }
                        break;
                    case "list":
                        $player->sendMessage(TF::YELLOW."Areas: ");
                        foreach ($this->areas as $area) {
                            $player->sendMessage(TF::YELLOW.$area->getName().", ");
                        }
                        break;
                    default:
                        return false;
                        break;
                }
                $player->sendMessage($o);
                return true;
            }else{
                $player->sendMessage(TF::RED."YOU NEED PERMISSION TO USE THIS COMMAND!");
                return true;
            }
        }
        return false;
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function InteractionPrevention(PlayerInteractEvent $event) {
        $p = $event->getPlayer();
        $b = $event->getBlock();
        $ppos = $p->getPosition();
        if($this->sel1[$p->getName()] == true) {
            $this->pos1 = new Position($b->getX(),$b->getY(),$b->getZ(),$p->getLevel());
            $this->getLogger()->debug(TF::YELLOW."First Position Set");
            $p->sendMessage(TF::YELLOW."First Position Set");
            $this->sel1[$p->getName()] = null;
        }
        if($this->sel2[$p->getName()] == true) {
            $this->pos2 = new Position($b->getX(),$b->getY(),$b->getZ(),$p->getLevel());
            $this->getLogger()->debug(TF::YELLOW."Second Position Set");
            $p->sendMessage(TF::YELLOW."Second Position Set");
            $this->sel2[$p->getName()] = null;
        }
        if(count($this->areas) > 0) {
            foreach($this->areas as $area) {
                if($area->contains($ppos) or $area->contains($b)) {
                    $this->getLogger()->debug("There has been an interaction within an unsecured area!");
                } else {
                    if($p->hasPermission("act.bypass")) {
                        $this->getLogger()->debug("There has been an interaction inside a secured area!");
                    } else {
                        $this->getLogger()->debug("There has been an interaction prevented inside a secured area!");
                        $event->setCancelled();
                    }
                }
            }
        } else {
            if($p->hasPermission("act.bypass")) {
                $event->setCancelled(false);
            } else {
                $event->setCancelled(true);
                $this->getLogger()->info($p->getName()." tried to do something!");
            }
        }
        return;
    }

    /**
     * @return bool
     */
    public function saveAreas() {
        $arr = array();
        $names = array();
        $vars = array();
        foreach ($this->areas as $area) {
            array_push($names,$area->getName(), $area->getName());
            array_push($vars,$area->getPos1(),$area->getPos2());
            $arr = array_combine($names,$vars);
        }
        var_dump($arr);
        $this->a->set("zones",$arr);
        if(!$this->checkSaved($arr)) {
            $this->getLogger()->debug("Areas data did not save! or there aren't any to save!");
            return false;
        }
        return true;
    }

    /**
     * @param mixed $check
     * @return bool
     */
    public function checkSaved($check) {
        if($this->a->get("zones", array()) != $check) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return array
     */
    public function getAreas() {
        $data = yaml_parse(file_get_contents($this->getDataFolder()."areas.yml"));
//        var_dump($data);
//        var_dump($data["zones"]);
        $a = $data["zones"];
        if($a !== array()) {
            foreach($a as $b) {
//                var_dump($b);
                var_dump($b["pos1"]);
                var_dump($b["pos2"]);
                $area = new Area($b["name"],new Vector3($b["pos1"][0],$b["pos1"][1], $b["pos1"][2]),new Vector3($b["pos2"][0],$b["pos2"][1], $b["pos2"][2]),$this);
                array_push($this->areas,$area);
            }
        }
        return $this->areas;
    }

    /**
     *
     */
    public function onDisable() {
        $this->saveAreas();
        $this->getLogger()->notice(TF::GREEN."Disabled!");
    }
}