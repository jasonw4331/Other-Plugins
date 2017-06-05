<?php

/*
 * CustomAlerts (v1.6) by EvolSoft
 * Developer: EvolSoft (Flavius12)
 * Website: http://www.evolsoft.tk
 * Date: 09/05/2015 01:14 PM (UTC)
 * Copyright & License: (C) 2014-2015 EvolSoft
 * Licensed under MIT (https://github.com/EvolSoft/CustomAlerts/blob/master/LICENSE)
 */

namespace CustomAlerts\Events;

use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;

class CustomAlertsQuitEvent extends PluginEvent {
	
	public static $handlerList = null;
	
	/** @var Player $player */
	private $player;
	
	/** @var string $pocketminemessage The default PocketMine quit message */
	private $pocketminemessage;
	
	/**
	 * @param Player $player
	 * @param string $pocketminemessage The default PocketMine quit message
	 */
	public function __construct(Player $player, $pocketminemessage){
		$this->player = $player;
		$this->pocketminemessage = $pocketminemessage;
	}
	
	/**
	 * Get quit event player
	 *
	 * @return Player
	 */
	public function getPlayer(){
		return $this->player;
	}
	
	/**
	 * Get default PocketMine quit message
	 *
	 * @return string
	 */
	public function getPocketMineQuitMessage(){
		return $this->pocketminemessage;
	}
}
?>
