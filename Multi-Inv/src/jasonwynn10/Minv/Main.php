<?php
namespace jasonwynn10\Minv;

use pocketmine\entity\Human;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase implements Listener {
	public function onEnable() {
		$this->saveDefaultConfig();
		$this->getLogger()->notice(TF::GREEN."Enabed!");
	}

	/**
	 * @param string $itemString
	 *
	 * @return Item
	 */
	private function parseItem(string $itemString) {
		$arr = explode(':',$itemString);
		if(isset($arr[3])) {
			$tag = hex2bin($arr[3]);
			if(!$this->isPhar())
				safe_var_dump($tag);
		}else{
			$tag = "";
		}
		return Item::get($arr[0], $arr[1], $arr[2], $tag);
	}

	/**
	 * @ignoreCancelled false
	 * @priority HIGH
	 *
	 * @param EntityLevelChangeEvent $ev
	 */
	public function onLevelChange(EntityLevelChangeEvent $ev) {
		if($ev->isCancelled()) {
			return;
		}
		/** @var Human $entity */
		if(($entity = $ev->getEntity()) instanceof Human) {
			$items = [];
			foreach($entity->getInventory()->getContents() as $i => $item) {
				$items[$i] = "{$item->getId()}:{$item->getDamage()}:{$item->getCount()}".($item->hasCompoundTag() ? ":".bin2hex($item->getCompoundTag()) : "");
			}
			$this->getConfig()->set($entity->getName(),[$ev->getOrigin()->getName() => $items]);
			/** @var string[] $items */
			$items = $this->getConfig()->getNested($entity->getName().'.'.$ev->getOrigin()->getName(),[]);
			$playerInvContents = [];
			foreach($items as $s => $itemString) {
				$playerInvContents[$s] = $this->parseItem($itemString);
			}
			$entity->getInventory()->setContents($playerInvContents);
		}
	}
}