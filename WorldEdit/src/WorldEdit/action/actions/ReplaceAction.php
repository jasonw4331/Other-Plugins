<?php

namespace WorldEdit\action\actions;

use pocketmine\block\Block;
use pocketmine\math\Vector3;
use WorldEdit\action\ActionHandler;
use WorldEdit\action\WorldEditAction;
use WorldEdit\selection\Selection;

class ReplaceAction implements WorldEditAction {

    /** @var ActionHandler */
    private $handler;

    /** @var Selection */
    private $selection;

    /** @var Block */
    private $replacer;

    /** @var Block */
    private $replaced;

    /** @var int */
    private $blocksChanged = 0;

    /**
     * ReplaceAction constructor.
     *
     * @param ActionHandler $handler
     * @param Selection $selection
     * @param Block $replacer
     * @param Block $replaced
     */
    public function __construct(ActionHandler $handler, Selection $selection, Block $replacer, Block $replaced) {
        $this->handler = $handler;
        $this->selection = $selection;
        $this->replacer = $replacer;
        $this->replaced = $replaced;
        $handler->addAction($this);
    }

    /**
     * @return int
     */
    public function getBlocksChanged() {
        return $this->blocksChanged;
    }

    public function start() {
        $selection = $this->selection;
        $vector = new Vector3();
        for($x = $selection->getMinX(); $x <= $selection->getMaxX(); $x++) {
            for($y = $selection->getMinY(); $y <= $selection->getMaxY(); $y++) {
                for($z = $selection->getMinZ(); $z <= $selection->getMaxZ(); $z++) {
                    if($selection->getPosition1()->getLevel()->getBlockIdAt($x, $y, $z) == $this->replaced->getId()) {
                        $selection->getPosition1()->getLevel()->setBlock($vector->setComponents($x, $y, $z), $this->replacer, true, false);
                        $this->blocksChanged++;
                    }
                }
            }
        }
    }

    public function stop() {
        $this->handler->removeAction($this);
    }

}