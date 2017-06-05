<?php

/*
 * CustomAlerts (v1.6) by EvolSoft
 * Developer: EvolSoft (Flavius12)
 * Website: http://www.evolsoft.tk
 * Date: 09/05/2015 01:04 PM (UTC)
 * Copyright & License: (C) 2014-2015 EvolSoft
 * Licensed under MIT (https://github.com/EvolSoft/CustomAlerts/blob/master/LICENSE)
 */

namespace CustomAlerts\Events;

use pocketmine\event\plugin\PluginEvent;
use pocketmine\level\Level;
use pocketmine\Player;

class CustomAlertsWorldChangeEvent extends PluginEvent {
	
	public static $handlerList = null;
	
	/** @var Player $player */
	private $player;
	
	/** @var Level $origin */
	private $origin;
	
	/** @var Level $target */
	private $target;
	
	/**
	 * @param Player $player
	 * @param Level $origin
	 * @param Level $target
	 */
	public function __construct(Player $player, Level $origin, Level $target){
		$this->player = $player;
		$this->origin = $origin;
		$this->target = $target;
	}
	
	/**
	 * Get world change event player
	 *
	 * @return Player
	 */
	public function getPlayer(){
		return $this->player;
	}
	
	/**
	 * Get origin level
	 *
	 * @return Level
	 */
	public function getOrigin(){
		return $this->origin;
	}
	
	/**
	 * Get target level
	 *
	 * @return Level
	 */
	public function getTarget(){
		return $this->target;
	}
	
}
?>
