<?php
namespace jasonwynn10\CrossOnlineCount;

use jasonwynn10\CrossOnlineCount\libs\MinecraftQuery;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;

class UpdateTask extends PluginTask {
	/** @var array $arr */
	public $arr = [];
	/** @var MinecraftQuery $query */
	private $query;
	public function __construct(Plugin $owner, array $arr, MinecraftQuery $query) {
		parent::__construct($owner);
		$this->arr = $arr;
		$this->query = $query;
	}
	public function onRun(int $currentTick) {
		foreach($this->arr as $eid => $ip) {
			if(empty($ip)) {
				unset($this->arr[$eid]);
				continue;
			}
			$server = explode(":", $ip);
			try{
				$this->query->Connect($server[0], $server[1]);
			}catch(\Exception $e) {
				$this->getOwner()->getLogger()->error($e->getMessage());
			}
			$online = $this->query->GetInfo()["numplayers"] ?? 0;

			$entity = $this->getOwner()->getServer()->findEntity($eid);

			$lines = explode("\n", $entity->getNameTag());
			$lines[0] = $online." Online";
			$nametag = implode("\n", $lines);

			$entity->setNameTag($nametag);
		}
	}
}