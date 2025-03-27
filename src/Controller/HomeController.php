<?php

namespace App\Controller;

use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(FormationRepository $formationRepository): Response
    {
        $formations = $formationRepository->findPublished(3);

        return $this->render('home/index.html.twig', [
            'formations' => $formations,
        ]);
    }
}
