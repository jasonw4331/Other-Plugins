<?php

/*
 * CustomAlerts (v1.6) by EvolSoft
 * Developer: EvolSoft (Flavius12)
 * Website: http://www.evolsoft.tk
 * Date: 28/05/2015 04:29 PM (UTC)
 * Copyright & License: (C) 2014-2015 EvolSoft
 * Licensed under MIT (https://github.com/EvolSoft/CustomAlerts/blob/master/LICENSE)
 */

namespace CustomAlerts;

use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo as Info;
use pocketmine\Player;
use pocketmine\Server;

use CustomAlerts\Events\CustomAlertsDeathEvent;
use CustomAlerts\Events\CustomAlertsFullServerKickEvent;
use CustomAlerts\Events\CustomAlertsJoinEvent;
use CustomAlerts\Events\CustomAlertsMotdUpdateEvent;
use CustomAlerts\Events\CustomAlertsOutdatedClientKickEvent;
use CustomAlerts\Events\CustomAlertsOutdatedServerKickEvent;
use CustomAlerts\Events\CustomAlertsQuitEvent;
use CustomAlerts\Events\CustomAlertsWhitelistKickEvent;
use CustomAlerts\Events\CustomAlertsWorldChangeEvent;

class EventListener implements Listener {
	/** @var CustomAlerts $plugin */
	private $plugin;

	public function __construct(CustomAlerts $plugin){
        $this->plugin = $plugin;
    }
    
    public function onReceivePacket(DataPacketReceiveEvent $event){
		if($this->plugin->isOutdatedClientMessageCustom() and $this->plugin->isOutdatedServerMessageCustom()) {
			return;
		}
    	$player = $event->getPlayer();
    	/** @var DataPacket $packet */
    	$packet = $event->getPacket();
    	if($packet->pid() == Info::LOGIN_PACKET){
    		/** @var LoginPacket $packet */
    		if($packet->protocol < Info::CURRENT_PROTOCOL){
    			//Check if outdated client message is custom
    			if($this->plugin->isOutdatedClientMessageCustom()){
    				$this->plugin->setOutdatedClientMessage($this->plugin->getDefaultOutdatedClientMessage($player));
    			}
    			//Outdated Client Kick Event
    			$this->plugin->getServer()->getPluginManager()->callEvent(new CustomAlertsOutdatedClientKickEvent($player, $this->plugin));
    			//Check if Outdated Client message is not empty
    			if($this->plugin->getOutdatedClientMessage() != null){
    				$player->close("", $this->plugin->getOutdatedClientMessage());
    				$event->setCancelled();
    			}
    		}elseif($packet->protocol > Info::CURRENT_PROTOCOL){
    			//Check if outdated server message is custom
    			if($this->plugin->isOutdatedServerMessageCustom()){
    				$this->plugin->setOutdatedServerMessage($this->plugin->getDefaultOutdatedServerMessage($player));
    			}
    			//Outdated Server Kick Event
    			$this->plugin->getServer()->getPluginManager()->callEvent(new CustomAlertsOutdatedServerKickEvent($player, $this->plugin));
    			//Check if Outdated Server message is not empty
    			if($this->plugin->getOutdatedServerMessage() != null){
    				$player->close("", $this->plugin->getOutdatedServerMessage());
    				$event->setCancelled();
    			}
    		}
    	}
    }
    
    /**
     * @param PlayerPreLoginEvent $event
     *
     * @priority HIGHEST
     */
    public function onPlayerPreLogin(PlayerPreLoginEvent $event){
    	$player = $event->getPlayer();
    	if(count($this->plugin->getServer()->getOnlinePlayers()) - 1 < $this->plugin->getServer()->getMaxPlayers()){
    		if(!$this->plugin->getServer()->isWhitelisted($event->getPlayer()->getName())){
    			//Check if Whitelist message is custom
    			if($this->plugin->isWhitelistMessageCustom()){
    				$this->plugin->setWhitelistMessage($this->plugin->getDefaultWhitelistMessage($player));
    			}
    			//Whitelist Kick Event
    			$this->plugin->getServer()->getPluginManager()->callEvent(new CustomAlertsWhitelistKickEvent($player, $this->plugin));
    			//Check if Whitelist message is not empty
    			if($this->plugin->getWhitelistMessage() != null){
    				$player->close("", $this->plugin->getWhitelistMessage());
    				$event->setCancelled();
    			}
    		}
    	}else{
    		//Check if Full Server message is custom
    		if($this->plugin->isFullServerMessageCustom()){
    			$this->plugin->setFullServerMessage($this->plugin->getDefaultFullServerMessage($player));
    		}
    		//Full Server Kick Event
    		$this->plugin->getServer()->getPluginManager()->callEvent(new CustomAlertsFullServerKickEvent($player, $this->plugin));
    		//Check if Full Server message is not empty
    		if($this->plugin->getFullServerMessage() != null){
    			$player->close("", $this->plugin->getFullServerMessage());
    			$event->setCancelled();
    		}
    	}
    }
    
