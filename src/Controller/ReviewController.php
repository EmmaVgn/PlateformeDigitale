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
    #[Route('/formations/{id}/review', name: 'formation_review ', methods: ['GET', 'POST'])]
    public function comment(Request $request, Formation $formation, EntityManagerInterface $em): Response
    {
        // Assurez-vous que l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
    
        // Créer un nouveau commentaire
        $review = new Review();
        $review ->setUser($user); // Lier l'utilisateur connecté au commentaire
        $review ->setFormation($formation); // Associer le commentaire a la formation
    
        $form = $this->createForm(ReviewType::class, $review );
        $form->handleRequest($request);
    
        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Ajoutez une vérification pour rating ici
                if (null === $review->getRating()) {
                    $review->setRating(0); // Valeur par défaut si rating est null
                }
                $review->setCreatedAt(new \DateTimeImmutable());
                $review->setIsValidated(false); // Par défaut, les commentaires ne sont pas validés
    
                $em->persist($review);
                $em->flush();
    
                $this->addFlash('success', 'Votre commentaire a été soumis et sera visible après validation.');
                return $this->redirectToRoute('app_formation_show', ['slug' => $formation->getSlug()]);
            } else {
                $this->addFlash('error', 'Il y a eu un problème avec votre commentaire. Veuillez réessayer.');
            }
        }
    
        return $this->render('review/review.html.twig', [
            'form' => $form->createView(),
            'formation' => $formation,
        ]);
    }
}    