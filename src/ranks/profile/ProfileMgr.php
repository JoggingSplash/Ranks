<?php

namespace ranks\profile;

use pocketmine\player\Player;
use ranks\Loader;
use WeakMap;

class ProfileMgr {
    private static WeakMap $data;

    public function get(Player $player) : Profile{
        if(!isset(self::$data)){
            /** @phpstan-var WeakMap<Player, Profile> $map */
            $map = new WeakMap();
            self::$data = $map;
        }

        return self::$data[$player] ??= new Profile($player);
    }

    public function onEnable(): void    {
        if(!is_dir(Loader::getInstance()->getDataFolder() . 'profiles')){
            @mkdir(Loader::getInstance()->getDataFolder() . 'profiles');
        }
    }
}