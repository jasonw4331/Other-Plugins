<?php
namespace jasonwynn10\CrossOnlineCount;

class UpdateTask extends Task {
	private $plugin;
	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
	}
	public function onRun(int $currentTick) {
		$this->plugin->update();
	}
}
