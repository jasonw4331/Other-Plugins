<?php

#Plugin Traducido al Español por @visito66

namespace FRISCOWZ;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Effect;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener {
	private $pvp;
	private $prefix = TextFormat::DARK_GRAY . "[" . TextFormat::RED . "§bUHC" . TextFormat::DARK_GRAY . "] " . TextFormat::GRAY;
	private $globalmute = false;
	private $spam = [];

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new class($this) extends PluginTask{
			/**
			 * @param int $currentTick
			 */
			public function onRun(int $currentTick){
				$this->getOwner()->cord();
			}
		}, 1);
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new class($this) extends PluginTask{
			/**
			 * @param int $currentTick
			 */
			public function onRun(int $currentTick){
				$this->getOwner()->alive();
			}
		}, 20 * 1160);
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new class($this) extends PluginTask{
			/**
			 * @param int $currentTick
			 */
			public function onRun(int $currentTick){
				$this->getOwner()->msg();
			}
		}, 20 * 1120);
		$this->pvp = false;
	}

	public function cord(){
		foreach($this->getServer()->getOnlinePlayers() as $player){
			if($player->getInventory()->getItemInHand()->getId() === "345"){
				$x = $player->getFloorX();
				$y = $player->getFloorY();
				$z = $player->getFloorZ();
				$player->sendPopup($this->prefix . "§bX: §7$x §bY: §7$y §bZ: §7$z");
			}

		}
	}

	public function alive(){
		foreach($this->getServer()->getOnlinePlayers() as $p){
			$p->sendMessage($this->prefix . "§7 Siguenos en Nuestro Twitte :D");
		}
	}

	public function msg(){
		foreach($this->getServer()->getOnlinePlayers() as $p){
			$p->sendMessage($this->prefix . "diviertete En Nuestros Eventos :)");
		}
	}

	public function onBreak(BlockBreakEvent $event){
		if($event->getBlock()->getId() === 15){
			$drops = array(Item::get(265, 0, 2));
			$event->setDrops($drops);
		}
		if($event->getBlock()->getId() === 14){
			$drops = array(Item::get(266, 0, 2));
			$event->setDrops($drops);
		}
		if($event->getBlock()->getId() === 18){
			$drops = array(Item::get(260, 0, 1));
			$event->setDrops($drops);
		}
		if($event->getBlock()->getId() === 161){
			$drops = array(Item::get(260, 0, 1));
			$event->setDrops($drops);
		}
		if($event->getBlock()->getId() === 17){
			$drops = array(Item::get(5, 0, 5));
			$event->setDrops($drops);
		}
		if($event->getBlock()->getId() === 13){
			$drops = array(Item::get(262, 0, 15));
			$event->setDrops($drops);
		}
		if($event->getBlock()->getId() === 16){
			$drops = array(Item::get(438, 6, 1));
			$event->setDrops($drops);
		}
		if($event->getBlock()->getId() === 49){
			$drops = array(Item::get(116, 0, 1));
			$event->setDrops($drops);
		}
		if($event->getBlock()->getId() === 74){
			$drops = array(Item::get(438, 13, 1));
			$event->setDrops($drops);
		}
		if($event->getBlock()->getId() === 83){
			$drops = array(Item::get(438, 15, 1));
			$event->setDrops($drops);
		}
		if($event->getBlock()->getId() === 2){
			$drops = array(Item::get(364, 0, 2));
			$event->setDrops($drops);
		}
		if($event->getBlock()->getId() === 3){
			$drops = array(Item::get(364, 0, 2));
			$event->setDrops($drops);
		}
	}

	public function onJoin(PlayerJoinEvent $event){
		$event->getPlayer()->sendTip("§8[§bCréditos @Visito66§8]\n\n\n\n\n\n\n\n\n");
		$this->getServer()->broadcastMessage("§7[§b+§7]  " . $event->getPlayer()->getName() . " ");
		$event->setJoinMessage("");
	}

	public function onQuit(PlayerQuitEvent $event){
		$this->getServer()->broadcastMessage("§7[§4-§7]  " . $event->getPlayer()->getName() . " ");
		$event->setQuitMessage("");
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $commandLabel, array $args) : bool{
		if($sender instanceof Player and $sender->isOp()){
			switch($args[0]){
				case "reset":
					foreach($this->getServer()->getOnlinePlayers() as $p){
						$p->setMaxHealth(20);
						$p->setHealth(20);
						$p->setFood(20);
						$p->setGamemode(0);
						$p->getInventory()->clearAll();
						$p->removeAllEffects();
					}
					$this->getServer()->broadcastMessage("§7[§bUHC§7] §7El UHC ha sido Reseteado !");

					return true;
					break;
				case "help":
					$sender->sendMessage("§7<< §bUHC Edit By @visito66 §7>> ");
					$sender->sendMessage("§b/uhc reset: §7Restablece el UHC!");
					$sender->sendMessage("§b/uhc meetup: §7Da Kit Meetup");
					$sender->sendMessage("§b/uhc start: §7 Enpieza El UHC");
					$sender->sendMessage("§b/uhc tpall: §7Tpall Hacia ti");
					$sender->sendMessage("§b/uhc meetop: §7Da kit meetup Encantado!!");
					$sender->sendMessage("§b/uhc pvp <on/off>: §7Habilita y deshabilita  PvP!");
					$sender->sendMessage("§b/uhc scenario <hero>: §7Selecciona un escenario!");
					$sender->sendMessage("§b/uhc globalmute: §7Silencia a todos los no ops!");

					return true;
					break;
				case "pvp":
					if($args[1] === "on"){
						$this->pvp = true;
						$this->getServer()->broadcastMessage("§7[§bUHC§7] §7PvP activado!");
					}
					if($args[1] === "off"){
						$this->pvp = false;
						$this->getServer()->broadcastMessage("§7[§bUHC§7] §7PvP desactivado");
					}

					return true;
					break;
				case "scenario":
					if($args[1] === "hero"){
						$this->getServer()->broadcastMessage("§7[§bUHC§7] §7El escenario es §b...!");
						$this->getServer()->broadcastMessage("§7[§bUHC§7] §7Se le ha dado un efecto al azar!");
						foreach($this->getServer()->getOnlinePlayers() as $p){
							$kit = rand(1, 2);
							$speed = Effect::getEffect($kit);
							$speed->setAmplifier(1);
							$speed->setVisible(true);
							$speed->setDuration(1000000);
							$p->addEffect($speed);
						}
					}

					return true;
					break;
				case "meetup":
					foreach($this->getServer()->getOnlinePlayers() as $p){
						$p->setMaxHealth(20);
						$p->setHealth(20);
						$p->setFood(20);
						$p->setGamemode(0);
						$p->getInventory()->clearAll();
						$p->getInventory()->addItem(Item::get(276, 0, 1));
						$p->getInventory()->addItem(Item::get(ITEM::GOLDEN_APPLE, 0, 6));
						$p->getInventory()->addItem(Item::get(ITEM::GOLDEN_APPLE, 0, 9));
						$p->getInventory()->addItem(Item::get(364, 0, 64));
						$p->getInventory()->addItem(Item::get(278, 0, 1));
						$p->getInventory()->addItem(Item::get(279, 0, 1));
						$p->getInventory()->addItem(Item::get(1, 0, 64));
						$p->getInventory()->addItem(Item::get(5, 0, 64));
						$p->getInventory()->setHelmet(Item::get(310, 0, 1));
						$p->getInventory()->setChestplate(Item::get(311, 0, 1));
						$p->getInventory()->setLeggings(Item::get(312, 0, 1));
						$p->getInventory()->setBoots(Item::get(313, 0, 1));
						$p->getInventory()->sendArmorContents($p);
					}
					$this->getServer()->broadcastMessage("§7[§bUHC§7-§3MEETUP§7] §7Se le ha dado el kit §bMEETUP§7-§3Normal!");

					return true;
					break;
				case "start":
					foreach($this->getServer()->getOnlinePlayers() as $p){
						$p->setMaxHealth(20);
						$p->setHealth(20);
						$p->setFood(20);
						$p->setGamemode(0);
						$p->getInventory()->clearAll();
						$p->getInventory()->addItem(Item::get(257, 0, 1));
						$p->getInventory()->addItem(Item::get(364, 0, 64));
						$p->getInventory()->addItem(Item::get(50, 0, 16));
						$p->getInventory()->addItem(Item::get(345, 0, 1));
						$p->getInventory()->setBoots(Item::get(301, 0, 1));
						$p->getInventory()->sendArmorContents($p);
					}
					$this->getServer()->broadcastMessage("§7[§bUHC§7] §7¡UHC que comienza en 10 segundos! §cNO Se Valla.");
					sleep(10); //TODO WHY THE HECK IS THIS HERE
					$this->getServer()->broadcastMessage("§7[§bUHC§7] §7¡El UHC ha comenzado!");

					return true;
					break;
				case "meetop":
					foreach($this->getServer()->getOnlinePlayers() as $p){
						$p->setMaxHealth(20);
						$p->setHealth(20);
						$p->setFood(20);
						$p->setGamemode(0);
						$p->getInventory()->clearAll();
						$p->getPlayer()->removeAllEffects();
						$casco = Item::get(Item::DIAMOND_HELMET, 0, 1);
						$protection = Enchantment::getEnchantment(0);
						$protection->setLevel(1);
						$casco->addEnchantment($protection);
						$p->getInventory()->setHelmet($casco);
						$peto = Item::get(Item::DIAMOND_CHESTPLATE, 0, 1);
						$protection = Enchantment::getEnchantment(0);
						$protection->setLevel(2);
						$peto->addEnchantment($protection);
						$p->getInventory()->setChestplate($peto);
						$pantalon = Item::get(Item::DIAMOND_LEGGINGS, 0, 1);
						$protection = Enchantment::getEnchantment(0);
						$protection->setLevel(2);
						$pantalon->addEnchantment($protection);
						$p->getInventory()->setLeggings($pantalon);
						$botas = Item::get(Item::DIAMOND_BOOTS, 0, 1);
						$protection = Enchantment::getEnchantment(0);
						$protection->setLevel(1);
						$botas->addEnchantment($protection);
						$p->getInventory()->setBoots($botas);
						$espada = Item::get(Item::DIAMOND_SWORD, 0, 1);
						$sharpness = Enchantment::getEnchantment(9);
						$sharpness->setLevel(2);
						$espada->addEnchantment($sharpness);
						$p->getInventory()->addItem($espada);
						$pico = Item::get(Item::DIAMOND_PICKAXE, 0, 1);
						$efficiency = Enchantment::getEnchantment(15);
						$efficiency->setLevel(3);
						$pico->addEnchantment($efficiency);
						$p->getInventory()->addItem($pico);
						$hacha = Item::get(Item::DIAMOND_AXE, 0, 1);
						$efficiency = Enchantment::getEnchantment(15);
						$efficiency->setLevel(3);
						$hacha->addEnchantment($efficiency);
						$p->getInventory()->addItem($hacha);
						$p->getInventory()->addItem(Item::get(322, 0, 15));
						$p->getInventory()->addItem(Item::get(364, 0, 64));
						$p->getInventory()->addItem(Item::get(1, 0, 64));
						$p->getInventory()->addItem(Item::get(5, 0, 64));
						$p->getInventory()->addItem(Item::get(30, 0, 64));
						$this->getServer()->broadcastMessage("§7[§bUHC§7-§3MEETUP§7] §7Se le ha dado el kit §bMEETUP§7-§3OP!");
					}

					return true;
					break;
				///GlobalMute///
				case "globalmute":
					if($sender->hasPermission("UHC.host")){
						if($this->globalmute === false){
							$this->getServer()->broadcastMessage($this->prefix . TextFormat::GRAY . "Global Mute Ha sido activado!");
							$this->globalmute = true;

							return true;
						}else{
							$this->getServer()->broadcastMessage($this->prefix . TextFormat::GRAY . "Global Mute ha sido desactivado.");
							$this->globalmute = false;

							return true;
						}
					}
					break;
				case "tpall":
					foreach($this->getServer()->getOnlinePlayers() as $p){
						$p->teleport(new Vector3($sender->x, $sender->y, $sender->z));
						$this->getServer()->broadcastMessage("§7[§bUHC§7]  §7Teleportando...");
					}

					return true;
					break;
			}
		}else{
			$sender->sendMessage("§7[§bUHC§7] §cNo tienes permitido hacer esto.");
		}

		return true;
	}

	public function onPlayerDeath(PlayerDeathEvent $event){
		$player = $event->getPlayer();
		$player->setGamemode(3);
		$cause = $player->getLastDamageCause();
		if($cause instanceof EntityDamageByEntityEvent and $cause->getDamager() instanceof Player){
			/** @var Player $killer */
			$killer = $cause->getDamager();
			$killer->setHealth($killer->getHealth() + 10);
			$killer->sendMessage("§7[§bUHC§7] ha resivido  §c10 <3 §7de vida");
			$event->setDeathMessage($this->prefix . TextFormat::RED . $player->getName() . " §7was killed by§3 " . $killer->getName() . ".");
		}else{
			$cause = $player->getLastDamageCause()->getCause();
			if($cause === EntityDamageEvent::CAUSE_SUFFOCATION){
				$event->setDeathMessage($this->prefix . TextFormat::RED . $player->getName() . " §7Se ha Sofocado.");
			}elseif($cause === EntityDamageEvent::CAUSE_DROWNING){
				$event->setDeathMessage($this->prefix . TextFormat::RED . $player->getName() . " §7Se ha Ahogado.");
			}elseif($cause === EntityDamageEvent::CAUSE_FALL){
				$event->setDeathMessage($this->prefix . TextFormat::RED . $player->getName() . " §7 Ha Muerto de Caida .");
			}elseif($cause === EntityDamageEvent::CAUSE_FIRE){
				$event->setDeathMessage($this->prefix . TextFormat::RED . $player->getName() . " §7 Se haQuemado.");
			}elseif($cause === EntityDamageEvent::CAUSE_FIRE_TICK){
				$event->setDeathMessage($this->prefix . TextFormat::RED . $player->getName() . " burned.");
			}elseif($cause === EntityDamageEvent::CAUSE_LAVA){
				$event->setDeathMessage($this->prefix . TextFormat::RED . $player->getName() . " §7 Intento nadar en la Lava.");
			}elseif($cause === EntityDamageEvent::CAUSE_BLOCK_EXPLOSION){
				$event->setDeathMessage($this->prefix . TextFormat::RED . $player->getName() . " §7 Ha explotado.");
			}else{
				$event->setDeathMessage($this->prefix . TextFormat::RED . $player->getName() . " Ha Muerto.");
			}
		}
	}

	////Mute and Grade////
	public function onChat(PlayerChatEvent $event){
		$player = $event->getPlayer();
		if($this->globalmute){
			if(!$event->getPlayer()->hasPermission("UHC.host")){
				$event->setCancelled();
				$player->sendMessage($this->prefix . "§7No se puede chatear mientras Global Mute se ha habilitado!");
			}
		}else{
			if(!$player->hasPermission("UHC.host")){
				if(!isset($this->spam[$player->getName()])){
					$lastTime = 0;
				}else{
					$lastTime = $this->spam[$player->getName()];
				}
				if(time() - $lastTime > 5){
					$this->spam[$player->getName()] = time();
				}else{
					$event->setCancelled(true);
					$player->sendMessage($this->prefix . TextFormat::GRAY . "§7 No Spam");
				}
			}
			if($player->hasPermission("UHC.group.Owner")){
				$event->setFormat(TextFormat::WHITE . "[" . TextFormat::DARK_RED . "§bOwner" . TextFormat::WHITE . "] " . TextFormat::LIGHT_PURPLE . $player->getName() . ": " . TextFormat::GRAY . $event->getMessage());
			}elseif($player->hasPermission("UHC.group.Host")){
				$event->setFormat(TextFormat::WHITE . "[" . TextFormat::DARK_RED . "Host" . TextFormat::WHITE . "] " . TextFormat::LIGHT_PURPLE . $player->getName() . ": " . TextFormat::GRAY . $event->getMessage());
			}elseif($player->hasPermission("UHC.group.Owner")){
				$event->setFormat(TextFormat::WHITE . "[" . TextFormat::DARK_RED . "Owner" . TextFormat::WHITE . "] " . TextFormat::LIGHT_PURPLE . $player->getName() . ": " . TextFormat::GRAY . $event->getMessage());
			}elseif($player->hasPermission("UHC.group.Admin")){
				$event->setFormat(TextFormat::WHITE . "[" . TextFormat::RED . "Admin" . TextFormat::WHITE . "] " . TextFormat::LIGHT_PURPLE . $player->getName() . ": " . TextFormat::GRAY . $event->getMessage());
			}elseif($player->hasPermission("UHC.group.Dev")){
				$event->setFormat(TextFormat::WHITE . "[" . TextFormat::RED . "Dev" . TextFormat::WHITE . "] " . TextFormat::LIGHT_PURPLE . $player->getName() . ": " . TextFormat::GRAY . $event->getMessage());
			}elseif($player->hasPermission("UHC.group.Mod")){
				$event->setFormat(TextFormat::WHITE . "[" . TextFormat::DARK_AQUA . "Mod" . TextFormat::WHITE . "] " . TextFormat::LIGHT_PURPLE . $player->getName() . ": " . TextFormat::GRAY . $event->getMessage());
			}elseif($player->hasPermission("UHC.group.Trainee")){
				$event->setFormat(TextFormat::WHITE . "[" . TextFormat::GREEN . "Trainee" . TextFormat::WHITE . "] " . TextFormat::LIGHT_PURPLE . $player->getName() . ": " . TextFormat::GRAY . $event->getMessage());
			}elseif($player->hasPermission("UHC.group.Helper")){
				$event->setFormat(TextFormat::WHITE . "[" . TextFormat::GREEN . "Helper" . TextFormat::WHITE . "] " . TextFormat::LIGHT_PURPLE . $player->getName() . ": " . TextFormat::GRAY . $event->getMessage());
			}elseif($player->hasPermission("UHC.group.YouTube")){
				$event->setFormat(TextFormat::WHITE . "[" . TextFormat::RED . "YouTube" . TextFormat::WHITE . "] " . TextFormat::LIGHT_PURPLE . $player->getName() . ": " . TextFormat::GRAY . $event->getMessage());
			}else{
				$event->setFormat(TextFormat::WHITE . $player->getName() . ": " . TextFormat::GRAY . strtolower($event->getMessage()));
			}
		}
	}
}