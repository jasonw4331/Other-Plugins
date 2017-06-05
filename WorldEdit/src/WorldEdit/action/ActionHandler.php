<?php

namespace WorldEdit\action;

use pocketmine\block\Block;
use WorldEdit\action\actions\SetAction;
use WorldEdit\selection\Selection;
use WorldEdit\WorldEdit;

class ActionHandler {

    /** @var WorldEdit */
    private $plugin;

    /** @var WorldEditAction[] */
    private $actions = [];

    /**
     * ActionHandler constructor.
     *
     * @param WorldEdit $plugin
     */
    public function __construct(WorldEdit $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @return WorldEdit
     */
    public function getPlugin() {
        return $this->plugin;
    }
    /**
     * @return WorldEditAction[]
     */
    public function getActions() {
        return $this->actions;
    }

    /**
     * @param WorldEditAction[] $actions
     */
    public function setActions($actions) {
        $this->actions = $actions;
    }

    /**
     * @param WorldEditAction $action
     */
    public function addAction(WorldEditAction $action) {
        $this->actions[] = $action;
    }

    /**
     * @param WorldEditAction $action
     */
    public function removeAction(WorldEditAction $action) {
        if(in_array($action, $this->actions)) {
            unset($this->actions[array_search($action, $this->actions)]);
        }
    }

    public function set(Selection $selection, Block $block) {
        $action = new SetAction($this, $selection, $block);
        if($selection->isSelectionReady()) {
            $action->start();
        }
        $action->stop();
    }

}