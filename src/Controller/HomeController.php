<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Service\SendMailService;
use App\Repository\ReviewRepository;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(FormationRepository $formationRepository, ReviewRepository $reviewRepository): Response
    {
        $formations = $formationRepository->findPublished(3);

        $reviews = $reviewRepository->findBy(['isValidated' => true], ['createdAt' => 'DESC'], 3);
        $averageRating = $reviewRepository->averageRating();

        $latestReviews = $reviewRepository->findBy(
            ['isValidated' => true], // ou selon ta logique de validation
            ['createdAt' => 'DESC'],
            2 // les 2 derniers avis par exemple
        );

        return $this->render('home/index.html.twig', [
            'formations' => $formations,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'latestReviews' => $latestReviews,
        ]);
    }

    #[Route('/donner-avis', name: 'home_notice')]
    public function notice(Request $request, EntityManagerInterface $em, SendMailService $mail): Response
{
    $review = new Review();
    $form = $this->createForm(ReviewType::class, $review);
    $form->handleRequest($request);


    // Debug: afficher les données reçues par le formulaire
    if ($form->isSubmitted() && $form->isValid()) {
        $user = $this->getUser();
        if ($user !== null) {
            $review->setUser($user);
        }

        $em->persist($review);
        $em->flush();

        $mail->sendEmail(
            'contact@cameleon-solutions.fr',
            'Demande de contact',
            'contact@cameleon-solutions.fr',
            'Nouveau commentaire sur le site',
            'review',
            []
        );

        $this->addFlash('success', 'Votre avis a bien été envoyé, il sera publié après validation !');
        return $this->redirectToRoute('homepage');
    }

    return $this->render('home/notice.html.twig', [
        'form' => $form->createView()
    ]);
}

}
