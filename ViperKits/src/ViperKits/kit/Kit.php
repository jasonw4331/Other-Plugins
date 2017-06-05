<?php
namespace ViperKits\kit;

use ViperKits\ViperKits;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\Player;

class Kit{

    private $plugin;
    private $name;
    private $activeIn;
    private $commands;
    private $onGiveMessage;
    private $cost;
    /** @var Item[] */
    private $items;

    public function __construct($name,$kitData, ViperKits $plugin) {
        $this->plugin = $plugin;
        $this->name = $name;
        if(isset($kitData['active-in'])) {
            $this->activeIn = $kitData['active-in'];
        }else{
            $this->activeIn = [];
        }
        if(isset($kitData['commands'])) {
            $this->commands = $kitData['commands'];
        }else{
            $this->commands = [];
        }
        if(isset($kitData['on-give-message'])) {
            $this->onGiveMessage = $kitData['on-give-message'];
        }else{
            $this->onGiveMessage = false;
        }
        if(isset($kitData['cost'])) {
            $this->cost = $kitData['cost'];
        }else{
            $this->cost = false;
        }
        if(isset($kitData['items']) && count($kitData['items']) > 0) {
            $this->items = [];
            foreach($kitData['items'] as $slotId => $itemStr) {
                if(strpos($itemStr, ":") !== false) {
                    $itemArr = explode(":", $itemStr);
                    if(count($itemArr) == 2 && is_numeric($itemArr[0]) && is_numeric($itemArr[1])) {
                        $this->items[$slotId] = Item::get($itemArr[0], 0, $itemArr[1]);
                    }
                    else{
                        $this->getPlugin()->getLogger()->info("Skipping item $itemStr due to it not being an item");
                    }
                }
                else{
                    if(is_numeric($itemStr)) {
                        $this->items[$slotId] = Item::get($itemStr);
                    }
                    else{
                        $this->getPlugin()->getLogger()->info("Skipping item $itemStr due to it not being an item");
                    }
                }
            }
        }else{
            $this->getPlugin()->getLogger()->info("You have a kit without any items, seems useless.");
            $this->items = [];
        }
    }
    public function applyTo(Player $p) {
        $inventory = $p->getInventory()->getContents();
        $size = $p->getInventory()->getSize();
        foreach($this->items as $id => $item) {
            if(!is_numeric($id)) {
                switch($id) {
                    case 'helmet':
                        $id = $size;
                        break;
                    case 'chestplate':
                        $id = $size + 1;
                        break;
                    case 'leggings':
                        $id = $size + 2;
                        break;
                    case 'boots':
                        $id = $size + 3;
                        break;
                }
            }
            $inventory[$id] = $item;
        }
        $p->getInventory()->setContents($inventory);
        $p->getInventory()->sendArmorContents([$p]);
        foreach($this->commands as $command) {
            $command = str_replace("{player}", $p->getName(), $command);
            $this->getPlugin()->getServer()->dispatchCommand(new ConsoleCommandSender(), $command);
        }
        if($this->onGiveMessage !== false) {
            $p->sendMessage($this->onGiveMessage);
        }
    }
    public function getCommands() {
        return $this->commands;
    }
    public function isCommandKit() {
        return count($this->commands) > 0;
    }
    public function isActiveIn(Level $level) {
        if($this->activeIn === false) {
            return true;
        }
        else{
            return in_array($level->getName(), $this->activeIn);
        }
    }
    public function getActiveIn() {
        return $this->activeIn;
    }
    public function getCost() {
        return $this->cost;
    }
    public function isFree() {
        return $this->getCost() == 0;
    }
    public function getItems() {
        return $this->items;
    }
    public function getOnGiveMessage() {
        return $this->onGiveMessage;
    }
    public function getName() {
        return $this->name;
    }
    public function getPlugin() {
        return $this->plugin;
    }
}