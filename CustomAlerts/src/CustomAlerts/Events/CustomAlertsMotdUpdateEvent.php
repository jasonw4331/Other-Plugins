<?php

/*
 * CustomAlerts (v1.6) by EvolSoft
 * Developer: EvolSoft (Flavius12)
 * Website: http://www.evolsoft.tk
 * Date: 09/05/2015 01:21 PM (UTC)
 * Copyright & License: (C) 2014-2015 EvolSoft
 * Licensed under MIT (https://github.com/EvolSoft/CustomAlerts/blob/master/LICENSE)
 */

namespace CustomAlerts\Events;

use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;

class CustomAlertsMotdUpdateEvent extends PluginEvent {
	
	public static $handlerList = null;
	
	/** @var string $pocketminemotd The default PocketMine motd message */
	private $pocketminemessage;
	
	/**
	 * @param string $pocketminemessage The default PocketMine motd message
	 */
	public function __construct($pocketminemessage){
		$this->pocketminemessage = $pocketminemessage;
	}
	
	/**
	 * Get default PocketMine Motd message
	 * 
	 * @return string
	 */
	public function getPocketMineMotd(){
		return $this->pocketminemessage;
	}
}
?>
