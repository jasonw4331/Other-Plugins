<?php
namespace jasonwynn10\iStone;


use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase implements Listener {
  public $set = false, $blocks = [];
  public function onEnable() {
    $this->saveDefaultConfig();
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->getLogger()->notice(TF::GREEN."Enabled!");
  }
  public function infinity(BlockBreakEvent $event) {
    $p = $event->getPlayer();
    $b = $event->getBlock();
    if($this->set[$p->getName()]) {
      $event->setCancelled();
      $this->set[$p->getName()] = false;
      array_push($this->blocks, $b);
      return;
    }
    foreach($this->blocks as $block) {
      if($block === $b) {
        $n = rand(1,6);
        $motion = true;
        $delay = 4;
        foreach($b->getDrops(null) as $item) {
          $p->getLevel()->dropItem(new Vector3($b->getX(), $b->getY(), $b->getZ()), $item, $motion, $delay);
        }
        switch($n) {
          case 1:
            // set drops to be 3 iron ore
            $p->getLevel()->dropItem(new Vector3($b->getX(), $b->getY(), $b->getZ()), new Item(15,0,3), $motion, $delay);
          break;
          case 2:
            // set drops to be 3 gold ore
            $p->getLevel()->dropItem(new Vector3($b->getX(), $b->getY(), $b->getZ()), new Item(14,0,3), $motion, $delay);
          break;
          case 3:
            // set drops to be 5 lapiz unless silk touched
            $p->getLevel()->dropItem(new Vector3($b->getX(), $b->getY(), $b->getZ()), new Item(531,3,5), $motion, $delay);
          break;
          case 4:
            // set drops to be 5 redstone unless silk touched
            $p->getLevel()->dropItem(new Vector3($b->getX(), $b->getY(), $b->getZ()), new Item(331,0,5), $motion, $delay);
          break;
          case 5:
            // set drops to be 1 diamond unless silk touched
            $p->getLevel()->dropItem(new Vector3($b->getX(), $b->getY(), $b->getZ()), new Item(264), $motion, $delay);
          break;
          case 6:
            // set drops to be 1 emerald unless silk touched
            $p->getLevel()->dropItem(new Vector3($b->getX(), $b->getY(), $b->getZ()), new Item(388), $motion, $delay);
          break;
        }
      $event->setCancelled();
      }
    }
  }
  public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
    if(strtolower($command) === "infinity") {
      if($sender->hasPermission("infinity.cmd")) {
        $this->set[$sender->getName()] = true;
        return true;
      }
    }
    return true;
  }
  public function onDisable() {
    $this->getLogger()->notice(TF::GREEN."Disabled!");
  }
}
