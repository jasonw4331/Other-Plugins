<?php
namespace WarpPads;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

use WarpPads\commands\DelWP;
use WarpPads\commands\SetWP;

class MainClass extends PluginBase implements Listener {
	/** @var string[] $wpStep1 */
	public $wpStep1 = [];

	/** @var mixed[] $wpStep2 */
	public $wpStep2 = [];

	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
		$commandMap = $this->getServer()->getCommandMap();
		$commandMap->register("setwp", new SetWP($this));
		$commandMap->register("delwp", new DelWP($this));
	}

	/**
	 * @param PlayerInteractEvent $e
	 */
	public function onInteract(PlayerInteractEvent $e) {
		$p = $e->getPlayer();
		$b = $e->getBlock();
		if($e->getAction() !== PlayerInteractEvent::LEFT_CLICK_BLOCK) {
			return;
		}
		if(isset($this->wpStep1[$p->getName()])) {
			if(!isset($this->wpStep2[$p->getName()])) {
				$this->wpStep2[$p->getName()] = [$b->getX(), $b->getY(), $b->getZ(), $b->getLevel()->getFolderName()];
				$p->sendMessage(TextFormat::GREEN."Please tap the ending Warp Pad");
				$e->setCancelled();
				return;
			}
			$cfg = $this->getConfig()->getAll();
			$cfg["Warp Pads"][$this->wpStep1[$p->getName()]] = [
				"start" => [
					"x" => $this->wpStep2[$p->getName()][0],
					"y" => $this->wpStep2[$p->getName()][1],
					"z" => $this->wpStep2[$p->getName()][2]
				],
				"end" => [
					"x" => floor($b->getX()),
					"y" => floor($b->getY()),
					"z" => floor($b->getZ()),
					"level" => $b->getLevel()->getFolderName()
				]
			];
			$this->getConfig()->setAll($cfg);
			$this->getConfig()->save();
			$p->sendMessage(TextFormat::GREEN."Warp Pad successfully set");
			unset($this->wpStep1[$p->getName()]);
			unset($this->wpStep2[$p->getName()]);
			$this->updateCommands();
			$e->setCancelled();
		}
	}

	/**
	 * @param Player[] $players
	 */
	public function updateCommands(array $players = []) {
		$players = !empty($players) ? $players : $this->getServer()->getOnlinePlayers();
		foreach ($players as $player) {
			$player->sendCommandData();
		}
	}

	/**
	 * @param PlayerMoveEvent $e
	 */
	public function onMove(PlayerMoveEvent $e) {
		$p = $e->getPlayer();
		$warps = $this->getConfig()->get("Warp Pads", []);
		foreach($warps as $wps) {
			if($wps["start"]["x"] === floor($p->getX()) and $wps["start"]["y"] === floor($p->getY()) - 1 and $wps["start"]["z"] === floor($p->getZ())) {
				$p->sendTip($this->translateColors($this->getConfig()->get("Teleport Message")));
				$endPosition = new Position($wps["end"]["x"], $wps["end"]["y"] + 1, $wps["end"]["z"], $this->getServer()->getLevelByName($wps["end"]["level"]));
				$p->teleport($endPosition);
			}
		}
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public function translateColors(string $string) : string {
		$string = str_replace("&1",TextFormat::DARK_BLUE, $string);
		$string = str_replace("&2",TextFormat::DARK_GREEN, $string);
		$string = str_replace("&3",TextFormat::DARK_AQUA, $string);
		$string = str_replace("&4",TextFormat::DARK_RED, $string);
		$string = str_replace("&5",TextFormat::DARK_PURPLE, $string);
		$string = str_replace("&6",TextFormat::GOLD, $string);
		$string = str_replace("&7",TextFormat::GRAY, $string);
		$string = str_replace("&8",TextFormat::DARK_GRAY, $string);
		$string = str_replace("&9",TextFormat::BLUE, $string);
		$string = str_replace("&0",TextFormat::BLACK, $string);
		$string = str_replace("&a",TextFormat::GREEN, $string);
		$string = str_replace("&b",TextFormat::AQUA, $string);
		$string = str_replace("&c",TextFormat::RED, $string);
		$string = str_replace("&d",TextFormat::LIGHT_PURPLE, $string);
		$string = str_replace("&e",TextFormat::YELLOW, $string);
		$string = str_replace("&f",TextFormat::WHITE, $string);
		$string = str_replace("&o",TextFormat::ITALIC, $string);
		$string = str_replace("&l",TextFormat::BOLD, $string);
		$string = str_replace("&r",TextFormat::RESET, $string);
		return $string;
	}
}