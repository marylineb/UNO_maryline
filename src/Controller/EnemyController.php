<?php

namespace App\Controller;

use App\Game\Model\Card;
use App\Game\Model\GameState;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Attribute\Route;

final class EnemyController extends AbstractController
{
    #[Route('/enemy', name: 'enemy_play')]
    public function play(Request $request, RequestStack $requestStack): RedirectResponse
    {
        $session = $requestStack->getSession();
        $gameData = $session->get('game');

        if (!$gameData) {
            return $this->redirectToRoute('home');
        }

        $gameState = GameState::fromArray($gameData);

        $enemyId = filter_var($request->query->get('id'), FILTER_VALIDATE_INT);

        if ($enemyId === false || $enemyId === null) {
            return $this->redirectToRoute('play');
        }

        $player = $gameState->getPlayerById($enemyId);
        $topCard = $gameState->getTopCard();
        $currentPlayer = $gameState->getCurrentPlayer();

        if (!$player || !$topCard || !$currentPlayer || $currentPlayer->getId() !== $enemyId) {
            return $this->redirectToRoute('play');
        }

        $drawStack = $gameState->getDrawStack();
        $playable = [];

        foreach ($player->getHand() as $index => $card) {
            if ($drawStack > 0) {
                if ($card->getValue() === '+2') {
                    $playable[] = $index;
                }
            } else {
                if (
                    $card->getColor() === $topCard->getColor()
                    || $card->getValue() === $topCard->getValue()
                ) {
                    $playable[] = $index;
                }
            }
        }

        if (!empty($playable)) {
            $chosen = $playable[array_rand($playable)];

            if ($player->getAiLevel() === 1) {
                foreach ($playable as $i) {
                    $candidate = $player->getCardByIndex($i);
                    if ($candidate && in_array($candidate->getValue(), ['+2', 'X', 'S'], true)) {
                        $chosen = $i;
                        break;
                    }
                }
            }

            $playedCard = $player->removeCardByIndex($chosen);

            if ($playedCard) {
                $gameState->addToDiscardPile($playedCard);

                switch ($playedCard->getValue()) {
                    case 'X':
                        $gameState->setSkipNextPlayer(true);
                        break;
                    case 'S':
                        $gameState->reverseDirection();
                        break;
                    case '+2':
                        $gameState->addDrawStack(2);
                        break;
                }
            }
        } else {
            $cardsToDraw = $drawStack > 0 ? $drawStack : 1;

            for ($i = 0; $i < $cardsToDraw; $i++) {
                $drawnCard = $gameState->drawCard();
                if ($drawnCard instanceof Card) {
                    $player->addCard($drawnCard);
                }
            }

            if ($drawStack > 0) {
                $gameState->resetDrawStack();
            }
        }

        if ($player->hasWon()) {
            $gameState->setGameOver(true);
            $gameState->setWinnerId($player->getId());
            $session->set('game', $gameState->toArray());

            return $this->redirectToRoute('end');
        }

        $gameState->nextPlayer();
        $session->set('game', $gameState->toArray());

        return $this->redirectToRoute('play');
    }
}