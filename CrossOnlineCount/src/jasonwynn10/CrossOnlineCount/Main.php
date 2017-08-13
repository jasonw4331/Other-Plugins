<?php
namespace jasonwynn10\CrossOnlineCount;

use jasonwynn10\CrossOnlineCount\libs\MinecraftQuery;

use pocketmine\event\Listener;
use pocketmine\nbt\tag\StringTag;
use pocketmine\plugin\PluginBase;
use slapper\events\SlapperCreationEvent;
use slapper\events\SlapperDeletionEvent;

class Main extends PluginBase implements Listener {
	/** @var MinecraftQuery $Query */
	private $query;
	/** @var UpdateTask $task */
	private $task;
	public function onEnable() {
		$arr = [];
		foreach($this->getServer()->getLevels() as $level) {
			if(!$level->isClosed()) {
				foreach($level->getEntities() as $entity) {
					if(isset($entity->namedtag->server)) {
						$ip = $entity->namedtag->server->getValue();
						$arr[$entity->getId()] = $ip;
					}
				}
			}
		}
		$handler = $this->getServer()->getScheduler()->scheduleRepeatingTask(new UpdateTask($this, $arr, $this->query = new MinecraftQuery()), 5);
		$this->task = $handler->getTask();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	public function onDisable() {
		foreach($this->task->arr as $eid => $ip) {
			$entity = $this->getServer()->findEntity($eid);
			if(isset($entity->namedtag->server)) {
				$lines = explode("\n", $entity->getNameTag());
				$lines[0] = $entity->namedtag->server->getValue();
				$nametag = implode("\n", $lines);
				$entity->setNameTag($nametag);
			}
		}
	}
	public function onSlapperCreate(SlapperCreationEvent $ev) {
		$entity = $ev->getEntity();
		$lines = explode("\n", $entity->getNameTag());
		if(preg_match("/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}):(\d{1,5})/", $lines[0], $matches) == 1) {
			if(isset($matches[0])) {
				$entity->namedtag->server = new StringTag("server", $matches[0]);
				$this->task->arr[$entity->getId()] = $matches[0];
				
				$server = explode(":", $matches[0]);
				$this->query->Connect($server[0], $server[1]);
				$onlinePlayers = $this->query->GetInfo()["numplayers"] ?? 0;

				$lines = explode("\n", $entity->getNameTag());
				$lines[0] = $onlinePlayers." Online";
				$nametag = implode("\n", $lines);

				$entity->setNameTag($nametag);
			}else{
				$this->getLogger()->debug("regex failed");
			}
		}
	}
	public function onSlapperDelete(SlapperDeletionEvent $ev) {
		$entity = $ev->getEntity();
		if(isset($this->task->arr[$entity->getId()])) {
			unset($this->task->arr[$entity->getId()]);
		}
		if(isset($entity->namedtag->server)) {
			unset($entity->namedtag->server);
		}
	}
}