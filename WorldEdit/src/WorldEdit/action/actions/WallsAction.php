<?php

namespace WorldEdit\action\actions;

use pocketmine\block\Block;
use pocketmine\math\Vector3;
use WorldEdit\action\ActionHandler;
use WorldEdit\action\WorldEditAction;
use WorldEdit\selection\Selection;

class WallsAction implements WorldEditAction {

    /** @var ActionHandler */
    private $handler;

    /** @var Selection */
    private $selection;

    /** @var Block */
    private $block;

    /** @var int */
    private $blocksChanged = 0;

    /**
     * WallsAction constructor.
     *
     * @param ActionHandler $handler
     * @param Selection $selection
     * @param Block $block
     */
    public function __construct(ActionHandler $handler, Selection $selection, Block $block) {
        $this->handler = $handler;
        $this->selection = $selection;
        $this->block = $block;
        $handler->addAction($this);
    }

    /**
     * @return int
     */
    public function getBlocksChanged() {
        return $this->blocksChanged;
    }

    public function start() {
        $vector = new Vector3();
        $selection = $this->selection;
        $level = $selection->getPosition1()->getLevel();
        for($y = $selection->getMinY(); $y <= $selection->getMaxY(); $y++) {
            for($x = $selection->getMinX(); $x <= $selection->getMaxX(); $x++) {
                $level->setBlock($vector->setComponents($x, $y, $selection->getMinZ()), $this->block, true, false);
                $level->setBlock($vector->setComponents($x, $y, $selection->getMaxZ()), $this->block, true, false);
                $this->blocksChanged += 2;
            }
            for($z = $selection->getMinZ(); $z <= $selection->getMaxZ(); $z++) {
                $level->setBlock($vector->setComponents($selection->getMinX(), $y, $z), $this->block, true, false);
                $level->setBlock($vector->setComponents($selection->getMaxX(), $y, $z), $this->block, true, false);
                $this->blocksChanged += 2;
            }
        }
    }

    public function stop() {
        $this->handler->removeAction($this);
    }

}