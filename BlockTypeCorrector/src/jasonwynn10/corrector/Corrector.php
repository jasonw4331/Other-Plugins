<?php
namespace jasonwynn10\corrector;

use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;

class Corrector extends PluginTask {
	/** @var int $bpt */
	private $bpt;
	/** @var Level $level */
	private $level;
	/** @var int[] $blockTypes */
	private $blockTypes = [];
	public function __construct(Plugin $plugin, Level $level) {
		parent::__construct($plugin);
		$this->level = $level;
		$this->bpt = $plugin->getConfig()->get("BlocksPerTick",256);
		$this->blockTypes = $plugin->getConfig()->get("blocks", []);
		$plugin->getLogger()->info("CORRECTOR STARTING");
	}
	public function onRun(int $currentTick) {
        $this->getOwner()->getLogger()->info("CORRECTOR STARTED");
		$blocks = 0;
        $height = $this->level->getProvider()->getWorldHeight();
        foreach($this->level->getChunks() as $chunk) {
            if(!$chunk->isPopulated() or !$chunk->isGenerated()) {
                continue;
            }
            $this->getOwner()->getLogger()->info("CORRECTOR found chunk to fix");
            for($x = 0; $x <= 16; $x++) {
                for($z = 0; $z <= 16; $z++) {
                    for($y = 0; $y <= $height; $y++) {
                        if($blocks < $this->bpt) {
                            $pos = new Vector3(($chunk->getX() << 4) + $x, $y, ($chunk->getZ() << 4) + $z);
                            $block = $this->level->getBlock($pos);
                            if(array_key_exists("{$block->getId()}:{$block->getDamage()}", $this->blockTypes)) {
                                $arr = explode(":",$this->blockTypes["{$block->getId()}:{$block->getDamage()}"]);
                                $chunk->setBlock($x, $y, $z, (int)$arr[0], (int)$arr[1]);
                                $this->getOwner()->getLogger()->info("CORRECTOR fixed a block!");
                                $blocks++;
                            }
                        }else{
                            $this->getOwner()->getServer()->getScheduler()->scheduleDelayedTask($this, 1);
                        }
                    }
                }
            }
        }
	}
}
