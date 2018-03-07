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
use pocketmine\Server;

class Main extends PluginBase implements Listener {
	/** @var Main $insance */
	private static $insance;

	public function onEnable() {
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		self::$insance = $this;
	}

	/**
	 * @param EntityArmorChangeEvent $event
	 * @throws \ReflectionException
	 */
	public function onMask(EntityArmorChangeEvent $event) {
		$entity = $event->getEntity();
		if($entity instanceof Player) {
			$mask = $event->getNewItem();
			/** @noinspection PhpUnhandledExceptionInspection */
			$class = new \ReflectionClass(Effect::class);
			if($mask->getId() === Item::MOB_HEAD) {
				switch($mask->getDamage()) {
					case 0: //SKELETON
						$settings = $this->getConfig()->getNested("Skeleton Mask", []);
						foreach($settings as $setting => $amplifier) {
							if($setting === "Flight") {
								if($amplifier == true) {
									$entity->setAllowFlight(true);
								}else {
									$entity->setAllowFlight(false);
								}
								continue;
							}
							foreach($class->getConstants() as $name => $value) {
								if(strpos(strtolower($setting), str_replace("_", " ", strtolower($name))) !== false) {
									if($amplifier > 0) {
										$entity->addEffect(Effect::getEffect($value)->setDuration(INT32_MAX)->setAmplifier($amplifier));
									}else {
										$entity->removeEffect($value);
									}
									break;
								}
							}
						}
						break;
					case 2: //ZOMBIE
						$settings = $this->getConfig()->getNested("Zombie Mask", []);
						foreach($settings as $setting => $amplifier) {
							if($setting === "Flight") {
								if($amplifier == true) {
									$entity->setAllowFlight(true);
								}else {
									$entity->setAllowFlight(false);
								}
								continue;
							}
							foreach($class->getConstants() as $name => $value) {
								if(strpos(strtolower($setting), str_replace("_", " ", strtolower($name))) !== false) {
									if($amplifier > 0) {
										$entity->addEffect(Effect::getEffect($value)->setDuration(INT32_MAX)->setAmplifier($amplifier));
									}else {
										$entity->removeEffect($value);
									}
									break;
								}
							}
						}
						break;
					case 4: //CREEPER
						$settings = $this->getConfig()->getNested("Creeper Mask", []);
						foreach($settings as $setting => $amplifier) {
							if($setting === "Flight") {
								if($amplifier == true) {
									$entity->setAllowFlight(true);
								}else {
									$entity->setAllowFlight(false);
								}
								continue;
							}
							foreach($class->getConstants() as $name => $value) {
								if(strpos(strtolower($setting), str_replace("_", " ", strtolower($name))) !== false) {
									if($amplifier > 0) {
										$entity->addEffect(Effect::getEffect($value)->setDuration(INT32_MAX)->setAmplifier($amplifier));
									}else {
										$entity->removeEffect($value);
									}
									break;
								}
							}
						}
						break;
					case 5: //DRAGON
						$settings = $this->getConfig()->getNested("Dragon Mask", []);
						foreach($settings as $setting => $amplifier) {
							if($setting === "Flight") {
								if($amplifier == true) {
									$entity->setAllowFlight(true);
								}else {
									$entity->setAllowFlight(false);
								}
								continue;
							}
							foreach($class->getConstants() as $name => $value) {
								if(strpos(strtolower($setting), str_replace("_", " ", strtolower($name))) !== false) {
									if($amplifier > 0) {
										$entity->addEffect(Effect::getEffect($value)->setDuration(INT32_MAX)->setAmplifier($amplifier));
									}else {
										$entity->removeEffect($value);
									}
									break;
								}
							}
						}
						break;
					default:
						$settings = $this->getConfig()->getNested("No Mask", []);
						foreach($settings as $setting => $amplifier) {
							if($setting === "Flight") {
								if($amplifier == true) {
									$entity->setAllowFlight(true);
								}else {
									$entity->setAllowFlight(false);
								}
								continue;
							}
							foreach($class->getConstants() as $name => $value) {
								if(strpos(strtolower($setting), str_replace("_", " ", strtolower($name))) !== false) {
									if($amplifier > 0) {
										$entity->addEffect(Effect::getEffect($value)->setDuration(INT32_MAX)->setAmplifier($amplifier));
									}else {
										$entity->removeEffect($value);
									}
									break;
								}
							}
						}
						break;
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
				if($mask->getId() === Item::MOB_HEAD) {
					switch($mask->getDamage()) {
						case 0: //SKELETON
							$settings = $this->getConfig()->getNested("Skeleton Mask", []);
							if($settings["Poison Attacks"] > 0) {
								$damaged->addEffect(Effect::getEffect(Effect::POISON)->setDuration(INT32_MAX)->setAmplifier($settings["Poison Attacks"]));
							}
							break;
						case 2: //ZOMBIE
							$settings = $this->getConfig()->getNested("Zombie Mask", []);
							if($settings["Poison Attacks"] > 0) {
								$damaged->addEffect(Effect::getEffect(Effect::POISON)->setDuration(INT32_MAX)->setAmplifier($settings["Poison Attacks"]));
							}
							break;
						case 4: //CREEPER
							$settings = $this->getConfig()->getNested("Creeper Mask", []);
							if($settings["Poison Attacks"] > 0) {
								$damaged->addEffect(Effect::getEffect(Effect::POISON)->setDuration(INT32_MAX)->setAmplifier($settings["Poison Attacks"]));
							}
							break;
						case 5: //DRAGON
							$settings = $this->getConfig()->getNested("Dragon Mask", []);
							if($settings["Poison Attacks"] > 0) {
								$damaged->addEffect(Effect::getEffect(Effect::POISON)->setDuration(INT32_MAX)->setAmplifier($settings["Poison Attacks"]));
							}
							break;
						default:
							$settings = $this->getConfig()->getNested("No Mask", []);
							if($settings["Poison Attacks"] > 0) {
								$damaged->addEffect(Effect::getEffect(Effect::POISON)->setDuration(INT32_MAX)->setAmplifier($settings["Poison Attacks"]));
							}
							break;
					}
				}elseif($mask->getId() === Item::AIR) {
					$settings = $this->getConfig()->getNested("No Mask", []);
					if($settings["Poison Attacks"] > 0) {
						$damaged->addEffect(Effect::getEffect(Effect::POISON)->setDuration(INT32_MAX)->setAmplifier($settings["Poison Attacks"]));
					}
				}
			}
		}
	}

	public function onTap(PlayerInteractEvent $event) {
		if($event->getItem()->getId() === Item::BOOK) {
			$player = $event->getPlayer();
			$inventory = $player->getInventory();
			$rand = mt_rand(1, 100);
			$item = Item::get(Item::MOB_HEAD);
			if($rand <= 20) {
				$inventory->setItemInHand($item->setDamage(0));
			}elseif($rand > 20 and $rand <= 40) {
				$inventory->setItemInHand($item->setDamage(2));
			}elseif($rand > 40 and $rand <= 60) {
				$inventory->setItemInHand($item->setDamage(4));
			}elseif($rand > 60 and $rand <= 80) {
				$inventory->setItemInHand($item->setDamage(5));
			}elseif($rand > 80 and $rand <= 100) {
				$inventory->setItemInHand($item->setDamage(mt_rand(0, 6))); // TODO: make better
			}
			$inventory->sendHeldItem($inventory->getHolder()->getLevel()->getPlayers());
			$inventory->sendContents($inventory->getHolder());
		}
	}

	/**
	 * @param CommandSender $sender
	 * @param Command $command
	 * @param string $label
	 * @param array $args
	 * @return bool
	 */
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
		if($sender instanceof Player) {
			$config = $this->getConfig();
			/** @var FormAPI $formsAPI */
			$formsAPI = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
			$form = $formsAPI->createCustomForm(function(Player $player, $data) use ($config) {
				if(empty($data)) {
					return;
				}
				$settingsArray = array_chunk($data, 21);
				$endSettings = [];
				foreach($settingsArray as $key => $settings) {
					switch($key) {
						case 0:
							$mask = "Zombie Mask";
							break;
						case 1:
							$mask = "Skeleton Mask";
							break;
						case 2:
							$mask = "Creeper Mask";
							break;
						case 3:
							$mask = "Dragon Mask";
							break;
						default:
							$mask = "No mask";
							break;
					}
					$endSettings[$mask]["Speed"] = (int) $settings[1];
					$endSettings[$mask]["Slowness"] = (int) $settings[2];
					$endSettings[$mask]["Haste"] = (int) $settings[3];
					$endSettings[$mask]["Fatigue"] = (int) $settings[4];
					$endSettings[$mask]["Strength"] = (int) $settings[5];
					$endSettings[$mask]["Jump"] = (int) $settings[6];
					$endSettings[$mask]["Nausea"] = (int) $settings[7];
					$endSettings[$mask]["Regeneration"] = (int) $settings[8];
					$endSettings[$mask]["Resistance"] = (int) $settings[9];
					$endSettings[$mask]["Fire Resistance"] = (int) $settings[10];
					$endSettings[$mask]["Water Breathing"] = (int) $settings[11];
					$endSettings[$mask]["Invisibility"] = (int) $settings[12];
					$endSettings[$mask]["Blindness"] = (int) $settings[13];
					$endSettings[$mask]["Night Vision"] = (int) $settings[14];
					$endSettings[$mask]["Weakness"] = (int) $settings[15];
					$endSettings[$mask]["Poison"] = (int) $settings[16];
					$endSettings[$mask]["Wither"] = (int) $settings[17];
					$endSettings[$mask]["Health Boost"] = (int) $settings[18];
					$endSettings[$mask]["Flight"] = (bool) $settings[19];
					$endSettings[$mask]["Poison Attacks"] = (int) $settings[20];
				}
				$config->setAll($endSettings);
				$config->save();
			});
			$form->setTitle("Mask Settings");

			foreach($config->getAll() as $mask => $settings) {
				$form->addLabel($mask . " Settings");
				foreach($settings as $setting => $value) {
					if($setting === "Flight") {
						$form->addToggle($setting, (bool) $value);
					}else {
						$form->addSlider($setting, 0, 100, -1, $value);
					}
				}
			}
			$form->sendToPlayer($sender);
		}
		return true;
	}

