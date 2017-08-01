<?php
namespace Ad5001\Elytra;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\item\Item;

use Ad5001\Elytra\tasks\AdminGotoTask;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener {

	protected $ops = [];

	public function onEnable() {
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new AdminGotoTask($this), 10);
		Item::$list[444] = Elytra::class;
		Item::addCreativeItem(new Elytra());
	}

	/**
	 * @priority LOW
	 *
	 * @param EntityDamageEvent $event
	 */
	public function onEntityDamage(EntityDamageEvent $event) {
		$damaged = $event->getEntity();
		if($damaged instanceof Player) {
			if($event->getCause() == 4 and $damaged->getInventory()->getChestplate()->getId() == 444) {
				$event->setCancelled();
			}
		}
	}

	/**
	 * @priority LOW
	 *
	 * @param PlayerKickEvent $event
	 */
	public function onPlayerKick(PlayerKickEvent $event) {
		if(strpos(strtolower($event->getReason()), "flying") !== false and $event->getPlayer()->getInventory()->getChestplate()->getId() == 444) {
			$event->setCancelled();
		}
	}

	/**
	 * @priority LOW
	 *
	 * @param PlayerMoveEvent $event
	 */
	public function onPlayerMove(PlayerMoveEvent $event) {
		$player = $event->getPlayer();
		   if($player->getInventory()->getChestplate()->getId() == 444) {
			   $flyingUp = false;
			   // TODO: Show current player in elytra mode
			   // TODO: change Bounding Box of player depending on their angle of flight
			   for($i = 2; $i > 0; $i--) {
				   if($player->getLevel()->getBlock(new Vector3(round($player->x), round($player->y) - $i, round($player->z)))->getId() !== 0) {
					   $flyingUp = true;
				   }
			   }
			   if(isset($this->ops[$player->getName()]) and $flyingUp) {
				   $player->setMotion(new Vector3($player->getMotion()->x, 3, $player->getMotion()->z));
			   }
			   $flyingUp = false;
			   for($i = 4; $i > 0; $i--) {
				   $id = $player->getLevel()->getBlock(new Vector3 (round($player->x), round($player->y) - $i, round($player->z)))->getId();
				   if(in_array($id, $this->getConfig()->get("bouncable_blocks",[]))) {
					   $flyingUp = true;
				   }
			   }
			   if($flyingUp) {
				   $player->setMotion(new Vector3($player->getMotion()->x, 3, $player->getMotion()->z));
				   $player->setDataFlag(Player::DATA_FLAGS, Player::DATA_FLAG_GLIDING, true);
			   }
		   }
	}

	public function onDatapacket(DataPacketReceiveEvent $ev) {
		$packet = $ev->getPacket();
		if($packet instanceof PlayerActionPacket) {
			if($packet->action === PlayerActionPacket::ACTION_START_GLIDE) {
				$ev->getPlayer()->setDataFlag(Player::DATA_FLAGS, Player::DATA_FLAG_GLIDING, true, Player::DATA_TYPE_BYTE);
			}elseif($packet->action === PlayerActionPacket::ACTION_STOP_GLIDE) {
				$ev->getPlayer()->setDataFlag(Player::DATA_FLAGS, Player::DATA_FLAG_GLIDING, false, Player::DATA_TYPE_BYTE);
			}
		}
	}

	/**
	 * @param CommandSender $sender
	 * @param Command $cmd
	 * @param string $label
	 * @param array $args
	 * @return bool
	 */
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
		switch($cmd->getName()) {
			case "opelytra":
				if($sender instanceof Player) {
					if(isset($this->ops[$sender->getName()])) {
						unset($this->ops[$sender->getName()]);
						$sender->sendMessage(TextFormat::GREEN."You are back to the original elytra!");
					} else {
						$this->ops[$sender->getName()] = true;
						$sender->sendMessage(TextFormat::GREEN."You are now in the admin elytra mode! Go try out your powers!");
					}
				}
			break;
			case "boost":
				if($sender instanceof Player and $sender->getInventory()->getChestplate()->getId() == 444) {
					if(!isset($args[0])) $args[0] = 2;
					$sender->setMotion(new Vector3($sender->getMotion()->x, $args[0], $sender->getMotion()->z));
				}
			break;
		}
		return false;
	}
}