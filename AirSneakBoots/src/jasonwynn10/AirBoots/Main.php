<?php
namespace jasonwynn10\AirBoots;

/*
 *      _      _          ____                           _      ____                    _
 *     / \    (_)  _ __  / ___|   _ __     ___    __ _  | | __ | __ )    ___     ___   | |_   ___
 *    / _ \   | | | '__| \___ \  | '_ \   / _ \  / _` | | |/ / |  _ \   / _ \   / _ \  | __| / __|
 *   / ___ \  | | | |     ___) | | | | | |  __/ | (_| | |   <  | |_) | | (_) | | (_) | | |_  \__ \
 *  /_/   \_\ |_| |_|    |____/  |_| |_|  \___|  \__,_| |_|\_\ |____/   \___/   \___/   \__| |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author jasonwynn10
 * @link https://github.com/jasonwynn10/AirSneakBoots
 */

use pocketmine\block\Block;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerToggleFlightEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\item\Armor;
use pocketmine\item\DiamondBoots;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->notice("Enabled!");
    }

    /**
     * @param CommandSender $sender
     * @param Command $command
     * @param string $label
     * @param array $args
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
		if(strtolower($command) == "boots") {
			if($sender instanceof Player) {
				$item = Item::get(Armor::DIAMOND_BOOTS);
				$item->setCustomName("Diamond Boots of Flight");
				$ench = Enchantment::getEnchantment(Enchantment::TYPE_ARMOR_PROTECTION);
				$ench->setLevel(3);
				$item->addEnchantment($ench);
				#$sender->getInventory()->addItem($item);
				return $sender->getInventory()->setBoots($item);
			}
			return false;
		}
		return true;
	}

    /**
     * @param PlayerMoveEvent $ev
     * @priority MONITOR
     */
	public function onMove(PlayerMoveEvent $ev) {
		$to = $ev->getTo();
		$toX = $to->getFloorX();
        $toY = $to->getFloorY();
        $toZ = $to->getFloorZ();
		$from = $ev->getFrom();
		$fromX = $from->getFloorX();
		$fromY = $from->getFloorY();
		$fromZ = $from->getFloorZ();
		$p = $ev->getPlayer();
		if($toX === $fromX and $toY === $fromY and $toZ === $fromZ and $to->getLevel() === $from->getLevel()) {
		    return;
        }
		if($ev->getPlayer()->getInventory()->getBoots() instanceof DiamondBoots and $p->getInventory()->getBoots()->getCustomName() == "Diamond Boots of Flight") {
			#$this->getLogger()->debug("Named Item Found (Movement)");
			if($p->isSneaking()) {
				for($x = -1; $x <= 1; $x++) {
					for($z = -1; $z <= 1; $z++) {
					    $p->add(0,0.75);
                        $newVec = new Vector3($toX+$x, $toY-1, $toZ+$z);
                        if($p->getLevel()->getBlockIdAt($toX+$x, $toY-1, $toZ+$z) == Block::AIR) {
                            $p->getLevel()->setBlock($newVec, Block::get(Block::INVISIBLE_BEDROCK));
                            $this->getLogger()->debug("Set block at {$newVec} to invisible bedrock");
                        }
                        $oldVec = new Vector3($fromX+$x, $fromY-1, $fromZ+$z);
                        if($p->getLevel()->getBlockIdAt($fromX+$x, $fromY-1, $fromZ+$z) == Block::INVISIBLE_BEDROCK) {
                            $p->getLevel()->setBlock($oldVec, Block::get(Block::AIR));
                            $this->getLogger()->debug("Set block at {$oldVec} to air");
                        }
                    }
                }
            }
        }
	}

    /**
     * @param PlayerToggleSneakEvent $ev
     * @priority MONITOR
     */
	public function onSneakToggle(PlayerToggleSneakEvent $ev) {
        if(!$ev->isSneaking() and $ev->getPlayer()->getInventory()->getBoots() instanceof DiamondBoots) {
            if($ev->getPlayer()->getInventory()->getBoots()->getCustomName() != "Diamond Boots of Flight") {
                return;
            }
            #$this->getLogger()->debug("Named Item Found (Sneak Toggle)");
            $p = $ev->getPlayer();
            for($x = -1;$x <= 1; $x++) {
                for($z = -1;$z <= 1; $z++) {
                    $vec = new Vector3($p->getFloorX()+$x, $p->getFloorY()-1, $p->getFloorZ()+$z);
                    if($p->getLevel()->getBlockIdAt($p->getFloorX()+$x, $p->getFloorY()-1, $p->getFloorZ()+$z) == Block::INVISIBLE_BEDROCK) {
                        $p->getLevel()->setBlock($vec, Block::get(Block::AIR));
                        $this->getLogger()->debug("Set block at {$vec} to air");
                    }
                }
            }
        }
	}

    /**
     * @param PlayerToggleFlightEvent $ev
     * @priority MONITOR
     */
	public function onFlightToggle(PlayerToggleFlightEvent $ev) {
        if($ev->isFlying() and $ev->getPlayer()->getInventory()->getBoots() instanceof DiamondBoots) {
            if($ev->getPlayer()->getInventory()->getBoots()->getCustomName() != "Diamond Boots of Flight") {
                return;
            }
            #$this->getLogger()->debug("Named Item Found (Sneak Toggle)");
            $p = $ev->getPlayer();
            for($x = -1;$x <= 1; $x++) {
                for($z = -1;$z <= 1; $z++) {
                    $vec = new Vector3($p->getFloorX()+$x, $p->getFloorY()-1, $p->getFloorZ()+$z);
                    if($p->getLevel()->getBlockIdAt($p->getFloorX()+$x, $p->getFloorY()-1, $p->getFloorZ()+$z) == Block::INVISIBLE_BEDROCK) {
                        $p->getLevel()->setBlock($vec, Block::get(Block::AIR));
                        $this->getLogger()->debug("Set block at {$vec} to air");
                    }
                }
            }
        }
    }

    /**
     * @param PlayerQuitEvent $ev
     * @priority MONITOR
     */
    public function onLeave(PlayerQuitEvent $ev) {
        if($ev->getPlayer()->getInventory()->getBoots() instanceof DiamondBoots) {
            if($ev->getPlayer()->getInventory()->getBoots()->getCustomName() != "Diamond Boots of Flight") {
                return;
            }
            #$this->getLogger()->debug("Named Item Found (Player Quit)");
            $p = $ev->getPlayer();
            for($x = -1;$x <= 1; $x++) {
                for($z = -1;$z <= 1; $z++) {
                    $vec = new Vector3($p->getFloorX()+$x, $p->getFloorY()-1, $p->getFloorZ()+$z);
                    if($p->getLevel()->getBlockIdAt($p->getFloorX()+$x, $p->getFloorY()-1, $p->getFloorZ()+$z) == Block::INVISIBLE_BEDROCK) {
                        $p->getLevel()->setBlock($vec, Block::get(Block::AIR));
                        $this->getLogger()->debug("Set block at {$vec} to air");
                    }
                }
            }
        }
    }
}