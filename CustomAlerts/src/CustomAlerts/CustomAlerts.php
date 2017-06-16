<?php

/*
 * CustomAlerts (v1.6) by EvolSoft
 * Developer: EvolSoft (Flavius12)
 * Website: http://www.evolsoft.tk
 * Date: 14/07/2015 01:34 PM (UTC)
 * Copyright & License: (C) 2014-2015 EvolSoft
 * Licensed under MIT (https://github.com/EvolSoft/CustomAlerts/blob/master/LICENSE)
 */

namespace CustomAlerts;

use CustomAlerts\Commands\Commands;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class CustomAlerts extends PluginBase {
    
	//About Plugin Const
	
	/** @var string PRODUCER Plugin producer */
	const PRODUCER = "EvolSoft";
	
	/** @var string VERSION Plugin version */
	const VERSION = "1.6";
	
	/** @var string MAIN_WEBSITE Plugin producer website */
	const MAIN_WEBSITE = "http://www.evolsoft.tk";
	
	//Other Const
	
	/** @var string PREFIX Plugin prefix */
	const PREFIX = "&b[&aCustom&cAlerts&b] ";
	
	//Messages
	
	/** @var string $message_motd The current motd message */
	private $message_motd;
	
	/** @var string $message_outdated_client The current outdated client message */
	private $message_outdated_client;
	
	/** @var string $message_outdated_server The current outdated server message */
	private $message_outdated_server;
	
	/** @var string $message_whitelist The current whitelist message */
	private $message_whitelist;
	
	/** @var string $message_fullserver The current full server message */
	private $message_fullserver;
	
	/** @var string $message_join The current join message */
	private $message_join;
	
	/** @var string $message_quit The current quit message */
	private $message_quit;
	
	/** @var string $message_world_change The current world change message */
	private $message_world_change;
	
	/** @var string $message_death The current death message */
	private $message_death;
	
	/** @var CustomAlerts $instance Plugin instance */
	private static $instance = null;

	/** @var Config $cfg Config instance */
	private $cfg;
	
	/**
	 * Get CustomAlerts API
	 *
	 * @return CustomAlerts CustomAlerts API
	 */
	public static function getAPI(){
		return self::$instance;
	}
	
	public function onLoad(){
		if(!self::$instance instanceof CustomAlerts){
			self::$instance = $this;
		}
	}
	
	/**
	 * Translate Minecraft colors
	 *
	 * @param string $symbol Color symbol
	 * @param string $message The message to be translated
	 *
	 * @return string The translated message
	 */
	public function translateColors($symbol, $message){
	
		$message = str_replace($symbol."0", TextFormat::BLACK, $message);
		$message = str_replace($symbol."1", TextFormat::DARK_BLUE, $message);
		$message = str_replace($symbol."2", TextFormat::DARK_GREEN, $message);
		$message = str_replace($symbol."3", TextFormat::DARK_AQUA, $message);
		$message = str_replace($symbol."4", TextFormat::DARK_RED, $message);
		$message = str_replace($symbol."5", TextFormat::DARK_PURPLE, $message);
		$message = str_replace($symbol."6", TextFormat::GOLD, $message);
		$message = str_replace($symbol."7", TextFormat::GRAY, $message);
		$message = str_replace($symbol."8", TextFormat::DARK_GRAY, $message);
		$message = str_replace($symbol."9", TextFormat::BLUE, $message);
		$message = str_replace($symbol."a", TextFormat::GREEN, $message);
		$message = str_replace($symbol."b", TextFormat::AQUA, $message);
		$message = str_replace($symbol."c", TextFormat::RED, $message);
		$message = str_replace($symbol."d", TextFormat::LIGHT_PURPLE, $message);
		$message = str_replace($symbol."e", TextFormat::YELLOW, $message);
		$message = str_replace($symbol."f", TextFormat::WHITE, $message);
	
		$message = str_replace($symbol."k", TextFormat::OBFUSCATED, $message);
		$message = str_replace($symbol."l", TextFormat::BOLD, $message);
		$message = str_replace($symbol."m", TextFormat::STRIKETHROUGH, $message);
		$message = str_replace($symbol."n", TextFormat::UNDERLINE, $message);
		$message = str_replace($symbol."o", TextFormat::ITALIC, $message);
		$message = str_replace($symbol."r", TextFormat::RESET, $message);
	
		return $message;
	}
	
    public function onEnable(){
    	@mkdir($this->getDataFolder());
    	$this->saveDefaultConfig();
    	$this->cfg = $this->getConfig()->getAll();
    	$this->getServer()->getCommandMap()->register("customalerts", new Commands($this));
    	$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    	$this->getServer()->getScheduler()->scheduleRepeatingTask(new MotdTask($this), 20);
    }
    
    //API Functions
    
    /** @var string API_VERSION CustomAlerts API version */
    const API_VERSION = "1.2";
    
    /**
     * Get CustomAlerts version
     *
     * @return string CustomAlerts version
     */
    public function getVersion(){
    	return CustomAlerts::VERSION;
    }
    
    /**
     * Get CustomAlerts API version
     *
     * @return string CustomAlerts API version
     */
    public function getAPIVersion(){
    	return CustomAlerts::API_VERSION;
    }
    
    /**
     * Check if motd is custom
     * 
     * @return boolean
     */
    public function isMotdCustom(){
    	$cfg = $this->getConfig()->getAll();
    	return $cfg["Motd"]["custom"];
    }
    
    /**
     * Get default motd message
     *
     * @return string The default motd message
     */
    public function getDefaultMotdMessage(){
    	$cfg = $this->getConfig()->getAll();
    	$message = $cfg["Motd"]["message"];
    	$message = str_replace("{MAXPLAYERS}", $this->getServer()->getMaxPlayers(), $message);
    	$message = str_replace("{TOTALPLAYERS}", count($this->getServer()->getOnlinePlayers()), $message);
    	$message = str_replace("{TIME}", date($cfg["datetime-format"]), $message);
    	return $this->translateColors("&", $message);
    }
    
    /**
     * Get current motd message
     *
     * @return string The current motd message
     */
    public function getMotdMessage(){
    	return $this->message_motd;
    }
    
    /**
     * Set current motd message
     *
     * @param string $message The message
     */
    public function setMotdMessage($message){
    	$this->message_motd = $message;
    	$this->getServer()->getNetwork()->setName($this->message_motd);
    }
    
    /**
     * Check if outdated client message is custom
     *
     * @return boolean
     */
    public function isOutdatedClientMessageCustom(){
    	$cfg = $this->getConfig()->getAll();
    	return $cfg["OutdatedClient"]["custom"];
    }

    /**
     * Get default outdated client message
     *
     * @param Player $player
     *
     * @return string The default outdated client message
     */
    public function getDefaultOutdatedClientMessage(Player $player){
    	$cfg = $this->getConfig()->getAll();
    	$message = $cfg["OutdatedClient"]["message"];
    	$message = str_replace("{PLAYER}", $player->getName(), $message);
    	$message = str_replace("{MAXPLAYERS}", $this->getServer()->getMaxPlayers(), $message);
    	$message = str_replace("{TOTALPLAYERS}", count($this->getServer()->getOnlinePlayers()), $message);
    	$message = str_replace("{TIME}", date($cfg["datetime-format"]), $message);
    	return $this->translateColors("&", $message);
    }
    
    /**
     * Get current outdated client message
     *
     * @return string The current outdated client message
     */
    public function getOutdatedClientMessage(){
    	return $this->message_outdated_client;
    }
    
    /**
     * Set current outdated client message
     *
     * @param string $message The message
     */
    public function setOutdatedClientMessage($message){
    	$this->message_outdated_client = $message;
    }
    
    /**
     * Check if outdated server message is custom
     *
     * @return boolean
     */
    public function isOutdatedServerMessageCustom(){
    	$cfg = $this->getConfig()->getAll();
    	return $cfg["OutdatedServer"]["custom"];
    }
    
    /**
     * Get default outdated server message
     *
	 * @param Player $player
	 *
     * @return string The default outdated server message
     */
    public function getDefaultOutdatedServerMessage(Player $player){
    	$cfg = $this->getConfig()->getAll();
    	$message = $cfg["OutdatedServer"]["message"];
    	$message = str_replace("{PLAYER}", $player->getName(), $message);
    	$message = str_replace("{MAXPLAYERS}", $this->getServer()->getMaxPlayers(), $message);
    	$message = str_replace("{TOTALPLAYERS}", count($this->getServer()->getOnlinePlayers()), $message);
    	$message = str_replace("{TIME}", date($cfg["datetime-format"]), $message);
    	return $this->translateColors("&", $message);
    }
    
    /**
     * Get current outdated server message
     *
     * @return string The current outdated server message
     */
    public function getOutdatedServerMessage(){
    	return $this->message_outdated_server;
    }
    
    /**
     * Set current outdated server message
     *
     * @param string $message The message
     */
    public function setOutdatedServerMessage($message){
    	$this->message_outdated_server = $message;
    }
    
    /**
     * Check if whitelist message is custom
     *
     * @return boolean
     */
    public function isWhitelistMessageCustom(){
    	$cfg = $this->getConfig()->getAll();
    	return $cfg["WhitelistedServer"]["custom"];
    }
    
    /**
     * Get default whitelist message
     *
	 * @param Player $player
	 *
     * @return string The default whitelist message
     */
    public function getDefaultWhitelistMessage(Player $player){
    	$cfg = $this->getConfig()->getAll();
    	$message = $cfg["WhitelistedServer"]["message"];
    	$message = str_replace("{PLAYER}", $player->getName(), $message);
    	$message = str_replace("{MAXPLAYERS}", $this->getServer()->getMaxPlayers(), $message);
    	$message = str_replace("{TOTALPLAYERS}", count($this->getServer()->getOnlinePlayers()), $message);
    	$message = str_replace("{TIME}", date($cfg["datetime-format"]), $message);
    	return $this->translateColors("&", $message);
    }
    
    /**
     * Get current whitelist message
     *
     * @return string The current whitelist message
     */
    public function getWhitelistMessage(){
    	return $this->message_whitelist;
    }
    
    /**
     * Set current whitelist message
     *
     * @param string $message The message
     */
    public function setWhitelistMessage($message){
    	$this->message_whitelist = $message;
    }
    
    /**
     * Check if full server message is custom
     *
     * @return boolean
     */
    public function isFullServerMessageCustom(){
    	$cfg = $this->getConfig()->getAll();
    	return $cfg["FullServer"]["custom"];
    }
    
    /**
     * Get default full server message
     *
	 * @param Player $player
	 *
     * @return string The default full server message
     */
    public function getDefaultFullServerMessage(Player $player){
    	$cfg = $this->getConfig()->getAll();
    	$message = $cfg["FullServer"]["message"];
    	$message = str_replace("{PLAYER}", $player->getName(), $message);
    	$message = str_replace("{MAXPLAYERS}", $this->getServer()->getMaxPlayers(), $message);
    	$message = str_replace("{TOTALPLAYERS}", count($this->getServer()->getOnlinePlayers()), $message);
    	$message = str_replace("{TIME}", date($cfg["datetime-format"]), $message);
    	return $this->translateColors("&", $message);
    }
    
    /**
     * Get current full server message
     *
     * @return string The current full server message
     */
    public function getFullServerMessage(){
    	return $this->message_fullserver;
    }
    
    /**
     * Set current full server message
     *
     * @param string $message The message
     */
    public function setFullServerMessage($message){
    	$this->message_fullserver = $message;
    }
    
    
    /**
     * Get if default first join message is enabled
     * 
     * @return boolean
     */
    public function isDefaultFirstJoinMessageEnabled(){
    	$cfg = $this->getConfig()->getAll();
    	return $cfg["FirstJoin"]["enable"];
    }

    /**
     * Get default first join message
     * 
     * @param Player $player
     * 
     * @return string The default first join message
     */
    public function getDefaultFirstJoinMessage(Player $player){
    	$cfg = $this->getConfig()->getAll();
    	$message = $cfg["FirstJoin"]["message"];
    	$message = str_replace("{PLAYER}", $player->getName(), $message);
    	$message = str_replace("{MAXPLAYERS}", $this->getServer()->getMaxPlayers(), $message);
    	$message = str_replace("{TOTALPLAYERS}", count($this->getServer()->getOnlinePlayers()), $message);
    	$message = str_replace("{TIME}", date($cfg["datetime-format"]), $message);
    	return $this->translateColors("&", $message);
    }
    
    /**
     * Check if default join message is custom
     * 
     * @return boolean
     */
    public function isDefaultJoinMessageCustom(){
    	$cfg = $this->getConfig()->getAll();
    	return $cfg["Join"]["custom"];
    }
    
    /**
     * Check if default join message is hidden
     *
     * @return boolean
     */
    public function isDefaultJoinMessageHidden(){
    	$cfg = $this->getConfig()->getAll();
    	return $cfg["Join"]["hide"];
    }
    
    /**
     * Get default join message
     * 
     * @param Player $player
     * 
     * @return string The default join message
     */
    public function getDefaultJoinMessage(Player $player){
    	$cfg = $this->getConfig()->getAll();
    	$message = $cfg["Join"]["message"];
    	$message = str_replace("{PLAYER}", $player->getName(), $message);
    	$message = str_replace("{MAXPLAYERS}", $this->getServer()->getMaxPlayers(), $message);
    	$message = str_replace("{TOTALPLAYERS}", count($this->getServer()->getOnlinePlayers()), $message);
    	$message = str_replace("{TIME}", date($cfg["datetime-format"]), $message);
    	return $this->translateColors("&", $message);
    }
    
    /**
     * Get current join message
     * 
     * @return string The current join message
     */
    public function getJoinMessage(){
    	return $this->message_join;
    }
    
    /**
     * Set current join message
     * 
     * @param string $message The message
     */
    public function setJoinMessage($message){
    	$this->message_join = $message;
    }
    
    /**
     * Check if default quit message is custom
     *
     * @return boolean
     */
    public function isQuitCustom(){
    	$cfg = $this->getConfig()->getAll();
    	return $cfg["Quit"]["custom"];
    }
    
    /**
     * Check if default quit message is hidden
     *
     * @return boolean
     */
    public function isQuitHidden(){
    	$cfg = $this->getConfig()->getAll();
    	return $cfg["Quit"]["hide"];
    }
    
    /**
     * Get default quit message
     * 
     * @param Player $player
     * 
     * @return string The default quit message
     */
    public function getDefaultQuitMessage(Player $player){
    	$cfg = $this->getConfig()->getAll();
    	$message = $cfg["Quit"]["message"];
    	$message = str_replace("{PLAYER}", $player->getName(), $message);
    	$message = str_replace("{MAXPLAYERS}", $this->getServer()->getMaxPlayers(), $message);
    	$message = str_replace("{TOTALPLAYERS}", count($this->getServer()->getOnlinePlayers()), $message);
    	$message = str_replace("{TIME}", date($cfg["datetime-format"]), $message);
    	return $this->translateColors("&", $message);
    }
    
    /**
     * Get current quit message
     * 
     * @return string The current quit message
     */
    public function getQuitMessage(){
    	return $this->message_quit;
    }
    
    /**
     * Set current quit message
     * 
     * @param string $message The message
     */
    public function setQuitMessage($message){
    	$this->message_quit = $message;
    }
    
    /**
     * Get if default world change message is enabled
     *
     * @return boolean
     */
    public function isDefaultWorldChangeMessageEnabled(){
    	$cfg = $this->getConfig()->getAll();
    	return $cfg["WorldChange"]["enable"];
    }
    
    /**
     * Get default quit message
     *
     * @param Player $player
     * @param Level $origin
     * @param Level $target
     *
     * @return string The default world change message
     */
    public function getDefaultWorldChangeMessage(Player $player, Level $origin, Level $target){
    	$cfg = $this->getConfig()->getAll();
    	$message = $cfg["WorldChange"]["message"];
    	$message = str_replace("{ORIGIN}", $origin->getName(), $message);
    	$message = str_replace("{TARGET}", $target->getName(), $message);
    	$message = str_replace("{PLAYER}", $player->getName(), $message);
    	$message = str_replace("{MAXPLAYERS}", $this->getServer()->getMaxPlayers(), $message);
    	$message = str_replace("{TOTALPLAYERS}", count($this->getServer()->getOnlinePlayers()), $message);
    	$message = str_replace("{TIME}", date($cfg["datetime-format"]), $message);
    	return $this->translateColors("&", $message);
    }
    
    /**
     * Get current world change message
     *
     * @return string The current world change message
     */
    public function getWorldChangeMessage(){
    	return $this->message_world_change;
    }
    
    /**
     * Set current world change message
     *
     * @param string $message The message
     */
    public function setWorldChangeMessage($message){
    	$this->message_world_change = $message;
    }
    
    /**
     * Check if death messages are custom
     * 
     * @param EntityDeathEvent $cause Check message by cause
     *
     * @return boolean
     */
    public function isDeathCustom($cause = null){
        $cfg = $this->getConfig()->getAll();
        if($cause instanceof EntityDamageEvent){
        	if($cause->getCause() == EntityDamageEvent::CAUSE_CONTACT){
        		return $cfg["Death"]["death-contact-message"]["custom"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_ENTITY_ATTACK){
        		return $cfg["Death"]["kill-message"]["custom"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_PROJECTILE){
        		return $cfg["Death"]["death-projectile-message"]["custom"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_SUFFOCATION){
        		return $cfg["Death"]["death-suffocation-message"]["custom"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_FALL){
        		return $cfg["Death"]["death-fall-message"]["custom"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_FIRE){
        		return $cfg["Death"]["death-fire-message"]["custom"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_FIRE_TICK){
        		return $cfg["Death"]["death-on-fire-message"]["custom"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_LAVA){
        		return $cfg["Death"]["death-lava-message"]["custom"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_DROWNING){
        		return $cfg["Death"]["death-drowning-message"]["custom"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_ENTITY_EXPLOSION || $cause->getCause() == EntityDamageEvent::CAUSE_BLOCK_EXPLOSION){
        		return $cfg["Death"]["death-explosion-message"]["custom"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_VOID){
        		return $cfg["Death"]["death-void-message"]["custom"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_SUICIDE){ 
        		return $cfg["Death"]["death-suicide-message"]["custom"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_MAGIC){
        		return $cfg["Death"]["death-magic-message"]["custom"];
        	}else{
        		return $cfg["Death"]["custom"];
        	}
    	}else{
    		return $cfg["Death"]["custom"];
    	}
    }
    
    /**
     * Check if death messages are hidden
     * 
     * @param EntityDamageEvent $cause Check message by cause
     *
     * @return boolean
     */
    public function isDeathHidden($cause = null){
    	$cfg = $this->getConfig()->getAll();
        if($cause instanceof EntityDamageEvent){
        	if($cause->getCause() == EntityDamageEvent::CAUSE_CONTACT){
        		return $cfg["Death"]["death-contact-message"]["hide"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_ENTITY_ATTACK){
        		return $cfg["Death"]["kill-message"]["hide"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_PROJECTILE){
        		return $cfg["Death"]["death-projectile-message"]["hide"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_SUFFOCATION){
        		return $cfg["Death"]["death-suffocation-message"]["hide"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_FALL){
        		return $cfg["Death"]["death-fall-message"]["hide"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_FIRE){
        		return $cfg["Death"]["death-fire-message"]["hide"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_FIRE_TICK){
        		return $cfg["Death"]["death-on-fire-message"]["hide"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_LAVA){
        		return $cfg["Death"]["death-lava-message"]["hide"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_DROWNING){
        		return $cfg["Death"]["death-drowning-message"]["hide"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_ENTITY_EXPLOSION || $cause->getCause() == EntityDamageEvent::CAUSE_BLOCK_EXPLOSION){
        		return $cfg["Death"]["death-explosion-message"]["hide"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_VOID){
        		return $cfg["Death"]["death-void-message"]["hide"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_SUICIDE){ 
        		return $cfg["Death"]["death-suicide-message"]["hide"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_MAGIC){
        		return $cfg["Death"]["death-magic-message"]["hide"];
        	}else{
        		return $cfg["Death"]["hide"];
        	}
    	}else{
    		return $cfg["Death"]["hide"];
    	}
    }
    
    /**
     * Get default death message related to the specified cause
     *
     * @param Player $player
     * @param EntityDamageEvent $cause Get message related to the specified cause
     *
     * @return string The default death message related to the specified cause
     */
    public function getDefaultDeathMessage(Player $player, $cause = null){
    	$cfg = $this->getConfig()->getAll();
        if($cause instanceof EntityDamageEvent){
        	if($cause->getCause() == EntityDamageEvent::CAUSE_CONTACT){
        		$message = $cfg["Death"]["death-contact-message"]["message"];
        		if($cause instanceof EntityDamageByBlockEvent){
        			$message = str_replace("{BLOCK}", $cause->getDamager()->getName(), $message);
        		}else{
        			$message = str_replace("{BLOCK}", "Unknown", $message);
        		}
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_ENTITY_ATTACK){
        	    /** @var EntityDamageByEntityEvent $cause */
        		$message = $cfg["Death"]["kill-message"]["message"];
        	    $killer = $cause->getDamager();
        		if($killer instanceof Living){
        			$message = str_replace("{KILLER}", $killer->getName(), $message);
        		}else{
        			$message = str_replace("{KILLER}", "Unknown", $message);
        		}
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_PROJECTILE){
        		$message = $cfg["Death"]["death-projectile-message"]["message"];
        		/** @var EntityDamageByChildEntityEvent $cause */
        		$killer = $cause->getDamager();
        		if($killer instanceof Living){
        			$message = str_replace("{KILLER}", $killer->getName(), $message);
        		}else{
        			$message = str_replace("{KILLER}", "Unknown", $message);
        		}
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_SUFFOCATION){
        		$message = $cfg["Death"]["death-suffocation-message"]["message"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_FALL){
        		$message = $cfg["Death"]["death-fall-message"]["message"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_FIRE){
        		$message = $cfg["Death"]["death-fire-message"]["message"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_FIRE_TICK){
        		$message = $cfg["Death"]["death-on-fire-message"]["message"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_LAVA){
        		$message = $cfg["Death"]["death-lava-message"]["message"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_DROWNING){
        		$message = $cfg["Death"]["death-drowning-message"]["message"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_ENTITY_EXPLOSION || $cause->getCause() == EntityDamageEvent::CAUSE_BLOCK_EXPLOSION){
        		$message = $cfg["Death"]["death-explosion-message"]["message"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_VOID){
        		$message = $cfg["Death"]["death-void-message"]["message"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_SUICIDE){
        		$message = $cfg["Death"]["death-suicide-message"]["message"];
        	}elseif($cause->getCause() == EntityDamageEvent::CAUSE_MAGIC){
        		$message = $cfg["Death"]["death-magic-message"]["message"];
        	}else{
        		$message = $cfg["Death"]["message"];
        	}
    	}else{
    		$message = $cfg["Death"]["message"];
    	}
    	$message = str_replace("{PLAYER}", $player->getName(), $message);
    	$message = str_replace("{MAXPLAYERS}", $this->getServer()->getMaxPlayers(), $message);
    	$message = str_replace("{TOTALPLAYERS}", count($this->getServer()->getOnlinePlayers()), $message);
    	$message = str_replace("{TIME}", date($cfg["datetime-format"]), $message);
    	return $this->translateColors("&", $message);
    }
    
    /**
     * Get current death message
     *
     * @return string The current death message
     */
    public function getDeathMessage(){
    	return $this->message_death;
    }
    
    /**
     * Set current death message
     *
     * @param string $message The message
     */
    public function setDeathMessage($message){
    	$this->message_death = $message;
    }

}
