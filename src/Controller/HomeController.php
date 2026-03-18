<?php

namespace App\Controller;

use App\Game\Model\Card;
use App\Game\Model\GameState;
use App\Game\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(RequestStack $requestStack): RedirectResponse
    {
        $session = $requestStack->getSession();

        $colors = ['Rouge', 'Bleu', 'Vert', 'Jaune'];
        $values = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'X', 'S', '+2'];

        $deck = [];
        foreach ($colors as $color) {
            foreach ($values as $value) {
                $deck[] = new Card($color, $value);
                $deck[] = new Card($color, $value);
            }
        }

        shuffle($deck);

        $players = [
            new Player(0, 'Human', true),
            new Player(1, 'Bot 1'),
            new Player(2, 'Bot 2'),
            new Player(3, 'Bot 3'),
        ];

        for ($i = 0; $i < 7; $i++) {
            foreach ($players as $player) {
                $card = array_shift($deck);
                if ($card) {
                    $player->addCard($card);
                }
            }
        }

        $firstCard = array_shift($deck);

        $gameState = new GameState($players, $deck, [$firstCard]);
        $gameState->setCurrentPlayerIndex(0);

        $session->set('game', $gameState->toArray());

        return $this->redirectToRoute('play');
    }
}