<?php

namespace ranks\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use ranks\Loader;
use ranks\rank\Rank;

class RankCommand extends Command {

    public function __construct(private Loader $loader) {
        parent::__construct('rank', 'Set rank to any player');
        $this->setUsage(TextFormat::colorize("&c/rank help"));
        $this->setPermission("ranks.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$this->testPermission($sender)) {
            return;
        }

        if (empty($args[0])) {
            $sender->getServer()->dispatchCommand($sender, 'rank help');
            return;
        }

        switch ($args[0]) {
            case 'help':
                $sender->sendMessage(TextFormat::colorize("&l&cRank Commands: &r" . "\n" . "&c/rank set <playerName> <rankName>" . "\n" . "/rank remove [playerName] " . "\n" . "/rank list"));
                break;
            case 'set':
                if(empty($args[1])) {
                    $sender->sendMessage(TextFormat::colorize("&cArgument [playerName] required"));
                    return;
                }

                $target = $sender->getServer()->getPlayerByPrefix($args[1]);

                if ($target === null) {
                    $sender->sendMessage(TextFormat::colorize("&cPlayer is not online!"));
                    return;
                }

                $profile = $this->loader->getProfileManager()->get($target);

                if (empty($args[2])) {
                    $sender->sendMessage(TextFormat::colorize("&cArgument [rank] required"));
                    return;
                }

                $rank = $this->loader->getRankManager()->getRank($args[2]);

                if ($rank === null) {
                    $sender->sendMessage(TextFormat::colorize("&cRank doesnt exist"));
                    return;
                }

                $profile->setRank($rank);
                $sender->sendMessage(TextFormat::colorize("&aYou added the " . $rank->getName() . " rank to " . $target->getName()));
                break;
            case 'remove':
                if(empty($args[1])) {
                    $sender->sendMessage(TextFormat::colorize("&cArgument [playerName] required"));
                    return;
                }

                $target = $sender->getServer()->getPlayerByPrefix($args[1]);

                if ($target === null) {
                    $sender->sendMessage(TextFormat::colorize("&cPlayer is not online!"));
                    return;
                }

                $profile = $this->loader->getProfileManager()->get($target);
                $defaultRank = $this->loader->getRankManager()->getRank('user' ?? new \RuntimeException('Default rank user not exists'));

                $profile->setRank($defaultRank);
                $sender->sendMessage(TextFormat::colorize("&aYou removed the " . $defaultRank->getName() . " rank to " . $target->getName()));
                break;
            case 'list':
                $sender->sendMessage(TextFormat::colorize("&cRank list:" . "\n" . implode("\n", array_map(fn(Rank $rank) => '&7' . $rank->getEnumName() . ': ' . $rank->getColor() . $rank->getName(), $this->loader->getRankManager()->getRanks()))));
                break;
            default:
                $sender->sendMessage(TextFormat::colorize("&cInvalid subcommand for /rank"));
                break;
        }
    }
}