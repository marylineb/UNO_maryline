<?php

namespace App\Game\Model;

class Player
{
    private int $id;
    private string $name;
    private string $type;
    private int $aiLevel;

    /**
     * @var Card[]
     */
    private array $hand = [];

    public function __construct(
        int $id,
        string $name,
        string $type = 'human',
        int $aiLevel = 0
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->setType($type);
        $this->setAiLevel($aiLevel);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isHuman(): bool
    {
        return $this->type === 'human';
    }

    public function isComputer(): bool
    {
        return $this->type === 'computer';
    }

    public function getAiLevel(): int
    {
        return $this->aiLevel;
    }

    public function isSmartBot(): bool
    {
        return $this->isComputer() && $this->aiLevel === 1;
    }

    public function isMediumBot(): bool
    {
        return $this->isComputer() && $this->aiLevel === 2;
    }

    public function isWeakBot(): bool
    {
        return $this->isComputer() && $this->aiLevel === 3;
    }

    /**
     * @return Card[]
     */
    public function getHand(): array
    {
        return $this->hand;
    }

    public function addCard(Card $card): void
    {
        $this->hand[] = $card;
    }

    public function removeCardByIndex(int $index): ?Card
    {
        if (!isset($this->hand[$index])) {
            return null;
        }

        $card = $this->hand[$index];
        array_splice($this->hand, $index, 1);

        return $card;
    }

    public function getCardByIndex(int $index): ?Card
    {
        return $this->hand[$index] ?? null;
    }

    public function getCardCount(): int
    {
        return count($this->hand);
    }

    public function hasWon(): bool
    {
        return count($this->hand) === 0;
    }

    public function hasPlayableCard(Card $topCard): bool
    {
        foreach ($this->hand as $card) {
            if (
                $card->getColor() === $topCard->getColor()
                || $card->getValue() === $topCard->getValue()
            ) {
                return true;
            }
        }

        return false;
    }

    public function getPlayableCardIndexes(Card $topCard): array
    {
        $playableIndexes = [];

        foreach ($this->hand as $index => $card) {
            if (
                $card->getColor() === $topCard->getColor()
                || $card->getValue() === $topCard->getValue()
            ) {
                $playableIndexes[] = $index;
            }
        }

        return $playableIndexes;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'aiLevel' => $this->aiLevel,
            'hand' => array_map(
                fn(Card $card) => $card->toArray(),
                $this->hand
            ),
        ];
    }

    public static function fromArray(array $data): self
    {
        $player = new self(
            $data['id'],
            $data['name'],
            $data['type'] ?? 'human',
            $data['aiLevel'] ?? 0
        );

        foreach ($data['hand'] as $cardData) {
            $player->addCard(Card::fromArray($cardData));
        }

        return $player;
    }

    private function setType(string $type): void
    {
        $allowedTypes = ['human', 'computer'];

        if (!in_array($type, $allowedTypes, true)) {
            $type = 'human';
        }

        $this->type = $type;
    }

    private function setAiLevel(int $aiLevel): void
    {
        if ($this->type === 'human') {
            $this->aiLevel = 0;
            return;
        }

        if ($aiLevel < 1 || $aiLevel > 3) {
            $aiLevel = 3;
        }

        $this->aiLevel = $aiLevel;
    }
}