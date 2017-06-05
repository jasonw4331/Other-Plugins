<?php

namespace WorldEdit\command;

use pocketmine\command\Command;
use WorldEdit\command\commands\Pos1Command;
use WorldEdit\command\commands\Pos2Command;
use WorldEdit\command\commands\ReplaceCommand;
use WorldEdit\command\commands\SetCommand;
use WorldEdit\WorldEdit;

class CommandHandler {

    /** @var WorldEdit */
    private $plugin;

    /** @var Command[] */
    private $commands = [];

    /**
     * CommandHandler constructor.
     *
     * @param WorldEdit $plugin
     */
    public function __construct(WorldEdit $plugin) {
        $this->plugin = $plugin;
        $this->loadDefaultCommands();
    }

    /**
     * @return WorldEdit
     */
    public function getPlugin() {
        return $this->plugin;
    }

    /**
     * @return Command[]
     */
    public function getCommands() {
        return $this->commands;
    }

    /**
     * @param Command $command
     */
    public function loadCommand(Command $command) {
        $this->commands[$command->getName()] = $command;
        $this->plugin->getServer()->getCommandMap()->register($command->getName(), $command);
    }

    public function loadDefaultCommands() {
        $this->loadCommand(new Pos1Command($this));
        $this->loadCommand(new Pos2Command($this));
        $this->loadCommand(new SetCommand($this));
        $this->loadCommand(new ReplaceCommand($this));
    }

}