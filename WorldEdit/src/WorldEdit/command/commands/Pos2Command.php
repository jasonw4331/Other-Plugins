<?php

namespace WorldEdit\command\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use WorldEdit\command\CommandHandler;
use WorldEdit\WorldEdit;

class Pos2Command extends Command {

    /** @var WorldEdit */
    private $plugin;

    /**
     * Pos2Command constructor
     *
     * @param CommandHandler $handler
     */
    public function __construct(CommandHandler $handler) {
        $this->plugin = $handler->getPlugin();
        parent::__construct("/pos2", "WorldEdit pos2 command", "/pos2", ["/2"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if($sender instanceof Player) {
            if($sender->hasPermission("worldedit.command.*") or $sender->hasPermission("worldedit.command.pos2")) {
                $selectionHandler = $this->plugin->getSelectionHandler();
                if($selectionHandler->hasSelection($sender)) {
                    $selectionHandler->getSelectionByPlayer($sender)->setPosition2($sender->getPosition());
                }
                else {
                    $selectionHandler->createSelection($sender)->setPosition2($sender->getPosition());
                }
                $sender->sendMessage("You successfully set the second position!");
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