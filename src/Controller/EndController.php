<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EndController extends AbstractController
{
    #[Route('/end', name: 'app_end')]
    public function index(): Response
    {
        return $this->render('end/index.html.twig', [
            'controller_name' => 'EndController',
        ]);
    }
}
