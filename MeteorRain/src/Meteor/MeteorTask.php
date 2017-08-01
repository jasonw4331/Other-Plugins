<?php
namespace Meteor;

use pocketmine\scheduler\PluginTask;

class MeteorTask extends PluginTask{

	private $m;

	public function __construct(Main $main) {

		$this->m = $main;
		parent::__construct($main);

	}

	public function onRun(int $ticks) {

		$meteor = $this->m->makeMeteor();
		if($meteor instanceof Meteor) {
			$meteor->spawnToAll(); // What if meteor is null?
		}
	}
}