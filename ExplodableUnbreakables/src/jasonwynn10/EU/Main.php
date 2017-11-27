<?php
declare(strict_types=1);
namespace jasonwynn10\EU;

use pocketmine\block\Bedrock;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\Obsidian;
use pocketmine\entity\PrimedTNT;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\Listener;
use pocketmine\level\Level;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {
	/** @var Block[] $blocks */
	private $blocks = [];
	public function onEnable() {
		BlockFactory::registerBlock(new class() extends Bedrock {
			public function getBlastResistance() : float {
				return 36.41; // accounts for 2 steps out from explosion
			}
		}, true);
		BlockFactory::registerBlock(new class() extends Obsidian {
			public function getBlastResistance() : float {
				return 36.41; // accounts for 2 steps out from explosion
			}
		}, true);
		@mkdir($this->getDataFolder());
		new Config($this->getDataFolder()."config.yml", Config::YAML,[
			"TNT-count" => 4
		]);
		$this->getConfig()->reload();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	public function onExplode(EntityExplodeEvent $ev) {
		$entity = $ev->getEntity();
		var_dump($this->blocks);
		if($entity instanceof PrimedTNT) {
			$ev->setBlockList(array_filter($ev->getBlockList(), function(Block $block) {
				if($block->getId() !== Block::BEDROCK and $block->getId() !== Block::OBSIDIAN)
					return true;
				if($this->getConfig()->get("TNT-count", 4) <= 0)
					return false;
				$hash = Level::blockHash($block->x, $block->y, $block->z);
				if(isset($this->blocks[$hash]) and ($this->getConfig()->get("TNT-count", 4) === 1 or $this->blocks[$hash] >= (int) $this->getConfig()->get("TNT-count", 4))) {
					unset($this->blocks[$hash]);
					return true;
				}elseif(!isset($this->blocks[$hash])) {
					$this->blocks[$hash] = 1;
				}else{
					$this->blocks[$hash]++;
				}
				return false;
			}));
		}else{
			$ev->setBlockList(array_filter($ev->getBlockList(), function(Block $block) {
				return $block->getId() !== Block::BEDROCK and $block !== Block::OBSIDIAN;
			}));
		}
		return;
	}
}