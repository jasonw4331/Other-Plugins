<?php

namespace WorldEdit\selection;

use pocketmine\Player;
use WorldEdit\WorldEdit;

class SelectionHandler {

    /** @var WorldEdit */
    private $plugin;

    /** @var Selection[] */
    private $selections = [];

    /**
     * SelectionHandler constructor.
     *
     * @param WorldEdit $plugin
     */
    public function __construct(WorldEdit $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @return Selection[]
     */
    public function getSelections() {
        return $this->selections;
    }

    /**
     * @param Player $player
     * @return null|Selection
     */
    public function getSelectionByPlayer(Player $player) {
        return ($this->hasSelection($player)) ? $this->selections[$player->getName()] : null;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function hasSelection(Player $player) {
        return isset($this->selections[$player->getName()]);
    }

    /**
     * @param Player $player
     * @return Selection
     */
    public function createSelection(Player $player) {
        $selection = new Selection($this, $player);
        $this->selections[$player->getName()] = $selection;
        return $selection;
    }

    /**
     * @param Selection $selection
     */
    public function removeSelection(Selection $selection) {
        if(in_array($selection, $this->selections)) {
            unset($this->selections[array_search($selection, $this->selections)]);
        }
    }

}