<?php

namespace App\Controller;

use App\Game\Model\Card;
use App\Game\Model\GameState;
use App\Game\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/new', name: 'new_game')]
    public function newGame(RequestStack $requestStack): RedirectResponse
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
            new Player(0, 'Toi', 'human'),
            new Player(1, 'Ordi 1', 'computer', 1),
            new Player(2, 'Ordi 2', 'computer', 2),
            new Player(3, 'Ordi 3', 'computer', 3),
        ];

        for ($i = 0; $i < 7; $i++) {
            foreach ($players as $player) {
                $card = array_shift($deck);

                if ($card instanceof Card) {
                    $player->addCard($card);
                }
            }
        }

        do {
            $firstCard = array_shift($deck);
        } while ($firstCard instanceof Card && in_array($firstCard->getValue(), ['X', 'S', '+2'], true));

        if (!$firstCard instanceof Card) {
            return $this->redirectToRoute('home');
        }

        $gameState = new GameState($players, $deck, [$firstCard]);
        $gameState->setCurrentPlayerIndex(0);

        $session->set('game', $gameState->toArray());

        return $this->redirectToRoute('play');
    }
}