<?php

namespace ranks\rank;

use pocketmine\utils\Config;
use ranks\Loader;

class RankMgr {

    /** @var Rank[] */
    private array $ranks = [];

    public function getRanks(): array {
        return $this->ranks;
    }

    public function addRank(string $enumName, string $name, string $color, string $format, ?array $permissions = []): void {
        $this->ranks[$enumName] = new Rank($enumName, $name, $color,$format, $permissions ?? []);
    }

    public function getRank(string $enumName): ?Rank {
        return $this->ranks[$enumName] ?? null;
    }

    public function onEnable(): void {
        $dir = Loader::getInstance()->getDataFolder() . 'datasource';

        if (!is_dir($dir)) {
            @mkdir($dir);
        }

        $config = new Config($dir . DIRECTORY_SEPARATOR . 'ranks.yml', Config::YAML);
        $data = $config->getAll();

        foreach ($data as $name => $rank) {
            $this->addRank($name, $rank['name'], $rank['color'], $rank['format'], $rank['permissions'] ?? []);
        }
    }

    public function onDisable(): void {
        $config = new Config(Loader::getInstance()->getDataFolder() . 'datasource' . DIRECTORY_SEPARATOR . 'ranks.yml', Config::YAML);

        foreach ($this->ranks as $name => $rank) {
            $config->set($name, [
                'name' => $rank->getName(),
                'color' => $rank->getColor(),
                'format' => $rank->getFormat(),
                'permissions' => $rank->getPermissions() ?? []
            ]);
        }

        try{
            $config->save();
        } catch (\JsonException $e){
            //ñeri
        }
    }
}