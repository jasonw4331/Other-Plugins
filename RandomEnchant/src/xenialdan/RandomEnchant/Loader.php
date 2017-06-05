<?php

namespace xenialdan\RandomEnchant;

use pocketmine\block\Block;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Tool;
use pocketmine\plugin\PluginBase;

class Loader extends PluginBase implements Listener
{
    public function onEnable()
    {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->notice("Enabled!");
    }

    /**
     * @param PlayerInteractEvent $event
     * @return bool
     */
    public function onClickTable(PlayerInteractEvent $event) {
        if (($player = $event->getPlayer())->hasPermission('randomEnchant') and $event->getBlock()->getId() === Block::ENCHANT_TABLE) {
            $item = $player->getInventory()->getItemInHand();
            if ($item instanceof Tool or $item instanceof Armor) {
                for($i = mt_rand(1,4); $i > 0; $i--) {
                    $enchantment = Enchantment::getEnchantment(mt_rand(0, 24));
                    if($enchantment->getId() === Enchantment::TYPE_INVALID)
                        continue;
                    $item->addEnchantment($enchantment);
                }
                $player->getInventory()->setItemInHand($item);
                $event->setCancelled();
            }
        }
        return true;
    }
}