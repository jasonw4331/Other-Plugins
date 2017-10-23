<?php

namespace Primus\CrateKeys;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\ChestInventory;
use pocketmine\inventory\DoubleChestInventory;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\utils\Config;

class Main extends PluginBase {
    /** @var Config $lang */
    public $lang;
    /** String */
    private $prefix = "";
    
    public function onEnable() {
        @mkdir($this->getDataFolder());
        $this->saveResource("messages.yml");
        $this->lang = new Config($this->getDataFolder()."messages.yml", Config::YAML,
            [
                "incorrect-pattern" => "The chest must be standing on {PATTERNBLOCK} for the key to work",
                "key-receive-message" => "You've received a Crate Key.",
                "key-receive-broadcast-message" => "{PLAYER} Found a Crate Key.",
                "key-use-message" => "You have opened chest with Crate Key.",
                "occupied-chest" => "The chest must be empty when in use",
                "no-permission-for-use" => "You dont have permission to use Crate Keys",
                "no-permission-for-receive" => "You dont have permission to receive Crate Keys",
                "crate-chest-created" => "You have created a Crate Chest.",
                "no-permission-for-crate-chest-create" => "You dont have permission to create Crate Chest",
                "cant-open-double-chest" => "You cannot use Crate Key on double chests"
            ]
        );

       $this->prefix = $this->getConfig()->get('prefix');

       $this->saveDefaultConfig();
       $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
       $this->getLogger()->info("Enabled");
   }

    public function onDisable() {
     #$this->getConfig()->save();
     $this->getLogger()->info("Disabled");
   }

    public function getChance($type = null) {
        $type = $type != null ? $type : $this->getConfig()->get('chance');
        $r = rand(0, 500);
        switch($type) {
            case "tiny":
                if($r < 5)
                    return true;
                return false;
                break;
            case 'small':
                if ($r  <= 10)
                    return true;
                return false;
                break;
            case "normal":
                if ($r <= 50)
                    return true;
                return false;
                break;
            case "big":
                if ($r <= 80)
                    return true;
                return false;
                break;
            case "sure":
            default:
                return true;
        }
    }

    public function putRandomContent(Inventory $inventory) {
        if($inventory instanceof ChestInventory or $inventory instanceof DoubleChestInventory) {
            $items = $this->getRandomItems();
            for ($index = 0; $index <= $inventory->getSize(); $index++) {
                if(rand(1, $this->getConfig()->get('filtering-level')) <= 2)
                    $inventory->setItem($index, $items[rand($index, count($items) - 1)]);
            }
        }
   }

    public function getRandomItems() {
        $items = $this->getConfig()->get('possible-items');
        $rI = array();
        foreach($items as $item) {
            $item = explode(':', $item);
            $item = Item::get($item[0], $item[1], $item[2]);
            if ($item instanceof Item) {
                $rI[] = $item;
            }else{
                $this->getLogger()->alert('Invalid item given! Please make sure to use correct format in the config!');
            }

       }
        return $rI;
    }

    public function getLang($needle, $player = null) {
        $msg = $this->lang->get($needle);
        if($msg) {
            if($player instanceof Player) {
                $msg = str_replace("{PLAYER}", $player->getName(), $msg);
            }
            $msg = str_replace("{PATTERNBLOCK}", strtolower(Block::get($this->getConfig()->get('pattern-block-id'))->getName()), $msg);
            $msg = $this->prefix.' '.$msg;
            return $msg;
        }
        return "";
   }
}