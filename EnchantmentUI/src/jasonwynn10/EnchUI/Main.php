<?php
declare(strict_types=1);
namespace jasonwynn10\EnchUI;

use jojoe77777\FormAPI\FormAPI;
use onebone\economyapi\EconomyAPI;
use PiggyCustomEnchants\CustomEnchants\CustomEnchants;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener {
	/** @var array $dataStore */
	private static $dataStore = [];

	/**
	 * @param CommandSender $sender
	 * @param Command $command
	 * @param string $label
	 * @param array $args
	 *
	 * @return bool
	 * @throws \ReflectionException
	 */
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
		if($sender instanceof Player) {
			self::createPlayerDataStore($sender);

			/** @noinspection PhpUnhandledExceptionInspection */
			$class = new \ReflectionClass(CustomEnchants::class);
			/** @var Enchantment[] $enchantments */
			$enchantments = $class->getStaticProperties()['enchantments'];
			$enchantments = array_unique(array_filter($enchantments), SORT_REGULAR);

			/** @var FormAPI $formsAPI */
			$formsAPI = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
			$form = $formsAPI->createSimpleForm(function(Player $player, $data) use ($enchantments) {
				$i = 0;
				foreach($enchantments as $enchantment) {
					if($i === $data) {
						$dataStore = Main::getFromDataStore($player);
						$dataStore[] = $enchantment->getId();
						Main::setToDataStore($player, $dataStore);
						break;
					}
					$i++;
				}

				/** @var FormAPI $formsAPI */
				$formsAPI = Server::getInstance()->getPluginManager()->getPlugin("FormAPI");
				$form = $formsAPI->createCustomForm(function(Player $player, $data) {

					$dataStore = Main::getFromDataStore($player);
					$dataStore[] = (int) $data[0];
					$dataStore[] = (int) (((int) $data[0]) * 5000000);
					Main::setToDataStore($player, $dataStore);
					/** @var FormAPI $formsAPI */
					$formsAPI = Server::getInstance()->getPluginManager()->getPlugin("FormAPI");

					$economy = EconomyAPI::getInstance();
					$money = $economy->myMoney($player);
					if($money - $dataStore[2] < 0) {
						$form = $formsAPI->createCustomForm();
						$form->setTitle("Enchantment Shop");
						$form->addLabel("You don't have enough money to buy that!");
					}else{
						$form = $formsAPI->createModalForm(function(Player $player, $data) {
							if($data) {
								$dataStore = Main::getFromDataStore($player);
								$enchantment = CustomEnchants::getEnchantment($dataStore[0]);
								if($enchantment !== null) {
									$inventory = $player->getInventory();
									$item = $inventory->getItemInHand();
									$item->addEnchantment(new EnchantmentInstance($enchantment, $dataStore[1]));
									$inventory->setItemInHand($item);
									$inventory->sendHeldItem($inventory->getHolder()->getLevel()->getPlayers());
									$inventory->sendContents($inventory->getHolder());
									$economy = EconomyAPI::getInstance();
									$economy->reduceMoney($player, $dataStore[2], false, "EnchantmentShop");
									$player->sendMessage(TextFormat::GREEN."Enchantment Purchased!");
								}else{
									$player->sendMessage(TextFormat::RED."There was an error! That enchantment doesn't exist!");
								}
							}
						});
						$form->setTitle("Enchantment Shop");
						$form->setContent("Do you accept your charge of " . $economy->getConfig()->get("monetary-unit", "$") . $dataStore[2] . "?");
						$form->setButton1("Yes, I accept the charge");
						$form->setButton2("No, I don't want to pay for this");
					}
					$form->sendToPlayer($player);
				});
				$form->setTitle("Enchantment Shop");
				$form->addSlider("Enchantment Level", 1, (int) (((int) EconomyAPI::getInstance()->getConfig()->get("max-money", 9999999999)) / 5000000));
				$form->sendToPlayer($player);
			});
			$form->setTitle("Enchantment Shop");
			$form->setContent("Choose an enchantment to add to your item");

			foreach($enchantments as $enchantment) {
				$form->addButton($enchantment->getName());
			}
			$form->sendToPlayer($sender);
		}
		return true;
	}

	/**
	 * @param Player $player
	 *
	 * @return array
	 */
	public static function createPlayerDataStore(Player $player) : array {
		static::$dataStore[$player->getName()] = [];
		return static::$dataStore[$player->getName()];
	}

	/**
	 * @param Player $player
	 *
	 * @return array
	 */
	public static function getFromDataStore(Player $player) : array {
		return static::$dataStore[$player->getName()];
	}

	/**
	 * @param Player $player
	 * @param array $data
	 */
	public static function setToDataStore(Player $player, array $data) : void {
		static::$dataStore[$player->getName()] = $data;
	}

	/**
	 * @param Player $player
	 */
	public static function clearFromDataStore(Player $player) : void {
		unset(static::$dataStore[$player->getName()]);
	}
}