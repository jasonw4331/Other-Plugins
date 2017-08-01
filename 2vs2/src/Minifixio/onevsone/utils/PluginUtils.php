<?php

namespace Minifixio\onevsone\utils;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

/**
 * Utility methods for my plugin
 */
class PluginUtils{
	/**
	 * Log on the server console
	 *
	 * @param string $message
	 */
	public static function logOnConsole(string $message){
		$logger = Server::getInstance()->getLogger();
		$logger->info("[1vs1] " . $message);
	}

	public static function sendDefaultMessage(Player $player, string $message){
		$player->sendMessage(TextFormat::GOLD . TextFormat::BOLD . "[1vs1] " . TextFormat::WHITE . $message);
	}
}



