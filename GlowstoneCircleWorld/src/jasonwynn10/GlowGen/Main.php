<?php
namespace jasonwynn10\GlowGen;

use pocketmine\level\generator\Generator;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase {
    public function onLoad() {
        Generator::addGenerator(GlowstoneGenerator::class, "Glowstone");
        $this->getLogger()->notice("Enabled!");
    }
}