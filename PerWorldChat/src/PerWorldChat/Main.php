<?php
namespace PerWorldChat;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener {
	const PREFIX = "&a[&bPer&cWorld&dChat&a] ";

	/**
	 * @param string $symbol
	 * @param string $message
	 *
	 * @return mixed|string
	 */
	public function translateColors(string $symbol, string $message){
		
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
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	/**
	 * @param string $level
	 *
	 * @return bool
	 */
	public function isChatDisabled(string $level){
		return array_search($level, $this->getConfig()->getAll()["disabled-in-worlds"]) !== false;
	}

	/**
	 * @priority LOW
	 * @ignoreCancelled false
	 *
	 * @param PlayerChatEvent $event
	 */
	public function onChat(PlayerChatEvent $event){
		if($event->isCancelled())
			return;
		$player = $event->getPlayer();
		$recipients = $event->getRecipients();
		foreach($recipients as $key => $recipient){
			if($recipient instanceof Player){
				if($recipient->getLevel()->getName() != $player->getLevel()->getName()){
					unset($recipients[$key]);
				}
			}
		}
		$event->setRecipients($recipients);
		//Checking Chat Disabled
		if($this->isChatDisabled($player->getLevel()->getName())){
			//Check if log-chat-disabled is enabled
			if($this->getConfig()->getAll()["chat-disabled-message"] == true){
				$player->sendMessage($this->translateColors("&", Main::PREFIX . "&cChat is disabled in this world"));
			}
			$event->setCancelled();
		}
	}
}