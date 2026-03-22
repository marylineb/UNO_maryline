<?php

namespace App\Controller;

use App\Game\Model\Card;
use App\Game\Model\GameState;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Attribute\Route;

final class PlayerController extends AbstractController
{
    #[Route('/player', name: 'player_play')]
    public function play(Request $request, RequestStack $requestStack): RedirectResponse
    {
        $session = $requestStack->getSession();
        $gameData = $session->get('game');

        if (!$gameData) {
            return $this->redirectToRoute('home');
        }

        $gameState = GameState::fromArray($gameData);
        $player = $gameState->getPlayerById(0);
        $currentPlayer = $gameState->getCurrentPlayer();
        $topCard = $gameState->getTopCard();

        if (!$player || !$currentPlayer || !$topCard) {
            return $this->redirectToRoute('play');
        }

        if (!$player->isHuman() || $currentPlayer->getId() !== 0) {
            return $this->redirectToRoute('play');
        }

        $drawStack = $gameState->getDrawStack();

        /*
         * CAS 1 : le joueur subit un empilement de +2
         * Il ne peut répondre qu'avec un +2.
         * Sinon il pioche tout le stack puis passe son tour.
         */
        if ($drawStack > 0) {
            $hasPlusTwo = false;

            foreach ($player->getHand() as $handCard) {
                if ($handCard->getValue() === '+2') {
                    $hasPlusTwo = true;
                    break;
                }
            }

            if (!$hasPlusTwo) {
                for ($i = 0; $i < $drawStack; $i++) {
                    $drawnCard = $gameState->drawCard();

                    if ($drawnCard instanceof Card) {
                        $player->addCard($drawnCard);
                    }
                }

                $gameState->resetDrawStack();
                $gameState->nextPlayer();
                $session->set('game', $gameState->toArray());

                return $this->redirectToRoute('play');
            }
        } else {
            
            if (!$player->hasPlayableCard($topCard)) {
                $drawnCard = $gameState->drawCard();

                if ($drawnCard instanceof Card) {
                    $player->addCard($drawnCard);
                }

                $gameState->nextPlayer();
                $session->set('game', $gameState->toArray());

                return $this->redirectToRoute('play');
            }
        }

        $cardIndex = filter_var($request->query->get('id'), FILTER_VALIDATE_INT);

        if ($cardIndex === false || $cardIndex === null) {
            return $this->redirectToRoute('play');
        }

        $card = $player->getCardByIndex($cardIndex);

        if (!$card) {
            return $this->redirectToRoute('play');
        }

        /*
         * Si drawStack > 0, seule une carte +2 est autorisée.
         * Sinon, règle normale : même couleur ou même valeur.
         */
        if ($drawStack > 0) {
            if ($card->getValue() !== '+2') {
                return $this->redirectToRoute('play');
            }
        } else {
            $canPlay = $card->getColor() === $topCard->getColor()
                || $card->getValue() === $topCard->getValue();

            if (!$canPlay) {
                return $this->redirectToRoute('play');
            }
        }

        $playedCard = $player->removeCardByIndex($cardIndex);

        if (!$playedCard) {
            return $this->redirectToRoute('play');
        }

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