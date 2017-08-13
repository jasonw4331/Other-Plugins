<?php
namespace Primus\CrateKeys\event;

use pocketmine\block\Block;
use pocketmine\event\player\PlayerEvent;
use pocketmine\Player;
use pocketmine\event\Cancellable;
use pocketmine\item\Item;

class PlayerCrateKeyUseEvent extends PlayerEvent implements Cancellable{
	public static $handlerList = null;

    /** @var  Block $target */
    protected $target;
    /** @var Item $key */
    protected $key;

    public function __construct(Player $player, Item $key, Block $target) {
        $this->player = $player;
        $this->key = $key;
        $this->target = $target;
    }

    public function getKey() {
        return $this->key;
    }

    public function getTarget() {
        return $this->target;
    }
}