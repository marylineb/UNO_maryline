<?php

namespace App\Game\Model;

class GameState
{
    /**
     * @var Player[]
     */
    private array $players = [];

    /**
     * @var Card[]
     */
    private array $deck = [];

    /**
     * @var Card[]
     */
    private array $discardPile = [];

    private int $currentPlayerIndex = 0;
    private int $direction = 1;
    private int $drawStack = 0;
    private bool $skipNextPlayer = false;
    private bool $gameOver = false;
    private ?int $winnerId = null;

    /**
     * @param Player[] $players
     * @param Card[] $deck
     * @param Card[] $discardPile
     */
    public function __construct(array $players = [], array $deck = [], array $discardPile = [])
    {
        $this->players = $players;
        $this->deck = $deck;
        $this->discardPile = $discardPile;
    }

    public function getPlayers(): array
    {
        return $this->players;
    }

    public function setPlayers(array $players): void
    {
        $this->players = $players;
    }

    public function getPlayerById(int $id): ?Player
    {
        foreach ($this->players as $player) {
            if ($player->getId() === $id) {
                return $player;
            }
        }

        return null;
    }

    public function getDeck(): array
    {
        return $this->deck;
    }

    public function setDeck(array $deck): void
    {
        $this->deck = $deck;
    }

    public function drawCard(): ?Card
    {
        return array_shift($this->deck) ?: null;
    }

    public function addToDiscardPile(Card $card): void
    {
        $this->discardPile[] = $card;
    }

    public function getDiscardPile(): array
    {
        return $this->discardPile;
    }

    public function getTopCard(): ?Card
    {
        if (empty($this->discardPile)) {
            return null;
        }

        return $this->discardPile[count($this->discardPile) - 1];
    }

    public function getCurrentPlayerIndex(): int
    {
        return $this->currentPlayerIndex;
    }

    public function setCurrentPlayerIndex(int $currentPlayerIndex): void
    {
        $this->currentPlayerIndex = $currentPlayerIndex;
    }

    public function getCurrentPlayer(): ?Player
    {
        return $this->players[$this->currentPlayerIndex] ?? null;
    }

    public function getDirection(): int
    {
        return $this->direction;
    }

    public function reverseDirection(): void
    {
        $this->direction *= -1;
    }

    public function getDrawStack(): int
    {
        return $this->drawStack;
    }

    public function setDrawStack(int $drawStack): void
    {
        $this->drawStack = $drawStack;
    }

    public function addDrawStack(int $amount): void
    {
        $this->drawStack += $amount;
    }

    public function resetDrawStack(): void
    {
        $this->drawStack = 0;
    }

    public function shouldSkipNextPlayer(): bool
    {
        return $this->skipNextPlayer;
    }

    public function setSkipNextPlayer(bool $skipNextPlayer): void
    {
        $this->skipNextPlayer = $skipNextPlayer;
    }

    public function isGameOver(): bool
    {
        return $this->gameOver;
    }

    public function setGameOver(bool $gameOver): void
    {
        $this->gameOver = $gameOver;
    }

    public function getWinnerId(): ?int
    {
        return $this->winnerId;
    }

    public function setWinnerId(?int $winnerId): void
    {
        $this->winnerId = $winnerId;
    }

    public function nextPlayer(): void
    {
        $count = count($this->players);

        if ($count === 0) {
            return;
        }

        $this->currentPlayerIndex = ($this->currentPlayerIndex + $this->direction + $count) % $count;

        if ($this->skipNextPlayer) {
            $this->skipNextPlayer = false;
            $this->currentPlayerIndex = ($this->currentPlayerIndex + $this->direction + $count) % $count;
        }
    }

    public function toArray(): array
    {
        return [
            'players' => array_map(fn(Player $player) => $player->toArray(), $this->players),
            'deck' => array_map(fn(Card $card) => $card->toArray(), $this->deck),
            'discardPile' => array_map(fn(Card $card) => $card->toArray(), $this->discardPile),
            'currentPlayerIndex' => $this->currentPlayerIndex,
            'direction' => $this->direction,
            'drawStack' => $this->drawStack,
            'skipNextPlayer' => $this->skipNextPlayer,
            'gameOver' => $this->gameOver,
            'winnerId' => $this->winnerId,
        ];
    }

    public static function fromArray(array $data): self
    {
        $players = array_map(fn(array $playerData) => Player::fromArray($playerData), $data['players']);
        $deck = array_map(fn(array $cardData) => Card::fromArray($cardData), $data['deck']);
        $discardPile = array_map(fn(array $cardData) => Card::fromArray($cardData), $data['discardPile']);

        $state = new self($players, $deck, $discardPile);
        $state->setCurrentPlayerIndex($data['currentPlayerIndex']);
        $state->direction = $data['direction'];
        $state->drawStack = $data['drawStack'];
        $state->skipNextPlayer = $data['skipNextPlayer'];
        $state->gameOver = $data['gameOver'];
        $state->winnerId = $data['winnerId'];

        return $state;
    }
}