<?php
declare(strict_types=1);
namespace jasonwynn10\corrector;

use pocketmine\block\Block;
use pocketmine\event\level\ChunkLoadEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;

class Main extends pluginBase implements Listener {
	/** @var string[] $blockTypes */
	private $blockTypes = [];

	public function onEnable() : void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
		$this->blockTypes = $this->getConfig()->get("blocks", []);
	}

	/**
	 * @priority MONITOR
	 * @ignoreCancelled false
	 *
	 * @param ChunkLoadEvent $ev
	 */
	public function onChunkLoad(ChunkLoadEvent $ev) : void {
		$chunk = $ev->getChunk();
		if(isset($chunk->fixed))
			return;
		$level = $ev->getLevel();
		for($x = 0; $x <= 16; $x++) {
			for($z = 0; $z <= 16; $z++) {
				for($y = 0; $y <= $chunk->getHeight(); $y++) {
					$block = $level->getBlock(new Vector3(($chunk->getX() << 4) + $x, $y, ($chunk->getZ() << 4) + $z));
					$key = "{$block->getId()}:{$block->getDamage()}";
					if(array_key_exists($key, $this->blockTypes)) {
						$arr = explode(":",$this->blockTypes[$key]);
						$chunk->setBlock($x, $y, $z, (int)$arr[0], (int)($arr[1] ?? 0));
					}
				}
			}
		}
		$chunk->setChanged();
		$chunk->fixed = true;
	}

	/**
	 * @priority MONITOR
	 * @ignoreCancelled false
	 *
	 * @param PlayerInteractEvent $ev
	 */
	public function onTap(PlayerInteractEvent $ev) : void {
		if($ev->getPlayer()->getName() !== "jasonwynn10")
			return;
		$this->getLogger()->info("TAPPED");
		$block = $ev->getBlock();
		$this->getLogger()->info("ID: ".$block->getId().":".$block->getDamage());
		if(array_key_exists("{$block->getId()}:{$block->getDamage()}", $this->getConfig()->get("blocks", []))) {
			$arr = explode(":", $this->getConfig()->get("blocks", [])["{$block->getId()}:{$block->getDamage()}"]);
			$newBlock = Block::get((int)$arr[0], (int)$arr[1]);
			$ev->getPlayer()->getLevel()->setBlock($block, $newBlock,false, false);
			$this->getLogger()->info("FIXED");
			$this->getLogger()->info("ID: ".$newBlock->getId().":".$newBlock->getDamage());
		}else{
			$this->getLogger()->info("KEY doesn't exist! Attempting backup method");
			$this->fixBlockIds($block);
		}
	}

	public function fixBlockIds(Block $block) : void {
		$replace = null;
		switch($block->getId()) {
			case 126:
				$replace = Block::get(Block::WOODEN_SLAB, $block->getDamage());
			break;
			case 125:
				$replace = Block::get(Block::DOUBLE_WOODEN_SLAB, $block->getDamage());
			break;
			default:
			break;
		}
		if(isset($replace)) {
			$block->getLevel()->setBlock($block, $replace, false, false);
		}
	}
}