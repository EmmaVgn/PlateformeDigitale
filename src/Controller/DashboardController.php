<?php

namespace App\Controller;

use App\Repository\ProgressionRepository;
use App\Repository\UserFormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(
        UserFormationRepository $userFormationRepository,
        ProgressionRepository $progressionRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        $inscriptions = $userFormationRepository->findBy(['user' => $user]);

        $progressions = $progressionRepository->findBy(['user' => $user]);
        $progressionsByFormationId = [];

        foreach ($progressions as $progression) {
            $progressionsByFormationId[$progression->getFormation()->getId()] = $progression->getProgress();
        }

        return $this->render('dashboard/index.html.twig', [
            'inscriptions' => $inscriptions,
            'progressions' => $progressionsByFormationId,
        ]);
    }
}
