<?php
namespace Primus\CrateKeys\event;

use pocketmine\Player;
use pocketmine\block\Block;
use pocketmine\event\player\PlayerEvent;
use pocketmine\event\Cancellable;

class PlayerCrateKeyRecieveEvent extends PlayerEvent implements Cancellable{
	public static $handlerList = null;

    /** @var Block $source */
    protected $source;

    public function __construct(Player $player, Block $source) {
        $this->player = $player;
        $this->source = $source; // What block was mined to get the key
    }

    public function getBlock() {
        return $this->source;
    }
}