    /**
     * @param PlayerJoinEvent $event
     *
     * @priority MONITOR
     */
    public function onPlayerJoin(PlayerJoinEvent $event){
    	$player = $event->getPlayer();
    	//Motd Update
    	//Check if Motd message is custom
    	if($this->plugin->isMotdCustom()){
    		$this->plugin->setMotdMessage($this->plugin->getDefaultMotdMessage());
    	}else{
    		$this->plugin->setMotdMessage($this->plugin->translateColors("&", $this->plugin->getServer()->getMotd()));
    	}
    	//Motd Update Event
    	$this->plugin->getServer()->getPluginManager()->callEvent(new CustomAlertsMotdUpdateEvent($this->plugin->getServer()->getMotd(), $this->plugin));
    	$this->plugin->getServer()->getNetwork()->setName($this->plugin->getMotdMessage());
    	//Join Message
    	$status = 0;
    	$this->plugin->setJoinMessage($event->getJoinMessage());
    	//Get First Join
    	if(!$player->hasPlayedBefore()){
    		//Check if FirstJoin message is enabled
    		if($this->plugin->isDefaultFirstJoinMessageEnabled()){
    			$this->plugin->setJoinMessage($this->plugin->getDefaultFirstJoinMessage($player));
    			$status = 1;
    		}
    	}
    	//Default Join Message
    	if($status == 0){
    		//Check if Join message is hidden
    		if($this->plugin->isDefaultJoinMessageHidden()){
    			$this->plugin->setJoinMessage("");
    		}else{
    			//Check if Join message is custom
    			if($this->plugin->isDefaultJoinMessageCustom()){
    				$this->plugin->setJoinMessage($this->plugin->getDefaultJoinMessage($player));
    			}
    		}
    	}
    	//Join Event
    	$this->plugin->getServer()->getPluginManager()->callEvent(new CustomAlertsJoinEvent($player, $event->getJoinMessage(), $this->plugin));
    	$event->setJoinMessage($this->plugin->getJoinMessage());
    }
    
    /**
     * @param PlayerQuitEvent $event
     *
     * @priority HIGHEST
     */
    public function onPlayerQuit(PlayerQuitEvent $event){
    	 $player = $event->getPlayer();
    	 //Motd Update
    	 if($this->plugin->isMotdCustom()){
    	 	$this->plugin->setMotdMessage($this->plugin->getDefaultMotdMessage());
    	 }else{
    	 	$this->plugin->setMotdMessage($this->plugin->translateColors("&", $this->plugin->getServer()->getMotd()));
    	 }
    	 //Motd Update Event
    	 $this->plugin->getServer()->getPluginManager()->callEvent(new CustomAlertsMotdUpdateEvent($this->plugin->getServer()->getMotd(), $this->plugin));
    	 $this->plugin->getServer()->getNetwork()->setName($this->plugin->getMotdMessage());
    	 $this->plugin->setQuitMessage($event->getQuitMessage());
    	 //Check if Quit message is hidden
    	 if($this->plugin->isQuitHidden()){
    	 	$this->plugin->setQuitMessage("");
    	 }else{
    	 	//Check if Quit message is custom
    	 	if($this->plugin->isQuitCustom()){
    	 		$this->plugin->setQuitMessage($this->plugin->getDefaultQuitMessage($player));
    	 	}
    	 }
    	 //Quit Event
    	 $this->plugin->getServer()->getPluginManager()->callEvent(new CustomAlertsQuitEvent($player, $event->getQuitMessage(), $this->plugin));
    	 $event->setQuitMessage($this->plugin->getQuitMessage());
    }

    /**
     * @param EntityLevelChangeEvent $event
	 * 
	 * @priority MONITOR
     */
    public function onWorldChange(EntityLevelChangeEvent $event){
    	$entity = $event->getEntity();
    	$this->plugin->setWorldChangeMessage("");
    	//Check if the Entity is a Player
    	if($entity instanceof Player){
    		$player = $entity;
    		$origin = $event->getOrigin();
    		$target = $event->getTarget();
    		//Check if Default WorldChange Message is enabled
    		if($this->plugin->isDefaultWorldChangeMessageEnabled()){
    			$this->plugin->setWorldChangeMessage($this->plugin->getDefaultWorldChangeMessage($player, $origin, $target));
    		}
    	    //WorldChange Event
    	    $this->plugin->getServer()->getPluginManager()->callEvent(new CustomAlertsWorldChangeEvent($player, $origin, $target, $this->plugin));
    		if($this->plugin->getWorldChangeMessage() != ""){
    			Server::getInstance()->broadcastMessage($this->plugin->getWorldChangeMessage());
    		}
    	}
    }
    
    
    /**
     * @param PlayerDeathEvent $event
     *
     * @priority MONITOR
     */
    public function onPlayerDeath(PlayerDeathEvent $event){
    	$player = $event->getEntity();
    	$this->plugin->setDeathMessage($event->getDeathMessage());
    	if($player instanceof Player){
    		$cause = $player->getLastDamageCause();
    		if($this->plugin->isDeathHidden($cause)){
    			$this->plugin->setDeathMessage("");
    		}else{
    			//Check if Death message is custom
    			if($this->plugin->isDeathCustom($cause)){
    				$this->plugin->setDeathMessage($this->plugin->getDefaultDeathMessage($player, $cause));
    			}
    		}
            //Death Event
    	    $this->plugin->getServer()->getPluginManager()->callEvent(new CustomAlertsDeathEvent($player, $cause, $this->plugin));
    		$event->setDeathMessage($this->plugin->getDeathMessage());
    	}
    }
}