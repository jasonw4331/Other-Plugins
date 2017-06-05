<?php

namespace WorldEdit\command\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use WorldEdit\command\CommandHandler;
use WorldEdit\WorldEdit;

class Pos1Command extends Command {

    /** @var WorldEdit */
    private $plugin;

    /**
     * Pos1Command constructor.
     *
     * @param CommandHandler $handler
     */
    public function __construct(CommandHandler $handler) {
        $this->plugin = $handler->getPlugin();
        parent::__construct("/pos1", "WorldEdit pos1 command", "/pos1", ["/1"]);
    }

    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if($sender instanceof Player) {
            if($sender->hasPermission("worldedit.command.*") or $sender->hasPermission("worldedit.command.pos1")) {
                $selectionHandler = $this->plugin->getSelectionHandler();
                if($selectionHandler->hasSelection($sender)) {
                    $selectionHandler->getSelectionByPlayer($sender)->setPosition1($sender->getPosition());
                }
                else {
                    $selectionHandler->createSelection($sender)->setPosition1($sender->getPosition());
                }
                $sender->sendMessage("You successfully set the first position!");
            }
            else {
                $sender->sendMessage("You must have permission to execute this command!");
            }
        }
        else {
            $sender->sendMessage("Please, run this command in game");
        }
        return true;
    }

}