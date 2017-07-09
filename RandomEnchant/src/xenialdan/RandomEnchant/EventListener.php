<?php

namespace xenialdan\RandomEnchant;

use pocketmine\block\Block;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\plugin\Plugin;

class EventListener implements Listener {

	/**
	 * EventListener constructor.
	 * @param Plugin $plugin
	 */
	public function __construct(Plugin $plugin) {
		$this->owner = $plugin;
	}

	/**
	 * @param PlayerInteractEvent $event
	 * @return bool
	 */
	public function onClickTable(PlayerInteractEvent $event) {
		if (($player = $event->getPlayer())->hasPermission('randomenchant') && $event->getBlock()->getId() === Block::ENCHANT_TABLE) {
			$item = $player->getInventory()->getItemInHand();
			if ($item instanceof Tool || $item instanceof Armor) {
				while (!$item->hasEnchantments()) {
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