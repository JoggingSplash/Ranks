<?php

namespace ranks\profile;

use pocketmine\permission\PermissionAttachment;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use ranks\Loader;
use ranks\rank\Rank;

final class Profile {
    private ?Rank $rank = null;

    /** @var string[] */
    private array $permissions = [];

    /** @var PermissionAttachment[] */
    private array $attachments = [];

    private Config $config;

    public function __construct(
        private readonly Player $player
    ) {
        $this->config = new Config(Loader::getInstance()->getDataFolder() . 'profiles' . DIRECTORY_SEPARATOR . $this->player->getXuid() . '.json', Config::JSON);
    }

    public function getRank(): ?Rank {
        return $this->rank;
    }

    public function setRank(Rank $rank): void {
        $this->rank = $rank;
        $this->updatePermissions();
    }

    public function join(): void {
        $defaultRank = Loader::getInstance()->getRankManager()->getRank('user' ?? new \RuntimeException('Default rank user not exists'));
        $profileConfig = $this->config->getAll();

        if(!isset($profileConfig['rank'])) {
            $this->rank = $defaultRank;
            return;
        }

        $rank = Loader::getInstance()->getRankManager()->getRank($profileConfig['rank']);

        if($rank === null) {
            throw new \RuntimeException('Rank ' . $profileConfig['rank'] . ' not found.');
        }

        $this->setRank($rank);
        $permissions = explode(',', $profileConfig['perms'] ?? '');
        $this->permissions = $permissions;
    }

    public function quit(): void {
        $this->config->setAll($this->data());
        try{
            $this->config->save();
        } catch (\JsonException $e){
        }
    }

    private function updatePermissions(): void {
        $rank = $this->rank;
        $permissions = $this->permissions;
        $permissions = array_merge($rank->getPermissions(), $permissions);

        if (count($this->attachments) !== 0) {
            foreach ($this->attachments as $attachment) {
                $this->player->removeAttachment($attachment);
            }
            $this->attachments = [];
        }

        foreach ($permissions as $permission) {
            $this->attachments[] = $this->player->addAttachment(Loader::getInstance(), $permission, true);
        }
    }

    private function data(): array    {
        return [
            'player' => $this->player->getName(),
            'rank' => $this->getRank()?->getEnumName() ?? 'user',
            'perms' => implode(',', $this->getRank()?->getPermissions() ?? $this->permissions)
        ];
    }
}