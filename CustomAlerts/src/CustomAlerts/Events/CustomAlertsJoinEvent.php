<?php

/*
 * CustomAlerts (v1.6) by EvolSoft
 * Developer: EvolSoft (Flavius12)
 * Website: http://www.evolsoft.tk
 * Date: 09/05/2015 01:13 PM (UTC)
 * Copyright & License: (C) 2014-2015 EvolSoft
 * Licensed under MIT (https://github.com/EvolSoft/CustomAlerts/blob/master/LICENSE)
 */

namespace CustomAlerts\Events;

use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;

class CustomAlertsJoinEvent extends PluginEvent {
	
	public static $handlerList = null;
	
	/** @var Player $player */
	private $player;
	
	/** @var string $pocketminemessage The default PocketMine join message */
	private $pocketminemessage;
	
	/**
	 * @param Player $player
	 * @param string $pocketminemessage The default PocketMine join message
	 */
	public function __construct(Player $player, $pocketminemessage){
		$this->player = $player;
		$this->pocketminemessage = $pocketminemessage;
	}

	/**
	 * Get join event player
	 * 
	 * @return Player
	 */
	public function getPlayer(){
		return $this->player;
	}
	
	/**
	 * Get default PocketMine join message
	 * 
	 * @return string
	 */
	public function getPocketMineJoinMessage(){
		return $this->pocketminemessage;
	}
}
?>
