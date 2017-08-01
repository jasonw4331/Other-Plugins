<?php
namespace jasonwynn10\Loader;

use pocketmine\event\level\ChunkUnloadEvent;
use pocketmine\event\Listener;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase implements Listener {

    /** @var Chunk[][] $chunks */
    private $chunks;

    public function onEnable() {
        @mkdir($this->getDataFolder());
        new Config($this->getDataFolder()."config.yml", Config::YAML, []);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->notice(TF::GREEN."Enabled!");
    }

    /**
     * @param CommandSender $sender
     * @param Command $command
     * @param string $label
     * @param array $args
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        if(strtolower($command) == "chunk") {
            if(count($args) == 3) {
                if(strtolower($args[0]) == "save" and $sender->hasPermission("chunk.save")) {
                    if(is_integer($args[1])) {
                        if(is_integer($args[2])) {
                            if(($level = $this->getServer()->getLevelByName($args[3])) instanceof Level and !$level->isClosed()) {
                                $chunk = $level->getChunk($args[1] << 4, $args[2] << 4);
                                $this->chunks[$level->getName()][] = $chunk;
                                $chunks = $this->getConfig()->get($level->getName(), []);
                                $i = count($chunks)-1;
                                $this->getConfig()->set($level->getName(), [
                                    "chunk{$i}" => [
                                        $chunk->getX(),
                                        $chunk->getZ()
                                    ]
                                ]);
                                $this->getConfig()->save();
                                return true;
                            }else{
                                $sender->sendMessage(TF::RED."That level doesn't exist or is not loaded!");
                            }
                        }
                    }
                }elseif(strtolower($args[0]) == "unsave" and $sender->hasPermission("chunk.unsave")) {
                    if(is_integer($args[1])) {
                        if(is_integer($args[2])) {
                            if(($level = $this->getServer()->getLevelByName($args[3])) instanceof Level and !$level->isClosed()) {
                                $chunk = $level->getChunk($args[1] << 4, $args[2] << 4);
                                $key = array_search($chunk, $this->chunks[$level->getName()], true);
                                unset($this->chunks[$level->getName()][$key]);
                                $cfg = $this->getConfig()->getAll();
                                $key = array_search([$chunk->getX(), $chunk->getZ()],$cfg[$level->getName()]);
                                unset($cfg[$level->getName()][$key]);
                                $this->getConfig()->setAll($cfg);
                                $this->getConfig()->save();
                                return true;
                            }else{
                                $sender->sendMessage(TF::RED."That level doesn't exist or is not loaded!");
                            }
                        }
                    }
                }
            }
            return false;
        }
        return true;
    }

    /**
     * @priority LOWEST
     * @param ChunkUnloadEvent $ev
     */
    public function onChunkUnload(ChunkUnloadEvent $ev) {
        foreach ($this->getServer()->getLevels() as $level) {
            foreach($this->chunks[$level->getName()] as $chunk) {
                if($ev->getChunk()->getX() == $chunk->getX() and $ev->getChunk()->getZ() == $chunk->getZ()) {
                    $ev->setCancelled();
                }
            }
        }
    }

    public function onDisable() {
        $this->getLogger()->notice(TF::GREEN."Disabled!");
    }
}