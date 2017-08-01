<?php
namespace LDX\TimeCommander;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\scheduler\PluginTask;

class TimeCommand extends PluginTask {
	/** @var string $cmd */
	private $cmd;
	/** @var bool $start */
	private $start = false;
	public function __construct(Main $plugin, $cmd) {
		parent::__construct($plugin);
		$this->cmd = $cmd;
		$this->start = false;
	}
	public function onRun(int $ticks) {
		if($this->start) {
			$this->getOwner()->getServer()->dispatchCommand(new ConsoleCommandSender(), $this->cmd);
		} else {
			$this->start = true;
		}
	}
}