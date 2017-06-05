<?php
namespace corrector;

use pocketmine\math\Vector3;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;

class Corrector extends PluginTask {
    /** @var int $bpt */
    private $bpt;
    /** @var int[] $blockTypes */
    private $blockTypes = [
        "0:0" => "0:0"
    ];
    public function __construct(Plugin $plugin, $bpt) {
        parent::__construct($plugin);
        $this->bpt = $bpt;
    }
    public function onRun($currentTick) {
        $blocks = 0;
        foreach($this->getOwner()->getServer()->getLevels() as $level) {
            $height = 256; //TODO set based on level provider
            foreach($level->getChunks() as $chunk) {
                for($x = 0; $x <= 16; $x++) {
                    for($z = 0; $z <= 16; $z++) {
                        for($y = 0; $y <= $height; $y++) {
                            if($blocks < $this->bpt) {
                                $pos = new Vector3(($chunk->getX() << 4) + $x, $y, ($chunk->getZ() << 4) + $z);
                                $block = $level->getBlock($pos);
                                if(array_key_exists("{$block->getId()}:{$block->getDamage()}", $this->blockTypes)) {
                                    $arr = explode(":",$this->blockTypes["{$block->getId()}:{$block->getDamage()}"]);
                                    if(is_int($arr[0]) and is_int($arr[1])) {
                                        $chunk->setBlock($x, $y, $z, $arr[0], $arr[1]);
                                    }
                                }
                            }else{
                                $this->getOwner()->getServer()->getScheduler()->scheduleDelayedTask(new self($this->getOwner(),$this->bpt), 1); //TODO
                            }
                        }
                    }
                }
            }
        }
    }
}