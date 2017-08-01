<?php
namespace MysteryCrates;

use pocketmine\scheduler\PluginTask;

class Cooldown extends PluginTask {

    private $player;

    public function __construct(Main $plugin, string $player) {
        parent::__construct($plugin);
        $this->player = $player;
    }

    public function onRun(int $tick) {
        $this->getOwner()->setAllowed($this->getOwner()->getServer()->getPlayer($this->player), true);
    }
}