<?php

namespace App\Controller;

use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormationController extends AbstractController
{
    #[Route('/formations', name: 'app_formations')]
    public function index(FormationRepository $formationRepository): Response
    {
        $formations = $formationRepository->findBy(['isPublished' => true]);

        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }

    #[Route('/formation/{slug}', name: 'app_formation_show')]
    public function show(string $slug, FormationRepository $formationRepository): Response
    {
        $formation = $formationRepository->findOneBy(['slug' => $slug]);

        if (!$formation || !$formation->isPublished()) {
            throw $this->createNotFoundException('Formation introuvable.');
        }

        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }

}

