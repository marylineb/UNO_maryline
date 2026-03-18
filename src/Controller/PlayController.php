<?php

namespace App\Controller;

use App\Game\Model\GameState;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PlayController extends AbstractController
{
    #[Route('/play', name: 'play')]
    public function index(RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $gameData = $session->get('game');

        if (!$gameData) {
            return $this->redirectToRoute('home');
        }

        $gameState = GameState::fromArray($gameData);

        if ($gameState->isGameOver()) {
            return $this->redirectToRoute('end');
        }

        $players = $gameState->getPlayers();
        $currentPlayer = $gameState->getCurrentPlayer();
        $topCard = $gameState->getTopCard();

        if (!$currentPlayer || !$topCard) {
            return $this->redirectToRoute('home');
        }

        return $this->render('play/index.html.twig', [
            'players' => $players,
            'currentPlayer' => $currentPlayer,
            'topCard' => $topCard,
            'drawStack' => $gameState->getDrawStack(),
        ]);
    }
}