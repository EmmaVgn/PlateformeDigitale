<?php

namespace App\Controller;

use App\Entity\FeedbackFormation;
use App\Entity\Formation;
use App\Form\FeedbackFormationType;
use App\Repository\UserFormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\DateTime\DateTimeImmutable;

class FeedbackFormationController extends AbstractController
{
    #[Route('/formation/{id}/feedback', name: 'app_feedback_formation')]
    #[IsGranted('ROLE_USER')]
    public function feedback(
        Formation $formation,
        Request $request,
        EntityManagerInterface $em,
        UserFormationRepository $userFormationRepository
    ): Response {

        $user = $this->getUser();

        // Vérifie si l'utilisateur a complété la formation (modules et quiz)
        $userFormation = $userFormationRepository->findOneBy([
            'user' => $user,
            'formation' => $formation,
        ]);

        if (!$userFormation || !$userFormation->isCompleted()) {
            $this->addFlash('warning', 'Vous devez terminer la formation avant de donner votre avis.');
            return $this->redirectToRoute('app_formations');
        }

        // Vérifie si un feedback existe déjà
        foreach ($formation->getFeedbackFormations() as $feedback) {
            if ($feedback->getUser() === $user) {
                $this->addFlash('info', 'Vous avez déjà donné votre avis sur cette formation.');
                return $this->redirectToRoute('app_formation_show', [
                    'id' => $formation->getId(),
                    'slug' => $formation->getSlug()
                ]);
            }
        }

        $feedback = new FeedbackFormation();
        $form = $this->createForm(FeedbackFormationType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $feedback->setUser($user);
            $feedback->setFormation($formation);
            $feedback->setCreatedAt(new \DateTimeImmutable());

            $em->persist($feedback);
            $em->flush();

            $this->addFlash('success', 'Merci pour votre retour sur cette formation !');

            return $this->redirectToRoute('app_formation_show', [
                'id' => $formation->getId(),
                'slug' => $formation->getSlug()
            ]);
        }

        return $this->render('feedback_formation/form.html.twig', [
            'form' => $form->createView(),
            'formation' => $formation
        ]);
    }
}
