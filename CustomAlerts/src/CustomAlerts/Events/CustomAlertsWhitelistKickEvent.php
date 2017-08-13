<?php

/*
 * CustomAlerts (v1.6) by EvolSoft
 * Developer: EvolSoft (Flavius12)
 * Website: http://www.evolsoft.tk
 * Date: 09/05/2015 01:46 PM (UTC)
 * Copyright & License: (C) 2014-2015 EvolSoft
 * Licensed under MIT (https://github.com/EvolSoft/CustomAlerts/blob/master/LICENSE)
 */

namespace CustomAlerts\Events;

use CustomAlerts\CustomAlerts;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;

class CustomAlertsWhitelistKickEvent extends PluginEvent {
	
	public static $handlerList = null;
	
	/** @var Player $player */
	private $player;
	
	/**
	 * @param Player $player
	 * @param CustomAlerts $plugin
	 */
	public function __construct(Player $player, CustomAlerts $plugin){
		$this->player = $player;
		parent::__construct($plugin);
	}

	/**
	 * Get whitelist kick event player
	 * 
	 * @return Player
	 */
	public function getPlayer(){
		return $this->player;
	}
}
