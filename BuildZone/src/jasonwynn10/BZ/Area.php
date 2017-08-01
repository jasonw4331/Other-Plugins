<?php
namespace jasonwynn10\BZ;

use pocketmine\math\Vector3;

class Area {
    private $name;
    /** @var  Vector3 $pos1 */
    private $pos1;
    /** @var  Vector3 $pos2 */
    private $pos2;
    /** @var Main $plugin */
    private $plugin;
    /**
     * Area constructor.
     * @param $name
     * @param $pos1
     * @param $pos2
     * @param Main $plugin
     */
    public function __construct($name, $pos1, $pos2, Main $plugin) {
        $this->name = strtolower($name);
        $this->pos1 = $pos1;
        $this->pos2 = $pos2;
        $this->plugin = $plugin;
        $this->save();
    }
    public function getName() {
        return $this->name;
    }
    public function getPos1() {
        return array($this->pos1->getX(),$this->pos1->getY(),$this->pos1->getZ());
    }
    public function getPos2() {
        return array($this->pos2->getX(),$this->pos2->getY(),$this->pos2->getZ());
    }
    public function contains(Vector3 $pos) {
        if((min($this->pos1->getX(),$this->pos2->getX()) <= $pos->getX()) && (max($this->pos1->getX(),$this->pos2->getX()) >= $pos->getX()) && (min($this->pos1->getY(),$this->pos2->getY()) <= $pos->getY()) && (max($this->pos1->getY(),$this->pos2->getY()) >= $pos->getY()) && (min($this->pos1->getZ(),$this->pos2->getZ()) <= $pos->getZ()) && (max($this->pos1->getZ(),$this->pos2->getZ()) >= $pos->getZ())) {
            return true;
        }
        return false;
    }
    public function save() {
        $this->plugin->areas[$this->name] = $this;
        return true;
    }
    public function delete() {
        unset($this->plugin->areas[$this->getName()]);
        $this->plugin->saveAreas();
        return true;
    }
}