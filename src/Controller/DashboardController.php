<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserFormationRepository;

final class DashboardController extends AbstractController{
    #[Route('/profile', name: 'profile')]
    public function index(UserFormationRepository $userFormationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
    
        $inscriptions = $userFormationRepository->findBy(['user' => $user]);
    
        return $this->render('dashboard/index.html.twig', [
            'inscriptions' => $inscriptions,
        ]);
    }
}
