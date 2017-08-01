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

use CustomAlerts\CustomAlerts;
use pocketmine\event\plugin\PluginEvent;

class CustomAlertsMotdUpdateEvent extends PluginEvent {
	
	public static $handlerList = null;
	
	/** @var string $pocketminemotd The default PocketMine motd message */
	private $pocketminemessage;
	
	/**
	 * @param string $pocketminemessage The default PocketMine motd message
	 * @param CustomAlerts $plugin
	 */
	public function __construct(string $pocketminemessage, CustomAlerts $plugin){
		$this->pocketminemessage = $pocketminemessage;
		parent::__construct($plugin);
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
