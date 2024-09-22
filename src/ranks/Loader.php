<?php

namespace ranks;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use ranks\command\RankCommand;
use ranks\listener\MainHandler;
use ranks\profile\ProfileMgr;
use ranks\rank\RankMgr;

class Loader extends PluginBase {
    use SingletonTrait;

    private RankMgr $rank_manager;
    private ProfileMgr $profile_manager;


    protected function onLoad(): void {
        self::setInstance($this);
        $this->saveResource('datasource/ranks.yml');
    }

    protected function onEnable(): void {
        $this->rank_manager = new RankMgr;
        $this->rank_manager->onEnable();

        $this->profile_manager = new ProfileMgr;
        $this->profile_manager->onEnable();

        new MainHandler($this);


        $this->getServer()->getCommandMap()->register('rank', new RankCommand($this));
    }

    protected function onDisable(): void    {
        $this->rank_manager->onDisable();
    }

    public function getProfileManager(): ProfileMgr    {
        return $this->profile_manager;
    }

    public function getRankManager(): RankMgr    {
        return $this->rank_manager;
    }

}