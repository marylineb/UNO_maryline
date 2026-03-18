<?php

namespace App\Game\Model;

class Card
{
    private string $color;
    private string $value;

    public function __construct(string $color, string $value)
    {
        $this->color = $color;
        $this->value = $value;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isSpecial(): bool
    {
        return in_array($this->value, ['X', 'S', '+2'], true);
    }

    public function toArray(): array
    {
        return [
            'color' => $this->color,
            'value' => $this->value,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['color'],
            $data['value']
        );
    }

    public function getLabel(): string
    {
        return $this->color . ' ' . $this->value;
    }
}