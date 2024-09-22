<?php

namespace ranks\rank;

readonly final class Rank {

    public function __construct(
        private string $enumName,
        private string $name,
        private string $color,
        private string $format,
        private array $permissions = []
    ) {
    }

    public function getEnumName(): string {
        return $this->enumName;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getColor(): string {
        return $this->color;
    }

    public function getFormat(): string {
        return $this->format;
    }

    public function getPermissions(): array {
        return $this->permissions;
    }
}