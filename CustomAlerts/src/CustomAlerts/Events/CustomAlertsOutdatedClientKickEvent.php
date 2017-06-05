<?php

/*
 * CustomAlerts (v1.6) by EvolSoft
 * Developer: EvolSoft (Flavius12)
 * Website: http://www.evolsoft.tk
 * Date: 09/05/2015 01:48 PM (UTC)
 * Copyright & License: (C) 2014-2015 EvolSoft
 * Licensed under MIT (https://github.com/EvolSoft/CustomAlerts/blob/master/LICENSE)
 */

namespace CustomAlerts\Events;

use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;

class CustomAlertsOutdatedClientKickEvent extends PluginEvent {
	
	public static $handlerList = null;
	
	/** @var Player $player */
	private $player;
	
	/**
	 * @param Player $player
	 */
	public function __construct(Player $player){
		$this->player = $player;
	}

	/**
	 * Get outdated client kick event player
	 * 
	 * @return Player
	 */
	public function getPlayer(){
		return $this->player;
	}
}
?>
