<?php
namespace sleep;

use pocketmine\level\Level;
use pocketmine\scheduler\PluginTask;

class SleepTask extends PluginTask {
    /** @var string $levelName */
    private $levelName;
    /** @var bool $visual */
    private $visual;

    public function __construct(Main $plugin, $levelName, bool $visual) {
        parent::__construct($plugin);
        $this->levelName = $levelName;
        $this->visual = $visual;
    }
    
    public function onRun(int $currentTick) {
        $l = $this->getOwner()->getServer()->getLevelByName($this->levelName);
        if($this->visual) {
            $time = $l->getTime();
            $l->setTime($time + (Level::TIME_FULL / 5));
        }else{
            $l->setTime(Level::TIME_FULL-2);
            $this->getOwner()->getServer()->getScheduler()->cancelTask($this->getTaskId());
        }
    }
}
