<?php
namespace jasonwynn10\CrossOnlineCount;

use libpmquery\PMQuery;
use libpmquery\PmQueryException;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use slapper\events\SlapperCreationEvent;
use slapper\events\SlapperDeletionEvent;
use spoondetector\SpoonDetector;

class Main extends PluginBase implements Listener {

	public function onEnable() {
		SpoonDetector::printSpoon($this, "spoon.txt");
		$this-getScheduler()->scheduleRepeatingTask(new UpdateTask($this), 5); // server updates query data every 5 ticks
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onDisable() {
		foreach($this->getServer()->getLevels() as $level) {
			foreach($level->getEntities() as $entity) {
				if(!empty($entity->namedtag->getString("server", ""))) {
					$lines = explode("\n", $entity->getNameTag());
					$lines[0] = $entity->namedtag->getString("server", "");
					$nametag = implode("\n", $lines);
					$entity->setNameTag($nametag);
				}
			}
		}
	}

	/**
	 * @priority LOW
	 *
	 * @param SlapperCreationEvent $ev
	 */
	public function onSlapperCreate(SlapperCreationEvent $ev) {
		$entity = $ev->getEntity();
		$lines = explode("\n", $entity->getNameTag());
		if($this->isValidIP($lines[0]) or $this->is_valid_domain_name($lines[0])) {
			$entity->namedtag->setString("server", $lines[0]);
			$this->update();
		}
	}

	/**
	 * @priority LOW
	 *
	 * @param SlapperDeletionEvent $ev
	 */
	public function onSlapperDelete(SlapperDeletionEvent $ev) {
		$entity = $ev->getEntity();
		if(!empty($entity->namedtag->getString("server", ""))) {
			$entity->namedtag->removeTag("server");
		}
	}

	/**
	 * @api
	 */
	public function update() {
		foreach($this->getServer()->getLevels() as $level) {
			foreach($level->getEntities() as $entity) {
				if(!empty($entity->namedtag->getString("server", ""))) {
					$server = explode(":", $entity->namedtag->getString("server", ""));
					try {
						$queryData = PMQuery::query($server[0], $server[1]); //TODO make async
						$online = (int) $queryData['num'];

						$lines = explode("\n", $entity->getNameTag());
						$lines[0] = TextFormat::YELLOW.$online." Online".TextFormat::WHITE;
						$nametag = implode("\n", $lines);
						$entity->setNameTag($nametag);
					}catch(PmQueryException $e) {
						$this->getLogger()->logException($e);
						$lines = explode("\n", $entity->getNameTag());
						$lines[0] = TextFormat::DARK_RED."Server Offline".TextFormat::WHITE;
						$nametag = implode("\n", $lines);
						$entity->setNameTag($nametag);
					}
				}
			}
		}
	}

	/**
	 * @api
	 *
	 * @param string $domain_name
	 *
	 * @return bool
	 */
	public function is_valid_domain_name(string $domain_name) {
		return (preg_match("/([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*:(\d{1,5})/i", $domain_name) //valid chars check
		        and preg_match("/.{1,253}/", $domain_name) //overall length check
		        and preg_match("/[^\.]{1,63}(\.[^\.]{1,63})*/", $domain_name)); //length of each label
	}

	/**
	 * @api
	 *
	 * @param string $ip
	 *
	 * @return bool
	 */
	public function isValidIP(string $ip) {
		return (preg_match("/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}):(\d{1,5})/", $ip) !== false);
	}
}
