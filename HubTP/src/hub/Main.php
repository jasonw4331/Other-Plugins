<?php
namespace hub;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener {

    public function onEnable() {
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->notice(TF::GREEN."Enabled!");
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        if(strtolower($command) == "setbar") {
            if($sender instanceof Player) {
                $barSize = $sender->getInventory()->getHotbarSize();
                $slots = [];
                $items = [];
                for($i = 0; $i <= $barSize; $i++) {
                    $slots[] = $sender->getInventory()->getHotbarSlotIndex($i);
                }
                foreach($slots as $index) {
                    /** @var Item[] $items */
                    $items[] = $sender->getInventory()->getItem($index);
                }
                for($i = 0; $i <= $barSize; $i++) {
                    $arr[0] = $items[$i]->getId();
                    $arr[1] = $items[$i]->getDamage();
                    $arr[2] = $items[$i]->getCustomName();
                    $this->getConfig()->setNested("Hotbar.slot" . ($i + 1) . ".item", "{$arr[0]}:{$arr[1]}:{$arr[2]}");
                    $this->getConfig()->save();
                }
            }
        }
    }

    public function onDisable() {
        $this->getLogger()->notice(TF::GREEN."Disabled!");
    }

    /**
     * @priority MONITOR
     * @param PlayerInteractEvent $ev
     */
    public function onPlayerInteract(PlayerInteractEvent $ev) {
        $p = $ev->getPlayer();
        $i = $ev->getItem();
        $slotItems = $this->getConfig()->get("Hotbar",[
            "slot1" => ["item" => "0:0:&aLabel", "command" => "/command"],
            "slot2" => ["item" => "0:0:&cLabel", "command" => "/command"],
            "slot3" => ["item" => "0:0:label", "command" => "/command"],
            "slot4" => ["item" => "0:0:label", "command" => "/command"],
            "slot5" => ["item" => "0:0:label", "command" => "/command"],
            "slot6" => ["item" => "0:0:label", "command" => "/command"],
            "slot7" => ["item" => "0:0:label", "command" => "/command"],
            "slot8" => ["item" => "0:0:label", "command" => "/command"],
            "slot9" => ["item" => "0:0:label", "command" => "/command"],
            "slot10" => ["item" => "0:0:label", "command" => "/command"]
        ]);
        for($n = 1; $n < 11; $n++) {
            $arr = $slotItems["slot".$n];
            $itemString = $arr["item"];
            $command = $arr["command"];
            $itemStringParsed = explode(":", $itemString);
            $item = Item::get($itemStringParsed[0],$itemStringParsed[1]);
            if(isset($itemStringParsed[2])) {
                $customName = $this->applyColors($itemStringParsed[2]);
                $item->setCustomName($customName);
            }
            if($i === $item) {
                if(stripos($command, "/")) {
                    $parsedCommand = explode('/', $command);
                    $this->getServer()->dispatchCommand($p, $parsedCommand[1]);
                }else{
                    $this->getServer()->dispatchCommand($p, $command);
                }
            }
        }
    }

    /**
     * @priority MONITOR
     * @param EntityLevelChangeEvent $ev
     */
    public function onLevelChange(EntityLevelChangeEvent $ev) {
        $player = $ev->getEntity();
        $c = null;
        if($player instanceof Player) {
            if($ev->getTarget()->getName() == $this->getConfig()->get("Lobby Level","")) {
                $playerData = new Config($this->getDataFolder().$player->getName().".dat", Config::SERIALIZED, $player->getInventory()->getContents());
                $playerData->save(true);
                $items = [];
                for($n = 1; $n < 11; $n++) {
                    $itemString = $this->getConfig()->getNested("Hotbar.slot".$n.".item","");
                    if(isset($itemString)) {
                        $itemStringParsed = explode(":",$itemString);
                        if(is_integer($itemStringParsed[0]) and  is_integer($itemStringParsed[1])) {
                            $i = new Item($itemStringParsed[0], $itemStringParsed[1]);
                            if(isset($itemStringParsed[2])) {
                                $customName = $this->applyColors($itemStringParsed[2]);
                                $i->setCustomName($customName);
                            }
                            $items[] = $i;
                        }
                    }
                }
                $player->getInventory()->setContents($items);
                $player->getInventory()->resetHotbar(true);
            }
            if($ev->getOrigin()->getName() == $this->getConfig()->get("Lobby Level","")) {
                if(file_exists($this->getDataFolder().$player->getName().".dat")) {
                    $playerData = new Config($this->getDataFolder().$player->getName().".dat", Config::SERIALIZED, []);
                    $player->getInventory()->setContents($playerData->getAll());
                }
            }
        }
    }

    /**
     * @priority MONITOR
     * @param PlayerJoinEvent $ev
     */
    public function onPlayerJoin(PlayerJoinEvent $ev) {
        $player = $ev->getPlayer();
        if($player->getLevel()->getName() === $this->getConfig()->get("Lobby Level", "Lobby")) {
            $playerData = new Config($this->getDataFolder().$player->getName().".dat", Config::SERIALIZED, $player->getInventory()->getContents());
            #$playerData->save(true);
            $items = [];
            for($n = 1; $n < 11; $n++) {
                $itemString = $this->getConfig()->getNested("Hotbar.slot".$n.".item","");
                if(isset($itemString)) {
                    $itemStringParsed = explode(":",$itemString);
                    if(is_integer($itemStringParsed[0]) and  is_integer($itemStringParsed[1])) {
                        $i = new Item($itemStringParsed[0], $itemStringParsed[1]);
                        if(isset($itemStringParsed[2])) {
                            $customName = $this->applyColors($itemStringParsed[2]);
                            $i->setCustomName($customName);
                        }
                        $items[] = $i;
                    }
                }
            }
            $player->getInventory()->setContents($items);
            $player->getInventory()->resetHotbar(true);
        }
    }

    /**
     * @param string $string
     * @return string
     */
    public function applyColors(string $string) {
        $string = str_replace("&0", TextFormat::BLACK, $string);
        $string = str_replace("&1", TextFormat::DARK_BLUE, $string);
        $string = str_replace("&2", TextFormat::DARK_GREEN, $string);
        $string = str_replace("&3", TextFormat::DARK_AQUA, $string);
        $string = str_replace("&4", TextFormat::DARK_RED, $string);
        $string = str_replace("&5", TextFormat::DARK_PURPLE, $string);
        $string = str_replace("&6", TextFormat::GOLD, $string);
        $string = str_replace("&7", TextFormat::GRAY, $string);
        $string = str_replace("&8", TextFormat::DARK_GRAY, $string);
        $string = str_replace("&9", TextFormat::BLUE, $string);
        $string = str_replace("&a", TextFormat::GREEN, $string);
        $string = str_replace("&b", TextFormat::AQUA, $string);
        $string = str_replace("&c", TextFormat::RED, $string);
        $string = str_replace("&d", TextFormat::LIGHT_PURPLE, $string);
        $string = str_replace("&e", TextFormat::YELLOW, $string);
        $string = str_replace("&f", TextFormat::WHITE, $string);
        $string = str_replace("&k", TextFormat::OBFUSCATED, $string);
        $string = str_replace("&l", TextFormat::BOLD, $string);
        $string = str_replace("&m", TextFormat::STRIKETHROUGH, $string);
        $string = str_replace("&n", TextFormat::UNDERLINE, $string);
        $string = str_replace("&o", TextFormat::ITALIC, $string);
        $string = str_replace("&r", TextFormat::RESET, $string);

        return $string;
    }
}