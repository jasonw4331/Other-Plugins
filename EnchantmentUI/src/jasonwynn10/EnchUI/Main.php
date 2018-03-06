<?php
declare(strict_types=1);
namespace jasonwynn10\EnchUI;

use jojoe77777\FormAPI\FormAPI;
use onebone\economyapi\EconomyAPI;
use PiggyCustomEnchants\CustomEnchants\CustomEnchants;
use pocketmine\event\Listener;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class Main extends PluginBase implements Listener {
	public function onEnable() {
		$dataStore = [];

		/** @var FormAPI $formsAPI */
		$formsAPI = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $formsAPI->createSimpleForm(function(Player $player, $data) use (&$dataStore) {

			$dataStore[$player->getName()][] = $data[0];

			/** @var FormAPI $formsAPI */
			$formsAPI = Server::getInstance()->getPluginManager()->getPlugin("FormAPI");
			$form = $formsAPI->createCustomForm(function(Player $player, $data) use (&$dataStore) {

				var_dump($data); //TODO remove
				$dataStore[$player->getName()][] = (int) $data[""];

				/** @var FormAPI $formsAPI */
				$formsAPI = Server::getInstance()->getPluginManager()->getPlugin("FormAPI");

				$economy = EconomyAPI::getInstance();
				$money = $economy->myMoney($player);
				if($money - $dataStore[$player->getName()][3] < 0) {
					$form = $formsAPI->createSimpleForm();
					$form->setTitle("Enchantment Shop");
					$form->setContent("You don't have enough money to buy that!");
				}
				else {
					$form = $formsAPI->createModalForm(function(Player $player, $data) use (&$dataStore) {

						var_dump($data); //TODO remove
						if($data) {
							$economy = EconomyAPI::getInstance();
							$economy->reduceMoney($player, $dataStore[$player->getName()][2], false, "EnchantmentShop");
							$player->getInventory()->getItemInHand()->addEnchantment(new EnchantmentInstance(CustomEnchants::getEnchantmentByName($dataStore[$player->getName()][0]), $dataStore[$player->getName()][1]));
						}
					});
					$form->setTitle("Enchantment Shop");
					$form->setContent("Do you accept your charge of " . $economy->getConfig()->get("monetary-unit", "$") . $dataStore[$player->getName()][2] . "?");
				}
			});
			$form->setTitle("Enchantment Shop");
			$maxLevel = (int) (((int) EconomyAPI::getInstance()->getConfig()->get("max-money", 9999999999)) / 5000000);
			$form->addSlider("Enchantment Level", 1, $maxLevel);
		});
		$form->setTitle("Enchantment Shop");
		$form->setContent("Choose an enchantment to add to your item");

		/** @noinspection PhpUnhandledExceptionInspection */
		$class = new \ReflectionClass(CustomEnchants::class);
		/** @var Enchantment[] $enchantments */
		$enchantments = $class->getStaticPropertyValue("enchantments", []);
		foreach($enchantments as $enchantment) {
			$form->addButton($enchantment->getName());
		}
	}
}