<?php

namespace WorldEdit;

use pocketmine\plugin\PluginBase;
use WorldEdit\action\ActionHandler;
use WorldEdit\command\CommandHandler;
use WorldEdit\selection\SelectionHandler;

class WorldEdit extends PluginBase {

    /** @var WorldEdit */
    private static $instance = null;

    /** @var SelectionHandler */
    private $selectionHandler;

    /** @var ActionHandler */
    private $actionHandler;

    /** @var CommandHandler */
    private $commandHandler;

    public function onLoad() {
        self::$instance = $this;
    }

    public function onEnable() {
        $this->selectionHandler = new SelectionHandler($this);
        $this->actionHandler = new ActionHandler($this);
        $this->commandHandler = new CommandHandler($this);
        $this->getLogger()->info("WorldEdit by @GiantAmethyst was enabled");
    }

    public function onDisable() {
        $this->getLogger()->info("WorldEdit by @GiantAmethyst was disabled");
    }

    /**
     * @return SelectionHandler
     */
    public function getSelectionHandler() {
        return $this->selectionHandler;
    }

    /**
     * @return ActionHandler
     */
    public function getActionHandler() {
        return $this->actionHandler;
    }

    /**
     * @return CommandHandler
     */
    public function getCommandHandler() {
        return $this->commandHandler;
    }

    /**
     * @return WorldEdit
     */
    public static function getInstance() {
        return self::$instance;
    }
}
