<?php

namespace ranks\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\chat\LegacyRawChatFormatter;
use pocketmine\utils\TextFormat;
use ranks\Loader;

class MainHandler implements Listener {

    public function __construct(private readonly Loader $loader)    {
        $this->loader->getServer()->getPluginManager()->registerEvents($this, $this->loader);
    }

    public function handleJoin(PlayerJoinEvent $event): void {
        $profile = $this->loader->getProfileManager()->get($event->getPlayer());
        $profile->join();
    }

    public function handleQuit(PlayerQuitEvent $event): void {
        $profile = $this->loader->getProfileManager()->get($event->getPlayer());
        $profile->quit();
    }

    public function handleChat(PlayerChatEvent $event): void {
        $profile = $this->loader->getProfileManager()->get($event->getPlayer());

        $rank = $profile->getRank();
        $filter = str_replace(['&', '§'], '', $event->getMessage());

        $format = $rank->getFormat() . TextFormat::RESET . ' ' . $event->getPlayer()->getDisplayName() . ': ' . $filter;

        $event->setFormatter(new LegacyRawChatFormatter(TextFormat::colorize($format))); 
    }
}