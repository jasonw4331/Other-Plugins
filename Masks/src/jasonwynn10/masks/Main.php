<?php
declare(strict_types=1);
namespace jasonwynn10\masks;

use jojoe77777\FormAPI\FormAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Effect;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {
	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	/**
	 * @param EntityArmorChangeEvent $event
	 */
	public function onMask(EntityArmorChangeEvent $event) {
		$entity = $event->getEntity();
		if($entity instanceof Player) {
			$mask = $event->getNewItem();
			if($mask->getId() === Item::MOB_HEAD) {
				switch($mask->getDamage()) {
					case 0: //SKELETON
						$entity->addEffect(Effect::getEffect(Effect::HASTE)->setAmplifier(5)->setDuration(INT32_MAX));
						$entity->addEffect(Effect::getEffect(Effect::STRENGTH)->setAmplifier(5)->setDuration(INT32_MAX));
						$entity->addEffect(Effect::getEffect(Effect::SPEED)->setAmplifier(4)->setDuration(INT32_MAX));
						$entity->setAllowFlight(true);
					break;
					case 2: //ZOMBIE
						$entity->addEffect(Effect::getEffect(Effect::HASTE)->setAmplifier(5)->setDuration(INT32_MAX));
						$entity->addEffect(Effect::getEffect(Effect::STRENGTH)->setAmplifier(5)->setDuration(INT32_MAX));
						$entity->addEffect(Effect::getEffect(Effect::SPEED)->setAmplifier(3)->setDuration(INT32_MAX));
						//TODO: Give poison to the other player level 5 if possible
					break;
					case 4: //CREEPER
						$entity->addEffect(Effect::getEffect(Effect::HASTE)->setAmplifier(5)->setDuration(INT32_MAX));
						$entity->addEffect(Effect::getEffect(Effect::STRENGTH)->setAmplifier(5)->setDuration(INT32_MAX));
						$entity->addEffect(Effect::getEffect(Effect::SPEED)->setAmplifier(3)->setDuration(INT32_MAX));
						$entity->addEffect(Effect::getEffect(Effect::REGENERATION)->setAmplifier(5)->setDuration(INT32_MAX));
						$entity->setAllowFlight(true);
						//TODO: Give poison to the Other player level 5 of possible
					break;
					case 5: //DRAGON
						$entity->addEffect(Effect::getEffect(Effect::HASTE)->setAmplifier(5)->setDuration(INT32_MAX));
						$entity->addEffect(Effect::getEffect(Effect::STRENGTH)->setAmplifier(5)->setDuration(INT32_MAX));
						$entity->addEffect(Effect::getEffect(Effect::SPEED)->setAmplifier(2)->setDuration(INT32_MAX));
						$entity->setAllowFlight(true);
					break;
					default: // OTHER HEADS
						$entity->addEffect(Effect::getEffect(Effect::HASTE)->setAmplifier(5)->setDuration(INT32_MAX));
						$entity->addEffect(Effect::getEffect(Effect::STRENGTH)->setAmplifier(3)->setDuration(INT32_MAX));
						$entity->addEffect(Effect::getEffect(Effect::SPEED)->setAmplifier(1)->setDuration(INT32_MAX));
				}
			}elseif($event->getOldItem()->getId() === Item::MOB_HEAD) {
				$entity->removeAllEffects();
				$entity->setAllowFlight(false);
			}
		}
	}

	/**
	 * @param EntityDamageEvent $event
	 */
	public function onDamage(EntityDamageEvent $event) {
		if($event instanceof EntityDamageByEntityEvent) {
			$damager = $event->getDamager();
			$damaged = $event->getEntity();
			if($damager instanceof Player and $damaged instanceof Living) {
				$mask = $damager->getArmorInventory()->getHelmet();
				if($mask->getId() === Item::MOB_HEAD and ($mask->getDamage() === 2 or $mask->getDamage() === 4)) {
					$damaged->addEffect(Effect::getEffect(Effect::POISON)->setAmplifier(5)->setDuration(20*5));
				}
			}
		}
	}

	public function onTap(PlayerInteractEvent $event) {
		if($event->getAction() === $event::LEFT_CLICK_BLOCK and $event->getItem()->getId() === Item::BOOK) {
			$player = $event->getPlayer();
			$inventory = $player->getInventory();
			$rand = mt_rand(1, 100);
			if($rand) {
				//
			}else{
				//
			}
		}
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
		if($sender instanceof Player) {
			/** @var FormAPI $formsAPI */
			$formsAPI = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
			$form = $formsAPI->createCustomForm(function(Player $player, $data) {
				//
			});
			$form->setTitle("Mask Settings");
			$form->addLabel("Zombie Mask Settings");

			$this->getConfig()->getNested("");

			$class = new \ReflectionClass(Effect::class);
			foreach($class->getConstants() as $name => $id) {
				$form->addToggle(ucwords($name)." Effect");
			}
		}
	}
}