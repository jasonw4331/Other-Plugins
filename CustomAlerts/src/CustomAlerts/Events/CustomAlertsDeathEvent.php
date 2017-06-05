<?php

/*
 * CustomAlerts (v1.6) by EvolSoft
 * Developer: EvolSoft (Flavius12)
 * Website: http://www.evolsoft.tk
 * Date: 09/05/2015 01:12 PM (UTC)
 * Copyright & License: (C) 2014-2015 EvolSoft
 * Licensed under MIT (https://github.com/EvolSoft/CustomAlerts/blob/master/LICENSE)
 */

namespace CustomAlerts\Events;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\level\Level;
use pocketmine\Player;

class CustomAlertsDeathEvent extends PluginEvent {
	
	public static $handlerList = null;
	
	/** @var Player $player */
	private $player;
	
	/** @var EntityDamageEvent|null $cause */
	private $cause;
	
	/**
	 * @param Player $player
	 * @param Level $origin
	 * @param Level $target
	 */
	public function __construct(Player $player, $cause = null){
		$this->player = $player;
		$this->cause = $cause;
	}
	
	/**
	 * Get death event player
	 *
	 * @return Player
	 */
	public function getPlayer(){
		return $this->player;
	}
	
	/**
	 * Get death event cause
	 *
	 * @return EntityDamageEvent|null
	 */
	public function getCause(){
		return $this->cause;
	}
	
}
?>