	public static function liveUpdateEffects() {
		foreach(Server::getInstance()->getOnlinePlayers() as $player) {
			$player->removeAllEffects();
			$mask = $player->getArmorInventory()->getHelmet();
			/** @noinspection PhpUnhandledExceptionInspection */
			$class = new \ReflectionClass(Effect::class);
			if($mask->getId() === Item::MOB_HEAD) {
				switch($mask->getDamage()) {
					case 0: //SKELETON
						$settings = self::$insance->getConfig()->getNested("Skeleton Mask", []);
						foreach($settings as $setting => $amplifier) {
							if($setting === "Flight") {
								if($amplifier == true) {
									$player->setAllowFlight(true);
								}else {
									$player->setAllowFlight(false);
								}
								continue;
							}
							foreach($class->getConstants() as $name => $value) {
								if(strpos(strtolower($setting), str_replace("_", " ", strtolower($name))) !== false) {
									if($amplifier > 0) {
										$player->addEffect(Effect::getEffect($value)->setDuration(INT32_MAX)->setAmplifier($amplifier));
									}else {
										$player->removeEffect($value);
									}
									break;
								}
							}
						}
						break;
					case 2: //ZOMBIE
						$settings = self::$insance->getConfig()->getNested("Zombie Mask", []);
						foreach($settings as $setting => $amplifier) {
							if($setting === "Flight") {
								if($amplifier == true) {
									$player->setAllowFlight(true);
								}else {
									$player->setAllowFlight(false);
								}
								continue;
							}
							foreach($class->getConstants() as $name => $value) {
								if(strpos(strtolower($setting), str_replace("_", " ", strtolower($name))) !== false) {
									if($amplifier > 0) {
										$player->addEffect(Effect::getEffect($value)->setDuration(INT32_MAX)->setAmplifier($amplifier));
									}else {
										$player->removeEffect($value);
									}
									break;
								}
							}
						}
						break;
					case 4: //CREEPER
						$settings = self::$insance->getConfig()->getNested("Creeper Mask", []);
						foreach($settings as $setting => $amplifier) {
							if($setting === "Flight") {
								if($amplifier == true) {
									$player->setAllowFlight(true);
								}else {
									$player->setAllowFlight(false);
								}
								continue;
							}
							foreach($class->getConstants() as $name => $value) {
								if(strpos(strtolower($setting), str_replace("_", " ", strtolower($name))) !== false) {
									if($amplifier > 0) {
										$player->addEffect(Effect::getEffect($value)->setDuration(INT32_MAX)->setAmplifier($amplifier));
									}else {
										$player->removeEffect($value);
									}
									break;
								}
							}
						}
						break;
					case 5: //DRAGON
						$settings = self::$insance->getConfig()->getNested("Dragon Mask", []);
						foreach($settings as $setting => $amplifier) {
							if($setting === "Flight") {
								if($amplifier == true) {
									$player->setAllowFlight(true);
								}else {
									$player->setAllowFlight(false);
								}
								continue;
							}
							foreach($class->getConstants() as $name => $value) {
								if(strpos(strtolower($setting), str_replace("_", " ", strtolower($name))) !== false) {
									if($amplifier > 0) {
										$player->addEffect(Effect::getEffect($value)->setDuration(INT32_MAX)->setAmplifier($amplifier));
									}else {
										$player->removeEffect($value);
									}
									break;
								}
							}
						}
						break;
					default:
						$settings = self::$insance->getConfig()->getNested("No Mask", []);
						foreach($settings as $setting => $amplifier) {
							if($setting === "Flight") {
								if($amplifier == true) {
									$player->setAllowFlight(true);
								}else {
									$player->setAllowFlight(false);
								}
								continue;
							}
							foreach($class->getConstants() as $name => $value) {
								if(strpos(strtolower($setting), str_replace("_", " ", strtolower($name))) !== false) {
									if($amplifier > 0) {
										$player->addEffect(Effect::getEffect($value)->setDuration(INT32_MAX)->setAmplifier($amplifier));
									}else {
										$player->removeEffect($value);
									}
									break;
								}
							}
						}
						break;
				}
			}
		}
	}
}