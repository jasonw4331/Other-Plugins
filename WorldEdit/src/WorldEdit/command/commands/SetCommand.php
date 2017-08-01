<?php

namespace WorldEdit\command\commands;

use pocketmine\block\Block;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use WorldEdit\action\actions\SetAction;
use WorldEdit\command\CommandHandler;
use WorldEdit\WorldEdit;

class SetCommand extends Command {

    /** @var WorldEdit */
    private $plugin;

    /**
     * SetCommand constructor
     *
     * @param CommandHandler $handler
     */
    public function __construct(CommandHandler $handler) {
        $this->plugin = $handler->getPlugin();
        parent::__construct("/set", "WorldEdit set command", "//set <blockId>", []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if($sender instanceof Player) {
            if($sender->hasPermission("worldedit.command.*" or $sender->hasPermission("worldedit.command.set"))) {
                if(isset($args[0])) {
                    $arr = explode(":", $args[0]);
                    $block = Block::get((int) $arr[0], isset($arr[1]) ? (int)$arr[1] : 0);
                    if($block instanceof Block) {
                        $selectionHandler = $this->plugin->getSelectionHandler();
                        if($selectionHandler->hasSelection($sender)) {
                            $selection = $selectionHandler->getSelectionByPlayer($sender);
                            if($selection->isSelectionReady()) {
                                $action = new SetAction($this->plugin->getActionHandler(), $selection, $block);
                                $action->start();
                                $sender->sendMessage("You set {$action->getBlocksChanged()} blocks!");
                                $action->stop();
                            }
                            else {
                                $sender->sendMessage("Your selection isn't ready! You must set all positions before start.");
                            }
                        }
                        else {
                            $sender->sendMessage("You haven't a selection!");
                        }
                    }
                    else {
                        $sender->sendMessage("{$args[0]} isn't a valid block id!");
                    }
                }
                else {
                    $sender->sendMessage("Usage: //set <blockId>");
                }
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