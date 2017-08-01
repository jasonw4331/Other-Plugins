<?php
namespace jasonwynn10\Bancid;

use pocketmine\scheduler\PluginTask;

class TimeTask extends PluginTask {
    /** @var Main $owner */
    protected $owner;
    public function __construct(Main $owner) {
        parent::__construct($owner);
        $this->owner = $owner;
    }
    public  function onRun(int $currentTick) {
        $times = $this->owner->getConfig()->get("Times", []);
        foreach ($times as $id => $time) {
            if($time-- <= 0) {
                $this->owner->unBanPlayer($id);
            }
            $times = $this->owner->getConfig()->get("Times", []); //to prevent issues with time unbanning
            $times[$id] = $time;
        }
        $this->owner->getConfig()->get("Times", $times);
    }
}