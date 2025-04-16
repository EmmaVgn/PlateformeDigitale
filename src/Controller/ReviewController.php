<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Entity\Formation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReviewController extends AbstractController
{
    #[Route('/avis', name: 'avis')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $ReviewRepository = $entityManager->getRepository(Review::class);

        // Récupérer les avis validés
        $reviewValidated = $ReviewRepository->findBy(['isValidé' => true]);

        // Calculer la moyenne des notes
        $moyenne = 0;
        if (count($reviewValidated) > 0) {
            $total = 0;
            foreach ($reviewValidated as $avis) {
                $total += $avis->getNote();
            }
            $moyenne = $total / count($reviewValidated);
        }

        return $this->render('avis/index.html.twig', [
            'avis' => $reviewValidated,
            'moyenne' => $moyenne,
        ]);
    }
    
}    