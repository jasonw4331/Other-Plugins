<?php
namespace LostTeam;

use pocketmine\block\Block;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\TextFormat;

class BBall extends PluginBase{
	/** @var BBall $instance */
	public static $instance;
	/** @var CourtManager $courtManager */
	private $courtManager;
	/** @var Config $messages */
	public $messages;

	CONST SIGN_TITLE = '[bball]';

	public function onEnable() {
		self::$instance = $this;
		$this->getLogger()->notice(TF::GREEN."Enabled!");
		$this->saveDefaultConfig();
		$this->saveResource("messages.yml");
		$this->messages = new Config($this->getDataFolder() ."messages.yml");
		$this->courtManager = new CourtManager($this, $this->getConfig());
		$this->getServer()->getPluginManager()->registerEvents(new Events($this->courtManager), $this);
	}
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
		if(strtolower($command) === "bball") {
			if(!$sender instanceof Player) {
				$sender->sendMessage("Please use the command in-game");
				return true;
			}
			if(!$sender->hasPermission("game.play.join")) {
				$sender->sendMessage(TF::RED . "You don't have permission to use this command!");
				return true;
			}
			$this->courtManager->addNewPlayerToQueue($sender);
			return true;
		}
		if(strtolower($command) === "makecourt") {
			if(!$sender instanceof Player) {
				$sender->sendMessage("Please use the command in-game");
				return true;
			}
			if(!$sender->hasPermission("court.cmd.set")) {
				$sender->sendMessage(TF::RED . "You don't have permission to use this command!");
				return true;
			}
			$playerLocation = $sender->getLocation();
			$this->courtManager->referenceNewCourt($playerLocation);
			for($y = $playerLocation->getFloorY()-1; $y !== $playerLocation->getFloorY()+7; $y--) {
				for($x = $playerLocation->getFloorX()+8; $x !== null; $x--) {
					for($z = $playerLocation->getFloorZ()+5; $x !== null; $z--) {
						$block = Block::get(5);
						if($x == $playerLocation->getFloorX()+5 and $z == $playerLocation->getFloorZ()) {
							$z--;
						}
						if($x == $playerLocation->getFloorX()-5 and $z == $playerLocation->getFloorZ()) {
							$z--;
						}
						if($y >= $playerLocation->getFloorY()) {
							$block = Block::get(0);
							if($x == $playerLocation->getFloorX()+8 and $z == $playerLocation->getFloorZ()) {
								$block = Block::get(139);
							}
							if($x == $playerLocation->getFloorX()-8 and $z == $playerLocation->getFloorZ()) {
								$block = Block::get(139);
							}
							if($x == $playerLocation->getFloorX()+7 and $z == $playerLocation->getFloorZ() and $y == $playerLocation->getFloorY()+3) {
								$block = Block::get(30);
							}
							if($x == $playerLocation->getFloorX()-7 and $z == $playerLocation->getFloorZ() and $y == $playerLocation->getFloorY()+3) {
								$block = Block::get(30);
							}
							if($x == $playerLocation->getFloorX()+8 and $z == $playerLocation->getFloorZ()) {
								$block = Block::get(139);
							}
							if($x == $playerLocation->getFloorX()-8 and $z == $playerLocation->getFloorZ()) {
								$block = Block::get(139);
							}
						}
						$selected = new Vector3($x, $y, $z);
						if($block instanceof Block) {
							$sender->getLevel()->setBlock($selected, $block, false);
						}else{
							$this->getLogger()->error("The 'Block' wasn't a block!");
						}
					}
				}
			}
			$sender->sendMessage("[BBall] A new court has been made at your position ! There are now " . $this->courtManager->getNumberOfCourts() ." courts.");
			return true;
		}
		return false;
	}
	public static function getMessage($key) {
		return str_replace("&", TextFormat::ESCAPE, self::$instance->messages->get($key));
	}
	public function onDisable() {
		$this->getLogger()->notice(TF::GREEN."Disabled!");
	}
}