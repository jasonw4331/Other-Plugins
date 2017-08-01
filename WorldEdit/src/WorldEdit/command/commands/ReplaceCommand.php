<?php

namespace WorldEdit\command\commands;

use pocketmine\block\Block;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use WorldEdit\action\actions\ReplaceAction;
use WorldEdit\command\CommandHandler;
use WorldEdit\WorldEdit;

class ReplaceCommand extends Command {

    /** @var WorldEdit */
    private $plugin;

    /**
     * ReplaceCommand constructor
     *
     * @param CommandHandler $handler
     */
    public function __construct(CommandHandler $handler) {
        $this->plugin = $handler->getPlugin();
        parent::__construct("/replace", "WorldEdit replace command", "//replace <replacerId> <replacedId>", []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if($sender instanceof Player) {
            if($sender->hasPermission("worldedit.command.*" or $sender->hasPermission("worldedit.command.replace"))) {
                if(isset($args[0]) and isset($args[1])) {
                    $arr = explode(":", $args[0]);
                    $replacer = Block::get((int) $arr[0], isset($arr[1]) ? (int)$arr[1] : 0);
                    $arr = explode(":", $args[1]);
                    $replaced = Block::get((int) $arr[0], isset($arr[1]) ? (int)$arr[1] : 0);
                    if($replacer instanceof Block and $replaced instanceof Block) {
                        $selectionHandler = $this->plugin->getSelectionHandler();
                        if($selectionHandler->hasSelection($sender)) {
                            $selection = $selectionHandler->getSelectionByPlayer($sender);
                            if($selection->isSelectionReady()) {
                                $action = new ReplaceAction($this->plugin->getActionHandler(), $selection, $replacer, $replaced);
                                $action->start();
                                $sender->sendMessage("You replaced {$action->getBlocksChanged()} blocks!");
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
                        $sender->sendMessage("{$args[0]} or {$args[1]} isn't a valid block id!");
                    }
                }
                else {
                    $sender->sendMessage("Usage: //replace <replacedId> <replacerId>");
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