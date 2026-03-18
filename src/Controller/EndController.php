<?php

namespace App\Controller;

use App\Game\Model\GameState;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EndController extends AbstractController
{
    #[Route('/end', name: 'end')]
    public function index(RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $gameData = $session->get('game');

        if (!$gameData) {
            return $this->redirectToRoute('home');
        }

        $gameState = GameState::fromArray($gameData);

        if (!$gameState->isGameOver()) {
            return $this->redirectToRoute('play');
        }

        $winner = $gameState->getPlayerById($gameState->getWinnerId());

        return $this->render('end/index.html.twig', [
            'winner' => $winner,
        ]);
    }
}