<?php
namespace jasonwynn10\GlowGen;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\Generator;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class GlowstoneGenerator extends Generator {

    /** @var Level $level */
    private $level;

    public function __construct(array $settings = []) {
        parent::__construct($settings);
    }
    public function init(ChunkManager $level, Random $random) {
        $this->level = $level;
    }
    public function getName() : string {
        return "Glowstone";
    }
    public function getSettings() : array {
        return [];
    }
    public function generateChunk(int $chunkX, int $chunkZ) {
        $chunk = $this->level->getChunk($chunkX, $chunkZ);
        if($chunkX == 0 and $chunkZ == 0) {
            for ($Z = 0; $Z < 10; ++$Z) {
                for ($X = 0; $X < 10; ++$X) {
                    if($X == 0 or $Z == 0) {
                        continue;
                    }
                    if($X == 1 and $Z < 5) {
                        continue;
                    }
                    if($X == 1 and $Z > 5) {
                        continue;
                    }
                    if($X == 2 and $Z < 3) {
                        continue;
                    }
                    if($X == 2 and $Z > 8) {
                        continue;
                    }
                    if($X == 3 and $Z < 2) {
                        continue;
                    }
                    if($X == 3 and $Z > 9) {
                        continue;
                    }
                    if($X == 4 and $Z < 3) {
                        continue;
                    }
                    if($X == 4 and $Z > 8) {
                        continue;
                    }
                    if($X == 5 and $Z < 5) {
                        continue;
                    }
                    if($X == 5 and $Z > 5) {
                        continue;
                    }
                    if($X > 6 or $Z > 6) {
                        continue;
                    }
                    $chunk->setBlock($X, 64, $Z, Block::GLOWSTONE);
                }
            }
        }
        $chunk->setX($chunkX);
        $chunk->setZ($chunkZ);
        $chunk->setGenerated();
        $this->level->setChunk($chunkX, $chunkZ, $chunk);
    }
    public function populateChunk(int $chunkX, int $chunkZ) {}
    public function getSpawn() : Vector3 {
        return new Vector3(0, 64, 0);
    }
}